<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRiskRefusedReason extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risk_refused_reason', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('配置 ID');
            $table->text('text')->comment('拒绝原因');
            $table->timestamps();
        });

        $this->data();
    }

    private function data()
    {
        $id = DB::table('admin_role_permissions')->where('name', '审核管理')->where('rule', 'verify')->value('id');
        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule'      => 'riskrefusedreason/index',
                'name'      => '审核拒绝原因管理',
            ],

            [
                'parent_id' => $id,
                'rule'      => 'riskrefusedreason/create',
                'name'      => '添加审核拒绝原因',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'riskrefusedreason/edit',
                'name'      => '编辑审核拒绝原因',
            ],
            [
                'parent_id' => $id,
                'rule'      => 'riskrefusedreason/delete',
                'name'      => '删除审核拒绝原因',
            ],
        ]);


        //添加提现配置
        //添加默认配置项
        $time = \Carbon\Carbon::now()->toDateTimeString();
        DB::table('risk_refused_reason')->insert([
            ['text'       => '您好，您的消费未能达到提款要求，暂时不能提款。',
             'created_at' => $time,
             'updated_at' => $time
            ],
            ['text'       => '您好，由于您填写的银行卡信息不符，无法出款成功，请联系在线客服。',
             'created_at' => $time,
             'updated_at' => $time
            ],
            ['text'       => '您好，由于银行系统维护，无法出款，给您带来不便请多多谅解。',
             'created_at' => $time,
             'updated_at' => $time
            ],
            ['text'       => '您好，您存在大概率违规投注，请联系在线客服。',
             'created_at' => $time,
             'updated_at' => $time
            ],
            ['text'       => '您好，您存在全包违规投注，请联系在线客服。',
             'created_at' => $time,
             'updated_at' => $time
            ],
            ['text'       => '您好，您的注单存在同期单挑行为，超出平台规定单挑上限，请联系在线客服。',
             'created_at' => $time,
             'updated_at' => $time
            ],
            ['text'       => '您好，您的注单存在同期超限行为，超出平台规定奖金上限，请联系在线客服',
             'created_at' => $time,
             'updated_at' => $time
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
            Schema::dropIfExists('risk_refused_reason');
        }
    }
}
