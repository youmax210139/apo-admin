<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLotteryIntroduce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_introduce', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('subject', 64)->comment('标题');
            $table->text('content')->comment('内容');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->smallInteger('lottery_id')->default(0)->comment('彩种ID');
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
                'rule' => 'lotteryintroduce/index',
                'name' => '彩种介绍',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotteryintroduce/create',
                'name' => '添加彩种介绍',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotteryintroduce/edit',
                'name' => '编辑彩种介绍',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotteryintroduce/delete',
                'name' => '删除彩种介绍',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotteryintroduce/status',
                'name' => '启用或禁用彩种介绍',
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
        Schema::dropIfExists('lottery_introduce');
    }
}
