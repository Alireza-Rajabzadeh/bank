<?php

namespace App\Repositories;


use App\Models\SmsTemplates;
use App\Repositories\BaseRepository;

class SmsTemplatesRepository extends BaseRepository
{
    public function setModel()
    {
        return SmsTemplates::class;
    }
}
