<?php

namespace BFX\Models;

class AuthPermission
{
    protected $key;
    protected $read;
    protected $write;

    /**
     * @param array $data - auth permission data
     *                      [
     *                          'key' => string - operation key
     *                          'read' => number - read permission
     *                          'write' => number - write permission
     *                      ]
     */
    public function __construct($data = [])
    {
        $this->key = $data[0];
        $this->read = $data[1] === 1;
        $this->write = $data[2] === 1;
    }

    /**
     * @param array $data - data to convert to POJO
     *                      [
     *                          'key' => string - operation key
     *                          'read' => number - read permission
     *                          'write' => number - write permission
     *                      ]
     *
     * @return AuthPermission
     */
    public static function unserialize($data)
    {
        return new AuthPermission($data);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return bool
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @return bool
     */
    public function getWrite()
    {
        return $this->write;
    }
}
