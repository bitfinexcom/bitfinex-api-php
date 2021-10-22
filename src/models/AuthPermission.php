<?php

namespace BFX\models;

class AuthPermission
{
    private $key;
    private $read;
    private $write;

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
        $this->key = $data['key'];
        $this->read = $data['read'];
        $this->write = $data['write'];
        $this->read = $data['read'];
        $this->write = $data['write'];
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
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @return mixed
     */
    public function getWrite()
    {
        return $this->write;
    }
}
