<?php

namespace Tests\Models;

use App\Models\Grid;
use App\Models\Position;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    public function testGridCreation()
    {
        $grid = new Grid(5, 3);
        $this->assertInstanceOf(Grid::class, $grid);
    }

    public function testIsInBounds()
    {
        $grid = new Grid(5, 3);
        
        // Valid positions
        $this->assertTrue($grid->isInBounds(0, 0));
        $this->assertTrue($grid->isInBounds(5, 3));
        $this->assertTrue($grid->isInBounds(2, 1));
        
        // Invalid positions
        $this->assertFalse($grid->isInBounds(-1, 0));
        $this->assertFalse($grid->isInBounds(0, -1));
        $this->assertFalse($grid->isInBounds(6, 3));
        $this->assertFalse($grid->isInBounds(5, 4));
    }
}
