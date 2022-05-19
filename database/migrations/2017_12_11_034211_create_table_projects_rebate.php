<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectsRebate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects_rebate', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('project_id')->comment('Project表 ID');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->decimal('amount', 14, 4)->comment('返点金额');
            $table->decimal('diff_rebate', 4, 4)->comment('返点差');
            $table->smallInteger('status')->default('0')->comment('返点状态(0:未返;1:已返;2:已撤)');
            $table->smallInteger('cancel_status')->default('0')->comment('撤单状态(0:未撤;1:用户撤单;2:中奖后撤单;3:公司撤单)');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('更新时间');

            $table->index(['project_id', 'user_id', 'status', 'amount']);
        });

        DB::statement('ALTER TABLE projects_rebate SET (autovacuum_vacuum_scale_factor = 0.01)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('projects_rebate');
        }
    }
}
