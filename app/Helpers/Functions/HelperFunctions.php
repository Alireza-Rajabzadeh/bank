<?php

function convertToEnglishNumber($number)
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $english = range(0, 9);
    $number = str_replace($arabic, $english, $number);
    return str_replace($persian, $english, $number);
}

function apiResponse($status = true, $response = [], $message = [], $status_code = 200)
{

    if (empty($message)) {
        switch ($status) {
            case true:
                $message =  __('messages.success', []);
                break;
            case false:
                $message =  __('messages.error', []);
                break;
            default:
                # code...
                break;
        }
    }

    $result = [
        "status" => $status,
        "status_code" => $status_code,
        'message' => $message,
        'response' => $response
    ];

    return response()->json($result, $status_code);
}


function arrayOnly(array $array, $keys, $default_null_value = null)
{
    $filter = [];
    foreach ($keys as $value) {
        $filter[$value] = $array[$value] ?? $default_null_value;
    }
    return $filter;
}
