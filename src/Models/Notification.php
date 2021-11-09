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
     * @param object $data - user info data
     * @param numeric $data['mts'] - timestamp
     * @param string $data['type'] - (i.e. 'ucm-*' for broadcasts)
     * @param numeric $data['messageID'] - message ID
     * @param object $data['notifyInfo'] - metadata, set by client for broadcasts
     * @param numeric $data['code '] - code
     * @param string $data['status'] - status (i.e. 'error')
     * @param string $data['text'] - notification text to display to user
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
     * @param object $data - data to convert to POJO
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