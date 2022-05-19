<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserPrizelevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_prize_level', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unique()->comment('总代 ID')->unsigned();
            $table->integer('level')->default(0)->comment('奖级，如1700，1800')->unsigned();
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
            Schema::dropIfExists('user_prize_level');
        }
    }
}
