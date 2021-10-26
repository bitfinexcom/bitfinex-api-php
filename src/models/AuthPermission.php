<?php

namespace BFX\models;

class AuthPermission
{
    protected $key;
    protected $read;
    protected $write;

    /**
     * @param object $data - auth permission data
     * @param string $data.key - operation key
     * @param boolean $data.read - read permission
     * @param boolean $data.write - write permission
     */
    public function __construct($data = [])
    {
        $this->key = $data[0];
        $this->read = $data[1];
        $this->write = $data[2];
    }

    /**
     * @param object$data - data to convert to POJO
     */
    public static function unserialize($data)
    {
        return new AuthPermission($data);
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
