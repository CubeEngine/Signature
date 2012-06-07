<?php
    class Signature
    {
        private $request;
        private $config;
        private $params;
        private $paramNames;
        private $paramValues;
        private $fonts;
        private $databases;
        private $objectDefaults;
        private $background;
        private $cacheLifetime;
        private $format;
        
        private $basePath;
        private $resourcePath;
        private $cachePath;

        const BUFFERSIZE = 8196;
        const CACHE_PERMISSIONS = 0666;

        public function __construct(Request $request)
        {
            $this->request = $request;

            $this->basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'signatures' . DIRECTORY_SEPARATOR . $request->getSignature() . DIRECTORY_SEPARATOR;
            if (is_dir($this->basePath))
            {
                $configPath = $this->basePath . 'config.php';
                if (is_readable($configPath))
                {
                    $this->config = include $configPath;

                    // TODO holy shit, this should be cleaner
                    if (isset($this->config['basics'], $this->config['basics']['background'], $this->config['objects']) &&
                        is_array($this->config['basics']) &&
                        is_array($this->config['objects']))
                    {
                        $basics =& $this->config['basics'];
                        $this->resourcePath = $this->basePath . 'resources' . DIRECTORY_SEPARATOR;

                        // name the params
                        if (isset($basics['params']) && is_array($basics['params']))
                        {
                            $argv = $this->request->getArgs();
                            $argc = count($argv);
                            $paramCount = count($basics['params']);
                            if ($argc < $paramCount)
                            {
                                throw new Exception('Not all parameters where given!');
                            }
                            for ($i = 0; $i < $paramCount; ++$i)
                            {
                                $this->params[$basics['params'][$i]] = $argv[$i];
                                $this->paramNames[] = '{' . $basics['params'][$i] . '}';
                            }
                            $this->paramValues = array_values($this->params);
                        }

                        // generate the cacheKey from the params
                        $this->cachePath = $this->basePath . 'cache' . DIRECTORY_SEPARATOR . preg_replace('/[^\w\d]/i', '_', implode('-', $this->paramValues));

                        // get the cache lifetime
                        $this->cacheLifetime = 0;
                        if (isset($basics['cache_lifetime']) && is_int($basics['cache_lifetime']))
                        {
                            $this->cacheLifetime = $basics['cache_lifetime'];
                        }

                        // fill the db pool
                        if (isset($basics['databases']) && is_array($basics['databases']))
                        {
                            $this->databases = array();
                            foreach ($basics['databases'] as $name => $entry)
                            {
                                if (is_array($entry) && isset($entry['dsn'], $entry['user'], $entry['pass']))
                                {
                                    try
                                    {
                                        $this->databases[$name] = new PDO($entry['dsn'], $entry['user'], $entry['pass']);
                                    }
                                    catch (PDOException $e)
                                    {
                                        throw new Exception('The database connection for "' . $name . '" could not be established!', $e->getCode(), $e);
                                    }
                                }
                            }
                        }

                        // fill the font pool
                        if (isset($basics['fonts']) && is_array($basics['fonts']))
                        {
                            $this->fonts = array();
                            foreach ($basics['fonts'] as $name => $path)
                            {
                                $this->fonts[$name] = $this->resolveResource($path);
                            }
                        }

                        // the object defaults
                        if (isset($basics['defaults']) && is_array($basics['defaults']))
                        {
                            $this->objectDefaults =& $basics['defaults'];
                        }
                        else
                        {
                            $this->objectDefaults = array();
                        }

                        // resolve the background
                        if (isset($basics['background']))
                        {
                            $this->background = $this->resolveResource($basics['background']);
                            if (!is_readable($this->background))
                            {
                                throw new Exception('The background does not exist!');
                            }
                        }

                        // load image format
                        $formatType = 'png';
                        if (isset($basics['format']) && is_array($basics['format']) && isset($basics['format']['type']))
                        {
                            $formatType =& $basics['format']['type'];
                        }
                        $this->format = Loader::format($formatType);
                    }
                    else
                    {
                        throw new Exception('This signature\'s config is invalid!');
                    }
                }
                else
                {
                    throw new Exception('This signature is invalid!');
                }
            }
            else
            {
                throw new Exception('Signature not found!');
            }
        }

        public function getRequest()
        {
            return $this->request;
        }

        public function parseString($string)
        {
            return str_replace($this->paramNames, $this->paramValues, $string);
        }

        public function resolveResource($path)
        {
            static $slashes = array('/', '\\');
            if ($path[0] == '/')
            {
                $path = str_replace($slashes, DIRECTORY_SEPARATOR, substr($path, 1));
                return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $path;
            }
            else
            {
                return $this->resourcePath . DIRECTORY_SEPARATOR . str_replace($slashes, DIRECTORY_SEPARATOR, $path);
            }
        }

        public function getFont($name)
        {
            if (isset($this->fonts[$name]))
            {
                return $this->fonts[$name];
            }
            throw new RenderException("Font '$name' not found!");
        }

        public function getDatabase($name)
        {
            if (isset($this->databases[$name]))
            {
                return $this->databases[$name];
            }
            throw new RenderException("Database '$name' not found!");
        }

        public function getParam($name)
        {
            if (isset($this->params[$name]))
            {
                return $this->params[$name];
            }
            return null;
        }

        public function render()
        {
            if (is_readable($this->basePath . 'validate.php'))
            {
                include $this->basePath . 'validate.php';
                if (function_exists('validate'))
                {
                    if (!validate($this))
                    {
                        throw new RenderException('The request was not valid!');
                    }
                }
            }
            $signature = @imagecreatefrompng($this->background);
            if ($signature === false)
            {
                throw new RenderException('Failed to load the configured background image!');
            }
            if (isset($this->config['basics']['alphablending']) && @imagealphablending($signature, $this->config['basics']['alphablending']) === false)
            {
                throw new RenderException('Failed to set the configured alpha blending mode!');
            }
            if (isset($this->config['basics']['savealpha']) && @imagesavealpha($signature, $this->config['basics']['savealpha']) === false)
            {
                throw new RenderException('Failed to set the configured savealpha mode');
            }

            foreach ($this->config['objects'] as $objectCfg)
            {
                if (is_array($objectCfg) && isset($objectCfg['type']))
                {
                    $object = Loader::object($objectCfg['type']);
                    $object->render($this, $signature, array_merge($this->objectDefaults, $objectCfg));
                }
            }

            $formatCfg =& $this->config['basics']['format'];

            ob_start();
            $this->format->generate($signature, $formatCfg);
            $data = ob_get_clean();
            @imagedestroy($signature);
            $this->cache($data);
            
            return $data;
        }

        public function get()
        {
            $data = $this->getCached();
            if (!$data)
            {
                $data = $this->render();
            }

            return $data;
        }

        public function out()
        {
            header('X-Powered-By: Cube Island');
            header('Content-Type: ' . $this->format->getMimeType());

            echo $this->get();
        }

        private function getCached()
        {
            $data = null;
            if ($this->cacheLifetime <= 0)
            {
                return null;
            }
            if (file_exists($this->cachePath))
            {
                $file = @fopen($this->cachePath, 'rb');
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
                                $data .= fread($file, self::BUFFERSIZE);
                            }
                        }
                        else
                        {
                            fclose($file);
                            unlink($this->cachePath);
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
            return $data;
        }

        private function cache(&$data)
        {
            if ($this->cacheLifetime > 0)
            {
                @mkdir(dirname($this->cachePath), self::CACHE_PERMISSIONS, true);
                $file = @fopen($this->cachePath, 'wb');
                if ($file !== false)
                {
                    @flock($file, LOCK_EX);
                    $timestamp = str_pad(time() + $this->cacheLifetime, 15, "\0", STR_PAD_RIGHT);
                    @fwrite($file, $timestamp . $data, strlen($data) + 15);
                    @flock($file, LOCK_UN);
                    @fclose($file);
                }
                else
                {
                    throw new Exception('Failed to cache the image!');
                }
            }
        }

        public function getFormat()
        {
            return $this->format;
        }

        public function getConfig()
        {
            return $this->config;
        }
    }