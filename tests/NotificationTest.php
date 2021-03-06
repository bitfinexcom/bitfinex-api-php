<?php

use BFX\Models\Notification;
use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    private $notification;

    protected function setUp(): void
    {
        $this->notification = new Notification([
            0 => 1573521810000,
            1 => 'fon-req',
            2 => 1,
            4 => [
                0 => 41238905,
                4 => -1000,
                14 => 0.002,
                15 => 2
            ],
            5 => null,
            6 => 'SUCCESS',
            7 => 'Submitting funding bid of 1000.0 USD at 0.2000 for 2 days.',
        ]);
    }

    public function testMts()
    {
        $this->assertSame(1573521810000, $this->notification->getMts());
    }

    public function testType()
    {
        $this->assertSame('fon-req', $this->notification->getType());
    }

    public function testMessageId()
    {
        $this->assertSame(1, $this->notification->getMessageID());
    }

    public function testNotifyInfo()
    {
        $this->assertEquals([
            0 => 41238905,
            4 => -1000,
            14 => 0.002,
            15 => 2
        ], $this->notification->getNotifyInfo());
    }

    public function testCode()
    {
        $this->assertSame(null, $this->notification->getCode());
    }

    public function testStatus()
    {
        $this->assertSame('SUCCESS', $this->notification->getStatus());
    }

    public function testText()
    {
        $this->assertSame('Submitting funding bid of 1000.0 USD at 0.2000 for 2 days.', $this->notification->getText());
    }
}
