<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePrivateReturnJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_return_jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('time_type')->default(0)->comment('时间类型，1日，2小时，3奖期，4实时');
            $table->string('mark')->default('')->comment('特殊标识');
            $table->timestamp('last_time')->comment('结算最后时间');
            $table->timestamps();
            $table->unique(['time_type','mark']);
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
            Schema::dropIfExists('private_return_jobs');
        }
    }
}
