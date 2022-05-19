<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersPrizelevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //5个总代设置固定奖级
        $data = [
            ['user_id' => '1', 'level' => '1800'],
            ['user_id' => '2', 'level' => '1800'],
            ['user_id' => '3', 'level' => '1800'],
            ['user_id' => '4', 'level' => '1700'],
            ['user_id' => '5', 'level' => '1900']
        ];
        DB::table('user_prize_level')->insert($data);
    }
}
