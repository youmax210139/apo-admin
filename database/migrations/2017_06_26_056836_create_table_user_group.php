<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserGroup extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_group', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('用户组 ID');
            $table->string('name', 16)->unique()->comment('用户组名称');
        });
        $this->data();
    }

    private function data()
    {
        DB::table('user_group')->insert([
            [
                'name' => '正式组'
            ],
            [
                'name' => '测试组'
            ],
            [
                'name' => '试玩组'
            ]
        ]);

        $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon' => 'fa-user',
                'rule' => 'user',
                'name' => '用户管理',
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'usergroup/index',
                'name' => '用户组管理'
            ],
            [
                'parent_id' => $id,
                'rule' => 'usergroup/create',
                'name' => '添加用户组'
            ],
            [
                'parent_id' => $id,
                'rule' => 'usergroup/edit',
                'name' => '修改用户组'
            ],
            [
                'parent_id' => $id,
                'rule' => 'usergroup/delete',
                'name' => '删除用户组'
            ]
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
            Schema::dropIfExists('user_group');
        }
    }
}
