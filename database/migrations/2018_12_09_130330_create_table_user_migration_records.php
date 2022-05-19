<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserMigrationRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_migration_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 20)->comment('用户ID');
            $table->string('old_parent_username', 20)->comment('旧的父级');
            $table->string('new_parent_username', 20)->comment('新的父级');
            $table->string('admin_username', 20)->comment('操作管理员');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
        });

        $this->__permissions();
    }

    private function __permissions()
    {
        $row = DB::table('admin_role_permissions')->where('name', '用户管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon' => 'fa-user',
                'rule' => 'user',
                'name' => '用户管理',
            ]);
        } else {
            $id = $row->id;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'usermigration/index',
                'name' => '用户转移',
            ],
            [
                'parent_id' => $id,
                'rule' => 'usermigration/migrate',
                'name' => '转移操作',
            ],
            [
                'parent_id' => $id,
                'rule' => 'usermigration/records',
                'name' => '转移记录',
            ],
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'userquery/index',
                'name' => '账号反查',
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
        //
        if (app()->environment() != 'production') {
            Schema::dropIfExists('user_migration_records');
        }
    }
}
