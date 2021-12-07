<?php

use BFX\Models\UserInfo;
use PHPUnit\Framework\TestCase;

class UserInfoTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        $this->user = new UserInfo([
            0 => 1,
            1 => 'test@test.com',
            2 => 'test',
            7 => 2,
            21 => 1
        ]);
    }

    public function testId()
    {
        $this->assertEquals(1, $this->user->getId());
    }

    public function testUsername()
    {
        $this->assertEquals('test', $this->user->getUsername());
    }

    public function testEmail()
    {
        $this->assertEquals('test@test.com', $this->user->getEmail());
    }

    public function testTimezone()
    {
        $this->assertEquals(2, $this->user->getTimezone());
    }

    public function testIspapertradeenabled()
    {
        $this->assertEquals(1, $this->user->getIsPaperTradeEnabled());
    }
}
