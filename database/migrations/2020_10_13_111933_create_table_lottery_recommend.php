<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLotteryRecommend extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_recommend', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('lottery_id')->unique()->comment('彩种ID');
            $table->jsonb('data')->default('[]')->comment('推荐彩种数据');
            $table->string('tip', 200)->default('')->comment('提示文案');
            $table->integer('interval_minutes')->default(0)->comment('重复弹出间隔时间(分)');
            $table->tinyInteger('status')->default(0)->comment('状态:0禁用,1启用');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
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
                'rule' => 'lotteryrecommend/index',
                'name' => '推荐彩种',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotteryrecommend/create',
                'name' => '添加推荐彩种',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotteryrecommend/edit',
                'name' => '编辑推荐彩种',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotteryrecommend/delete',
                'name' => '删除推荐彩种',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotteryrecommend/status',
                'name' => '启用或禁用推荐彩种',
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
            Schema::dropIfExists('lottery_recommend');
        }
    }
}
