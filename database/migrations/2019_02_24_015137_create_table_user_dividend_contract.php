<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserDividendContract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('user_dividend_contract', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->tinyInteger('type')->nullable()->comment('分红类型：1-a线（分红金额固定）  2-b线（比例固定） 默认b线');
            $table->tinyInteger('mode')->nullable()->comment('分红模式：0:不累计 1:累计 (是上半月盈利 要不要抵消下半月的亏损)');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('契约状态:0等待确认中 1同意 2拒绝 3失效');
            $table->tinyInteger('base_rate')->unsigned()->default(0)->comment('保底分红比例');
            $table->tinyInteger('base_consume_day')->unsigned()->default(0)->comment('分红要求消费天数');
            $table->tinyInteger('base_min_day_sales')->unsigned()->default(0)->comment('每天最低要求日量');
            $table->tinyInteger('consume_type')->default(0)->comment('消费量类型：0:总消费量 1:平均日量');
            $table->tinyInteger('loss_type')->default(0)->comment('亏损量类型：0:总亏损量 1:平均日亏损量');
            $table->tinyInteger('reward_type')->default(0)->comment('奖励类型：0:百分比 1:固定金额');
            $table->jsonb('content')->comment('契约条件');
            $table->tinyInteger('created_stage')->unsigned()->comment('写入操作位置：1-前台，2-后台');
            $table->string('created_username', 20)->comment('创建者');
            $table->tinyInteger('delete_stage')->unsigned()->nullable()->comment('失效操作位置：1-前台，2-后台');
            $table->string('delete_username', 20)->nullable()->comment('终结者');
            $table->timestamp('delete_at')->nullable()->comment('失效时间');
            $table->timestamp('accept_at')->nullable()->comment('确认时间');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');

            $table->index(['user_id', 'status']);
        });

        $this->_user_menu();
        $this->_report_memu();
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
            Schema::dropIfExists('user_dividend_contract');
        }
    }

    private function _user_menu()
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
                'rule' => 'dividend/index',
                'name' => '分红契约列表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'dividend/record',
                'name' => '契约分红调整记录',
            ],
            [
                'parent_id' => $id,
                'rule' => 'dividend/createoredit',
                'name' => '签订分红契约',
            ],
            [
                'parent_id' => $id,
                'rule' => 'dividend/delete',
                'name' => '清除团队分红契约',
            ],
        ]);
    }

    public function _report_memu()
    {
        $row = DB::table('admin_role_permissions')->where('name', '报表管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            $id = DB::table('admin_role_permissions')->insertGetId([
                'parent_id' => 0,
                'icon' => 'fa-area-chart',
                'rule' => 'report',
                'name' => '报表管理',
            ]);
        } else {
            $id = $row->id;
        }

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'dividendreport/index',
                'name' => '分红契约报表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'dividendreport/check',
                'name' => '审核分红报表',
            ],
            [
                'parent_id' => $id,
                'rule' => 'dividendreport/detail',
                'name' => '分红报表明细',
            ],
            [
                'parent_id' => $id,
                'rule' => 'dividendreport/delete',
                'name' => '删除分红报表',
            ],
        ]);
    }
}
