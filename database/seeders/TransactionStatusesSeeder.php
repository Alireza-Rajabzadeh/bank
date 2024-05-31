<?php

namespace Database\Seeders;

use App\Models\TransactionStatuses;
use App\Models\TransactionTypes;
use Illuminate\Database\Seeder;

class TransactionStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transaction_statuses = [
            [
                'id' => 1,
                'name' => 'in progress',
            ],
            [
                'id' => 2,
                'name' => 'success',
            ],
            [
                'id' => 3,
                'name' => 'failed',
            ],
            [
                'id' =>4,
                'name' => 'Lack of inventory',
            ]
        ];
        $this->command->info('Creating transaction types...');

        foreach ($transaction_statuses as   $transaction_status) {

            $is_exist = TransactionStatuses::where("id", $transaction_status['id'])->first();
            if (empty($is_exist)) {
                TransactionStatuses::create($transaction_status);
            }
        }
    }
}
