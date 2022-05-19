<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserBehaviorLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_behavior_log')->insert([
            [
                'user_id' => 1,
                'db_user' => 'postgres',
                'level' => '0',
                'action' => '寻找漏洞',
                'description' => '用户尝试寻找任选二的玩法漏洞',
            ],
            [
                'user_id' => 2,
                'db_user' => 'postgres',
                'level' => '1',
                'action' => '改单行为',
                'description' => '用户尝试修改投注单号码为：4700，
                执行语句：update public.projects set code = \'4700\', code_position = \'1\' where id = 10000000',
            ],
            [
                'user_id' => 2,
                'db_user' => 'postgres',
                'level' => '1',
                'action' => '改单行为',
                'description' => '用户尝试修改投注单号码为：4800，
                执行语句：update public.projects set code = \'4800\', code_position = \'1\' where id = 10000000',
            ]
        ]);
    }
}
