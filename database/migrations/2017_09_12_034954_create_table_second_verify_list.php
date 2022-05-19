<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSecondVerifyList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('second_verify_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->comment('用户 ID');
            $table->integer('created_admin_id')->comment('添加记录管理员 ID');
            $table->integer('verify_admin_id')->default(0)->comment('审核管理员 ID');
            $table->string('verify_type')->comment('操作数据 changepass recharge deduct activity ');
            $table->string('verify_at')->default('')->comment("审核时间");
            $table->jsonb('data')->comment('操作数据');
            $table->tinyInteger('status')->default(0)->comment('审核状态 0 未审核 1 审核通过 2 审核不通过');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['status', 'verify_type']);
        });
        $this->data();
    }
    private function data()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
                        'parent_id' => 0,
                        'icon' => 'fa-balance-scale',
                        'rule' => 'verify',
                        'name' => '审核管理',
        ]);

        DB::table('admin_role_permissions')->insert([
                [
                        'parent_id' => $id,
                        'rule' => 'verifyrecharge/index',
                        'name' => '人工充值审核',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'verifyrecharge/verify',
                        'name' => '人工充值审核（执行）',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'verifydeduction/index',
                        'name' => '人工扣款审核',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'verifydeduction/verify',
                    'name' => '人工扣款审核（执行）',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'verifyactivity/index',
                    'name' => '活动礼金发放审核',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'verifyactivity/verify',
                    'name' => '活动礼金发放审核（执行）',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'verifychangepwd/index',
                        'name' => '密码修改审核',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'verifychangepwd/verify',
                    'name' => '密码修改审核(执行)',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'risk/index',
                        'name' => '风控提款审核',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'risk/setting',
                        'name' => '风控设置',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'risk/query',
                        'name' => '风控查询',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'risk/detail',
                    'name' => '风控审核详情',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'risk/deal',
                    'name' => '[提现审核]风控认领',
                ]
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
            Schema::dropIfExists('second_verify_list');
        }
    }
}
