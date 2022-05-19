<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('游戏帐变ID');
            $table->smallInteger('lottery_id')->default(0)->comment('彩种 ID');
            $table->integer('lottery_method_id')->default(0)->comment('玩法 ID');
            $table->bigInteger('package_id')->default(0)->comment('订单包 ID');
            $table->bigInteger('task_id')->default(0)->comment('追号 ID');
            $table->bigInteger('project_id')->default(0)->comment('注单 ID');
            $table->integer('from_user_id')->default(0)->comment('(发起人)用户 ID');
            $table->integer('to_user_id')->default(0)->comment('(关联人)用户 ID');
            $table->smallInteger('admin_user_id')->default(0)->comment('管理员 ID');
            $table->smallInteger('order_type_id')->comment('帐变类型');
            $table->decimal('amount', 14, 4)->comment('本条账变所产生的资金变化量');
            $table->decimal('pre_balance', 14, 4)->comment('帐变前账户余额');
            $table->decimal('pre_hold_balance', 14, 4)->comment('帐变前冻结资金');
            $table->decimal('balance', 14, 4)->comment('帐变后账户余额');
            $table->decimal('hold_balance', 14, 4)->comment('帐变后冻结资金');
            $table->smallInteger('mode')->default(0)->comment('模式');
            $table->string('comment', 256)->default('')->comment('备注');
            $table->smallInteger('client_type')->default(0)->comment('客户端类型');
            $table->ipAddress('ip')->nullable()->comment('客户端 IP');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('账变时间');

            $table->index('task_id');
            $table->index('project_id');
            $table->index('created_at');
            $table->index(['ip', 'created_at']);
            $table->index(['from_user_id', 'created_at']);
            $table->index(['admin_user_id', 'created_at']);
            $table->index(['lottery_id', 'order_type_id', 'created_at']);
            $table->index(['created_at', 'order_type_id', 'from_user_id']);
        });

        DB::statement('ALTER SEQUENCE IF EXISTS "orders_id_seq" RESTART WITH 10000000');
        DB::statement('ALTER TABLE orders SET (autovacuum_vacuum_scale_factor = 0.02)');

        $this->data();
    }

    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '账变管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon' => 'fa-table',
                'rule' => 'orders',
                'name' => '账变管理',
            ]);
        } else {
            $id = $row->id;
        }

        DB::table('admin_role_permissions')->insert([
                'parent_id' => $id,
                'icon' => '',
                'rule' => 'order/index',
                'name' => '账变列表',
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
            Schema::dropIfExists('orders');
        }
    }
}
