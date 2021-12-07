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
        $this->assertSame(1, $this->user->getId());
    }

    public function testUsername()
    {
        $this->assertSame('test', $this->user->getUsername());
    }

    public function testEmail()
    {
        $this->assertSame('test@test.com', $this->user->getEmail());
    }

    public function testTimezone()
    {
        $this->assertSame(2, $this->user->getTimezone());
    }

    public function testIspapertradeenabled()
    {
        $this->assertSame(1, $this->user->getIsPaperTradeEnabled());
    }
}
