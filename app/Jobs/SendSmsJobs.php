<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Services\SmsProvider\SmsFactory;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendSmsJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mobile;
    public $message;
    public $sms_provider;
    /**
     * Create a new job instance.
     */
    public function __construct($mobile, $message, $sms_provider)
    {
        $this->mobile = $mobile;
        $this->message = $message;
        $this->sms_provider = SmsFactory::init($sms_provider);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $result = $this->sms_provider->sendMessage($this->message, $this->mobile);
        echo ($result['result']);
    }
}
