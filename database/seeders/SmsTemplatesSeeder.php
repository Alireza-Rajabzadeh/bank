<?php

namespace Database\Seeders;

use App\Models\SmsTemplates;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmsTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sms_tempplates = [
            [
                'id' => 1,
                'title' => 'decrease credit',
                'message' => 'مبلغ {ammount} از حساب شما کم شد .',
            ],
            [
                'id' => 2,
                'title' => 'decrease credit',
                'message' => 'مبلغ {ammount}  به حساب شما واریز شد .',
            ],
        ];
        $this->command->info('Creating sms templates...');

        foreach ($sms_tempplates as   $tempplate) {

            $is_exist = SmsTemplates::where("id", $tempplate['id'])->first();
            if (empty($is_exist)) {
                SmsTemplates::create($tempplate);
            }
        }
    }
}
