<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportDailyWage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_daily_wage', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->integer('user_id')->comment('用户 ID');
            $table->decimal('amount', 14, 4)->comment('工资');

            $table->timestamp('created_at')->comment('哪一天的日工资');

            $table->unique(['user_id', 'created_at']);
            $table->index(['user_id', 'created_at', 'amount']);
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
            Schema::dropIfExists('report_daily_wage');
        }
    }
}
