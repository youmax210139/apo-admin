<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReportLotterySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Service\Models\ReportLottery::class, 20)->create();
    }
}
