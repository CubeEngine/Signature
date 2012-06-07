<?php
    class PlayerheadObject implements RenderObject
    {
        const SKIN_BASEURL = 'http://s3.amazonaws.com/MinecraftSkins/';
        const DEFAULT_SKIN = '/gfx/char.png';
        const HEADSIZE = 8;
        
        public function render(Signature $sig, $image, array $config)
        {
            $player = $sig->getParam('player');
            $playerhead = null;
            $playerSkinUrl = self::SKIN_BASEURL . $player . '.png';

            if (@getimagesize($playerSkinUrl) === false)
            { // default
                $playerhead = @imagecreatefrompng($sig->resolveResource(self::DEFAULT_SKIN));
            }
            else
            { // custom
                $playerhead = @imagecreatefrompng($playerSkinUrl);
            }
            if (!is_resource($playerhead))
            {
                throw new RenderException('Failed to read a skin file for the given player!');
            }
            $result = imagecopyresampled(
                $image,
                $playerhead,
                $config['position']->getX(),
                $config['position']->getY(),
                self::HEADSIZE,
                self::HEADSIZE,
                $config['size'],
                $config['size'],
                self::HEADSIZE,
                self::HEADSIZE
            );
            if ($result === false)
            {
                throw new RenderException('Failed to copy and resample the player head!');
            }
            @imagedestroy($playerhead);
        }
    }