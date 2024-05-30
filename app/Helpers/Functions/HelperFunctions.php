<?php



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
