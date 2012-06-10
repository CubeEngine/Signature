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
            self::$objectPath = 'objects' . DIRECTORY_SEPARATOR;
            self::$formatPath = 'formats' . DIRECTORY_SEPARATOR;
            spl_autoload_register(array('Loader', 'load'));
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

            $file = basename($file);

            header('HTTP/1.1 500 Internal Server Error');
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
            header('HTTP/1.1 500 Internal Server Error');
            if (self::DEBUG)
            {
                echo '<pre>';
                echo "[$class] $message @ $file:$line\n\n";
                echo $e->getTraceAsString();
                echo '</pre>';
            }
            Util::log("$class @ $file:$line", $message);
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
                self::load(self::$objectPath . $name);
            }
            if (class_exists($clazz))
            {
                return $clazz;
            }
            return null;
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
                self::load(self::$formatPath . $name);
            }
            if (class_exists($clazz))
            {
                return $clazz;
            }
            return null;
        }

        public static function load($path)
        {
            $path = self::$path . $path . '.php';
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