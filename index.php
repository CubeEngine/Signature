<?php
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'Loader.php';
    Loader::initialize();

    $signature = new Signature(new Request());
    $signature->out();
?>