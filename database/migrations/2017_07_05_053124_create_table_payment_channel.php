<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentChannel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_channel', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 40)->unique()->comment('后台名称');
            $table->string('front_name', 40)->comment('前台名字');
            $table->smallInteger('payment_category_id')->comment('支付渠道ID');
            $table->smallInteger('payment_method_id')->comment('支付类型ID');
            $table->smallInteger('payment_domain_id')->default(0)->comment('支付域名id');
            $table->smallInteger('platform')->default(0)->comment('限制使用平台，0不限 1PC 2手机');
            /*
            $table->string('account_number', 40)->comment('商户号或银行卡号');
            $table->string('account_key', 128)->comment('商户号密钥或私钥');
            $table->string('account_key2', 128)->comment('商户号公钥');
            $table->string('account_bank_name', 20)->default('')->comment('银行名称');
            $table->string('account_full_name', 15)->default('')->comment('银行卡姓名');
            $table->string('account_address', 30)->default('')->comment('银行卡开户地址');
            $table->integer('amount_min')->default(0)->unsigned()->comment('单笔最低充值额');
            $table->integer('amount_max')->default(0)->unsigned()->comment('单笔最高充值额');
            $table->boolean('user_fee_status')->default(0)->comment('用户手续费是否启用');
            $table->tinyInteger('user_fee_operation')->default(1)->comment('用户手续费类型，0减1加');
            $table->integer('user_fee_line')->default(0)->unsigned()->comment('用户手续费界定金额');
            $table->tinyInteger('user_fee_down_type')->default(1)->unsigned()->comment('用户低于界定的手续费类型，1百分比2固定值');
            $table->decimal('user_fee_down_value', 15, 4)->default(0)->unsigned()->comment('用户低于界定的手续费值');
            $table->tinyInteger('user_fee_up_type')->default(1)->unsigned()->comment('用户高于界定的手续费比例，1百分比2固定值');
            $table->decimal('user_fee_up_value', 15, 4)->default(0)->unsigned()->comment('用户高于界定的手续费值');
            $table->boolean('platform_fee_status')->default(0)->comment('平台手续费是否启用');
            $table->tinyInteger('platform_fee_operation')->default(1)->comment('平台手续费类型，1减2加');
            $table->integer('platform_fee_line')->default(0)->unsigned()->comment('平台手续费界定金额');
            $table->tinyInteger('platform_fee_down_type')->default(1)->unsigned()->comment('平台低于界定的手续费类型，1百分比2固定值');
            $table->decimal('platform_fee_down_value', 15, 4)->default(0)->unsigned()->comment('平台低于界定的手续费值');
            $table->tinyInteger('platform_fee_up_type')->default(1)->unsigned()->comment('平台高于界定的手续费比例，1百分比2固定值');
            $table->decimal('platform_fee_up_value', 15, 4)->default(0)->unsigned()->comment('平台高于界定的手续费值');
            $table->string('payment_bank', 255)->default('')->comment('在线网银的支付银行');
            */
            $table->smallInteger('register_time_limit')->default(0)->unsigned()->comment('用户注册时间限制，单位小时');
            //$table->integer('user_balance_limit')->default(0)->unsigned()->comment('用户余额限制');
            $table->smallInteger('recharge_times_limit')->default(0)->unsigned()->comment('充值次数限制');
            //$table->integer('recharge_amount_min_limt')->default(0)->unsigned()->comment('用户过往单次最少充值金额');
            $table->integer('recharge_amount_total_limit')->default(0)->unsigned()->comment('用户过往累计最少充值金额');
            $table->smallInteger('invalid_times_limit')->default(0)->unsigned()->comment('用户每天无效申请最多次数');
            $table->smallInteger('invalid_times_lock')->default(0)->unsigned()->comment('用户10分钟无效申请最多次数');
            $table->jsonb('top_user_ids')->default('[]')->comment('开通的总代user_id');
            $table->json('sync_status')->default('[]')->comment('同步状态');
            $table->tinyInteger('sort')->default(0)->comment('排序');
            $table->boolean('status')->default(0)->comment('是否启用');
            $table->timestamps();
        });

        $this->menu();
    }

    private function menu()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon'      => 'fa-dollar',
            'rule'      => 'payments',
            'name'      => '支付接口管理',
        ]);

        //支付域名菜单
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule'      => 'paymentdomain/index',
                'name'      => '支付域名管理',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentdomain/create',
                'name'      => '添加支付域名',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentdomain/edit',
                'name'      => '编辑支付域名',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentdomain/setstatus',
                'name'      => '设置支付域名状态',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentdomain/delrecord',
                'name'      => '删除支付域名',
            ],
        ]);

        //支付类型菜单
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule'      => 'paymentmethod/index',
                'name'      => '支付类型管理',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentmethod/create',
                'name'      => '添加支付类型',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentmethod/edit',
                'name'      => '编辑支付类型',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentmethod/setstatus',
                'name'      => '设置支付类型状态',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentmethod/delrecord',
                'name'      => '删除支付类型',
            ],
        ]);

        //支付渠道菜单
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule'      => 'paymentcategory/index',
                'name'      => '支付渠道管理',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentcategory/create',
                'name'      => '添加支付渠道',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentcategory/edit',
                'name'      => '编辑支付渠道',
            ],
        ]);

        //支付通道菜单
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule'      => 'paymentchannel/index',
                'name'      => '支付通道管理',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentchannel/create',
                'name'      => '添加支付通道',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentchannel/edit',
                'name'      => '编辑支付通道',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentchannel/refreshserver',
                'name'      => '同步到服务器',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentchannel/setstatus',
                'name'      => '设置支付通道状态',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'paymentchannel/delrecord',
                'name'      => '删除支付通道',
            ],
        ]);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('payment_channel');
        }
    }
}
