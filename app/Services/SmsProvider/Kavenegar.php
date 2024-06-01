<?php

namespace App\Services\SmsProvider;

use App\Services\SmsProvider\SmsInterface;

class Kavenegar implements SmsInterface
{

    private $base_url;
    private $api_key;
    private $sender;

    function __construct()
    {
        $this->base_url = env("KAVENEGAR_BASE_URL");
        $this->api_key = env("KAVENEGAR_API_KEY");
        $this->sender = 10004346;
    }

    function sendMessage($message, $mobile)
    {

        $url = $this->base_url . "/" . $this->api_key . "/sms/send.json";
        $data = [
            "receptor" => $mobile,
            "sender" => $this->sender,
            "message" => $message,
        ];

        return sendRequest($url, $data, "", "GET");
    }
}
