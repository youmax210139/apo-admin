<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjectsExpandcode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects_expandcode', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('流水号');
            $table->bigInteger('project_id')->comment('注单ID')->unsigned();
            $table->smallInteger('level')->default(1)->comment('多奖级中的奖级')->unsigned();
            $table->decimal('prize', 15, 5)->comment('总价');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');

            $table->index('project_id');
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
            Schema::dropIfExists('projects_expandcode');
        }
    }
}
