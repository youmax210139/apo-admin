<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_type', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('用户类型 ID');
            $table->string('name', 16)->unique()->comment('用户类型名称');
        });
        $this->data();
    }

    private function data()
    {
        DB::table('user_type')->insert([
            ['name'=>'总代'],
            ['name'=>'代理'],
            ['name'=>'会员'],
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
            Schema::dropIfExists('user_type');
        }
    }
}
