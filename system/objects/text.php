<?php
    class TextObject implements RenderObject
    {
        public function render(Signature $sig, $image, array $config)
        {
            $angle = 0;
            if (isset($config['angle']) && is_numeric($config['angle']))
            {
                $angle = doubleval($config['angle']);
            }

            Util::renderText($image, $sig->parseString($config['text']), $sig->getFont($config['font']), $config['size'], $angle, $config['position'], $config['color']);
        }

        public function requiredOptions()
        {
            return array('text', 'font', 'size', 'position', 'color');
        }
    }