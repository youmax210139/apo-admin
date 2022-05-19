<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGuestbook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guestbook', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('留言 ID');
            $table->string('appellation', 32)->default('')->comment('称呼');
            $table->string('account_name', 32)->default('')->comment('银行卡姓名');
            $table->string('app_name', 32)->default('')->comment('通讯软件');
            $table->string('app_account', 32)->default('')->comment('通讯软件帐号');
            $table->string('email', 64)->default('')->comment('邮箱');
            $table->string('title', 32)->default('')->comment('主题');
            $table->string('content', 256)->default('')->comment('内容');
            $table->tinyInteger('status')->default(0)->comment('状态 0待处理 1不需处理 2已处理');
            $table->string('remark', 256)->default('')->comment('备注');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
        });

        $this->data();
    }

    private function data()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon' => 'fa-globe',
            'rule' => 'web',
            'name' => '官网信息',
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'guestbook/index',
                'name' => '留言列表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'guestbook/edit',
                'name' => '处理留言',
            ],
            [
                'parent_id' => $id,
                'rule' => 'guestbook/detail',
                'name' => '查看留言',
            ],
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
            Schema::dropIfExists('guestbook');
        }
    }
}
