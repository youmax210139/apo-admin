<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportDailyThirdGame extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_daily_third_game', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('数据日期');
            $table->integer('platform_id')->comment('所属游戏平台ID');
            $table->jsonb('data')->comment('数据详情');
            $table->timestamp('updated_at')->comment('更新时间');

            $table->unique(['date', 'platform_id']);
        });
        $this->data();
    }

    /**
     * 插入数据
     */
    public function data()
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
                'rule' => 'thirdgamedaily/index',
                'name' => '三方每日报表',
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
            Schema::dropIfExists('report_daily_third_game');
        }
    }
}
