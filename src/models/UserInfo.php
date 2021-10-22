<?php

namespace BFX\models;

/**
 * User Info model
 */
class UserInfo
{
    private $id;
    private $email;
    private $username;
    private $timezone;
    private $isPaperTradeEnabled;

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
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->username = $data['username'];
        $this->timezone = $data['timezone'];
        $this->isPaperTradeEnabled = $data['isPaperTradeEnabled'];
    }

    /**
     * @param {object[]|object|Array[]|Array} $data - data to convert to POJO
     * @returns {object} pojo
     */
    public static function unserialize($data)
    {
        return new UserInfo($data);
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @return mixed
     */
    public function getIsPaperTradeEnabled()
    {
        return $this->isPaperTradeEnabled;
    }
}
