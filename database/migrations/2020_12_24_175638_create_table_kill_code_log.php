<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKillCodeLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kill_code_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('third_ident',32)->comment('第三方平台标识');
            $table->string('third_lottery',32)->comment('第三方彩种标识');
            $table->string('third_issue',64)->comment('第三方奖期');
            $table->smallInteger('third_serial')->default(1)->comment('第三方申请次数');
            $table->string('third_callback',256)->comment('第三方回调地址');
            $table->smallInteger('third_kill_flag_remaining')->default(0)->comment('剩余杀号次数');
            $table->string('third_ip',128)->comment('第三方IP');
            $table->text('third_request')->comment('第三方请求');
            $table->text('extend')->nullable()->comment('扩展');
            $table->string('local_lottery',16)->nullable()->comment('本地彩种标识');
            $table->string('local_issue',64)->nullable()->comment('本地奖期');
            $table->timestamp('local_issue_sale_end',64)->nullable()->comment('本地奖期销售截止时间');

            $table->decimal('all_bet_sum',14,4)->default(0)->comment('平台注单总计');
            $table->integer('all_bet_count')->default(0)->comment('平台注单数量');

            $table->tinyInteger('flag_switch')->default(0)->comment('点杀开关0关1开');
            $table->string('flag_users',128)->nullable()->comment('点杀用户ID');
            $table->decimal('flag_bet_sum',14,4)->default(0)->comment('点杀注单总计');
            $table->integer('flag_bet_count')->default()->comment('点杀注单数量');

            $table->tinyInteger('mode')->default(0)->unsigned()->comment('计算方式，0为web直接计算，1为cli');
            $table->tinyInteger('error')->default(0)->unsigned()->comment('是否报错');
            $table->tinyInteger('step')->default(0)->unsigned()->comment('当前步骤');
            $table->text('message')->nullable()->comment('消息');
            $table->text('code_map')->nullable()->comment('杀号JSON');

            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('calculated_at')->nullable()->comment('计算完成时间');
            $table->timestamp('posted_at')->nullable()->comment('推送完成时间');

            $table->index(['third_ident','third_lottery','third_issue']);//初始化专用
            $table->index(['third_ident','third_lottery','mode','step','error','created_at']);//队列专用
        });

        $this->data();
    }

    /***
     * 插入路由数据
     */
    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('rule','draw')
            ->where('parent_id', 0)
            ->first();
        if(!$row){
            return false;
        }
        $id = $row->id;

        $row = DB::table('admin_role_permissions')->where('rule','killcodelog/index')
            ->where('parent_id', $id)
            ->first();
        if(!$row){
            DB::table('admin_role_permissions')->insert([
                [
                    'parent_id' => $id,
                    'rule' => 'killcodelog/index',
                    'name' => '杀号日志查询',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'killcodelog/detail',
                    'name' => '杀号日志明细',
                ],
                [
                    'parent_id' => $id,
                    'rule' => 'killcodelog/clear',
                    'name' => '杀号日志清理',
                ],
            ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('kill_code_log');
        }
    }
}
