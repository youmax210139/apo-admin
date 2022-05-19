<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserBanksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $take = rand(100, 200);
        $users = DB::table('users')->take($take)->get();
        $banks = DB::table('banks')->where(['withdraw' => true, 'disabled' => false])->get();
        $regions = DB::table('regions')->get();
        $bankIds = [];
        foreach ($banks as $bank) {
            $bankIds[] = $bank->id;
        }
        $regionIds_1 = [];
        $regionIds_2 = [];
        foreach ($regions as $region) {
            if ($region->level == 2) {
                $regionIds_1[$region->id] = $region->parent_id;
            } elseif ($region->level == 3) {
                $regionIds_2[$region->parent_id][] = $region->id;
            }
        }
        $data = array();
        foreach ($users as $user) {
            $times = rand(1, 4);
            for ($i = 0; $i < $times; $i++) {
                $cityId = array_rand($regionIds_1);
                $provinceId = $regionIds_1[$cityId];
                $tmpKey = array_rand($regionIds_2[$cityId]);
                $districtId = $regionIds_2[$cityId][$tmpKey];
                $statusAll = [1]; //[1,2,3];
                $status = $statusAll[array_rand($statusAll)];
                $reason = '';
                if (in_array($status, [2, 3])) {
                    $reason = 'tell me why';
                }
                $bankId = $bankIds[array_rand($bankIds)];
                $data[] = [
                    'user_id' => $user->id,
                    'bank_id' => $bankId,
                    'province_id' => $provinceId,
                    'city_id' => $cityId,
                    'district_id' => $districtId,
                    'branch' => '中山' . rand(1, 3) . '路支行',
                    'account_name' => 'n' . $user->id,
                    'account' => rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999),
                    'is_default' => false,
                    'status' => $status,
                    'reason' => $reason,
                ];
            }
        }
        DB::table('user_banks')->insert($data);
    }
}
