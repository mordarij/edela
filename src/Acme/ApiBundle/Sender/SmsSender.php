<?php
/**
 * Created by PhpStorm.
 * User: palkin
 * Date: 7/29/14
 * Time: 12:39 PM
 */

namespace Acme\ApiBundle\Sender;


class SmsSender
{

    private $smsInfo;
    private $conn;

    public function __construct($smsInfo)
    {
        $this->smsInfo = $smsInfo;
        $this->conn = curl_init();

    }

    public function send($phone, $message)
    {
        $data = [
            'login' => $this->smsInfo['login'],
            'pwd' => md5($this->smsInfo['password']),
            'phones' => $phone,
            'message' => $message,
            'sender' => $this->smsInfo['sender']
        ];

        curl_setopt($this->conn, CURLOPT_URL, $this->smsInfo['host'] . $this->smsInfo['send_url']);
        curl_setopt($this->conn, CURLOPT_POST, 1);
        curl_setopt($this->conn, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->conn, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->conn, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($this->conn, CURLOPT_TIMEOUT, 20);
        $response = curl_exec($this->conn);
        curl_close($this->conn);
        if (substr($response, 0, 2) === 'Ok') {
            return true;
        }

        return false;

    }


} 