<?php

namespace App\Http\Requests\Trait;



trait ShowRequestTrait
{
    protected $show_rules= [
        'start_index' => "nullable|integer",
        'limit_index' => "nullable|integer",
        'order' => "nullable|string",
        'sort' => "nullable|string",
        'date_field' => "nullable|string",
        'from_date' => "nullable|date",
        'to_date' => "nullable|date",
        "dump" => "nullable|boolean"
    ];


}
