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
        $this->assertTrue($grid->isInBounds(new Position(0, 0)));
        $this->assertTrue($grid->isInBounds(new Position(5, 3)));
        $this->assertTrue($grid->isInBounds(new Position(2, 1)));
        
        // Invalid positions
        $this->assertFalse($grid->isInBounds(new Position(-1, 0)));
        $this->assertFalse($grid->isInBounds(new Position(0, -1)));
        $this->assertFalse($grid->isInBounds(new Position(6, 3)));
        $this->assertFalse($grid->isInBounds(new Position(5, 4)));
    }
}
