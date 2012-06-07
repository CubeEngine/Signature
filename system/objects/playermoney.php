<?php
    class PlayermoneyObject extends TextObject
    {
        public function render(Signature $sig, $image, array $config)
        {
            $money = 'placeholder';
            // retrieve money
            // format money

            $config['text'] = $money;
            parent::render($sig, $image, $config);
        }
    }