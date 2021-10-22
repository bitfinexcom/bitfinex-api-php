<?php

namespace BFX\models;

class LedgerEntry
{
    private $id;
    private $currency;
    private $currencyName;
    private $mts;
    private $amount;
    private $balance;
    private $description;
    private $wallet;

    /**
     * @param {object|Array} data - ledger entry data
     * @param {number} $data['id'] - id
     * @param {string} $data['currency'] - currency
     * @param {number} $data['mts'] - transaction timestamp
     * @param {number} $ata['amount'] - transaction amount
     * @param {number} $data['balance'] - balance at time of transaction
     * @param {string} $data['description'] - transaction description
     */
    public function __construct($data = [])
    {
        $this->id = $data['id'];
        $this->currency = $data['currency'];
        $this->currencyName = $data['currencyName'];
        $this->mts = $data['mts'];
        $this->amount = $data['amount'];
        $this->balance = $data['balance'];
        $this->description = $data['description'];
        $this->wallet = $data['wallet'];

        $this->$wallet = null;

        if (is_string($this->$description) && !empty($this->$description)) {
            $spl = $this->$description.str_split('wallet');
            $this->$wallet = ($spl && strlen($spl) > 1) ? $spl[strlen($spl) - 1].trim() : null;
        }
    }

    /**
     * @param {object[]|object|Array[]|Array} $data - data to convert to POJO
     * @returns {object} pojo
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
    public function getCurrencyName()
    {
        return $this->currencyName;
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
