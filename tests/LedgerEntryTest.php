<?php

use BFX\Models\LedgerEntry;
use PHPUnit\Framework\TestCase;

class LedgerEntryTest extends TestCase
{
    private $ledger;

    protected function setUp(): void
    {
        $this->ledger = new LedgerEntry([
            0 => 1,
            1 => 'ETH',
            3 => 1573521810000,
            5 => 0.01644445,
            6 => 0,
            8 => 'description',
        ]);
    }

    public function testId()
    {
        $this->assertEquals(1, $this->ledger->getId());
    }

    public function testCurrency()
    {
        $this->assertEquals('ETH', $this->ledger->getCurrency());
    }

    public function testNts()
    {
        $this->assertEquals(1573521810000, $this->ledger->getMts());
    }

    public function testAmount()
    {
        $this->assertEquals(0.01644445, $this->ledger->getAmount());
    }

    public function testBalance()
    {
        $this->assertEquals(0, $this->ledger->getBalance());
    }

    public function testDescription()
    {
        $this->assertEquals('description', $this->ledger->getDescription());
    }
}
