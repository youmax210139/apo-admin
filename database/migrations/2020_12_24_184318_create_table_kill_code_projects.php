<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKillCodeProjects extends Migration
{
    /**
     * 此表无效，可以删除
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kill_code_projects', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->comment('注单 ID');
            $table->integer('user_id')->comment('用户 ID')->unsigned();
            $table->bigInteger('package_id')->comment('销售单 ID')->unsigned();
            $table->bigInteger('task_id')->default(0)->comment('追号单 ID')->unsigned();
            $table->smallInteger('lottery_id')->comment('彩种 ID')->unsigned();
            $table->integer('lottery_method_id')->comment('玩法 ID')->unsigned();
            $table->string('issue', 32)->comment('奖期');
            $table->jsonb('prize_level')->default('[]')->comment('奖金级别');
            $table->mediumText('code')->comment('投注号码');
            $table->string('code_position', 16)->default('')->comment('任选单式投注位置 个十百千万');
            $table->decimal('total_price', 14, 4)->comment('总价');
            $table->smallInteger('mode')->default(0)->comment('元角分厘模式');
            $table->smallInteger('is_get_prize')->default(0)->comment('开奖状态(0. 未开奖; 1. 已中奖; 2. 未中奖; 3. 特殊玩法开和)');
            $table->smallInteger('share_or_follow_status')->default(0)->comment('跟单状态（0. 正常单; 1. 主单，2，跟单）');
            $table->string('ident', 32)->comment('lottery_method.ident 英文标识');
            $table->string('method_name', 32)->default('')->comment('lottery_method.name 玩法中文名称');
            $table->string('type', 16)->comment('lottery_method_category.ident 玩法频道英文标识');
            $table->string('username', 20)->comment('users.username用户帐号');

            $table->index(['lottery_id', 'lottery_method_id', 'issue', 'is_get_prize']);
        });
    }




    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('kill_code_projects');
        }
    }
}
