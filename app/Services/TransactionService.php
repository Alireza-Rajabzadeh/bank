<?php

namespace App\Services;

use App\Models\Transactions;
use Exception;
use App\Services\Traits\ShowServiceTrait;
use App\Repositories\TransactionRepository;


class TransactionService
{
    use ShowServiceTrait;
    public $transaction_repository;

    private $generate_try = 0;
    function __construct(TransactionRepository $transaction_repository)
    {
        $this->transaction_repository = $transaction_repository;
    }

    function isExist($inputs)
    {
        $search_inputs = [
            'where' => [
                'card_number' => $inputs['card_number'] ?? null
            ]
        ];

        $search_inputs['where'] = array_filter($search_inputs['where']);

        return  $this->transaction_repository->search($search_inputs)->first();
    }


    function openTransactionShouldNotExist($inputs)
    {

        $search_inputs = [
            'where' => [
                "status_id" => 1,
                'origin_card_id' => $inputs['origin_card']->id ?? $inputs['origin_card_id'],
                'destination_card_id' => $inputs['destination_card']->id ?? $inputs['destination_card_id'],
            ],
            'date' =>
            [
                "from_date" => date("Y-m-d H:i:s", strtotime("-10 minutes")),
                "to_date" => date("Y-m-d H:i:s"),
                "date_field" => "created_at",
            ]
        ];


        $transaction = $this->transaction_repository->search($search_inputs)->first();

        if (!empty($transaction)) {

            throw new Exception(__("validation.same_open_transaction_in", ['minute' => 10]), 422);
        }

        return true;
    }


    /**
     * defualt status_id 2 is for successful transaction
     */

    function completeTransaction(Transactions $transaction, $status_id = 2, $description = null) #2 : sunnessfull
    {
        $search_transaction = [
            'where' => [
                'id' => $transaction->id
            ]
        ];
        $update_tramsaction_data = [
            "status_id" => $status_id,
            "description" => $description,
        ];
        return  $this->transaction_repository->update($search_transaction, $update_tramsaction_data);
    }

    function startTransaction($inputs)
    {
        $this->openTransactionShouldNotExist($inputs);

        $insert_data = [
            "status_id" => 1,
            "type_id" => 1,
            'parrent_transaction_id' => $inputs['parrent_transaction_id'] ?? null,
            'origin_card_id' => $inputs['origin_card']->id ?? $inputs['origin_card_id'],
            'destination_card_id' => $inputs['destination_card']->id ?? $inputs['destination_card_id'],
            'ammount' => $inputs['ammount'],
            'description' => $inputs['description'] ?? null,
        ];

        $insert_data = array_filter($insert_data);

        $transaction = $this->transaction_repository->insert(
            $insert_data
        );

        $wage_transaction_data = [
            "status_id" => 1,
            "type_id" => 2,
            'parrent_transaction_id' => $transaction->id,
            'origin_card_id' => $inputs['origin_card']->id ?? $inputs['origin_card_id'],
            'destination_card_id' => env("BANK_WAGE_ACCOUNT_ID", 1),
            'ammount' => env("BANK_WAGE", 500),
            'description' => $inputs['description'] ?? null,
        ];

        $wage_transaction = $this->transaction_repository->insert(
            $wage_transaction_data
        );


        return [
            "transaction" => $transaction,
            "wage_transaction" => $wage_transaction
        ];
    }


    function transactionalUsersReport($inputs)
    {
        $limit_index = $inputs['limit_index'] ?? 3;
        $transactions_limit_index = $inputs['transactions_limit_index'] ?? 10;
        $start_index = $inputs['start_index'] ?? 0;
        $from_date = $inputs['from_date'] ?? date("Y-m-d H:i:s", strtotime("-10 minutes"));
        $transactionals_users = $this->transaction_repository->transactionalUsers($limit_index, $start_index, $from_date);
        foreach ($transactionals_users as $index => $record) {
            unset($transactionals_users[$index]->password);
            unset($transactionals_users[$index]->remember_token);
            $transactionals_users[$index]->transactions = $this->transaction_repository->usertransactions($record->id, $transactions_limit_index);
        };
        return $transactionals_users;
    }
    function indexTransactions($index_data)
    {

        $search_data = $this->getDefaultSearchParams($index_data);
        $date_clause_columns = $this->getDefaultDateParams($index_data);

        $search_data["count_relations"] = [];
        $search_data["relations"] = [
            'card',
            'card.account',
            'card.account.user',
        ];

        $where_clause_columns = [
            "id",
            "status_id",
            "type_id",
            "origin_card_id",
            "destination_card_id",
            "parrent_transaction_id",
        ];

        $like_clause_columns = [
            "ammount",
            "description"
        ];


        $index_data['date_field'] = $index_data['date_field'] ?? "created_at";
        $index_data['date_field'] = in_array($index_data['date_field'], ['created_at', 'updated_at']) ? $index_data['date_field'] : "created_at";
        $where_clause = array_filter(arrayOnly($index_data, $where_clause_columns));
        $date_clause = array_filter(arrayOnly($index_data, $date_clause_columns));
        $like_clause = array_filter(arrayOnly($index_data, $like_clause_columns));
        $search_data['where'] = $where_clause;
        $search_data['date'] = $date_clause;
        $search_data['like'] = $like_clause;

        $transactions = $this->transaction_repository->index($search_data);

        return $transactions;
    }
}
