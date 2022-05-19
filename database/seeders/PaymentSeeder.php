<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->data();
    }

    /**
     * 插入支付接口测试数据
     *
     */
    private function data()
    {

        DB::table('intermediate_servers')->insert([
            [
                'name' => '服务器A',
                'ip' => '127.0.0.1',
                'domain' => 'http://www.a.com',
                'status' => 1,
            ],
            [
                'name' => '服务器B',
                'ip' => '127.0.0.1',
                'domain' => 'http://www.b.com',
                'status' => 1,
            ],
            [
                'name' => '服务器C',
                'ip' => '127.0.0.1',
                'domain' => 'http://www.c.com',
                'status' => 1,
            ],
        ]);

        DB::table('payment_domain')->insert([
            [
                'domain' => 'http://pay1.xxxx.com',
                'payment_category_id' => 0,
                'intermediate_servers_id' => 1,
                'status' => 1,
            ],
            [
                'domain' => 'http://pay1.xxpay.com',
                'payment_category_id' => 1,
                'intermediate_servers_id' => 1,
                'status' => 1,
            ],
            [
                'domain' => 'http://pay2.xxpay.com',
                'payment_category_id' => 1,
                'intermediate_servers_id' => 2,
                'status' => 1,
            ],
            [
                'domain' => 'http://pay3.xxpay.com',
                'payment_category_id' => 1,
                'intermediate_servers_id' => 3,
                'status' => 1,
            ],
            [
                'domain' => 'http://pay1.b6pay.com',
                'payment_category_id' => 2,
                'intermediate_servers_id' => 1,
                'status' => 1,
            ],
            [
                'domain' => 'http://pay2.b6pay.com',
                'payment_category_id' => 3,
                'intermediate_servers_id' => 2,
                'status' => 1,
            ],
        ]);

        DB::table('payment_channel')->insert([
            [
                'name' => '易宝在线网银',
                'front_name' => '在线网银',
                'payment_category_id' => 1,
                'payment_method_id' => 2,
                'payment_domain_id' => 1,
                'platform' => 0,
                'register_time_limit' => 0,
                'recharge_times_limit' => 0,
                'recharge_amount_total_limit' => 0,
                'invalid_times_limit' => 99,
                'invalid_times_lock' => 0,
                'top_user_ids' => '["2", "3", "1", "4", "5"]',
                'sync_status' => '[]',
                'sort' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '易宝微信扫码',
                'front_name' => '微信扫码',
                'payment_category_id' => 1,
                'payment_method_id' => 3,
                'payment_domain_id' => 1,
                'platform' => 0,
                'register_time_limit' => 0,
                'recharge_times_limit' => 0,
                'recharge_amount_total_limit' => 0,
                'invalid_times_limit' => 99,
                'invalid_times_lock' => 0,
                'top_user_ids' => '["2", "3", "1", "4", "5"]',
                'sync_status' => '[]',
                'sort' => 2,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '工商银行',
                'front_name' => '手工转账',
                'payment_category_id' => 5,
                'payment_method_id' => 1,
                'payment_domain_id' => 0,
                'platform' => 0,
                'register_time_limit' => 0,
                'recharge_times_limit' => 0,
                'recharge_amount_total_limit' => 0,
                'invalid_times_limit' => 99,
                'invalid_times_lock' => 0,
                'top_user_ids' => '["2", "3", "1", "4", "5"]',
                'sync_status' => '[]',
                'sort' => 3,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '微信线下',
                'front_name' => '微信线下',
                'payment_category_id' => 7,
                'payment_method_id' => 16,
                'payment_domain_id' => 0,
                'platform' => 0,
                'register_time_limit' => 0,
                'recharge_times_limit' => 0,
                'recharge_amount_total_limit' => 0,
                'invalid_times_limit' => 99,
                'invalid_times_lock' => 0,
                'top_user_ids' => '["2", "3", "1", "4", "5"]',
                'sync_status' => '[]',
                'sort' => 4,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '同略云线下',
                'front_name' => '同略云线下转账',
                'payment_category_id' => 31,
                'payment_method_id' => 17,
                'payment_domain_id' => 1,
                'platform' => 0,
                'register_time_limit' => 0,
                'recharge_times_limit' => 0,
                'recharge_amount_total_limit' => 0,
                'invalid_times_limit' => 99,
                'invalid_times_lock' => 0,
                'top_user_ids' => '["2", "3", "1", "4", "5"]',
                'sync_status' => '[]',
                'sort' => 5,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
        DB::table('payment_channel_attribute')->insert([
            [
                'payment_channel_id' => 1,
                'type' => 'account_number',
                'value' => '1234567898',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'account_key',
                'value' => 'QbBYrzHvx1WtxTbrpyaZtyf4Q1QNFOD8jdWpgG2/BNF1k0CC+iVfKf5m73V3LC5iYM8Zyn6mxK+OHapbIOY6So7IegC6aEBtYphXk9LG71w=',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'account_key2',
                'value' => '',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'account_bank_name',
                'value' => '',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'account_full_name',
                'value' => '',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'account_address',
                'value' => '',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'amount_min',
                'value' => '20',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'amount_max',
                'value' => '50000',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'banks',
                'value' => '["ABC","ICBC","CCB","BOCOM","CMB","CMBC","CEB","CIB","PSBC","PAB","SPDB","CNCB","GDB","HXB","BOB"]',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'user_fee_status',
                'value' => '1',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'user_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'user_fee_line',
                'value' => '100',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'user_fee_down_type',
                'value' => '1',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'user_fee_down_value',
                'value' => '2',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'user_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'user_fee_up_value',
                'value' => '0.1',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'platform_fee_status',
                'value' => '1',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'platform_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'platform_fee_line',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'platform_fee_down_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'platform_fee_down_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'platform_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'platform_fee_up_value',
                'value' => '0.1',
            ],
            [
                'payment_channel_id' => 1,
                'type' => 'user_level_ids',
                'value' => json_encode(['1']),
            ]
        ]);
        DB::table('payment_channel_attribute')->insert([
            [
                'payment_channel_id' => 2,
                'type' => 'account_number',
                'value' => 'abcdefg',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'account_key',
                'value' => 'QbBYrzHvx1WtxTbrpyaZtyf4Q1QNFOD8jdWpgG2/BNF1k0CC+iVfKf5m73V3LC5iYM8Zyn6mxK+OHapbIOY6So7IegC6aEBtYphXk9LG71w=',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'account_key2',
                'value' => '',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'account_bank_name',
                'value' => '',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'account_full_name',
                'value' => '',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'account_address',
                'value' => '',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'amount_min',
                'value' => '20',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'amount_max',
                'value' => '3000',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'banks',
                'value' => '[]',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'user_fee_status',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'user_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'user_fee_line',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'user_fee_down_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'user_fee_down_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'user_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'user_fee_up_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'platform_fee_status',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'platform_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'platform_fee_line',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'platform_fee_down_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'platform_fee_down_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'platform_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'platform_fee_up_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 2,
                'type' => 'user_level_ids',
                'value' => json_encode(['1']),
            ]
        ]);
        DB::table('payment_channel_attribute')->insert([
            [
                'payment_channel_id' => 3,
                'type' => 'account_number',
                'value' => '45451234567898',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'account_key',
                'value' => '',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'account_key2',
                'value' => '',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'account_bank_name',
                'value' => '工商银行',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'account_full_name',
                'value' => '张三',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'account_address',
                'value' => '上海市',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'postscript_status',
                'value' => 1,
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'amount_min',
                'value' => '1',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'amount_max',
                'value' => '50000',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'banks',
                'value' => '[]',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'user_fee_status',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'user_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'user_fee_line',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'user_fee_down_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'user_fee_down_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'user_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'user_fee_up_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'platform_fee_status',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'platform_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'platform_fee_line',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'platform_fee_down_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'platform_fee_down_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'platform_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'platform_fee_up_value',
                'value' => '0.1',
            ],
            [
                'payment_channel_id' => 3,
                'type' => 'user_level_ids',
                'value' => json_encode(['1']),
            ]
        ]);
        DB::table('payment_channel_attribute')->insert([
            [
                'payment_channel_id' => 4,
                'type' => 'account_number',
                'value' => '',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'qrcode_type',
                'value' => '微信/支付宝',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'qrcode_url',
                'value' => 'http://www.test.com/',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'amount_min',
                'value' => '1',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'amount_max',
                'value' => '50000',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'user_fee_status',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'user_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'user_fee_line',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'user_fee_down_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'user_fee_down_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'user_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'user_fee_up_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'platform_fee_status',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'platform_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'platform_fee_line',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'platform_fee_down_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'platform_fee_down_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'platform_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'platform_fee_up_value',
                'value' => '1',
            ],
            [
                'payment_channel_id' => 4,
                'type' => 'user_level_ids',
                'value' => json_encode(['1']),
            ]
        ]);
        DB::table('payment_channel_attribute')->insert([
            [
                'payment_channel_id' => 5,
                'type' => 'account_number',
                'value' => '64654321987654321',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'account_key',
                'value' => '5Fm/ozz16dj8/8V8ySRPNiWk6Ljad7t9musKWV9T25h3pR+9BG3tp2XKjNhARc46hOLnfMI5uHI6KqlGdDLIym9dd5BS8m6V8uGFuodh2kY=',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'account_bank_flag',
                'value' => 'ABC_农业银行',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'account_full_name',
                'value' => '张三',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'account_address',
                'value' => '上海市',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'amount_min',
                'value' => '1',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'api_gateway',
                'value' => 'https://s02.tonglueyun.com',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'postscript_status',
                'value' => 1,
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'amount_max',
                'value' => '50000',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'banks',
                'value' => '[]',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'user_fee_status',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'user_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'user_fee_line',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'user_fee_down_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'user_fee_down_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'user_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'user_fee_up_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'platform_fee_status',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'platform_fee_operation',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'platform_fee_line',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'platform_fee_down_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'platform_fee_down_value',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'platform_fee_up_type',
                'value' => '0',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'platform_fee_up_value',
                'value' => '0.1',
            ],
            [
                'payment_channel_id' => 5,
                'type' => 'user_level_ids',
                'value' => json_encode(['1']),
            ]
        ]);
    }
}
