<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserDailywageLine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_dailywage_line', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('工资线ID');
            $table->integer('top_user_id')->unsigned()->comment('总代用户ID');
            $table->string('name', 15)->default(0)->comment('线名');
            $table->tinyInteger('type')->default(1)->comment('工资类型 1：日工资 2：实时工资 3:小时工资-中挂单,4-小时工资-普通,5日工资-中挂单,6实时工资-中挂单,7奖期亏损工资,8日工资-盈亏');
            $table->jsonb('content')->default('{}')->comment('线配置');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('修改时间');
            $table->unique(['top_user_id','type']);
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
                'rule' => 'userdailywagecontract/line',
                'name' => '工资线修改',
            ],[
                'parent_id' => $row->id,
                'rule' => 'userdailywagecontract/line/delete',
                'name' => '工资线清除',
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
            Schema::dropIfExists('user_dailywage_line');
        }
    }
}
