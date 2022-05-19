<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sender_name')->comment('发包人');
            $table->boolean('is_admin')->default(0)->comment('是否是管理');
            $table->string('title')->comment('红包名字');
            $table->string('content')->comment('红包内容');
            $table->smallInteger('type')->default(0)->comment('红包类型 0');
            $table->decimal('amount', 15, 4)->default(0)->comment('金额');
            $table->smallInteger('total_num')->default(0)->comment('红包个数');
            $table->smallInteger('send_num')->default(0)->comment('已发红包个数');
            $table->decimal('send_amount', 15, 4)->default(0)->comment('已发金额');
            $table->timestamp('expired_at')->nullable()->comment('过期时间');
            $table->timestamps();
        });
        $this->__permissions();
    }
    private function __permissions()
    {
        $row = DB::table('admin_role_permissions')->where('name', '活动管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'coupon/index',
                'name' => '红包雨',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'coupon/send',
                'name' => '发送红包',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'coupon/detail',
                'name' => '领取详情',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'coupon/config',
                'name' => '设置定时红包',
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'coupon/push',
                'name' => '推送红包',
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
            Schema::dropIfExists('coupon');
        }
    }
}
