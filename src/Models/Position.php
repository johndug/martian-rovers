<?php

namespace App\Models;

class Position
{
    public function __construct(
        public int $x,
        public int $y,
    ) {}
}