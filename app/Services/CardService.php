<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CardRepository;
use App\Services\Traits\ShowServiceTrait;
use Illuminate\Validation\ValidationException;

class CardService
{
    use ShowServiceTrait;
    public $card_repository;

    private $generate_try = 0;
    function __construct(CardRepository $card_repository)
    {
        $this->card_repository = $card_repository;
    }

    function isExist($inputs)
    {
        $search_inputs = [
            'where' => [
                'card_number' => $inputs['card_number'] ?? null
            ]
        ];

        $search_inputs['where'] = array_filter($search_inputs['where']);

        return  $this->card_repository->search($search_inputs)->first();
    }

    function shouldNotExist($inputs)
    {
        $card = $this->isExist($inputs);

        if (!empty($card)) {
            throw \Illuminate\Validation\ValidationException::withMessages([__(
                'validation.card_already_defiend',
                [
                    'card_number' => $inputs['card_number']
                ]
            )]);

        }
        return true;
    }



    function insert($inputs)
    {
        $this->shouldNotExist($inputs);

        $insert_data = [
            "account_id" => $inputs['account_id'],
            "status_id" => 1,
            'card_number' => $inputs['card_number']
        ];

        $card = $this->card_repository->insert(
            $insert_data
        );
        return $card;
    }




    function viewcardsReport($index_data)
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
            "card",
            "shortner_card"
        ];


        $index_data['date_field'] = $index_data['date_field'] ?? "created_at";
        $index_data['date_field'] = in_array($index_data['date_field'], ['created_at', 'updated_at']) ? $index_data['date_field'] : "created_at";
        $where_clause = array_filter(arrayOnly($index_data, $where_clause_columns));
        $date_clause = array_filter(arrayOnly($index_data, $date_clause_columns));
        $like_clause = array_filter(arrayOnly($index_data, $like_clause_columns));
        $search_data['where'] = $where_clause;
        $search_data['date'] = $date_clause;
        $search_data['like'] = $like_clause;

        $cards = $this->card_repository->index($search_data);

        return $cards;
    }
}
