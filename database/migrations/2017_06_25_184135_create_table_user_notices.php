<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserNotices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('user_notices', function (Blueprint $table) {
            $table->integer('user_id')->default(0)->comment('内容');
            $table->smallInteger('notice_id')->default(0)->comment('公告ID');
            $table->unique(['user_id', 'notice_id']);
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
            Schema::dropIfExists('user_notices');
        }
    }
}
