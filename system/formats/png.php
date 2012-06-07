<?php
    class PngFormat implements RenderFormat
    {
        public function getMimeType()
        {
            return 'image/png';
        }

        public function generate($image, array $config)
        {
            $quality = 0;
            if (isset($config['quality']))
            {
                $quality = intval($config['quality']);
            }
            
            $filters = PNG_NO_FILTER;
            if (isset($config['filters']))
            {
                $filters = intval($config['filters']);
            }

            if (isset($config['savealpha']) && is_bool($config['savealpha']))
            {
                @imagesavealpha($image, $config['savealpha']);
            }

            if (isset($config['alphablending']) && is_bool($config['alphablending']))
            {
                @imagesavealpha($image, $config['alphablending']);
            }
            
            if (@imagepng($image, null, $quality, $filters) === false)
            {
                throw new RenderException('Failed to generate the image!');
            }
        }
    }