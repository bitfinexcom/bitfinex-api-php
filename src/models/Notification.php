<?php

namespace BFX\models;

class Notification
{
    private $mts;
    private $type;
    private $messageID;
    private $notifyInfo;
    private $code;
    private $status;
    private $text;

    /**
     * @param {object|Array} $data - user info data
     * @param {number} $data['id'] - id
     * @param {string} $data['email'] - email
     * @param {string} $data['username'] - username
     * @param {number} $data['timezone'] - timezone as UTC offset
     * @param {number} $data['isPaperTradeEnabled'] - flag indicating paper trading account
     */
    public function __construct($data = [])
    {
        $this->mts = $data['mts'];
        $this->type = $data['type'];
        $this->messageID = $data['messageID'];
        $this->notifyInfo = $data['notifyInfo'];
        $this->code = $data['code'];
        $this->status = $data['status'];
        $this->text = $data['text'];
    }

    /**
     * @param {object[]|object|Array[]|Array} $data - data to convert to POJO
     * @returns {object} pojo
     */
    public static function unserialize($data)
    {
        return new Notification($data);
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getMessageID()
    {
        return $this->messageID;
    }

    /**
     * @return mixed
     */
    public function getNotifyInfo()
    {
        return $this->notifyInfo;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
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
    public function getText()
    {
        return $this->text;
    }
}
