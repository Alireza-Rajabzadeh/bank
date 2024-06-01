<?php

namespace App\Services\Providers;

use App\Exceptions\GeneralException;
use App\Repositories\Apies\TciApies;

class tci implements ProviderInterface
{

    protected $tci_api;
    public $info;


    public $tci_status = [
        "0" => [
            "description" => "نامشخص",
            "equivalent_status" => "0"
        ],
        "1" => [
            "description" => "آزاد",
            "equivalent_status" => "1"
        ],
        "2" => [
            "description" => "در انتظار دایری",
            "equivalent_status" => "5"
        ],
        "3" => [
            "description" => "دایر شده",
            "equivalent_status" => "7"
        ],
        "4" => [
            "description" => "لغو سفارش",
            "equivalent_status" => "18"
        ],
        "5" => [
            "description" => "در انتظار جمع آوری",
            "equivalent_status" => "13"
        ],
        "6" => [
            "description" => "جمع آوری شده",
            "equivalent_status" => "15"
        ],
        "7" => [
            "description" => "خطا در جمع آوری",
            "equivalent_status" => "14"
        ],
        "8" => [
            "description" => "خطا در دایری",
            "equivalent_status" => "6"
        ],
        "9" => [
            "description" => "خطا در لغو سفارش",
            "equivalent_status" => "17"
        ],
        "10" => [
            "description" => "تست نرم افزار",
            "equivalent_status" => "0"
        ],

    ];


    public function __construct()
    {
        $this->setApiRepository();
    }

    public function setApiRepository()
    {
        $this->tci_api = app(TciApies::class);
    }

    public function getInterface(array $data)
    {

        $detail_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];

        $detail =  $this->tci_api->getDetail($detail_input, $data['_request_id'])['response'];

        $interface_input = [
            'resourceId' => $detail['resourceId'],
        ];

        $interface_status = $this->tci_api->interfaceStatus($interface_input, $data['_request_id']);
        $interface_status_detail = $this->tci_api->interfaceStatusDetail($interface_input, $data['_request_id']);

        $interface_status_response = $interface_status['response'];
        $interface_status_detail_response = $interface_status_detail['response'];

        $get_interface_result = [
            'service_status' => $interface_status_response['status'] ?? "--",
            'lineProfile' => $interface_status_response['lineProfile'] ?? "--",
            'actualUpRate' => $interface_status_detail_response['actualUpStreamRate'] ?? "--",
            'actualDownRate' => $interface_status_detail_response['actualDownStreamRate'] ?? "--",
            'maxAttainableUpRate' => $interface_status_detail_response['maxUpStreamRate'] ?? "--",
            'maxAttainableDownRate' => $interface_status_detail_response['maxDownStreamRate'] ?? "--",
            'snrUp' => $interface_status_detail_response['upStreamSNR'] ?? "--",
            'adminstatus' => $interface_status_detail_response['adminStatus'] ?? "--",
            'snrDown' => $interface_status_detail_response['downStreamSNR'] ?? "--",
            'attUp' => $interface_status_detail_response['upStreamAtenuation'] ?? "--",
            'attDown' => $interface_status_detail_response['downStreamAtenuation'] ?? "--",
            'powerUp' => $interface_status_detail_response['upStreamPower'],
            'powerUp' => empty($interface_status_detail_response['upStreamPower']) ? "-" : $interface_status_detail_response['upStreamPower'],
            'powerDown' => $interface_status_detail_response['downStreamPower'] ?? "--",
        ];

