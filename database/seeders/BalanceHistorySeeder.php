<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;
class BalanceHistorySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $now = Carbon::now();
        $startDate = $now->copy()->subMonths(2);

        $batch = [];

        for ($date = $startDate; $date->lte($now); $date->addHour()) {
            foreach (range(1, 50) as $account) {
                foreach (range(1, 5) as $currency) {
                    $batch[] = [
                        'account_id'  => $account,
                        'currency_id' => $currency,
                        'amount'      => $faker->randomFloat(8, 0, 1000),
                        'created_at'  => $date->toDateTimeString(),
                        'updated_at'  => $date->toDateTimeString(),
                    ];

                    if (count($batch) >= 1000) {
                        DB::table('balance_histories')->insert($batch);
                        $batch = [];
                    }
                }
            }
        }

        if (!empty($batch)) {
            DB::table('balance_histories')->insert($batch);
        }
    }
}
