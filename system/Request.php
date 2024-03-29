<?php
    class Request
    {
        private $signature;
        private $args;

        public function __construct()
        {
            if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
            {
                $route = trim(trim($_SERVER['QUERY_STRING'], '/'));
                $segments = explode('/', $route);
                if (count($segments) > 0 && strlen($segments[0] = trim($segments[0])))
                {
                    $this->signature = strtolower(trim($segments[0]));
                    unset($segments[0]);
                }
                else
                {
                    throw new Exception('No signature specified!');
                }
                $this->args = array_merge($segments);
            }
            else
            {
                throw new Exception('No route specified!');
            }
        }

        public function getSignature()
        {
            return $this->signature;
        }

        public function getArgs()
        {
            return $this->args;
        }
    }