<?php

/**
 * Martian Rovers Simulation
 * 
 * Accepts grid size and rover placements with commands.
 * Rovers move sequentially and are marked as LOST if they go out of bounds.
 * Scent markers prevent other rovers from getting lost at the same position.
 */

// Configuration constants
const FILE_INPUT = 'input.txt';
const DIRECTIONS = ['N', 'E', 'S', 'W']; // Listed in clockwise order

/**
 * Represents a 2D position on the grid
 */
class Position
{
    public function __construct(
        public int $x,
        public int $y,
    ) {}
}

/**
 * Represents the Martian grid with boundaries
 */
class Grid
{
    public function __construct(
        private int $width,
        private int $height
    ) {}

    /**
     * Check if a position is within grid boundaries
     */
    public function isInBounds(Position $position): bool
    {
        return $position->x >= 0 
            && $position->x <= $this->width 
            && $position->y >= 0 
            && $position->y <= $this->height;
    }
}

/**
 * Represents a Martian rover with position, facing direction, and movement capabilities
 */
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

    /**
     * Execute a single command (F=forward, L=left turn, R=right turn)
     */
    public function command(string $command): void
    {
        if ($this->lost) {
            return;
        }

        match ($command) {
            'F' => $this->forward(),
            'L' => $this->turnLeft(),
            'R' => $this->turnRight(),
        };
    }

    /**
     * Get the current position and status as a formatted string
     */
    public function position(): string
    {
        return $this->lost
            ? "{$this->scentPosition->x}, {$this->scentPosition->y}, {$this->facing} LOST"
            : "{$this->position->x}, {$this->position->y}, {$this->facing}";
    }

    /**
     * Move the robot forward in its current facing direction
     */
    private function forward(): void
    {
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

    /**
     * Turn the robot 90 degrees to the left
     */
    private function turnLeft(): void
    {
        $index = array_search($this->facing, DIRECTIONS);
        $index = ($index - 1 + 4) % 4;
        $this->facing = DIRECTIONS[$index];
    }

    /**
     * Turn the robot 90 degrees to the right
     */
    private function turnRight(): void
    {
        $index = array_search($this->facing, DIRECTIONS);
        $index = ($index + 1) % 4;
        $this->facing = DIRECTIONS[$index];
    }
}

try {
    $instructions = file(FILE_INPUT, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if (empty($instructions)) {
        throw new Exception("Input file is empty");
    }

    // Parse grid dimensions
    [$width, $height] = explode(" ", $instructions[0]);
    
    if ($width > 50 || $height > 50) {
        throw new Exception("Grid dimensions must be less than 50");
    }

    $grid = new Grid((int)$width, (int)$height);

    if (count($instructions) < 3) {
        throw new Exception("Invalid instructions - need at least grid size and one robot");
    }

    // Process robot data (skip first line)
    for ($i = 1; $i < count($instructions); $i += 2) {
        if (!isset($instructions[$i]) || !isset($instructions[$i + 1])) {
            throw new Exception("Invalid robot data at line " . ($i + 1));
        }

        [$x, $y, $facing] = explode(" ", $instructions[$i]);
        $commandLine = $instructions[$i + 1];

        if (strlen($commandLine) > 100) {
            throw new Exception("Commands must be less than 100 characters");
        }

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
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}