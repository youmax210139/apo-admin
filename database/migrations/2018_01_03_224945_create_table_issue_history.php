<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIssueHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('lottery_id')->comment('彩种 ID');
            $table->string('issue', 32)->comment('奖期期号');
            $table->string('code', 64)->default("")->comment('开奖号码');
            $table->date('belong_date')->nullable()->comment('奖期所有日期');
            $table->text('miss_data')->default('')->comment('每期的号码遗漏数据(定位)');
            $table->text('total_miss')->default('')->comment('每期的号码遗漏数据(不定位，包含号码出现次数))');
            $table->text('series')->default('')->comment('连续出现值');
            $table->text('total_series')->default('')->comment('不分位最大连出');

            $table->index(['lottery_id', 'belong_date']);
            $table->index(['lottery_id', 'issue']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('issue_history');
        }
    }
}
