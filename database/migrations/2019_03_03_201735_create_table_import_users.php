<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableImportUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_users', function (Blueprint $table) {
            $table->integer('user_id')->comment('用户ID');
            $table->string('platform', 24)->comment('在第三方平台名称');
            $table->string('password', 128)->default('')->comment('登录密码');
            $table->string('security_password', 128)->default('')->comment('安全密码');
            $table->timestamp('created_at')->comment('创建时间');
            
            $table->index('platform');
            $table->primary('user_id');
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
            Schema::dropIfExists('import_users');
        }
    }
}
