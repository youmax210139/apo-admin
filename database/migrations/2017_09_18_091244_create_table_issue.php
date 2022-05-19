<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIssue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('奖期 ID');
            $table->smallInteger('lottery_id')->comment('彩种 ID');
            $table->string('code', 64)->default("")->comment('开奖号码');
            $table->string('issue', 32)->comment('奖期期号');
            $table->date('belong_date')->comment('属于哪天的奖期，与跨不跨天无关');
            $table->timestamp('sale_start')->comment('本期平台销售开始时间');
            $table->timestamp('sale_end')->comment('本期平台销售截至时间');
            $table->timestamp('cancel_deadline')->comment('本期平台停止撤单时间');
            $table->timestamp('earliest_write_time')->comment('最早录号时间');
            $table->timestamp('write_time')->nullable()->comment('实际录号时间');
            $table->string('write_admin')->default('')->comment('录号管理员ID');
            $table->timestamp('verify_time')->nullable()->comment('开奖号码验证时间');
            $table->string('verify_admin')->default('')->comment('验证管理员ID');
            $table->smallInteger('rank')->default(0)->comment('权重');
            $table->tinyInteger('fetch_status')->default(0)->comment('0:未抓号; 1:进行中;2:已完成');
            $table->tinyInteger('code_status')->default(0)->comment('开奖奖期状态 0:未写入;1:写入待验证;2:已验证;3:官方未开奖');
            $table->tinyInteger('deduct_status')->default(0)->comment('扣款状态(0:未完成;1:进行中;2:已经完成)');
            $table->tinyInteger('rebate_status')->default(0)->comment('返点状态(0:未开始;1:进行中;2:已完成)');
            $table->tinyInteger('check_bonus_status')->default(0)->comment('检查中奖状态(0:未开始;1:进行中;2:已经完成)');
            $table->tinyInteger('bonus_status')->default(0)->comment('返奖状态(0:未开始;1:进行中;2:已经完成)');
            $table->tinyInteger('task_to_project_status')->default(0)->comment('追号单转注单状态(0:未开始;1:进行中;2:已经完成)');
            $table->tinyInteger('report_status')->default(0)->comment('报表状态(-1:需要清理旧数据;0:未开始;1-4:进行中;5:已经完成)');
            $table->tinyInteger('lock_status')->default(0)->comment('锁封表是否生成的状态0:未生成;1:已生成');

            $table->unique(['lottery_id', 'issue']);
            $table->index(['lottery_id', 'earliest_write_time']);
            $table->index(['lottery_id', 'sale_start', 'sale_end']);
            $table->index(['lottery_id', 'sale_end']);
            $table->index(['lottery_id', 'code_status', 'sale_end']);
            $table->index(['lottery_id', 'check_bonus_status', 'sale_end']);
            $table->index(['lottery_id', 'bonus_status', 'sale_end']);
            $table->index(['lottery_id', 'deduct_status', 'sale_end']);
            $table->index(['lottery_id', 'rebate_status', 'sale_end']);
            $table->index(['lottery_id', 'task_to_project_status', 'sale_start']);
            $table->index(['lottery_id', 'report_status', 'sale_end']);
        });

        $this->data();
    }
    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '彩种管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            return;
        }

        DB::table('admin_role_permissions')->insert([
                [
                        'parent_id' => $row->id,
                        'rule' => 'lottery/issue',
                        'name' => '奖期列表',
                ],
                [
                        'parent_id' => $row->id,
                        'rule' => 'lottery/issuecreate',
                        'name' => '添加奖期',
                ],
                [
                        'parent_id' => $row->id,
                        'rule' => 'lottery/issuedelete/delete',
                        'name' => '删除奖期',
                ],
                [
                    'parent_id' => $row->id,
                    'rule' => 'lottery/editcode',
                    'name' => '自开彩种修改号码',
                ],
                [
                    'parent_id' => $row->id,
                    'rule' => 'lottery/resetissuestatus',
                    'name' => '重置奖期状态',
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
            Schema::dropIfExists('issue');
        }
    }
}
