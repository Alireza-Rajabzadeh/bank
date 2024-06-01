<?php

namespace App\Services;


use App\Models\User;
use App\Jobs\SendSmsJobs;
use App\Repositories\SmsTemplatesRepository;


class SmsService
{
    public $sms_repository;

    private $generate_try = 0;
    function __construct(SmsTemplatesRepository $sms_repository)
    {
        $this->sms_repository = $sms_repository;
    }


    function increaseMessage(User $user)
    {
        $search_data = [
            'where' => [
                'id' => 2
            ]
        ];
        $message = $this->sms_repository->search($search_data)->first();
        $message = str_replace("{ammount}", 1000, $message->message);
        dispatch(new SendSmsJobs($user->mobile, $message, "kavenegar"));
    }

    function decreaseMessage(User $user)
    {
        $search_data = [
            'where' => [
                'id' => 1
            ]
        ];
        $message = $this->sms_repository->search($search_data)->first();
        $message = str_replace("{ammount}", 1000, $message->message);
        dispatch(new SendSmsJobs($user->mobile, $message, "kavenegar"));
    }
}
