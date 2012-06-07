<?php
    class JpegFormat implements RenderFormat
    {
        public function getMimeType()
        {
            return 'image/jpeg';
        }
        
        public function generate($image, array $config)
        {
            $quality = 75;
            if (isset($config['quality']))
            {
                $quality = intval($config['quality']);
            }
            if (@imagejpeg($image, null, $quality))
            {
                throw new RenderException('Failed to generate the jpeg image!');
            }
        }
    }
