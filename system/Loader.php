<?php
    class Loader
    {
        private static $path;
        private static $objectPath;
        private static $formatPath;

        const DEBUG = true;

        private function __construct()
        {}

        public static function initialize()
        {
            error_reporting(-1);
            set_error_handler(array(__CLASS__, 'onError'), -1);
            set_exception_handler(array(__CLASS__, 'onException'));
            self::$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
            self::$objectPath = self::$path . 'objects' . DIRECTORY_SEPARATOR;
            self::$formatPath = self::$path . 'formats' . DIRECTORY_SEPARATOR;
            spl_autoload_register(array('Loader', 'autoload'));
        }

        public static function autoload($class)
        {
            self::load(self::$path . $class . '.php');
        }

        public static function onError($errno, $message, $file, $line)
        {
            if (!error_reporting())
            {
                return;
            }
            $type = 'unknown';
            switch ($errno)
            {
                case E_WARNING:
                    $type = 'warning';
                    break;
                case E_NOTICE:
                    $type = 'notice';
                    break;
                case E_STRICT:
                    $type = 'strict';
                    break;
            }

            $errfile = basename($errfile);
            
            if (self::DEBUG)
            {
                echo "[$type] $message @ $file:$line";
            }
            Util::log("$type @ $file:$line", $message);
            die();
        }

        public static function onException(Exception $e)
        {
            $class = get_class($e);
            $file = basename($e->getFile());
            $line = $e->getLine();
            $message = $e->getMessage();
            if (self::DEBUG)
            {
                echo '<pre>';
                echo "[$class] $message @ $file:$line\n\n";
                echo $e->getTraceAsString();
                echo '</pre>';
            }
            Util::log($class @ $file:$line", $message);
            die();
        }

        /**
         * @param string $name the name of the object
         * @return ImageObject
         */
        public static function object($name)
        {
            $name = strtolower($name);
            $clazz = ucfirst($name) . 'Object';
            if (!class_exists($clazz))
            {
                self::load(self::$objectPath . $name . '.php');
            }
            if (class_exists($clazz))
            {
                $instance = new $clazz();
                if ($instance instanceof RenderObject)
                {
                    return $instance;
                }
            }
            throw new RenderException('Could not find the render object ' . $name);
        }

        /**
         * @param string $name the name of the object
         * @return ImageObject
         */
        public static function format($name)
        {
            $name = strtolower($name);
            $clazz = ucfirst($name) . 'Format';
            if (!class_exists($clazz))
            {
                self::load(self::$formatPath . $name . '.php');
            }
            if (class_exists($clazz))
            {
                $instance = new $clazz();
                if ($instance instanceof RenderFormat)
                {
                    return $instance;
                }
            }
            throw new RenderException('Could not find the render format ' . $name);
        }

        private static function load($path)
        {
            if (is_readable($path))
            {
                require $path;
            }
        }
        
        public static function getPath()
        {
            return self::$path;
        }
    }