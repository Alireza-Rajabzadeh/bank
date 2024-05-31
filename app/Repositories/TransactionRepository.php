<?php

namespace App\Repositories;

use App\Models\Accounts;
use App\Models\Cards;
use App\Models\Transactions;
use App\Repositories\BaseRepository;

class TransactionRepository extends BaseRepository
{
    public function setModel()
    {
        return Transactions::class;
    }
}
