<?php

namespace App\Services\SmsProvider;

use Exception;

class SmsFactory
{
    public static function init($sms_provider)
    {
        $namespace = '\\App\\Services\\SmsProvider\\';
        $class_namespace = $namespace . ucfirst($sms_provider);
        if (!class_exists($class_namespace)) {
            $message = __('SmsProvider.miss_config');
            throw new Exception($message, 500);
        }
        return new $class_namespace();
    }
}
