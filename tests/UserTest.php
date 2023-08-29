<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserInitializationRole(): void
    {
        $user = new User();
        $this->assertTrue(true);
    }
}
