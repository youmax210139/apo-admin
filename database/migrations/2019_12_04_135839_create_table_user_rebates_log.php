<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserRebatesLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_rebates_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->string('type', 32)->comment('返点类型');
            $table->decimal('old_value', 4, 4)->comment('修改前值');
            $table->decimal('new_value', 4, 4)->comment('修改后值');
            $table->tinyInteger('operator_type')->default(0)->comment('修改者类型：0 网站用户，1 管理员');
            $table->integer('operator_id')->default(0)->comment('修改者用户ID');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->index('user_id');
            $table->index('created_at');
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
            Schema::dropIfExists('user_rebates_log');
        }
    }
}
