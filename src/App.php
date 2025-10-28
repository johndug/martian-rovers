<?php

namespace App;

use App\Models\Grid;
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
            
            
            $handle = fopen($args, 'r');
            if (!$handle) {
                throw new Exception("Cannot open file: " . $args);
            }
        
            // Parse grid dimensions
            [$width, $height] = preg_split('/\s+/', trim($this->readFileLine($handle)));
            
            if ($width > 50 || $height > 50) {
                throw new Exception("Grid dimensions must be less than 50");
            }
        
            $grid = new Grid((int)$width, (int)$height);
        
            // Process robot data (skip first line)
            while (($line = $this->readFileLine($handle)) !== false) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                [$x, $y, $facing] = preg_split('/\s+/', $line);
                $commandLine = $this->readFileLine($handle);

                $robot = new Robot(
                    (int)$x,
                    (int)$y,
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

    private function readFileLine($handle): string|false
    {
        return fgets($handle);
    }
}