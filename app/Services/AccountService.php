<?php

namespace App\Services;

use Exception;
use App\Models\Accounts;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AccountRepository;
use App\Services\Traits\ShowServiceTrait;


class AccountService
{
    use ShowServiceTrait;
    public $account_repository;

    private $generate_try = 0;
    function __construct(AccountRepository $account_repository)
    {
        $this->account_repository = $account_repository;
    }

    function isExist($inputs)
    {
        $search_inputs = [
            'where' => [
                'account_number' => $inputs['account_number'] ?? null,
                'id' => $inputs['id'] ?? null,
            ]
        ];

        $search_inputs['where'] = array_filter($search_inputs['where']);
        return  $this->account_repository->search($search_inputs)->first();
    }

    function shouldNotExist($inputs)
    {
        $account = $this->isExist($inputs);

        if (!empty($account)) {
            throw \Illuminate\Validation\ValidationException::withMessages([__(
                'account_already_defiend',
                [
                    'account_number' => $inputs['account_number']
                ]
            )]);
        }
        return true;
    }

    function shouldExist($inputs)
    {
        $account = $this->isExist($inputs);

        if (empty($account)) {
            throw \Illuminate\Validation\ValidationException::withMessages([__(
                'validation.account_not_defiend'
            )]);
        }
        return $account;
    }


    function hasEnoughCreditAndPermission($inputs, $amount)
    {
        $account = $this->shouldExist($inputs);

        if (!$account->status->is_allowed) {

            throw new Exception(__("validation.account_status_is", ["status_name" => $account->status->name]), 422);
        }

        if ($account->credit < ($amount + env("BANK_WAGE", 500))) {
            throw new Exception(__("validation.not_enouph_credit"), 422);
        }

        return $account;
    }


    function decreaseCredit(Accounts $account, $ammount)
    {
        $search = [
            "where" => [
                "id" => $account->id
            ]
        ];
        $update_data = [
            'credit' => (intval($account->credit) - intval($ammount))
        ];
        return $this->account_repository->update($search, $update_data);
    }

    function icreaseCredit(Accounts $account, $ammount)
    {
        $search = [
            "where" => [
                "id" => $account->id
            ]
        ];
        $update_data = [
            'credit' => (intval($account->credit) + intval($ammount))
        ];
        return $this->account_repository->update($search, $update_data);
    }


    function insert($inputs)
    {

        $account = $this->isExist($inputs);
        if (empty($account)) {
            $insert_data = [
                "user_id" => Auth::user()->id,
                "status_id" => 1,
                "account_number" => $inputs['account_number'],
                "credit" => 100000
            ];

            $account = $this->account_repository->insert(
                $insert_data
            );
        }
        return $account;
    }


    function viewaccountsReport($index_data)
    {

        $search_data = $this->getDefaultSearchParams($index_data);
        $date_clause_columns = $this->getDefaultDateParams($index_data);

        $search_data["count_relations"] = [
            "logs",
            "user"
        ];
        $search_data["relations"] = [
            "user"
        ];

        $where_clause_columns = [
            "id",
            "user_id"
        ];

        $like_clause_columns = [
            "account",
            "shortner_account"
        ];


        $index_data['date_field'] = $index_data['date_field'] ?? "created_at";
        $index_data['date_field'] = in_array($index_data['date_field'], ['created_at', 'updated_at']) ? $index_data['date_field'] : "created_at";
        $where_clause = array_filter(arrayOnly($index_data, $where_clause_columns));
        $date_clause = array_filter(arrayOnly($index_data, $date_clause_columns));
        $like_clause = array_filter(arrayOnly($index_data, $like_clause_columns));
        $search_data['where'] = $where_clause;
        $search_data['date'] = $date_clause;
        $search_data['like'] = $like_clause;

        $accounts = $this->account_repository->index($search_data);

        return $accounts;
    }
}
