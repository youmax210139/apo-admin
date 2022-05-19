<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('用户 ID');
            $table->string('usernick', 20)->unique()->comment('管理员昵称');
            $table->string('username', 20)->unique()->comment('管理员帐号');
            $table->string('password', 128)->comment('管理员密码');
            $table->boolean('is_locked')->default(0)->comment('是否锁定');
            $table->string('remember_token', 64)->default('')->comment('记住我');
            $table->ipAddress('last_ip')->nullable()->comment('最后一次登录IP');
            $table->timestamp('last_time')->nullable()->comment('最后登录时间');
            $table->string('last_session', 64)->default('')->comment('最近登陆SESSIONID');
            $table->string('google_key', 16)->default('')->comment('谷歌登录器秘钥');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('修改时间');
        });

        $this->data();
    }

    private function data()
    {
        DB::table('admin_users')->insert([
            'usernick' => 'apollo',
            'username' => 'apollo',
            'password' =>  bcrypt('admin123'),
            'remember_token' => Str::random(10),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('admin_users');
        }
    }
}
