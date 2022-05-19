<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLotteryCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_category', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 32)->comment('中文名称');
        });

        $this->data();
    }

    private function data()
    {
        DB::table('lottery_category')->insert([
                [
                        'ident' => 'Digit',
                        'name' => '数字',
                ],
                [
                        'ident' => 'Lotto',
                        'name' => '乐透',
                ],
                [
                        'ident' => 'Keno',
                        'name' => '基诺',
                ],
                [
                        'ident' => 'Other',
                        'name' => '其它',
                ]
        ]);

        $id = DB::table('admin_role_permissions')->insertGetId([
                        'parent_id' => 0,
                        'icon' => 'fa-soccer-ball-o',
                        'rule' => 'lottery',
                        'name' => '彩种管理',
        ]);

        DB::table('admin_role_permissions')->insert([
                [
                        'parent_id' => $id,
                        'rule' => 'lotterycategory/index',
                        'name' => '彩种分类',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'lotterycategory/create',
                        'name' => '添加彩种分类',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'lotterycategory/edit',
                        'name' => '编辑彩种分类',
                ],
                [
                        'parent_id' => $id,
                        'rule' => 'lotterycategory/delete',
                        'name' => '删除彩种分类',
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
            Schema::dropIfExists('lottery_category');
        }
    }
}
