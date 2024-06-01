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

function sendRequest($url, $body, $token, $http_verb = "POST", $request_headers = [], $SSL_VERIFYPEER = true)
{

    $body['token'] = $token;
    $postfields = json_encode($body, true);

    $base_headers = array(
        'Content-Type:application/json',
        'Content-Length: ' . strlen($postfields),
        'Accept:application/json',
        'Authorization:Bearer ' . $token
    );
    $headers = array_merge($base_headers, $request_headers);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_URL =>  $url,
        CURLOPT_CUSTOMREQUEST => $http_verb,
        // ssl verify false
        CURLOPT_SSL_VERIFYPEER => $SSL_VERIFYPEER,
        // set return transfer true for return json response
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_VERBOSE => 0,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $postfields,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 120,

    ]);

    $curl_result = curl_exec($ch);
    $response = parseCurlResponse($ch, $curl_result);
    curl_close($ch);

    return $response;
}
function parseCurlResponse($curl_handler, $curl_result)
{
    $header_size = curl_getinfo($curl_handler, CURLINFO_HEADER_SIZE);
    $header = substr($curl_result, 0, $header_size);
    $response['header_size'] = $header_size;
    $response['result'] = $curl_result;
    $response['curl_error_number'] = curl_errno($curl_handler);
    $response['curl_error_message'] = curl_error($curl_handler);
    $response['header']  = $header;
    $response['info']  = curl_getinfo($curl_handler);
    $response['http_code']  = curl_getinfo($curl_handler);


    if ($response['curl_error_number'] != 0) {
        switch ($response['curl_error_number']) {
            case '28':
                $message = __("messages.curl_time_out");
                $status_code = __("http_code.Gateway_Timeout");
                break;
            case '60':
                $message = __("messages.ssl_certificate_problem");
                $status_code = __("http_code.SSL_Certificate_Error");
                break;
            default:
                $message = $response['curl_error_message'];
                $status_code = (empty($response['info']['http_code']) && $response['info']['http_code'] = 0)  ? $response['info']['http_code'] :  __("http_code.Unknown_Error");
                break;
        }
        throw new Exception($message, $status_code, $response['curl_error_number'], "error", $response);
    }


    if (in_array($response['info']['http_code'], [403])) {

        $status_code = $response['info']['http_code'];
        $message = __("messages.error_code_catch", ['error_code' => $response['info']['http_code']]);

        throw new Exception($message, $status_code, $response['curl_error_number'], "error", $response);
    }


    return $response;
}
