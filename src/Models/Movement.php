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
     * @param array $data - movement data
     *                      [
     *                          'id' => int - id
     *                          'currency' => string - currency
     *                          'currencyName' => string - currency name
     *                          'mtsStarted' => int - movement start timestamp
     *                          'mtsUpdated' => int - last update timestamp
     *                          'status' => string - status
     *                          'amount' => float - moved amount
     *                          'fees' => float - paid fees
     *                          'destinationAddress' => string - destination address
     *                          'transactionId' => int - transaction ID
     *                          'note' => string - note
     *                      ]
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
     * @param array $data - data to convert to POJO
     *                      [
     *                          'id' => int - id
     *                          'currency' => string - currency
     *                          'currencyName' => string - currency name
     *                          'mtsStarted' => int - movement start timestamp
     *                          'mtsUpdated' => int - last update timestamp
     *                          'status' => string - status
     *                          'amount' => float - moved amount
     *                          'fees' => float - paid fees
     *                          'destinationAddress' => string - destination address
     *                          'transactionId' => int - transaction ID
     *                          'note' => string|null - note
     *                      ]
     *
     * @return Movement
     */
    public static function unserialize($data)
    {
        return new Movement($data);
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
     * @return string
     */
    public function getCurrencyName()
    {
        return $this->currencyName;
    }

    /**
     * @return int
     */
    public function getMtsStarted()
    {
        return $this->mtsStarted;
    }

    /**
     * @return int
     */
    public function getMtsUpdated()
    {
        return $this->mtsUpdated;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @return string
     */
    public function getDestinationAddress()
    {
        return $this->destinationAddress;
    }

    /**
     * @return int
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return string|null
     */
    public function getNote()
    {
        return $this->note;
    }
}
