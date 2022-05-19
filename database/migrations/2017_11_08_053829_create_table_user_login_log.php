<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserLoginLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->comment('用户 ID');
            $table->string('domain', 32)->defaulte('')->comment("请求域名");
            $table->string('province', 8)->defaulte('')->comment("来源地址");
            $table->string('browser', 32)->defaulte('')->comment("浏览器");
            $table->string('browser_version', 32)->defaulte('')->comment("浏览器版本");
            $table->string('os', 32)->defaulte('')->comment("操作系统");
            $table->string('device', 32)->defaulte('')->comment("设备类型");
            $table->ipAddress('ip')->default('0.0.0.0')->comment("用户 IP 地址");
            $table->json('request')->nullable()->comment("REQUEST 数据");
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');

            $table->index('ip');
            $table->index(['created_at', 'user_id']);
            $table->index(['created_at', 'ip']);
            $table->index(['created_at', 'province']);
            $table->index(['created_at', 'domain']);
            $table->index(['created_at', 'browser']);
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
            Schema::dropIfExists('user_login_log');
        }
    }
}
