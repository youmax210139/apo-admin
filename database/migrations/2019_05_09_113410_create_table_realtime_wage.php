<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRealtimeWage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('realtime_wage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned()->comment('注单ID');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->decimal('amount', 15, 4)->default(0)->comment('总计派发金额');
            $table->timestamp('send_date')->nullable()->comment('发放时间');
            $table->tinyInteger('status')->default(0)->comment('0-待确认,1-待发放,2-已发放');
            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->string('remark')->default('')->comment('备注');

            $table->unique(['user_id', 'project_id']);
            $table->index(['project_id', 'status']);
            $table->index(['status', 'report_status']);
            $table->index(['created_at', 'user_id']);
        });

        $this->_data();
    }

    public function _data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '报表管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'dailywagereport/realtime',
                'name' => '实时工资报表',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'dailywagereport/hourly',
                'name' => '小时工资报表',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'dailywagereport/float',
                'name' => '浮动工资报表',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'dailywagereport/issue',
                'name' => '奖期工资报表',
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
        //
        if (app()->environment() != 'production') {
            Schema::dropIfExists('realtime_wage');
        }
    }
}
