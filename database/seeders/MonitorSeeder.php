<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('monitor')->insert([
            [
                'id' => 1,
                'type' => '异常',
                'description' => '系统异常',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 2,
                'type' => '异常',
                'description' => '数据库异常',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 3,
                'type' => '异常',
                'description' => 'PHP异常',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
