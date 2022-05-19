<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWageJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wage_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->unique()->comment('工资类型');
            $table->timestamp('last_calculate_time')->default(DB::raw('LOCALTIMESTAMP'))->comment('上次计算时间');
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
            Schema::dropIfExists('wage_jobs');
        }
    }
}
