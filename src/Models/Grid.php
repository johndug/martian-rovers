<?php

namespace App\Models;

class Grid
{
    public function __construct(
        private int $width,
        private int $height
    ) {}

    public function isInBounds(Position $position): bool
    {
        return $position->x >= 0 
            && $position->x <= $this->width 
            && $position->y >= 0 
            && $position->y <= $this->height;
    }
}