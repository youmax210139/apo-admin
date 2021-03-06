<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserRequestLog extends Migration
{
    /**
     * 活动
     */
    public function up()
    {
        //Schema::connection(app()->isLocal() ? null : 'log')
        Schema::create('user_request_log', function (Blueprint $table) {
                $table->BigIncrements('id');
                $table->string('username', 20)->comment('用户名');
                $table->string('path', 64)->default('')->comment("访问路径");
                $table->text('request')->default('')->comment("REQUEST");
                $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('访问时间');

                $table->index(['created_at', 'path', 'username']);
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
            Schema::dropIfExists('user_request_log');
        }
    }
}
