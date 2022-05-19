<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSubscribe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribe', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('订阅 ID');
            $table->string('email', 64)->unique()->comment('邮箱');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
        });

        $this->data();
    }

    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '官网信息')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }


        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'subscribe/index',
                'name' => '订阅列表',
            ],
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
            Schema::dropIfExists('subscribe');
        }
    }
}
