<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_level', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32)->unique()->comment('层级名称');
            $table->dateTime('register_start_time')->comment('用户注册起始日');
            $table->dateTime('register_end_time')->comment('用户注册截止日');
            $table->integer('deposit_times')->default(0)->comment('存款次数');
            $table->decimal('deposit_count_amount', 15, 4)->default(0)->comment('存款总金额');
            $table->decimal('deposit_max_amount', 15, 4)->default(0)->comment('存款最大金额');
            $table->integer('withdrawal_times')->default(0)->comment('提现次数');
            $table->decimal('withdrawal_count_amount', 15, 4)->default(0)->comment('取款总金额');
            $table->decimal('expense_count_amount', 15, 4)->default(0)->comment('消费总金额');
            $table->string('remark', 255)->default('')->comment('备注');
            $table->boolean('status')->default(true)->comment('1正常 0停用');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
        });

        $this->__permissions();
        $this->__data();
    }

    private function __permissions()
    {
        $row = DB::table('admin_role_permissions')->where('name', '用户管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon' => 'fa-user',
                'rule' => 'user',
                'name' => '用户管理',
            ]);
        } else {
            $id = $row->id;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'userlevel/index',
                'name' => '用户分层管理',
            ],
            [
                'parent_id' => $id,
                'rule' => 'userlevel/create',
                'name' => '创建分层',
            ],
            [
                'parent_id' => $id,
                'rule' => 'userlevel/edit',
                'name' => '编辑分层',
            ],
            [
                'parent_id' => $id,
                'rule' => 'userlevel/delete',
                'name' => '删除分层',
            ],
        ]);
    }

    private function __data()
    {
        DB::table('user_level')->insert([
            [
                'name'                      => '未分层',
                'register_start_time'       => \Carbon\Carbon::now(),
                'register_end_time'         => \Carbon\Carbon::parse('2020-12-05')->toDateTimeString(),
                'deposit_times'             => 0,
                'deposit_count_amount'      => 0,
                'deposit_max_amount'        => 0,
                'withdrawal_times'          => 0,
                'withdrawal_count_amount'   => 0,
                'expense_count_amount'      => 0,
                'remark'                    => '',
                'status'                    => 1,
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
        //
        if (app()->environment() != 'production') {
            Schema::dropIfExists('user_level');
        }
    }
}
