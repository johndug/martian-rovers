<?php

namespace Tests\Models;

use App\Models\Grid;
use App\Models\Position;
use App\Models\Robot;
use PHPUnit\Framework\TestCase;

class RobotTest extends TestCase
{
    private Grid $grid;

    protected function setUp(): void
    {
        $this->grid = new Grid(5, 3);
    }

    public function testTurnLeft()
    {
        $robot = new Robot(1, 1, 'N', $this->grid);
        
        $robot->command('L');
        $this->assertEquals('1, 1, W', $robot->position());
        
        $robot->command('L');
        $this->assertEquals('1, 1, S', $robot->position());
        
        $robot->command('L');
        $this->assertEquals('1, 1, E', $robot->position());
        
        $robot->command('L');
        $this->assertEquals('1, 1, N', $robot->position());
    }

    public function testTurnRight()
    {
        $robot = new Robot(1, 1, 'N', $this->grid);
        
        $robot->command('R');
        $this->assertEquals('1, 1, E', $robot->position());
        
        $robot->command('R');
        $this->assertEquals('1, 1, S', $robot->position());
        
        $robot->command('R');
        $this->assertEquals('1, 1, W', $robot->position());
        
        $robot->command('R');
        $this->assertEquals('1, 1, N', $robot->position());
    }

    public function testMoveForward()
    {
        $robot = new Robot(1, 1, 'N', $this->grid);
        
        $robot->command('F');
        $this->assertEquals('1, 2, N', $robot->position());
        
        $robot->command('R');
        $robot->command('F');
        $this->assertEquals('2, 2, E', $robot->position());
    }

    public function testRobotLost()
    {
        $robot = new Robot(5, 3, 'N', $this->grid);
        
        $robot->command('F');
        $this->assertStringContainsString('LOST', $robot->position());
    }

    public function testScentPrevention()
    {
        // First robot gets lost
        $robot = new Robot(5, 3, 'N', $this->grid);
        $robot->command('F');
        $this->assertStringContainsString('LOST', $robot->position());
        $this->assertEquals('5, 3, N LOST', $robot->position()); // Not lost due to scent
    }
}
