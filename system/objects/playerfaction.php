<?php
    Loader::object('text');
    
    class PlayerfactionObject extends PlayermoneyObject
    {
        public function render(Signature $sig, $image, array $config)
        {
            $faction = 'placeholder';
            // retrieve faction

            $config['text'] = $faction;
            parent::render($sig, $image, $config);
        }
    }