<?php

use BFX\Models\Movement;
use PHPUnit\Framework\TestCase;

class MovementTest extends TestCase
{
    private $movement;

    protected function setUp(): void
    {
        $this->movement = new Movement([
            0 => 1,
            1 => 'ETH',
            2 => 'ETHEREUM',
            5 => 1569348774000,
            6 => 1569348774000,
            9 => 'COMPLETED',
            12 => 0.26300954,
            13 => -0.00135,
            16 => 'Address',
            20 => 25,
            21 => 'note'
        ]);
    }

    public function testId()
    {
        $this->assertSame(1, $this->movement->getId());
    }

    public function testCurrency()
    {
        $this->assertSame('ETH', $this->movement->getCurrency());
    }

    public function testCurrencyname()
    {
        $this->assertSame('ETHEREUM', $this->movement->getCurrencyName());
    }

    public function testMtsstarted()
    {
        $this->assertSame(1569348774000, $this->movement->getMtsStarted());
    }

    public function testMtsupdated()
    {
        $this->assertSame(1569348774000, $this->movement->getMtsUpdated());
    }

    public function testStatus()
    {
        $this->assertSame('COMPLETED', $this->movement->getStatus());
    }

    public function testAmount()
    {
        $this->assertSame(0.26300954, $this->movement->getAmount());
    }

    public function testFees()
    {
        $this->assertSame(-0.00135, $this->movement->getFees());
    }

    public function testDestinationaddress()
    {
        $this->assertSame('Address', $this->movement->getDestinationAddress());
    }

    public function testTransactionid()
    {
        $this->assertSame(25, $this->movement->getTransactionId());
    }

    public function testNote()
    {
        $this->assertSame('note', $this->movement->getNote());
    }
}
