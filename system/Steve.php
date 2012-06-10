<?php
    class Steve
    {
        private $skin;
        private $scale;
        private $width;
        private $height;
        private $unit;

        public function __construct($skin, $scale)
        {
            $this->skin = $skin;
            $this->scale = $scale;
            $this->unit = $this->scale * 4;
            
            $this->width = round(4 * $this->unit);
            $this->height = round(8 * $this->unit);
        }

        public function renderImage()
        {
            $u =& $this->unit;
            $u2 = $u * 2;
            $u3 = $u * 3;

            $image = imagecreatetruecolor($this->width, $this->height);
            $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $color);
            imagecolordeallocate($image, $color);

            // face
            imagecopyresampled($image, $this->skin, $u, 0, 8, 8, $u2, $u2, 8, 8);

            // face hat
            imagecopyresampled($image, $this->skin, $u, 0, 40, 8, $u2, $u2, 8, 8);

            // left arm front
            imagecopyresampled($image, $this->skin, 0, $u2, 44, 20, $u, $u3, 4, 12);

            // right arm front (left arm flipped)
            imagecopyresampled($image, $this->skin, $u3, $u2, 47, 20, $u, $u3, -4, 12);

            // body front
            imagecopyresampled($image, $this->skin, $u, $u2, 20, 20, $u2, $u3, 8, 12);

            // left leg front
            imagecopyresampled($image, $this->skin, $u, $u2 + $u3, 4, 20, $u, $u3, 4, 12);

            // right leg front (left leg flipped)
            imagecopyresampled($image, $this->skin, $u2, $u2 + $u3, 7, 20, $u, $u3, -4, 12);

            return $image;
        }

        public function getWidth()
        {
            return $this->width;
        }

        public function getHeight()
        {
            return $this->height;
        }
    }