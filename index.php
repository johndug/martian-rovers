<?php

/* accept size of grid
 * accept rovers placement according to the grid and facing (n, e, w, s)
 * if outside of the grid lost
 * rovers finish one after the next, no running workers
 */

// listed in clockwise order
const FILE_INPUT = 'input.txt';
const DIRECTIONS = ['N', 'E', 'S', 'W'];

class Position {
    function __construct(
        public int $x,
        public int $y,
    ) {}
}

class Grid {
    function __construct(
        private int $width,
        private int $height
    ) {}

    public function isInBounds(Position $position): bool {
        return $position->x >= 0 && $position->x <= $this->width && $position->y >= 0 && $position->y <= $this->height;
    }
}

class Robot {
    private Position $scentPosition;
    private static array $scents = [];

    function __construct(
        private Position $position,
        private string $facing,
        private Grid $grid,
        private $lost = false,
    ) {}

    public function command(string $command): void {
        if ($this->lost) {
            return;
        }

        match ($command) {
            'F' => $this->forward(),
            'L' => $this->turnLeft(),
            'R' => $this->turnRight(),
        };
    }

    public function position(): string {
        return $this->lost ?
            "{$this->scentPosition->x}, {$this->scentPosition->y}, {$this->facing} LOST" :
            "{$this->position->x}, {$this->position->y}, {$this->facing}";
    }

    private function forward(): void {
        $next = clone $this->position;

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

    private function turnLeft(): void {
        $index = array_search($this->facing, DIRECTIONS);
        $index = ($index - 1 + 4) % 4;
        $this->facing = DIRECTIONS[$index];
    }

    private function turnRight(): void {
        $index = array_search($this->facing, DIRECTIONS);
        $index = ($index + 1) % 4;
        $this->facing = DIRECTIONS[$index];
    }
}


$instructions = file(FILE_INPUT, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Parse grid dimensions
[$width, $height] = explode(" ", $instructions[0]);

if ($width > 50 || $height > 50) {
    throw new Exception("Grid dimensions must be less than 50");
}

$grid = new Grid((int)$width, (int)$height);

if (count($instructions) < 3) {
    throw new Exception("Invalid instructions");
}

if (strlen($instructions[1]) > 100) {
    throw new Exception("Commands must be less than 100 chars");
}

// Process robot data (skip first line)
for ($i = 1; $i < count($instructions); $i += 2) {
    if (isset($instructions[$i + 1]) && isset($instructions[$i])) {
        [$x, $y, $facing] = explode(" ", $instructions[$i]);
        $commandLine = $instructions[$i + 1];

        $robot = new Robot(
            new Position((int)$x, (int)$y), 
            $facing, 
            $grid,
        );
        
        // Execute commands
        $commands = str_split(trim($commandLine));

        foreach ($commands as $cmd) {
            $robot->command($cmd);
        }

        echo $robot->position() . "\n";
    }
}