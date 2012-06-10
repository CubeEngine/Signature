<?php
    class PlayerheadObject implements RenderObject
    {
        const HEADSIZE = 8;
        
        public function render(Signature $sig, $image, array $config)
        {
            $skin = Util::getSkin($sig, $sig->getParam('player'));
            $result = @imagecopyresampled($image, $skin, $config['position']->x, $config['position']->y, self::HEADSIZE, self::HEADSIZE, $config['size'], $config['size'], self::HEADSIZE, self::HEADSIZE);
            $result = @imagecopyresampled($image, $skin, $config['position']->x, $config['position']->y, self::HEADSIZE * 5, self::HEADSIZE, $config['size'], $config['size'], self::HEADSIZE, self::HEADSIZE);
            if ($result === false)
            {
                throw new RenderException('Failed to copy and resample the player head!');
            }
            @imagedestroy($skin);
        }
    }