<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo "生成4级用户数据：\n5个总代，\n每个总代有3个一级代理，\n每个一级代理有2个二级代理，\n每个二级代理2个三级【用户】，\n所有用户默认密码 admin123\n所需时间较长，请耐心等候\n";
        $yi_start = 1;
        $inserts = array();
        for ($yi = $yi_start; $yi <= $yi_start + 4; $yi++) {
            $inserts[] = $this->getInsertSql($yi, $yi, 0, []);
            $er_start = $yi * 20;
            for ($er = $er_start; $er <= $er_start + 3; $er++) {
                $inserts[] = $this->getInsertSql($er, $yi, $yi, [$yi]);
                $san_start = $er * 10;
                for ($san = $san_start; $san <= $san_start + 1; $san++) {
                    $inserts[] = $this->getInsertSql($san, $yi, $er, [$yi, $er]);
                    $si_start = $san * 10;
                    for ($si = $si_start; $si <= $si_start + 1; $si++) {
                        $inserts[] = $this->getInsertSql($si, $yi, $san, [$yi, $er, $san]);
                    }
                }
            }
        }
        DB::table('users')->insert($inserts);
        $this->shiwan();
    }

    private function getInsertSql($user_id, $lvtopid, $parent_id, $parent_tree_array)
    {
        static $date;
        if (empty($date)) {
            $date = date('Y-m-d H:i:s');
        }
        $user_group_id = 1; //1正式 2测试 3试玩
        if ($user_id == 2 || $lvtopid == 2) {
            $user_group_id = 2;
        } elseif ($user_id == 3 || $lvtopid == 3) {
            $user_group_id = 3;
        }
        $user_type_id = count($parent_tree_array) == 3 ? 3 : (count($parent_tree_array) ? 2 : 1); //1总代 2代理 3用户
        $username = '';
        $usernick = '';
        $map = [
            '1' => ['zhengshi', '真实总代'],
            '20' => ['zhuguan', '真实主管'],
            '200' => ['zhaoshang', '真实招商'],
            '2000' => ['zhishu', '真实直属'],

            '2' => ['ceshi', '测试总代'],
            '40' => ['ceshi_zhuguan', '测试主管'],
            '400' => ['ceshi_zhaoshang', '测试招商'],
            '4000' => ['ceshi_zhishu', '测试直属'],

            '3' => ['shiwan', '试玩总代'],
            '60' => ['shiwan_zhuguan', '试玩主管'],
            '600' => ['shiwan_zhaoshang', '试玩招商'],
            '6000' => ['shiwan_zhishu', '试玩直属'],

            '4' => ['zhengshi4', '真实4总代'],
            '80' => ['zhengshi4_zhuguan', '真实4主管'],
            '800' => ['zhengshi4_zhaoshang', '真实4招商'],
            '8000' => ['zhengshi4_zhishu', '真实4直属 '],

            '5' => ['zhengshi5', '真实5总代'],
            '100' => ['zhengshi5_zhuguan', '真实5主管'],
            '1000' => ['zhengshi5_zhaoshang', '真实5招商'],
            '10000' => ['zhengshi5_zhishu', '真实5直属 '],
        ];
        if (isset($map[$user_id])) {
            $username = $map[$user_id][0];
            $usernick = $map[$user_id][1];
        }

        $data = [
            'id' => $user_id,
            'top_id' => $lvtopid,
            'parent_id' => $parent_id,
            'parent_tree' => json_encode($parent_tree_array),
            'user_type_id' => $user_type_id,
            'user_group_id' => $user_group_id,
            'usernick' => $usernick ?: $this->faker->unique()->firstName,
            'username' => $username ?: $this->faker->unique()->word,
            'password' => bcrypt('admin123'),
            'remember_token' => Str::random(10),
            'created_ip' => '8.8.8.8',
            'last_ip' => '8.8.8.8',
            'created_at' => $date,
            'updated_at' => $date,
        ];
        return $data;
    }

    private function shiwan()
    {
        $top_id = 3; //试玩总代id
        $parent_id = $top_id;
        $parent_tree_array = [$top_id];
        $user_type_id = 3; //1总代 2代理 3用户
        $user_group_id = 3; //试玩组
        $date = date('Y-m-d H:i:s');
        $inserts = [];

        for ($i = 1; $i <= 1000; $i++) {
            $inserts[] = [
                //'id' => $user_id,
                'top_id' => $top_id,
                'parent_id' => $parent_id,
                'parent_tree' => json_encode($parent_tree_array),
                'user_type_id' => $user_type_id,
                'user_group_id' => $user_group_id,
                'usernick' => '试玩用户' . $i,
                'username' => 'shiwan' . $i,
                'password' => bcrypt('a123456'),
                'remember_token' => Str::random(10),
                'created_ip' => '8.8.8.8',
                'last_ip' => '8.8.8.8',
                'created_at' => $date,
                'updated_at' => $date,
            ];
        }
        DB::table('users')->insert($inserts);
        DB::update("UPDATE config SET \"value\"={$parent_id} WHERE \"key\"='reg_test_parent_id';");
    }
}
