<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableImportUserQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_user_question', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('用户ID');
            $table->integer('security_questions_id')->comment('密码问题ID');
            $table->string('answer', 128)->default('')->comment('密码问题答案');
            $table->timestamp('created_at')->comment('创建时间');
            
            $table->index('user_id');
            $table->index('security_questions_id');
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
            Schema::dropIfExists('import_user_question');
        }
    }
}
