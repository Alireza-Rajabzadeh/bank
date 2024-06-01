<?php

namespace Database\Seeders;

use App\Models\TransactionTypes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transaction_types = [
            [
                'id' => 1,
                'name' => 'Money transfer',
            ],
            [
                'id' => 2,
                'name' => 'ٌWage',
            ]
        ];
        $this->command->info('Creating transaction types...');

        foreach ($transaction_types as   $transaction_type) {

            $is_exist = TransactionTypes::where("id", $transaction_type['id'])->first();
            if (empty($is_exist)) {
                TransactionTypes::create($transaction_type);
            }
        }
    }
}
