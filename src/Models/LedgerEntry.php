<?php

namespace BFX\Models;

class LedgerEntry
{
    protected $id;
    protected $currency;
    protected $mts;
    protected $amount;
    protected $balance;
    protected $description;
    protected $wallet;

    /**
     * @param array $data - ledger entry data
     *                      [
     *                          'id' => int - id
     *                          'currency' => string - currency
     *                          'mts' => int - transaction timestamp
     *                          'amount' => float - transaction amount
     *                          'balance' => float - balance at time of transaction
     *                          'description' => string - transaction description
     *                      ]
     */
    public function __construct($data = [])
    {
        $this->id = $data[0];
        $this->currency = $data[1];
        $this->mts = $data[3];
        $this->amount = $data[5];
        $this->balance = $data[6];
        $this->description = $data[8];
        $this->wallet = null;

        if (is_string($this->description) && !empty($this->description)) {
            $spl = explode('wallet', $this->description);
            $this->wallet = ($spl && count($spl) > 1) ? trim($spl[count($spl) - 1]) : null;
        }
    }

    /**
     * @param array $data - data to convert to POJO
     *                      [
     *                          'id' => int - id
     *                          'currency' => string - currency
     *                          'mts' => int - transaction timestamp
     *                          'amount' => float - transaction amount
     *                          'balance' => float - balance at time of transaction
     *                          'description' => string - transaction description
     *                      ]
     *
     * @return LedgerEntry
     */
    public static function unserialize($data)
    {
        return new LedgerEntry($data);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return int
     */
    public function getMts()
    {
        return $this->mts;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getWallet()
    {
        return $this->wallet;
    }
}
