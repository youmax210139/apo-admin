<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->comment('用户 ID')->unsigned();
            $table->string('code', 128)->default('')->comment('开奖号码秒秒彩专用');
            $table->decimal('process_time', 14, 4)->default(0)->comment('整个订单执行时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');

            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
        });

        DB::statement('ALTER TABLE package SET (autovacuum_vacuum_scale_factor = 0.008)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('package');
        }
    }
}
