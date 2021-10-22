<?php

namespace BFX\models;

class Movement
{
    private $id;
    private $currency;
    private $currencyName;
    private $mtsStarted;
    private $mtsUpdated;
    private $status;
    private $amount;
    private $fees;
    private $destinationAddress;
    private $transactionId;
    private $note;

    /**
     * @param {object|Array} $data - movement data
     * @param {number} $data['id'] - id
     * @param {string} $data['currency'] - currency
     * @param {string} $data['currencyName'] - currency name
     * @param {number} $data['mtsStarted'] - movement start timestamp
     * @param {number} $data['mtsUpdated'] - last update timestamp
     * @param {string} $data['status'] - status
     * @param {number} $data['amount'] - moved amount
     * @param {number} $data['fees'] - paid fees
     * @param {string} $data['destinationAddress'] - destination address
     * @param {number} $data['transactionId'] - transaction ID
     * @param {string} $data['note'] - note
     */
    public function __construct($data = [])
    {
        $this->id = $data['id'];
        $this->currency = $data['currency'];
        $this->currencyName = $data['currencyName'];
        $this->mtsStarted = $data['mtsStarted'];
        $this->status = $data['status'];
        $this->amount = $data['amount'];
        $this->fees = $data['fees'];
        $this->destinationAddress = $data['destinationAddress'];
        $this->transactionId = $data['transactionId'];
        $this->note = $data['note'];
    }

    /**
     * @param {object[]|object|Array[]|Array} $data - data to convert to POJO
     * @returns {object} pojo
     */
    public static function unserialize($data)
    {
        return new Movement($data);
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
    public function getMtsStarted()
    {
        return $this->mtsStarted;
    }

    /**
     * @return mixed
     */
    public function getMtsUpdated()
    {
        return $this->mtsUpdated;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
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
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @return mixed
     */
    public function getDestinationAddress()
    {
        return $this->destinationAddress;
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }
}
