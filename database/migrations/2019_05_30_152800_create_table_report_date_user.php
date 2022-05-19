<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportDateUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_date_user', function (Blueprint $table) {

            $table->date('date')->comment('哪一天的数据');
            $table->jsonb('data')->default('{}')->comment('统计数据');

            $table->unique('date');
        });
        $this->data();
    }

    private function data()
    {
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('report_date_user');
        }
    }
}
