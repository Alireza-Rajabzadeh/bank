<?php

namespace App\Services\SmsProvider;

interface SmsInterface
{

    public function sendMessage($message,$mobile);
}
