<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserFundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::insert("INSERT INTO user_fund 
            SELECT id, 
                   random() * 10000.0423, 
                   random() * 100, 
                   random() * 10000
            FROM users");
    }
}
