<?php

namespace App\Models;

const DIRECTIONS = ['N', 'E', 'S', 'W'];

class Robot
{
    private Position $scentPosition;
    private static array $scents = [];

    public function __construct(
        private Position $position,
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
            ? "{$this->scentPosition->x}, {$this->scentPosition->y}, {$this->facing} LOST"
            : "{$this->position->x}, {$this->position->y}, {$this->facing}";
    }

    private function forward(): void
    {
        $next = clone $this->position; // Clone the position to avoid modifying the original

        match ($this->facing) {
            'N' => $next->y++,
            'E' => $next->x++,
            'W' => $next->x--,
            'S' => $next->y--,
        };

        if (!$this->grid->isInBounds($next)) {
            $key = "{$this->position->x},{$this->position->y},{$this->facing}";
            if (!isset(self::$scents[$key])) {
                self::$scents[$key] = true;
                $this->lost = true;
                $this->scentPosition = clone $this->position;
            }
            return;
        }

        $this->position = $next;
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