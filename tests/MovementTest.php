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
        $this->assertEquals(1, $this->movement->getId());
    }

    public function testCurrency()
    {
        $this->assertEquals('ETH', $this->movement->getCurrency());
    }

    public function testCurrencyname()
    {
        $this->assertEquals('ETHEREUM', $this->movement->getCurrencyName());
    }

    public function testMtsstarted()
    {
        $this->assertEquals(1569348774000, $this->movement->getMtsStarted());
    }

    public function testMtsupdated()
    {
        $this->assertEquals(1569348774000, $this->movement->getMtsUpdated());
    }

    public function testStatus()
    {
        $this->assertEquals('COMPLETED', $this->movement->getStatus());
    }

    public function testAmount()
    {
        $this->assertEquals(0.26300954, $this->movement->getAmount());
    }

    public function testFees()
    {
        $this->assertEquals(-0.00135, $this->movement->getFees());
    }

    public function testDestinationaddress()
    {
        $this->assertEquals('Address', $this->movement->getDestinationAddress());
    }

    public function testTransactionid()
    {
        $this->assertEquals(25, $this->movement->getTransactionId());
    }

    public function testNote()
    {
        $this->assertEquals('note', $this->movement->getNote());
    }
}
