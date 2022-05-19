<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportActivityTotal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_activity_total', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->integer('user_id')->comment('用户 ID');
            $table->decimal('bonus', 14, 4)->comment('活动奖金');

            $table->timestamp('created_at')->comment('写入时间');

            $table->unique(['user_id', 'created_at']);
            $table->index(['user_id', 'created_at', 'bonus']);
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
            Schema::dropIfExists('report_activity_total');
        }
    }
}
