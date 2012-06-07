<?php
    class TextObject implements RenderObject
    {
        public function render(Signature $sig, $image, array $config)
        {
            if (isset($config['text'], $config['font'], $config['size'], $config['angle'], $config['position'], $config['color']))
            {
                Util::renderText($image, $sig->parseString($config['text']), $sig->getFont($config['font']), $config['size'], $config['angle'], $config['position'], $config['color']);
            }
            else
            {
                throw new RenderException('Not all necessary parameters were given!');
            }
        }
    }