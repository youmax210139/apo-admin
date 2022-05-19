<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectsFee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects_fee', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->integer('project_id')->default(0)->comment('订单ID');
            $table->integer('task_id')->default(0)->comment('追号单ID');
            $table->decimal('rate', 4, 4)->default(0)->comment('费率');
            $table->decimal('amount', 15, 4)->default(0)->comment('金额');
            $table->tinyInteger('status')->default(0)->comment('状态0正常 1撤单返还');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');

            $table->index(['user_id']);
            $table->index(['project_id']);
            $table->index(['task_id']);
        });
        $this->_data();
    }

    public function _data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '报表管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon' => 'fa-area-chart',
                'rule' => 'report',
                'name' => '报表管理',
            ]);
        } else {
            $id = $row->id;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'projectsfeereport/index',
                'name' => '服务费报表',
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
            Schema::dropIfExists('projects_fee');
        }

    }
}
