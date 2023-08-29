<?php

namespace App\Tests;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskIsDone(): void
    {
        $task = new Task();
        $this->assertSame(false, $task->isDone());
    }

    public function testToggle(): void
    {
        $task = new Task();
        $task->toggle(true);
        $this->assertSame(true, $task->isDone());

    }
}
