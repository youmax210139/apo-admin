<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRebatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //5个总代固定设置返点
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.08 FROM users WHERE id IN (1,2,3)"); //1800
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.08 FROM users WHERE id IN (20,40,60)"); //1800
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.07 FROM users WHERE id IN (200,400,600)"); //1800
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.055 FROM users WHERE id IN (2000,4000,6000)"); //1800


        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.13 FROM users WHERE id=4"); //1700
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.13 FROM users WHERE id IN (80)"); //1700
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.12 FROM users WHERE id IN (800)"); //1700
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.105 FROM users WHERE id IN (8000)"); //1700


        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.03 FROM users WHERE id=5"); //1900
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.03 FROM users WHERE id IN (100)"); //1900
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.02 FROM users WHERE id IN (1000)"); //1900
        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.005 FROM users WHERE id IN (10000)"); //1900

        DB::insert("INSERT INTO user_rebates SELECT nextval('user_rebates_id_seq'),id,'lottery',0.003 FROM users WHERE id NOT IN(1,2,3,4,5,20,200,2000,40,400,4000,60,600,6000,80,800,8000,100,1000,10000)");
    }
}
