<?php

namespace App;

use App\Models\Grid;
use App\Models\Position;
use App\Models\Robot;
use Exception;

class App
{
    private const FILE_INPUT = 'input.txt';
    
    public function run()
    {
        global $argv;
        
        try {
            // read arguments from command line or default to input.txt
            $args = $argv[1] ?? self::FILE_INPUT;
            
            // Check if file exists and is readable
            if (!file_exists($args) || !is_readable($args)) {
                throw new Exception("File '$args' not found or not readable");
            }
            
            // Check if it's a text file by MIME type
            $mimeType = mime_content_type($args);
            if (!str_starts_with($mimeType, 'text/')) {
                throw new Exception("File '$args' is not a text file (MIME: $mimeType)");
            }
            
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
    }
}