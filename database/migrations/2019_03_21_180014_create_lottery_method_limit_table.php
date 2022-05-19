<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotteryMethodLimitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_method_limit', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lottery_id')->default(0)->commet('玩法id');
            $table->integer('lottery_method_id')->default(0)->commet('玩法id');
            $table->decimal('project_min', 10, 4)->default(0)->comment('用户单注最低投注');
            $table->integer('project_max')->default(0)->comment('用户单注最高投注');
            $table->integer('issue_max')->default(0)->comment('用户单期最高投注');
            $table->integer('max_bet_num')->default(0)->comment('最高投注注数，0不限制');
            $table->integer('issue_total_max')->default(0)->comment('全局单期最高投注金额，0不限制');
            $table->unique(['lottery_id','lottery_method_id']);
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
                'rule' => 'lotterymethodlimit/index',
                'name' => '投注限制',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotterymethodlimit/create',
                'name' => '添加投注限制',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotterymethodlimit/edit',
                'name' => '修改投注限制',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'lotterymethodlimit/delete',
                'name' => '删除投注限制',
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
            Schema::dropIfExists('lottery_method_limit');
        }
    }
}