        return  $get_interface_result;
        //TODO set adminStatus in data;

    }

    public function lineProfileInquiry(array $data)
    {
        $detail_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $detail =  $this->tci_api->getDetail($detail_input, $data['_request_id'])['response'];

        $line_profile_input = [
            'resourceId' => $detail['resourceId'],
        ];
        $line_profile_inquiry = $this->tci_api->lineProfileInquiry($line_profile_input, $data['_request_id']);

        $line_profile_inquiry_response = $line_profile_inquiry['response'];

        //TODO set adminStatus in data;
        return $line_profile_inquiry_response;
    }

    public function closeService(array $data)
    {
        $close_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $collect_result = $this->tci_api->closeBitstream($close_input, $data['_request_id']);
        return $collect_result['response'];
    }

    public function ticketTypeList(array $data)
    {
        $ticket_type_input = [
            'telNumber' => $data['service_number']
        ];
        $ticket_type_result = $this->tci_api->ticketTypeList($ticket_type_input, $data['_request_id']);
        return $ticket_type_result['response'];
    }

    public function profileInfo(array $data)
    {

        $detail_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $detail =  $this->tci_api->getDetail($detail_input, $data['_request_id'])['response'];

        $line_profile_input = [
            'resourceId' => $detail['resourceId'],
        ];
        $show_profiles_result = $this->tci_api->lineProfileInquiry($line_profile_input, $data['_request_id']);

        $parsed_response  = array_map(function ($item) {

            $record = [
                'id'=> $item->id ,
                'name'=> $item->name,
            ];
            return $record;
        }, $show_profiles_result['response']);

        $result = [
            "status" => $show_profiles_result['status'],
            "message" => $show_profiles_result['message'],
            "response" => $parsed_response
        ];

        return $result;
    }

    public function buyPackage(array $data)
    {
        $buy_package_input  = [
            "subscriber_number" => $data['subscriber_number'],
            "payment_id" => $data['payment_id'],
        ];
        $collect_result = $this->tci_api->buyPackage($buy_package_input, $data['_request_id']);
        return $collect_result['response'];
    }

    public function seltTest(array $data)
    {
        $detail_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $detail =  $this->tci_api->getDetail($detail_input, $data['_request_id'])['response'];
        $selt_input = [
            'resourceId' => $detail['resourceId'],
        ];
        $selt_result = $this->tci_api->selt($selt_input, $data['_request_id']);

        return $selt_result;
    }

    public function resetInterface(array $data)
    {
        $detail_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $detail =  $this->tci_api->getDetail($detail_input, $data['_request_id'])['response'];
        $reset_interface_input = [
            'resourceId' => $detail['resourceId'],
        ];
        $reset_interface_result = $this->tci_api->resetInterface($reset_interface_input, $data['_request_id']);

        return $reset_interface_result['response'];
    }

    public function lineProfileUpdate(array $data)
    {
        $detail_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $detail =  $this->tci_api->getDetail($detail_input, $data['_request_id'])['response'];

        $rate_profile_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $rate_profile = $this->tci_api->resourceDetail($rate_profile_input, $data['_request_id'])['response'];

        $profile_update_input = [
            'resourceId' => $detail['resourceId'],
            "rateProfileId" => strval(!empty($rate_profile["rateprofile"]) ? $rate_profile["rateprofile"] : 0),
            "id" => strval($data['id']),
        ];
        $profile_update_result = $this->tci_api->lineProfileUpdate($profile_update_input, $data['_request_id']);

        return $profile_update_result['response'];
    }

    public function cancelService(array $data)
    {
        $detail_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $detail =  $this->tci_api->getDetail($detail_input, $data['_request_id'])['response'];


        $cancel_bitstream_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $profile_update_result = $this->tci_api->cancelBitstream($cancel_bitstream_input, $data['_request_id']);

        return $profile_update_result['response'];
    }

    public function createticket(array $data)
    {
        $create_ticket_input = [
            'title' => $data['title'],
            'description' => trim($data['description']),
            'priority' => $data['priority'],
            'typeId' => $data['type_id'],
            'coordinatorName' => $data['coordinator_name'],
            'coordinatorMobileNumber' => $data['coordinator_mobile_number'],
            'serviceUsername' => $data['service_username'],
            'servicePassword' =>  $data['service_password'],
            'macAddress' => $data['macAddress'],
            'nasPortId' => $data['nasPortId'],
            'telNumber' => substr($data['service_number'], 1),
        ];
        $create_ticket_result = $this->tci_api->submitTicket($create_ticket_input, $data['_request_id']);

        return $create_ticket_result['response'];
    }

    public function updateTicketStatus(array $data)
    {
        $ticket_input = [
            'ticketId' => $data['ticket_id'],
        ];
        $create_ticket_result = $this->tci_api->updateTicketStatus($ticket_input, $data['_request_id']);
        return $create_ticket_result['response'];
    }

    public function insertTicketComment(array $data)
    {
        #TODO if ticket closed
        $ticket_input = [
            'ticketId' => $data['ticket_id'],
        ];
        $update_ticket_status = $this->tci_api->updateTicketStatus($ticket_input, $data['_request_id']);

        $ticket_input = [
            'comment' => $data['comment'],
            'ticketId' => $data['ticket_id'],
        ];
        $ticket_comment_result = $this->tci_api->addTicketComment($ticket_input, $data['_request_id']);
        return $ticket_comment_result['response'];
    }

    public function ticketDetailInquiry(array $data)
    {
        $ticket_input = [
            'ticketId' => $data['ticket_id'],
        ];
        $ticket_detail_result = $this->tci_api->ticketDetailInquiry($ticket_input, $data['_request_id']);
        return $ticket_detail_result['response'];
    }

    public function cancelRegistration(array $data)
    {
        $cancel_registration_input = [
            'order_id' => $data['order_id'],
        ];
        $cancel_registration_result = $this->tci_api->cancelRegistration($cancel_registration_input, $data['_request_id']);
        return $cancel_registration_result['response'];
    }

    public function range(array $data)
    {
        $inquiry_with_tci_input = [
            "technologyTypeId" => $data['technology_type_id'] == "adsl" ? 0 : 1,
            'bitStreamTypeId' => 4,
            'telNumber' => substr($data['service_number'], 1),
        ];
        $inquiry_with_tci = $this->tci_api->checkRegistryFeasibility($inquiry_with_tci_input, $data['_request_id']);

        $register_bitstream_input = [
            "technologyTypeId" => $data['technology_type_id'] == "adsl" ? 0 : 1,
            'terminalTypeId' => $data['terminal_type'] == "adsl" ? 0 : 1,
            "payment_id" => $data['payment_id'],
            "paymentTypeId" => 1,
            "subscriber_number" => $data['subscriber_number'],
        ];
        $register_bitstream_result = $this->tci_api->registerBitstream($register_bitstream_input, $data['_request_id']);

        return $register_bitstream_result['response'];
    }

    public function updateStatus(array $data)
    {
        $last_status_input = [
            'order_id' => $data['service']['provider_order_id'],
        ];
        $last_status_result =  $this->tci_api->getLastStatus($last_status_input, $data['_request_id']);
        $service_info = !empty($last_status_result['response']) ? (array) ($last_status_result['response'][0]) : [];
        $tci_status_id = $service_info['status_id'] ?? "0";
        $equivalent_status = $this->tci_status[$tci_status_id];
        $provider_messages = $last_status_result['message'];
        $result = [
            "provider_service_status_id" => $tci_status_id,
            "equivalent_status" => $equivalent_status['equivalent_status'],
            "message" => $provider_messages
        ];

        return $result;
    }




    public function inquiry(array $inputs)
    {
        $result = [
            "status" => "success",
            "message" => "امکان ارائه سرویس وجود دارد"
        ];
        return $result;
    }


    public function changeProfile(array $inputs)
    {
        $result = [
            "status" => "success",
            "message" => "امکان ارائه سرویس وجود دارد"
        ];
        return $result;
    }
    public function portinfo(array $inputs)
    {
        $result = [
            "status" => "success",
            "message" => "امکان ارائه سرویس وجود دارد"
        ];
        return $result;
    }
    public function changePort(array $inputs)
    {
        $result = [
            "status" => "success",
            "message" => "امکان ارائه سرویس وجود دارد"
        ];
        return $result;
    }
    public function registerPort(array $inputs)
    {
        $result = [
            "status" => "success",
            "message" => "امکان ارائه سرویس وجود دارد"
        ];
        return $result;
    }
    public function setPortstatus(array $inputs)
    {
        $result = [
            "status" => "success",
            "message" => "امکان ارائه سرویس وجود دارد"
        ];
        return $result;
    }
    public function failurePort(array $inputs)
    {
        $result = [
            "status" => "success",
            "message" => "امکان ارائه سرویس وجود دارد"
        ];
        return $result;
    }
    public function cancel(array $inputs)
    {
        $result = [
            "status" => "success",
            "message" => "امکان ارائه سرویس وجود دارد"
        ];
        return $result;
    }

    public function syncTicket(array $inputs)
    {
        throw new GeneralException(__("messages.not_available"), 424);
    }

    public function sendTicketComment(array $inputs)
    {
        throw new GeneralException(__("messages.not_available"), 424);
    }


    public function sendTicket(array $inputs)
    {
        $result = [
            "status" => "success",
            "message" => "امکان ارائه سرویس وجود دارد"
        ];
        return $result;
    }

    public function createTicketOptions(array $inputs)
    {

        $types = [
            [
                'id' => 707,
                "name" => __("words.technical_support"),
            ],            [
                'id' => 754,
                "name" => __("words.fixedـinـpersonـfailure"),
            ],            [
                'id' => 755,
                "name" => __("words.install"),
            ],
        ];

        $priorites = [
            [
                'id' => 1,
                "name" => __("words.low"),
            ],
            [
                'id' => 2,
                "name" => __("words.medium"),
            ],
            [
                'id' => 3,
                "name" => __("words.high"),
            ],
            [
                'id' => 4,
                "name" => __("words.very_high"),
            ],
            [
                'id' => 5,
                "name" => __("words.critical"),
            ],
        ];

        $response = [
            "priorites" => $priorites,
            "types" => $types
        ];

        return [
            "status" => 'success',
            "message" => __("messages.success"),
            "response" => $response
        ];
    }
}
