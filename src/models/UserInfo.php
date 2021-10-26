<?php

namespace BFX\models;

/**
 * User Info model
 */
class UserInfo
{
    protected $id;
    protected $email;
    protected $username;
    protected $timezone;
    protected $isPaperTradeEnabled;

    /**
     * @param object $data - user info data
     * @param numeric $data['id'] - id
     * @param string $data['email'] - email
     * @param string $data['username'] - username
     * @param numeric $data['timezone'] - timezone as UTC offset
     * @param numeric $data['isPaperTradeEnabled'] - flag indicating paper trading account
     */
    public function __construct($data = [])
    {
        $this->id = $data[0];
        $this->email = $data[1];
        $this->username = $data[2];
        $this->timezone = $data[7];
        $this->isPaperTradeEnabled = $data[21];
    }

    /**
     * @param object $data - data to convert to POJO
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
