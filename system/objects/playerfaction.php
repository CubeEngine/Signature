<?php
    class PlayerfactionObject extends TextObject
    {
        public function render(Signature $sig, $image, array $config)
        {
            $faction = 'placeholder';
            // retrieve faction

            $config['text'] = $faction;
            parent::render($sig, $image, $config);
        }
    }