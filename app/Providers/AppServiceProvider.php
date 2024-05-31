<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{

    private $times_algo = [
        2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1, 2, 1,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('card_number', function ($attribute, $value, $parameters) {

            $value = convertToEnglishNumber($value);

            if (!(strlen($value) == 16)) {
                return false;
            }


            $card_parts = str_split($value, 1);
            $sum = 0;
            foreach ($card_parts as $index => $number) {
                $temp_sum = ($card_parts[$index] * $this->times_algo[$index]);
                if ($temp_sum > 9) {

                    $temp_sum = $temp_sum - 9;
                }
                $sum = $sum + $temp_sum;
            }
            if (($sum % 10) != 0) {
                return false;
            }

            return true;
        });
    }
}
