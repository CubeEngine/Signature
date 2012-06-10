<?php
    class SteveObject implements RenderObject
    {
        public function render(Signature $sig, $image, array $config)
        {
            $skin = Util::getSkin($sig, $sig->getParam('player'));

            $scale = 4;
            if (isset($config['scale']) && is_int($config['scale']))
            {
                $scale = $config['scale'];
            }
            $steve = new Steve($skin, $scale);
            $steveImage = $steve->renderImage();
            imagecopy($image, $steveImage, 150, 10, 0, 0, $steve->getWidth(), $steve->getHeight());
            imagedestroy($skin);
            imagedestroy($steveImage);
        }
    }