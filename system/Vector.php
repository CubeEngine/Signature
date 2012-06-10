<?php
    class Vector
    {
        public $x;
        public $y;
        public $z;

        public function __construct($x = 0, $y = 0, $z = 0)
        {
            $this->x = $x;
            $this->y = $y;
            $this->z = $z;
        }

        public function add(Vector $other)
        {
            return new Vector($this->x + $other->x, $this->y + $other->y, $this->z + $other->z);
        }
    }