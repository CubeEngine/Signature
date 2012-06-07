<?php
    interface RequestValidator
    {
        public function validate(Signature $sig);
    }