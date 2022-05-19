<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNotices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('subject', 64)->unique()->comment('标题');
            $table->text('content')->comment('内容');
            $table->smallInteger('created_admin_user_id')->default(0)->comment('创造管理ID');
            $table->smallInteger('verified_admin_user_id')->default(0)->comment('审核管理ID');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->boolean('is_show')->default(false)->comment('是否显示');
            $table->boolean('is_top')->default(false)->comment('是否置顶');
            $table->boolean('is_alert')->default(false)->comment('是否弹出提示');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->timestamp('published_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('发布时间');
            $table->timestamp('verified_at')->nullable()->comment('审核时间');
            $table->timestamp('end_at')->nullable()->comment('结束时间');

            $table->index(['is_show', 'sort']);
        });
        $this->data();
    }

    private function data()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon' => 'fa-bullhorn',
            'rule' => 'notice',
            'name' => '公告管理',
        ]);

        DB::table('admin_role_permissions')->insert([
            'parent_id' => $id,
            'icon' => '',
            'rule' => 'notice/index',
            'name' => '公告列表',
        ]);
        DB::table('admin_role_permissions')->insert([
            'parent_id' => $id,
            'icon' => '',
            'rule' => 'notice/create',
            'name' => '添加公告',
        ]);
        DB::table('admin_role_permissions')->insert([
            'parent_id' => $id,
            'icon' => '',
            'rule' => 'notice/edit',
            'name' => '编辑公告',
        ]);
        DB::table('admin_role_permissions')->insert([
            'parent_id' => $id,
            'icon' => '',
            'rule' => 'notice/show',
            'name' => '显示或隐藏公告',
        ]);
        DB::table('admin_role_permissions')->insert([
            'parent_id' => $id,
            'icon' => '',
            'rule' => 'notice/alert',
            'name' => '弹出或取消弹出',
        ]);
        DB::table('admin_role_permissions')->insert([
            'parent_id' => $id,
            'icon' => '',
            'rule' => 'notice/verify',
            'name' => '审核公告',
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
            Schema::dropIfExists('notices');
        }
    }
}
