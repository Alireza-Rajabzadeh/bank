<?php

namespace App\Repositories;

use App\Models\Accounts;
use App\Models\Cards;
use App\Repositories\BaseRepository;

class CardRepository extends BaseRepository
{
    public function setModel()
    {
        return Cards::class;
    }
}
