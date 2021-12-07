<?php

namespace BFX\Models;

class Notification
{
    protected $mts;
    protected $type;
    protected $messageID;
    protected $notifyInfo;
    protected $code;
    protected $status;
    protected $text;

    /**
     * @param array $data - user info data
     *                      [
     *                          'mts' => int - timestamp
     *                          'type' => string - (i.e. 'ucm-*' for broadcasts)
     *                          'messageID' => int - message ID
     *                          'notifyInfo' => array|object - metadata, set by client for broadcasts
     *                          'code' => int - code
     *                          'status' => string - status (i.e. 'error')
     *                          'text' => string - notification text to display to user
     *                      ]
     */

    public function __construct($data = [])
    {
        $this->mts = $data[0];
        $this->type = $data[1];
        $this->messageID = $data[2];
        $this->notifyInfo = $data[4];
        $this->code = $data[5];
        $this->status = $data[6];
        $this->text = $data[7];
    }

    /**
     * @param array $data - data to convert to POJO
     *                      [
     *                          'mts' => int - timestamp
     *                          'type' => string - (i.e. 'ucm-*' for broadcasts)
     *                          'messageID' => int - message ID
     *                          'notifyInfo' => array|object - metadata, set by client for broadcasts
     *                          'code' => int - code
     *                          'status' => string - status (i.e. 'error')
     *                          'text' => string - notification text to display to user
     *                      ]
     *
     * @return Notification
     */
    public static function unserialize($data)
    {
        return new Notification($data);
    }

    /**
     * @return int
     */
    public function getMts()
    {
        return $this->mts;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getMessageID()
    {
        return $this->messageID;
    }

    /**
     * @return array|object
     */
    public function getNotifyInfo()
    {
        return $this->notifyInfo;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
}
