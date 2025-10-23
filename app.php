<?php
const FILE_INPUT = 'input.txt';
const DIRECTIONS = ['N', 'E', 'S', 'W']; // Listed in clockwise order

class Position
{
    public function __construct(
        public int $x,
        public int $y,
    ) {}
}

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

try {
    // read arguments from command line or default to input.txt
    $args = $argv[1] ?? FILE_INPUT;
    $instructions = file($args, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if (empty($instructions)) {
        throw new Exception("Input file is empty or not found");
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
    die;
}