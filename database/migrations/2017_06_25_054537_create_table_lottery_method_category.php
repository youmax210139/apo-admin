<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLotteryMethodCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_method_category', function (Blueprint $table) {
            $table->smallInteger('id');
            $table->smallInteger('parent_id')->comment('父级 ID');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 32)->comment('中文名称');
            $table->smallInteger('drop_point')->default(0)->comment('下降点数');

            $table->primary('id');
            $table->index(['ident', 'id']);
        });
        $this->data();
    }
    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '彩种管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            return;
        }

        DB::table('admin_role_permissions')->insert([
                [
                        'parent_id' => $row->id,
                        'rule' => 'lotterymethodcategory/index',
                        'name' => '玩法分类',
                ],
                [
                        'parent_id' => $row->id,
                        'rule' => 'lotterymethodcategory/create',
                        'name' => '添加玩法分类',
                ],
                [
                        'parent_id' => $row->id,
                        'rule' => 'lotterymethodcategory/edit',
                        'name' => '编辑玩法分类',
                ],
                [
                        'parent_id' => $row->id,
                        'rule' => 'lotterymethodcategory/delete',
                        'name' => '删除玩法分类',
                ]

        ]);

        DB::table('lottery_method_category')->insert([
            // 不要改变 ID 和 父 ID 的值
            ['id' => 10, 'parent_id' => 0, 'ident'=>'ssc', 'name'=>'时时彩', 'drop_point' => 0],
            ['id' => 11, 'parent_id' => 10, 'ident'=>'ssc_bz', 'name'=>'时时彩标准模式', 'drop_point' => 0],
            ['id' => 12, 'parent_id' => 10, 'ident'=>'ssc_pk', 'name'=>'时时彩盘口模式', 'drop_point' => 0],
            ['id' => 20, 'parent_id' => 0, 'ident'=>'3d', 'name'=>'3D', 'drop_point' => 40],
            ['id' => 21, 'parent_id' => 20, 'ident'=>'3d_bz', 'name'=>'3D 标准模式', 'drop_point' => 0],
            ['id' => 22, 'parent_id' => 20, 'ident'=>'3d_pk', 'name'=>'3D 盘口模式', 'drop_point' => 0],
            ['id' => 30, 'parent_id' => 0, 'ident'=>'11x5', 'name'=>'11 选 5', 'drop_point' => 20],
            ['id' => 31, 'parent_id' => 30, 'ident'=>'11x5_bz', 'name'=>'11 选 5 标准模式', 'drop_point' => 0],
            ['id' => 32, 'parent_id' => 30, 'ident'=>'11x5_pk', 'name'=>'11 选 5 盘口模式', 'drop_point' => 0],
            ['id' => 40, 'parent_id' => 0, 'ident'=>'kl8', 'name'=>'快乐 8', 'drop_point' => 360],
            ['id' => 41, 'parent_id' => 40, 'ident'=>'kl8_bz', 'name'=>'快乐 8 标准模式', 'drop_point' => 0],
            ['id' => 42, 'parent_id' => 40, 'ident'=>'kl8_pk', 'name'=>'快乐 8 盘口模式', 'drop_point' => 0],
            ['id' => 50, 'parent_id' => 0, 'ident'=>'k3', 'name'=>'快 3', 'drop_point' => 30],
            ['id' => 51, 'parent_id' => 50, 'ident'=>'k3_bz', 'name'=>'快 3 标准模式', 'drop_point' => 0],
            ['id' => 52, 'parent_id' => 50, 'ident'=>'k3_pk', 'name'=>'快 3 盘口模式', 'drop_point' => 0],
            ['id' => 60, 'parent_id' => 0, 'ident'=>'lhc', 'name'=>'六合彩', 'drop_point' => 11],
            ['id' => 61, 'parent_id' => 60, 'ident'=>'lhc_bz', 'name'=>'六合彩标准模式', 'drop_point' => 0],
            ['id' => 62, 'parent_id' => 60, 'ident'=>'lhc_pk', 'name'=>'六合彩盘口模式', 'drop_point' => 0],
            ['id' => 70, 'parent_id' => 0, 'ident'=>'pk10', 'name'=>'PK10', 'drop_point' => 0],
            ['id' => 71, 'parent_id' => 70, 'ident'=>'pk10_bz', 'name'=>'PK10 标准模式', 'drop_point' => 0],
            ['id' => 72, 'parent_id' => 70, 'ident'=>'pk10_pk', 'name'=>'PK10 盘口模式', 'drop_point' => 0],
            ['id' => 80, 'parent_id' => 0, 'ident'=>'kls', 'name'=>'快乐10分', 'drop_point' => 40],
            ['id' => 81, 'parent_id' => 80, 'ident'=>'kls_bz', 'name'=>'快乐10分 标准模式', 'drop_point' => 0],
            ['id' => 82, 'parent_id' => 80, 'ident'=>'kls_pk', 'name'=>'快乐10分 盘口模式', 'drop_point' => 0],
            ['id' => 90, 'parent_id' => 0, 'ident'=>'pcdd', 'name'=>'PC蛋蛋', 'drop_point' => 60],
            ['id' => 91, 'parent_id' => 90, 'ident'=>'pcdd_bz', 'name'=>'PC蛋蛋 标准模式', 'drop_point' => 0],
            ['id' => 92, 'parent_id' => 90, 'ident'=>'pcdd_pk', 'name'=>'PC蛋蛋 盘口模式', 'drop_point' => 0]
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
            Schema::dropIfExists('lottery_method_category');
        }
    }
}
