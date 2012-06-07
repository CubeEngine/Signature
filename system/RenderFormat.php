<?php
    interface RenderFormat
    {
        public function getMimeType();
        public function generate($image, array $config);
    }