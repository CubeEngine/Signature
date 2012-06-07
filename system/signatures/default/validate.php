<?php
    function validate(Signature $sig)
    {
        $config = $sig->getConfig();
        if (isset($config['basics']['paidonly']) && $config['basics']['paidonly'])
        {
            return (strtolower(trim(file_get_contents('http://www.minecraft.net/haspaid.jsp?user=' . $sig->getParam('player')))) != 'false');
        }
        return true;
    }

