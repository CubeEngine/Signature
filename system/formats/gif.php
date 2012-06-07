<?php
    class GifFormat implements RenderFormat
    {
        public function getMimeType()
        {
            return 'image/gif';
        }

        public function generate($image, array $config)
        {
            if (@imagegif($image) === false)
            {
                throw new RenderException('Failed to generate the gif image!');
            }
        }
    }