<?php

namespace BFX\Models;

class Movement
{
    protected $id;
    protected $currency;
    protected $currencyName;
    protected $mtsStarted;
    protected $mtsUpdated;
    protected $status;
    protected $amount;
    protected $fees;
    protected $destinationAddress;
    protected $transactionId;
    protected $note;

    /**
     * @param object $data - movement data
     * @param numeric $data['id'] - id
     * @param string $data['currency'] - currency
     * @param string $data['currencyName'] - currency name
     * @param numeric $data['mtsStarted'] - movement start timestamp
     * @param numeric $data['mtsUpdated'] - last update timestamp
     * @param string $data['status'] - status
     * @param numeric $data['amount'] - moved amount
     * @param numeric $data['fees'] - paid fees
     * @param string $data['destinationAddress'] - destination address
     * @param numeric $data['transactionId'] - transaction ID
     * @param string $data['note'] - note
     */
    public function __construct($data = [])
    {
        $this->id = $data[0];
        $this->currency = $data[1];
        $this->currencyName = $data[2];
        $this->mtsStarted = $data[5];
        $this->mtsUpdated = $data[6];
        $this->status = $data[9];
        $this->amount = $data[12];
        $this->fees = $data[13];
        $this->destinationAddress = $data[16];
        $this->transactionId = $data[20];
        $this->note = $data[21];
    }

    /**
     * @param object $data - data to convert to POJO
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
