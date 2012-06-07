<?php
    class Color
    {
        private $red;
        private $green;
        private $blue;
        private $alpha;

        public function __construct()
        {
            $this->alpha = false;
            
            $argv = func_get_args();
            $argc = count($argv);

            if ($argc > 0)
            {
                if (is_string($argv[0]) && $argv[0][0] == '#')
                {
                    $hex = substr($argv[0], 1);
                    $hexLen = strlen($hex);
                    if ($hexLen == 3)
                    {
                        $this->red = hexdec(str_repeat(substr($hex, 0, 1), 2));
                        $this->green = hexdec(str_repeat(substr($hex, 1, 1), 2));
                        $this->blue = hexdec(str_repeat(substr($hex, 2, 1), 2));
                    }
                    elseif ($hexLen == 6)
                    {
                        $this->red = hexdec(substr($hex, 0, 2));
                        $this->green = hexdec(substr($hex, 2, 2));
                        $this->blue = hexdec(substr($hex, 4, 2));
                    }
                    else
                    {
                        throw new Exception('Invalid hex color given!');
                    }
                    if ($argc > 1 && is_int($argv[1]))
                    {
                        $this->alpha = $argv[1];
                    }
                }
                elseif ($argc > 2)
                {
                    $this->red = intval($argv[0]);
                    $this->green = intval($argv[1]);
                    $this->blue = intval($argv[2]);
                    if ($argc > 3 && is_int($argv[3]))
                    {
                        $this->alpha = $argv[1];
                    }
                }
                else
                {
                    throw new Exception('Could not detect a valid color scheme!');
                }
            }
            else
            {
                throw new Exception('No color specified!');
            }
        }

        public function allocate($image)
        {
            if (!is_resource($image))
            {
                throw new RenderException('Colors can only be allocated for image resources!');
            }
            $color = false;
            if ($this->alpha === false)
            {
                $color = @imagecolorallocate($image, $this->red, $this->green, $this->blue);
            }
            else
            {
                $color = @imagecolorallocatealpha($image, $this->red, $this->green, $this->blue, $this->alpha);
            }
            if ($color === false)
            {
                throw new RenderException('Failed to allocate the color!');
            }

            return $color;
        }

        public static function deallocate($image, $color)
        {
            @imagecolordeallocate($image, $color);
        }

        public function getRed()
        {
            return $this->red;
        }

        public function getGreen()
        {
            return $this->green;
        }

        public function getBlue()
        {
            return $this->blue;
        }

        public function getAlpha()
        {
            return $this->alpha;
        }
    }