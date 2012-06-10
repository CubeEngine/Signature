<?php
    class ImageObject implements RenderObject
    {
        public function render(Signature $sig, $image, array $config)
        {
            if (@getimagesize($config['source']))
            {
                $subimage = @imagecreatefromstring(@file_get_contents($config['source']));
                if ($subimage !== false)
                {
                    $result = @imagecopyresampled(
                        $image,
                        $subimage,
                        $config['position']['to']->x,
                        $config['position']['to']->y,
                        $config['position']['from']->x,
                        $config['position']['from']->y,
                        $config['metrics']['to']->x,
                        $config['metrics']['to']->y,
                        $config['metrics']['from']->x,
                        $config['metrics']['from']->y
                    );
                    if ($result === false)
                    {
                        throw new RenderException('Failed to copy and resample a configured image!');
                    }
                    @imagedestroy($subimage);
                }
                else
                {
                    throw new RenderException('Failed to load a configured image!');
                }
            }
        }

        public function requiredOptions()
        {
            return array('position', 'metrics');
        }
    }