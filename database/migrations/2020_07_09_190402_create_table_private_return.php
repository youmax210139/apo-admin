<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTablePrivateReturn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_return', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->integer('contract_id')->unsigned()->comment('契约ID');
            $table->timestamp('start_time')->comment('结算开始时间');
            $table->timestamp('end_time')->comment('结算结束时间');
            $table->smallInteger('lottery_id')->default(0)->comment('彩种 ID');
            $table->string('issue', 32)->default('')->comment('奖期');
            $table->decimal('cardinal', 14, 4)->comment('私返基数');
            $table->decimal('rate', 6, 6)->comment('私返比例');
            $table->decimal('amount', 15, 4)->default(0)->comment('用户派发金额');
            $table->integer('source_user_id')->default(0)->comment('施益用户ID');
            $table->decimal('price', 14, 4)->comment('投注额');
            $table->decimal('bonus', 14, 4)->comment('奖金');
            $table->decimal('rebate', 14, 4)->comment('返点');
            $table->decimal('profit', 14, 4)->comment('利润');
            $table->integer('active')->default(0)->comment('活跃用户数');
            $table->jsonb('remark')->default('{}')->comment('备注');
            $table->tinyInteger('status')->default(0)->comment('0-待确认,1-待发放,2-已发放 3-已取消，4-未达标');
            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 已完成');//预留用于未来统计报表
            $table->timestamp('calculate_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('计算时间');
            $table->timestamp('verified_at')->nullable()->comment('审核时间');
            $table->timestamp('send_at')->nullable()->comment('发放时间');

            $table->index(['status', 'report_status']);//用于生成报表，如果需要的话
            $table->unique(['user_id','contract_id', 'start_time', 'end_time','lottery_id','issue']);
        });
        $this->__permissions();
    }

    private function __permissions()
    {
        //添加菜单报表
        $report_row = DB::table('admin_role_permissions')->where('name', '报表管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($report_row)) {
            return;
        }
        $private_return_row = DB::table('admin_role_permissions')->where('rule','privatereturn/index')->first();
        if($private_return_row){
            return;
        }
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $report_row->id,
                'rule' => 'privatereturn/index',
                'name' => '私返报表',
            ],
            [
                'parent_id' => $report_row->id,
                'rule' => 'privatereturn/detail',
                'name' => '私返明细',
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
            Schema::dropIfExists('private_return');
        }
    }
}
