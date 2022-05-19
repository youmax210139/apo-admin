<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSecurityQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('security_questions', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('question', 64)->comment('问题');
        });
         $this->data();
    }
    private function data()
    {
         DB::table('security_questions')->insert([
                [
                        'question'       => '您母亲的姓名是？',

                ],
                [
                        'question'       => '您配偶的生日是？',

                ],
                [
                        'question'       => '您的学号（或工号）是？',

                ],
                [
                        'question'       => '您母亲的生日是？？',

                ],
                [
                        'question'       => '您高中班主任的名字是？？',

                ],
                [
                        'question'       => '您父亲的姓名是？',

                ],
                [
                        'question'       => '您小学班主任的名字是？',

                ],
                [
                        'question'       => '您父亲的生日是？',

                ],
                [
                        'question'       => '您配偶的姓名是？',

                ],
                [
                        'question'       => '您初中班主任的名字是？',

                ],
                [
                        'question'       => '您最熟悉的童年好友名字是？',

                ],
                [
                        'question'       => '您最熟悉的学校宿舍室友名字是？',

                ],
                [
                        'question'       => '对您影响最大的人名字是？',

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
            Schema::dropIfExists('security_questions');
        }
    }
}
