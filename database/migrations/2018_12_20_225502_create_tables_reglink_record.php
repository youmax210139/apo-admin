<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablesReglinkRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reglink_record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->smallInteger('user_type_id')->comment('用户类型 ID');
            $table->string('domain', 64)->comment('域名地址');
            $table->string('code', 32)->comment('注册码');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
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
            Schema::dropIfExists('reglink_record');
        }
    }
}
