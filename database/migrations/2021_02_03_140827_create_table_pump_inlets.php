<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePumpInlets extends Migration
{
    /**
     * 彩票抽水表
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pump_inlets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->integer('project_id')->default(0)->comment('订单ID');
            $table->decimal('cardinal', 14, 4)->default(0)->comment('基数');
            $table->decimal('scale', 6, 6)->comment('比例');
            $table->decimal('amount', 15, 4)->default(0)->comment('抽水金额');
            $table->decimal('outlet_amount', 15, 4)->default(0)->comment('返水金额');
            $table->tinyInteger('status')->default(0)->comment('状态1计算完成2抽水完成3返水完成');
            $table->jsonb('extend')->default('[]')->comment('扩展备注');
            $table->timestamps();
            $table->index(['project_id','status','created_at']);//补发专用
        });
        $this->_permissions();
    }

    private function _permissions()
    {
        $row = DB::table('admin_role_permissions')->where('name', '报表管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'pump/index',
                'name' => '彩票抽返水纪录',
            ], [
                'parent_id' => $row->id,
                'rule' => 'pump/detail',
                'name' => '彩票抽返水明细',
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
            Schema::dropIfExists('pump_inlets');
        }
    }
}
