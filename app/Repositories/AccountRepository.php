<?php

namespace App\Repositories;

use App\Models\Accounts;
use App\Repositories\BaseRepository;

class AccountRepository extends BaseRepository
{
    public function setModel()
    {
        return Accounts::class;
    }
}
