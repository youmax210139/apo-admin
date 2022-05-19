<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportDailyLotteryBonus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_daily_lottery_bonus', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('数据日期')->unique();
            $table->jsonb('data')->comment('数据详情');
            $table->timestamps();
        });
        $this->data();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('report_daily_lottery_bonus');
        }
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
                                                            'rule'      => 'lotterybonusdaily/index',
                                                            'name'      => '平台每日报表',
                                                        ]]);
    }
}
