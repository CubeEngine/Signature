<?php
    require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'Loader.php';
    Loader::initialize();

    $signature = new Signature(new Request());
    $signature->out();

/*
    if (empty($imageData))
    {
        if ($config['basics']['paidonly'])
        {
            if (strtolower(trim(file_get_contents('http://www.minecraft.net/haspaid.jsp?user=' . $player))) == 'false')
            {
                die('The given player has not bought minecraft!');
            }
        }
    }
*/
?>