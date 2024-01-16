<?php

namespace Tests\Unit;

use App\Room;
use PHPUnit\Framework\TestCase;

class RoomTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function testHas(): void
    {
        $room = new Room(["Bonnie", "Clyde"]);
        $this->assertTrue($room->has("Bonnie"));
    }

    public function testHasnt(): void
    {
        $room = new Room(["Bonnie", "Clyde"]);
        $this->assertFalse($room->has("Iron Man"));
    }
}
