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
     * @param object data - ledger entry data
     * @param numeric $data['id'] - id
     * @param string $data['currency'] - currency
     * @param numeric $data['mts'] - transaction timestamp
     * @param numeric $ata['amount'] - transaction amount
     * @param numeric $data['balance'] - balance at time of transaction
     * @param string $data['description'] - transaction description
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
     * @param object $data - data to convert to POJO
     */
    public static function unserialize($data)
    {
        return new LedgerEntry($data);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getMts()
    {
        return $this->mts;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getWallet()
    {
        return $this->wallet;
    }
}
