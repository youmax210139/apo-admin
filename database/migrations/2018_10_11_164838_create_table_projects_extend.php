<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectsExtend extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects_extend', function (Blueprint $table) {
            $table->bigInteger('project_id')->comment('投注单 ID')->unsigned();
            $table->bigInteger('share_project_id')->default(0)->comment('跟单主单 ID')->unsigned();
            $table->integer('share_user_id')->default(0)->comment('跟单主单用户 ID')->unsigned();
            $table->decimal('share_rate', 4, 4)->default(0)->comment('佣金费率')->unsigned();
            $table->integer('follow_num')->default(0)->comment('跟单人数')->unsigned();

            $table->primary('project_id');

            $table->index('share_user_id');
            $table->index('share_project_id');
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
            Schema::dropIfExists('projects_extend');
        }
    }
}
