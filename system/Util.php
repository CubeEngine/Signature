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
    }