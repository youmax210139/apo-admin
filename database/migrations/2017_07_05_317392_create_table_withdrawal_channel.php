<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWithdrawalChannel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('withdrawal_channel', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 30)->comment('通道名称');
            $table->string('withdrawal_category_ident', 30)->comment('提现渠道');
            $table->string('merchant_id', 128)->comment('商户ID');
            $table->text('extra')->default("")->comment('扩展字段,存放密钥等');
            $table->decimal('amount_min', 15, 4)->default(0)->comment('最小提款金额');
            $table->decimal('amount_max', 15, 4)->default(1000)->comment('最大提款金额');
            $table->boolean('status')->default(true)->comment('1正常 0停用');
            $table->boolean('user_fee_status')->default(0)->comment('手续费是否启用');
            $table->tinyInteger('user_fee_operation')->default(0)->unsigned()->comment('用户手续费类型，0减1加');
            $table->decimal('user_fee_step', 15, 4)->default(0)->unsigned()->comment('手续费界定');
            $table->tinyInteger('user_fee_down_type')->default(1)->unsigned()->comment('低于界定的手续费类型，1百分比2固定值');
            $table->decimal('user_fee_down_value', 15, 11)->default(0)->unsigned()->comment('低于界定的手续费值');
            $table->tinyInteger('user_fee_up_type')->default(1)->unsigned()->comment('高于界定的手续费比例，1百分比2固定值');
            $table->decimal('user_fee_up_value', 15, 11)->default(0)->unsigned()->comment('高于界定的手续费值');

            $table->boolean('platform_fee_status')->default(0)->comment('平台手续费是否启用');
            $table->integer('platform_fee_step')->default(0)->unsigned()->comment('平台手续费界定金额');
            $table->tinyInteger('platform_fee_down_type')->default(1)->unsigned()->comment('平台低于界定的手续费类型，1百分比2固定值');
            $table->decimal('platform_fee_down_value', 15, 4)->default(0)->unsigned()->comment('平台低于界定的手续费值');
            $table->tinyInteger('platform_fee_up_type')->default(1)->unsigned()->comment('平台高于界定的手续费比例，1百分比2固定值');
            $table->decimal('platform_fee_up_value', 15, 4)->default(0)->unsigned()->comment('平台高于界定的手续费值');
            $table->timestamps();
        });

        $this->__data();
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
            Schema::dropIfExists('withdrawal_channel');
        }
    }

    private function __data()
    {
        DB::table('withdrawal_channel')->insert([
            [
                'name'                          => '通汇1',
                'withdrawal_category_ident'     => 'thkpay',
                'merchant_id'                   => 'a123',
                'extra'                         => ssl_encrypt(json_encode(['key'=>'abcdeds']), env('ENCRYPT_KEY')),
                'amount_min'                    => '1',
                'amount_max'                    => '100000',
            ],
        ]);
    }
}
