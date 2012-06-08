<?php
    class Util
    {
        public static function renderText(&$image, $text, $font, $size, $angle, Vector2 $position, Color $color)
        {
            $color = $color->allocate($image);
            //imagealphablending($image, true);
            if (@imagettftext($image, $size, $angle, $position->getX(), $position->getY(), $color, $font, $text) === false)
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
    }