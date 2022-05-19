<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('角色 ID');
            $table->string('name', 64)->unique()->comment('角色名称');
            $table->string('description')->comment('备注');
        });

        $this->data();
    }

    private function data()
    {
        DB::table('admin_roles')->insert([
            [
                'name'=>'平台管理员',
                'description'=>'平台管理员'
            ],
            [
                'name'=>'运营人员',
                'description'=>'运营人员'
            ],
            [
                'name'=>'风控人员',
                'description'=>'风控人员'
            ],
            [
                'name'=>'财务人员',
                'description'=>'财务人员'
            ],
            [
                'name'=>'客服人员',
                'description'=>'客服人员'
            ],
            [
                'name'=>'产品人员',
                'description'=>'产品人员'
            ],
            [
                'name'=>'技术主管',
                'description'=>'技术主管'
            ],
            [
                'name'=>'技术人员',
                'description'=>'技术人员'
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
            Schema::dropIfExists('admin_roles');
        }
    }
}
