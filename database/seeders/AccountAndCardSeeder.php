<?php

namespace Database\Seeders;

use App\Models\Accounts;
use App\Models\Cards;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountAndCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample accounts and cards...');
        $accounts = [
            [
                'id' => 1,
                'account_number' => "1234567891012",
                "user_id" => 1,
                "cards" => []
            ],
            [
                'account_number' => "0202901868005",
                "user_id" => User::all()->random()->id,
                "cards" => [
                    [
                        'card_number' => "6362147010005732"
                    ]
                ]
            ],
            [
                'account_number' => "117.750.5110110.1",
                "user_id" => User::all()->random()->id,
                "cards" => [
                    [
                        'card_number' => "6274121940067465"
                    ]
                ]
            ],
            [
                'account_number' => "146.834.820820.1",
                "cards" => [
                    [
                        'card_number' => "5057851990005131"
                    ]
                ]
            ],
            [
                'account_number' => "81000007660008",
                "user_id" => User::all()->random()->id,
                "cards" => [
                    [
                        'card_number' => "6221061208750556"
                    ]
                ]
            ]

        ];

        foreach ($accounts as $key => $account_info) {
            $account_data = [
                "user_id" => User::all()->random()->id,
                "status_id" => 1,
                "account_number" => $account_info['account_number'],
                "credit" => random_int(1, 5) * 10000000,
            ];

            $account = Accounts::where('account_number', $account_info['account_number'])->first();

            if (empty($account)) {
                $account =  Accounts::create($account_data);
            }

            foreach ($account_info['cards'] as $card_index => $account_card) {

                $card_data = [
                    "account_id" => $account->id,
                    "status_id" => 1,
                    'card_number' => $account_card['card_number']
                ];
                $card = Cards::where('card_number', $account_card['card_number'])->first();
                if (empty($card)) {
                    $card =  Cards::create($card_data);
                }
            }
        }
    }
}
