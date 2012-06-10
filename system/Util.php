<?php
    class Util
    {
        public static function renderText(&$image, $text, $font, $size, $angle, Vector $position, Color $color)
        {
            $color = $color->allocate($image);
            //imagealphablending($image, true);
            if (@imagettftext($image, $size, $angle, $position->x, $position->y, $color, $font, $text) === false)
            {
                throw new RenderException('Failed to write text on the image!');
            }
            Color::deallocate($image, $color);
        }
        
        public static function log($title, $message)
        {
            $string  = "====== $title ======\n";
            $string .= trim(strip_tags(str_replace('/(<br\s*/?>)+/i', "\n", $message)));
            $string .= "\n\n";
            
            $path = Loader::getPath() . 'log' . DIRECTORY_SEPARATOR;
            @mkdir($path, 0666, true);
            file_put_contents($path . 'error.log', $string, FILE_APPEND);
        }

        const SKIN_BASEURL = 'http://s3.amazonaws.com/MinecraftSkins/';
        const DEFAULT_SKIN = '/gfx/char.png';
        
        public static function getSkin(Signature $sig, $player)
        {
            $playerSkinUrl = self::SKIN_BASEURL . $player . '.png';

            $image = @imagecreatefrompng($playerSkinUrl);
            if ($image === false)
            {
                $image = @imagecreatefrompng($sig->resolveResource(self::DEFAULT_SKIN));
            }
            if (!is_resource($image))
            {
                throw new RenderException('Failed to read a skin file for the given player!');
            }

            return $image;
        }
    }