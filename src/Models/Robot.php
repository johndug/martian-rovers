<?php

namespace App\Models;

const DIRECTIONS = ['N', 'E', 'S', 'W'];

class Robot
{
    private array $scentPosition = [];

    public function __construct(
        private int $x,
        private int $y,
        private string $facing,
        private Grid $grid,
        private bool $lost = false,
    ) {}

    public function command(string $command): void
    {
        if ($this->lost) {
            return;
        }

        match ($command) {
            'F' => $this->forward(), // Move forward
            'L' => $this->turnLeft(), // Turn left
            'R' => $this->turnRight(), // Turn right
        };
    }

    public function position(): string
    {
        return $this->lost
            ? "{$this->scentPosition['x']}, {$this->scentPosition['y']}, {$this->scentPosition['facing']} LOST"
            : "{$this->x}, {$this->y}, {$this->facing}";
    }

    private function forward(): void
    {
        $next = [
            'x' => $this->x,
            'y' => $this->y,
            'facing' => $this->facing,
        ];

        match ($this->facing) {
            'N' => $next['y']++,
            'E' => $next['x']++,
            'W' => $next['x']--,
            'S' => $next['y']--,
        };

        if (!$this->grid->isInBounds($next['x'], $next['y'])) {
            if (!$this->grid->hasScent($this->x, $this->y, $this->facing)) {
                $this->grid->addScent($this->x, $this->y, $this->facing);
                $this->lost = true;
                $this->scentPosition = ['x' => $this->x, 'y' => $this->y, 'facing' => $this->facing];
            }
            return;
        }

        $this->x = $next['x'];
        $this->y = $next['y'];
        $this->facing = $next['facing'];
    }

    private function turnLeft(): void
    {
        $index = array_search($this->facing, DIRECTIONS);
        $index = ($index - 1 + 4) % 4;
        $this->facing = DIRECTIONS[$index];
    }

    private function turnRight(): void
    {
        $index = array_search($this->facing, DIRECTIONS);
        $index = ($index + 1) % 4;
        $this->facing = DIRECTIONS[$index];
    }
}