<?php

namespace App\Services;

use Exception;
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
        $link = $this->isExist($inputs);

        if ($link->count() > 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([__(
                'account_already_defiend',
                [
                    'account_number' => $inputs['account_number']
                ]
            )]);
        }
        return true;
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


    function viewLinksReport($index_data)
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
            "link",
            "shortner_link"
        ];


        $index_data['date_field'] = $index_data['date_field'] ?? "created_at";
        $index_data['date_field'] = in_array($index_data['date_field'], ['created_at', 'updated_at']) ? $index_data['date_field'] : "created_at";
        $where_clause = array_filter(arrayOnly($index_data, $where_clause_columns));
        $date_clause = array_filter(arrayOnly($index_data, $date_clause_columns));
        $like_clause = array_filter(arrayOnly($index_data, $like_clause_columns));
        $search_data['where'] = $where_clause;
        $search_data['date'] = $date_clause;
        $search_data['like'] = $like_clause;

        $links = $this->account_repository->index($search_data);

        return $links;
    }
}
