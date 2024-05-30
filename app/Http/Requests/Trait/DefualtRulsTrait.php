<?php

namespace App\Http\Requests\Trait;



trait DefualtRulsTrait
{

    protected $default_rules= [
        '_request_id' => "nullable|string",
        '_requester_system' => "nullable|string",
    ];


}
