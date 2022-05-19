<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMethodAnalyse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('method_analyse', function (Blueprint $table) {
            $table->date('belong_date')->comment('所属日期');
            $table->smallInteger('lottery_id')->comment('彩种 ID');
            $table->integer('lottery_method_id')->comment('玩法 ID');
            $table->integer('user_id')->comment('用户 ID');
            $table->integer('bet_count')->comment('投注单数');
            $table->integer('win_count')->comment('中奖单数');
            $table->decimal('price', 14, 4)->comment('投注额');
            $table->decimal('bonus', 14, 4)->comment('奖金');
            $table->decimal('rebate', 14, 4)->comment('返点');

            $table->unique(['belong_date', 'lottery_id', 'lottery_method_id', 'user_id']);
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

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'methodanalyse/index',
                'name' => '彩票玩法分析',
            ]]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('method_analyse');
        }
    }
}
