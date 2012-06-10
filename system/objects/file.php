<?php
    class FileObject extends TextObject
    {
        public function render(Signature $sig, $image, array $config)
        {
            if (isset($config['file']))
            {
                $content = @file_get_contents($config['file']);
                if ($content)
                {
                    $config['text'] = $content;
                    parent::render($image, $config);
                }
                else
                {
                    throw new RenderException('Failed to load the file!');
                }
            }
            else
            {
                throw new RenderException('No file was specified!');
            }
        }

        public function requiredOptions()
        {
            $options = parent::requiredOptions();

            $options[] = 'file';

            return $options;
        }
    }