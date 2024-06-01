<?php

namespace Database\Seeders;

use App\Models\AccountStatuses;
use App\Models\CardStatuses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountAndCartStatuses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account_statuses = [
            [
                'id' => 1,
                'name' => 'active',
                'is_allowed' => true
            ],
            [
                'id' => 2,
                'name' => 'banned',
                'is_allowed' => false
            ]
        ];
        $card_statuses = [
            [
                'id' => 1,
                'name' => 'active',
                'is_allowed' => true
            ],
            [
                'id' => 2,
                'name' => 'banned',
                'is_allowed' => false
            ]
        ];
        $this->command->info('Creating accounts statuses...');

        foreach ($account_statuses as   $account_status) {

            $is_exist = AccountStatuses::first($account_status['id']);
            if (empty($is_exist)) {
                AccountStatuses::create($account_status);
            }
        }
        $this->command->info('Creating cards statuses...');

        foreach ($card_statuses as   $card_statuse) {

            $is_exist = CardStatuses::first($card_statuse['id']);
            if (empty($is_exist)) {
                CardStatuses::create($card_statuse);
            }
        }
    }
}
