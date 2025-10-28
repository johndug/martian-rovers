<?php

namespace App\Models;

class Grid
{
    public function __construct(
        private int $width,
        private int $height,
        private array $scents = [],
    ) {}

    

    public function isInBounds(int $x, int $y): bool
    {
        return $x >= 0 
            && $x <= $this->width
            && $y >= 0 
            && $y <= $this->height;
    }

    public function hasScent(int $x, int $y, string $direction): bool
    {
        return isset($this->scents["{$x},{$y},{$direction}"]);
    }

    public function addScent(int $x, int $y, string $direction): void
    {
        $this->scents["{$x},{$y},{$direction}"] = true;
    }
}