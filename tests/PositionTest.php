<?php

namespace Tests\Models;

use App\Models\Position;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    public function testPositionCreation()
    {
        $position = new Position(5, 10);
        
        $this->assertEquals(5, $position->x);
        $this->assertEquals(10, $position->y);
    }
}
