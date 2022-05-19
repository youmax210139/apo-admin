<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBlackCardList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('black_card_list', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->comment('类型：1, 系统黑名单; 2, 共用银行卡黑名单');
            $table->string('account_name', 16)->comment('开户名');
            $table->string('account', 32)->comment('卡号');
            $table->string('remark')->default('')->comment('备注');
            $table->string('extra')->nullable()->comment('扩展字段');

            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');

            $table->unique(['account', 'type']);
            $table->index(['type', 'extra']);
        });

        $this->__permissions();
    }

    private function __permissions()
    {
        $row = DB::table('admin_role_permissions')->where('name', '用户管理')
            ->where('parent_id', 0)
            ->first();

        if (empty($row)) {
            return;
        }
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $row->id,
                'rule' => 'blackcardlist/index',
                'name' => '银行卡黑名单管理'
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'blackcardlist/create',
                'name' => '新增银行卡黑名单'
            ],
            [
                'parent_id' => $row->id,
                'rule' => 'blackcardlist/delete',
                'name' => '删除银行卡黑名单'
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
        //
        if (app()->environment() != 'production') {
            Schema::dropIfExists('black_card_list');
        }
    }
}
