<?php

namespace App\Http\Controllers;


use App\Services\CardService;
use App\Services\AccountService;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionService;
use App\Http\Requests\DoTransactionRequest;
use App\Http\Requests\TransactionsIndexRequest;
use App\Http\Requests\TransactionalUsersRequest;

class TransactionController extends Controller
{
    protected $account_service;
    protected $card_service;
    protected $transaction_service;
    function __construct(TransactionService $transaction_service, AccountService $account_service, CardService $card_service)
    {
        $this->account_service = $account_service;
        $this->card_service = $card_service;
        $this->transaction_service = $transaction_service;
    }


    function doTransaction(DoTransactionRequest $request)
    {


        $request_data = $request->validated();


        $origin_card_data = [
            "card_number" => $request_data['origin_card_number']
        ];
        $request_data['origin_card'] = $this->card_service->shouldExist($origin_card_data);
        $origin_account_data = [
            'id' => $request_data['origin_card']->account_id
        ];

        $request_data['origin_account'] = $this->account_service->hasEnoughCreditAndPermission($origin_account_data, $request_data['ammount']);


        $destination_card_number_data = [
            "card_number" => $request_data['destination_card_number']
        ];

        $request_data['destination_card'] = $this->card_service->shouldExist($destination_card_number_data);
        $destination_account_data = [
            'id' => $request_data['destination_card']->account_id
        ];
        $request_data['destination_account'] = $this->account_service->shouldExist($destination_account_data);


        $bank_wage_account_data = [
            'id' => env("BANK_WAGE_ACCOUNT_ID", 1)
        ];

        $request_data['bank_wage_account'] = $this->account_service->shouldExist($bank_wage_account_data);



        $transactions = $this->transaction_service->startTransaction($request_data);
        $request_data['transaction'] = $transactions['transaction'];
        $request_data['wage_transaction'] = $transactions['wage_transaction'];



        try {
            DB::transaction(function () use ($request_data) {

                $tansaction_ammount_and_wage = $request_data['ammount'] + env("BANK_WAGE", 500);

                $this->account_service->decreaseCredit($request_data['origin_account'], $tansaction_ammount_and_wage);
                $this->account_service->icreaseCredit($request_data['destination_account'], $request_data['ammount']);
                $this->account_service->icreaseCredit($request_data['bank_wage_account'], env("BANK_WAGE"));

                $this->transaction_service->completeTransaction($request_data['transaction']);
                $this->transaction_service->completeTransaction($request_data['wage_transaction']);
            });
        } catch (\Throwable $th) {

            $descriptions = $th->getMessage() ?? "";
            $this->transaction_service->completeTransaction($request_data['transaction'], 3, $descriptions); # 3 : failed
            $this->transaction_service->completeTransaction($request_data['wage_transaction'], 3, $descriptions); # 3 : failed
            throw $th;
        }

        return apiResponse();
    }

    function index(TransactionsIndexRequest $requeset)
    {
        $request_data = $requeset->validated();
        return $this->transaction_service->indexTransactions($request_data);
    }


    function transactionalUsers(TransactionalUsersRequest $request)
    {
        $request_data = $request->validated();
        $transactional_users =  $this->transaction_service->transactionalUsersReport($request_data);
        return apiResponse(true, $transactional_users);
    }
}
