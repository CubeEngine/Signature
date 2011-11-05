<?php
    error_reporting(-1);
    defined('DS')               or define('DS',               DIRECTORY_SEPARATOR);
    defined('BASE_PATH')        or define('BASE_PATH',        dirname(__FILE__));
    defined('CONFIG_PATH')      or define('CONFIG_PATH',      BASE_PATH . DS . 'configs');
    defined('RESOURCE_PATH')    or define('RESOURCE_PATH',    BASE_PATH . DS . 'resources');
    defined('CACHE_PATH')       or define('CACHE_PATH',       BASE_PATH . DS . 'cache');
    defined('CACHE_BUFFERSIZE') or define('CACHE_BUFFERSIZE', 8196);
    defined('SKIN_BASEURL')     or define('SKIN_BASEURL',     'http://s3.amazonaws.com/MinecraftSkins/');
    defined('SKIN_DEFAULT')     or define('SKIN_DEFAULT',     RESOURCE_PATH . DS . 'gfx' . DS . 'char.png');
    defined('SKIN_HEADSIZE')    or define('SKIN_HEADSIZE',    8);
    
    function writeText(&$image, $text, $font, $size, $angle, array $position, array $color)
    {
        $color = @imagecolorallocatealpha(
            $image,
            $color[0],
            $color[1],
            $color[2],
            $color[3]
        );
        if ($color === false)
        {
            die('Failed to allocate the configured font color!');
        }
        //imagealphablending($image, true);
        $result = @imagettftext($image, $size, $angle, $position[0], $position[1], $color, $font, $text);
        if ($result == false)
        {
            die('Failed to write text on the image!');
        }
        @imagecolordeallocate($image, $color);
        return $result;
    }
    
    if (!isset($_GET['player'], $_GET['config']))
    {
        die('Player or Configuration is missing!');
    }
    $player = trim($_GET['player']);
    if (!preg_match('/^[\w\d\.]+$/i', $player))
    {
        die('The given player is not a valid minecraft username!');
    }
    $config = trim($_GET['config']);
    if (!preg_match('/^[\w\d]+$/i', $config))
    {
        die('The given config is invalid!');
    }
    $cachePath = CACHE_PATH . DS . md5($config . $player) . '.cached';
    $imageData = '';
    
    if (file_exists($cachePath))
    {
        $file = @fopen($cachePath, 'rb');
        if ($file)
        {
            $filedeleted = false;
            flock($file, LOCK_EX);
            $timestamp = fread($file, 15);
            if ($timestamp)
            {
                $timestamp = intval(trim($timestamp, "\0"));
                if ($timestamp >= time())
                {
                    while(!feof($file))
                    {
                        $imageData .= fread($file, CACHE_BUFFERSIZE);
                    }
                }
                else
                {
                    fclose($file);
                    unlink($cachePath);
                    $filedeleted = true;
                }
            }
            if (!$filedeleted)
            {
                flock($file, LOCK_UN);
                fclose($file);
            }
        }
    }
    
    if (empty($imageData))
    {
        $configPath = CONFIG_PATH . DS . $config . '.php';
        if (!file_exists($configPath))
        {
            die('The given configuration does not exist!');
        }
        
        $config = require $configPath;
        if (
            !is_array($config) ||
            !isset($config['basics'], $config['data']) ||
            !is_array($config['basics']) ||
            !is_array($config['data'])
        )
        {
            die('The given config is invalid!');
        }
        
        if ($config['basics']['paidonly'])
        {
            if (strtolower(trim(file_get_contents('http://www.minecraft.net/haspaid.jsp?user=' . $player))) == 'false')
            {
                die('The given player has not bought minecraft!');
            }
        }
        
        $signature = @imagecreatefrompng($config['basics']['background']);
        if ($signature === false)
        {
            die('Failed to load the configured background image!');
        }
        $result = @imagealphablending($signature, $config['basics']['alphablending']);
        if ($result === false)
        {
            die('Failed to set the configured alpha blending mode!');
        }
        $result = @imagesavealpha($signature, $config['basics']['savealpha']);
        if ($result === false)
        {
            die('Failed to set the configured savealpha mode');
        }
        
        foreach ($config['data'] as $dataIndex => $dataBlock)
        {
            switch ($dataBlock['type'])
            {
                case 'playerhead':
                {
                    $playerhead = null;
                    $playerSkinUrl = SKIN_BASEURL . $player . '.png';

                    if (empty($player) || @getimagesize($playerSkinUrl) === false)
                    { // default
                        $playerhead = @imagecreatefrompng(SKIN_DEFAULT);
                    }
                    else
                    { // custom
                        $playerhead = @imagecreatefrompng($playerSkinUrl);
                    }
                    if (!is_resource($playerhead))
                    {
                        die('Failed to read a skin file for the given player!');
                    }
                    $result = @imagecopyresampled(
                        $signature,
                        $playerhead,
                        $dataBlock['position'][0],
                        $dataBlock['position'][1],
                        SKIN_HEADSIZE,
                        SKIN_HEADSIZE,
                        $dataBlock['size'],
                        $dataBlock['size'],
                        SKIN_HEADSIZE,
                        SKIN_HEADSIZE
                    );
                    if ($result === false)
                    {
                        die('Failed to copy and resample the player head!');
                    }
                    @imagedestroy($playerhead);
                    break;
                }
                case 'image':
                {
                    if (@getimagesize($dataBlock['source']))
                    {
                        $image = @imagecreatefromstring(@file_get_contents($dataBlock['source']));
                        if ($image !== false)
                        {
                            $result = @imagecopyresampled(
                                $signature,
                                $image,
                                $dataBlock['position']['to'][0],
                                $dataBlock['position']['to'][1],
                                $dataBlock['position']['from'][0],
                                $dataBlock['position']['from'][1],
                                $dataBlock['metrics']['to'][0],
                                $dataBlock['metrics']['to'][1],
                                $dataBlock['metrics']['from'][0],
                                $dataBlock['metrics']['from'][1]
                            );
                            if ($result === false)
                            {
                                die('Failed to copy and resample a configured image!');
                            }
                            @imagedestroy($image);
                        }
                        else
                        {
                            die('Failed to load a configured image!');
                        }
                    }
                }
                case 'playername':
                {
                    $dataBlock['text'] =& $player;
                }
                case 'text':
                {
                    writeText(
                        $signature,
                        $dataBlock['text'],
                        $dataBlock['font'],
                        $dataBlock['size'],
                        $dataBlock['angle'],
                        $dataBlock['position'],
                        $dataBlock['color']
                    );
                    break;
                }
                case 'file':
                {
                    $text = @file_get_contents($configBlock['source']);
                    if ($text !== false)
                    {
                        writeText(
                            $signature,
                            $text,
                            $dataBlock['font'],
                            $dataBlock['size'],
                            $dataBlock['angle'],
                            $dataBlock['position'],
                            $dataBlock['color']
                        );
                    }
                    break;
                }
                case 'database':
                {
                    $value = $dataBlock['default'];
                    try
                    {
                        $database = new PDO(
                            $dataBlock['source']['DSN'],
                            $dataBlock['source']['user'],
                            $dataBlock['source']['pass']
                        );
                        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $statement = $database->prepare($dataBlock['source']['query']);
                        $statement->execute(array($player));
                        $result = $statement->fetch(PDO::FETCH_NUM);
                        if (is_array($result))
                        {
                            $value = $result[0];
                        }
                        elseif ($dataBlock['required'])
                        {
                            die('A required database entry could not be found!');
                        }
                        $statement->closeCursor();
                        unset($result);
                        unset($statement);
                        unset($database);
                    }
                    catch (PDOException $e)
                    {}
                    writeText(
                        $signature,
                        strval($value),
                        $dataBlock['font'],
                        $dataBlock['size'],
                        $dataBlock['angle'],
                        $dataBlock['position'],
                        $dataBlock['color']
                    );
                    break;
                }
                default:
                {
                    die('Unknown type found!');
                }
            }
        }
        
        
        ob_start();
        $result = @imagepng($signature,
            null,
            $config['basics']['quality'],
            $config['basics']['filters']
        );
        if ($result === false)
        {
            die('Failed to generate the image!');
        }
        @imagedestroy($signature);
        $imageData = ob_get_clean();
        
        if ($config['basics']['cache_lifetime'] > 0)
        {
            $file = @fopen($cachePath, 'wb');
            if ($file !== false)
            {
                flock($file, LOCK_EX);
                $timestamp = str_pad(time() + $config['basics']['cache_lifetime'], 15, "\0", STR_PAD_RIGHT);
                fwrite($file, $timestamp . $imageData, strlen($imageData) + 15);
                flock($file, LOCK_UN);
                fclose($file);
            }
            else
            {
                die('Failed to cache the image!');
            }
        }
    }
    
    header('X-Powered-By: Code Infection');
    header('Content-Type: image/png');
    echo $imageData;
?>