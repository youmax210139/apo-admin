<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePumpJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pump_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('lottery_id')->comment('彩种 ID');
            $table->string('issue', 64)->nullable()->comment('本地奖期');
            $table->timestamp('sale_end')->comment('本期平台销售截至时间');
            $table->timestamps();
            $table->unique(['lottery_id']);
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
            Schema::dropIfExists('pump_jobs');
        }
    }
}
