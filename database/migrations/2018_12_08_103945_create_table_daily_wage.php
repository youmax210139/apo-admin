<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDailyWage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('daily_wage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->decimal('amount', 15, 4)->default(0)->comment('金额');
            $table->decimal('rate', 15, 4)->default(0)->comment('发放比例');
            $table->date('date')->comment('哪一天的日工资');
            $table->timestamp('send_date')->nullable()->comment('发放时间');
            $table->tinyInteger('status')->default(0)->comment('0-待确认,1-待发放,2-已发放，3-已拒绝');
            $table->tinyInteger('report_status')->default(0)->comment('报表汇总状态：0. 未开始; 1. 进行中; 2. 完成');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('deleted_at')->nullable()->comment('软删除删除时间');
            $table->jsonb('remark')->default('{}')->comment('备注');

            $table->unique(['user_id', 'date', 'deleted_at']);
            $table->index(['date', 'status', 'deleted_at']);
            $table->index('deleted_at');
            $table->index(['status', 'report_status']);
        });

        $this->__permissions();
    }

    private function __permissions()
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
                'rule' => 'dailywagereport/index',
                'name' => '日工资报表',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'dailywagereport/delete',
                'name' => '日工资删除',
            ],
            [
            'parent_id' => $row->id,
            'rule' => 'dailywagereport/check',
            'name' => '日工资确认',
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
            Schema::dropIfExists('daily_wage');
        }
    }
}
