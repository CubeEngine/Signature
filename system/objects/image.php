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
                        $config['position']['to'][0],
                        $config['position']['to'][1],
                        $config['position']['from'][0],
                        $config['position']['from'][1],
                        $config['metrics']['to'][0],
                        $config['metrics']['to'][1],
                        $config['metrics']['from'][0],
                        $config['metrics']['from'][1]
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
    }