<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableIssueError extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_error', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('错误奖期 ID');
            $table->smallInteger('lottery_id')->comment('彩种 ID');
            $table->string('issue', 32)->comment('奖期期号');
            $table->tinyInteger('error_type')->comment('错误类型 1:官方提前开奖[撤销派奖+系统撤单]; 2:录入号码错误[撤销派奖+重新判断中奖+重新派奖]; 3:官方未开奖 ');
            $table->timestamp('open_time')->nullable()->comment('提前开奖时间');
            $table->timestamp('write_time')->nullable()->comment('异常登记的时间');
            $table->string('write_admin', 32)->default('')->comment('异常登记的管理员');
            
            $table->string('old_code', 64)->default("")->comment('错误的开奖号码');
            $table->tinyInteger('old_code_status')->default(0)->comment('错误的开奖奖期状态 0:未写入;1:写入待验证;2:已验证;3:官方未开奖');
            $table->tinyInteger('old_deduct_status')->default(0)->comment('错误的扣款状态(0:未完成;1:进行中;2:已经完成)');
            $table->tinyInteger('old_rebate_status')->default(0)->comment('错误的返点状态(0:未开始;1:进行中;2:已完成)');
            $table->tinyInteger('old_checkbonus_status')->default(0)->comment('错误的检查中奖状态(0:未开始;1:进行中;2:已经完成)');
            $table->tinyInteger('old_bonus_status')->default(0)->comment('错误的返奖状态(0:未开始;1:进行中;2:已经完成)');
            $table->tinyInteger('old_tasktoproject_status')->default(0)->comment('错误的追号单转注单状态(0:未开始;1:进行中;2:已经完成)');
            
            $table->string('code', 64)->default("")->comment('新的开奖号码');
            $table->tinyInteger('code_status')->default(0)->comment('新的开奖奖期状态 0:未写入;1:写入待验证;2:已验证;3:官方未开奖');
            $table->tinyInteger('deduct_status')->default(0)->comment('新的扣款状态(0:未完成;1:进行中;2:已经完成)');
            $table->tinyInteger('rebate_status')->default(0)->comment('新的返点状态(0:未开始;1:进行中;2:已完成)');
            $table->tinyInteger('check_bonus_status')->default(0)->comment('新的检查中奖状态(0:未开始;1:进行中;2:已经完成)');
            $table->tinyInteger('bonus_status')->default(0)->comment('新的返奖状态(0:未开始;1:进行中;2:已经完成)');
            $table->tinyInteger('task_to_project_status')->default(0)->comment('新的追号单转注单状态(0:未开始;1:进行中;2:已经完成)');
            
            $table->tinyInteger('cancel_bonus_status')->default(0)->comment('撤销派奖状态 (0:未开始, 1=进行中, 2=已完成, 9=被忽略)');
            $table->tinyInteger('repeal_status')->default(0)->comment('系统撤单状态 (0:未开始, 1=进行中, 2=已完成, 9=被忽略)');

            $table->index(['lottery_id','issue', 'cancel_bonus_status', 'repeal_status']);
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
            Schema::dropIfExists('issue_error');
        }
    }
}
