<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCardRequest;
use App\Services\AccountService;
use App\Services\CardService;
use Illuminate\Support\Facades\DB;

class CardsController extends Controller
{
    protected $account_service;
    protected $card_service;
    function __construct(AccountService $account_service, CardService $card_service)
    {
        $this->account_service = $account_service;
        $this->card_service = $card_service;
    }


    function addCard(AddCardRequest $request)
    {
        $request_data = $request->validated();

        $add_card_transaction = DB::transaction(function () use ($request_data) {
            $account = $this->account_service->insert($request_data);

            $request_data['account_id'] = $account->id;

            $card = $this->card_service->insert($request_data);

            $result = [
                'account' => $account,
                'card' => $card,
            ];
            return $result;
        });

        return apiResponse(true, $add_card_transaction);
    }
}
