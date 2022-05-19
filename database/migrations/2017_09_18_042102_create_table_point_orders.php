<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePointOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('relate_id')->default(0)->comment('关联的ID');
            $table->tinyInteger('relate_type')->default(0)->comment('0 投注，1 积分兑换，2 管理员操作');
            $table->integer('user_id')->default(0)->comment('用户ID');
            $table->integer('admin_id')->default(0)->comment('管理员ID');
            $table->tinyInteger('order_type')->default(0)->comment('账类型 0增加 1 扣除');
            $table->integer('amount')->default(0)->comment('账变积分');
            $table->integer('points')->default(0)->comment('用户积分账变后');
            $table->string('description')->default('')->comment('备注');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('账变时间');
        });

        DB::statement('ALTER SEQUENCE IF EXISTS "point_orders_id_seq" RESTART WITH 10000000');

        $this->data();
    }

    
    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '账变管理')
        ->where('parent_id', 0)
        ->first();
        
        if (empty($row)) {
            return;
        }
        
        DB::table('admin_role_permissions')->insert([
            'parent_id' => $row->id,
            'icon' => '',
            'rule' => 'pointorders/index',
            'name' => '积分账变列表',
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
            Schema::dropIfExists('point_orders');
        }
    }
}
