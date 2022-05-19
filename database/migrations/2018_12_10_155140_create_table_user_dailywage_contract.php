<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserDailywageContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_dailywage_contract', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->unsignedTinyInteger('type')->default(0)->comment('工资类型');
            $table->decimal('top_rate', 15, 4)->default(0)->comment('最高比例');
            $table->jsonb('content')->default('[]')->comment('契约');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态，0-生效中,1-已失效');
            $table->tinyInteger('stage')->unsigned()->nullable()->comment('操作位置：1-前台，2-后台');
            $table->string('deleted_username', 20)->nullable()->comment('终结者');
            $table->string('created_username', 20)->comment('创建者');
            $table->timestamp('deleted_at')->nullable()->comment('失效时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'type','status']);
        });
        $this->__permissions();
    }

    private function __permissions()
    {
        $row = DB::table('admin_role_permissions')->where('name', '用户管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'userdailywagecontract/index',
                'name' => '工资契约列表',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'userdailywagecontract/record',
                'name' => '工资契约历史',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'userdailywagecontract/edit',
                'name' => '工资契约编辑',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'userdailywagecontract/delete',
                'name' => '工资契约删除',
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
            Schema::dropIfExists('user_dailywage_contract');
        }
    }
}
