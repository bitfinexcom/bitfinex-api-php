<?php

namespace BFX\Models;

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
     * @param array $data - user info data
     *                      [
     *                          'id' => int - id
     *                          'email' => string - email
     *                          'username' => string - username
     *                          'timezone' => int - timezone as UTC offset
     *                          'isPaperTradeEnabled' => int - flag indicating paper trading account
     *                      ]
     */
    public function __construct($data = [])
    {
        $this->id = $data[0];
        $this->email = $data[1];
        $this->username = $data[2];
        $this->timezone = $data[7];
        $this->isPaperTradeEnabled = $data[21] === 1;
    }

    /**
     * @param array $data - data to convert to POJO
     *                      [
     *                          'id' => int - id
     *                          'email' => string - email
     *                          'username' => string - username
     *                          'timezone' => int - timezone as UTC offset
     *                          'isPaperTradeEnabled' => int - flag indicating paper trading account
     *                      ]
     *
     * @return UserInfo
     */
    public static function unserialize($data)
    {
        return new UserInfo($data);
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return int
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @return bool
     */
    public function getIsPaperTradeEnabled()
    {
        return $this->isPaperTradeEnabled;
    }
}
