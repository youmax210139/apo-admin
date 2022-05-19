<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableProjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('注单 ID')->unsigned();
            $table->integer('top_id')->comment('总代 ID');
            $table->integer('user_id')->comment('用户 ID')->unsigned();
            $table->bigInteger('package_id')->comment('销售单 ID')->unsigned();
            $table->bigInteger('task_id')->default(0)->comment('追号单 ID')->unsigned();
            $table->smallInteger('lottery_id')->comment('彩种 ID')->unsigned();
            $table->integer('lottery_method_id')->comment('玩法 ID')->unsigned();
            $table->string('issue', 32)->comment('奖期');
            $table->jsonb('prize_level')->default('[]')->comment('奖金级别');
            $table->mediumText('code')->comment('投注号码');
            $table->string('code_position', 16)->default('')->comment('任选单式投注位置 个十百千万');
            $table->decimal('single_price', 14, 4)->comment('单价');
            $table->integer('multiple')->comment('倍数');
            $table->decimal('total_price', 14, 4)->comment('总价');
            $table->decimal('rebate', 4, 4)->comment('返点');
            $table->decimal('bonus', 14, 4)->default(0)->comment('实际发的奖金');
            $table->decimal('original_bonus', 14, 4)->default(0)->comment('原本要发的奖金');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('写入时间');
            $table->timestamp('deduct_at')->nullable()->comment('扣款时间');
            $table->timestamp('send_prize_at')->nullable()->comment('派奖时间时间');
            $table->timestamp('cancel_at')->nullable()->comment('撤单时间');
            $table->timestamp('requested_at')->nullable()->comment('请求时间');
            $table->smallInteger('mode')->default(0)->comment('元角分厘模式');
            $table->smallInteger('is_deduct')->default(0)->comment('是否扣款');
            $table->smallInteger('is_cancel')->default(0)->comment('是否被取消(0. 正常; 1. 本人撤单; 2. 管理员撤单; 3. 开错奖撤单,4 特殊玩法和撤单)');
            $table->smallInteger('is_get_prize')->default(0)->comment('开奖状态(0. 未开奖; 1. 已中奖; 2. 未中奖; 3. 特殊玩法开和)');
            $table->smallInteger('prize_status')->default(0)->comment('派奖状态(0. 未派奖; 1. 已派奖)');
            $table->smallInteger('share_or_follow_status')->default(0)->comment('跟单状态（0. 正常单; 1. 主单，2，跟单）');
            $table->decimal('share_or_follow_fee', 14, 4)->default(0)->comment('推单和跟单佣金');
            $table->smallInteger('is_wage_paid')->default(0)->comment('工资状态(0.未派发; 1. 已派发)');
            $table->smallInteger('is_pump_paid')->default(0)->comment('抽水状态(0.初始值;1已计算;2已抽水;3已返水)');
            $table->smallInteger('is_send_credit')->default(0)->comment('积分发放（0. 未发放; 1. 已发放）');
            $table->smallInteger('client_type')->comment('客户端类型');
            $table->ipAddress('ip')->nullable()->comment('用户 IP');

            $table->index('package_id');
            $table->index('task_id');
            $table->index('created_at');
            $table->index(['top_id', 'created_at']);
            $table->index(['top_id', 'is_wage_paid', 'deduct_at']);
            $table->index(['ip', 'created_at']);
            $table->index(['share_or_follow_status', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['lottery_id', 'created_at']);
            $table->index(['user_id', 'is_cancel', 'is_get_prize', 'created_at']);
            $table->index(['lottery_id', 'lottery_method_id', 'issue', 'is_get_prize']);
            $table->index(['lottery_id', 'is_cancel', 'is_get_prize', 'prize_status', 'bonus']);
            $table->index(['issue', 'is_cancel', 'user_id', 'lottery_id', 'lottery_method_id', 'total_price', 'bonus']);
            $table->index(['lottery_id', 'prize_status', 'is_pump_paid', 'created_at']);//抽水专用索引
        });

        DB::statement('ALTER TABLE projects SET (autovacuum_vacuum_scale_factor = 0.008)');
        DB::statement('ALTER SEQUENCE IF EXISTS "projects_id_seq" RESTART WITH 10000000');
        DB::statement('
        CREATE OR REPLACE FUNCTION project_code_update() RETURNS trigger AS $$
            BEGIN

                INSERT INTO user_behavior_log (
                    user_id,
                    db_user,
                    level,
                    action,
                    description
                ) VALUES (
                    OLD.user_id,
                    user,
                    1,
                    \'改单行为\',
                    \'用户尝试修改投注单号码为：\' || NEW.code || \'，执行语句：\' || current_query()
                );

                RETURN NULL;
            END;
        $$ LANGUAGE plpgsql
        ');

        DB::statement('
        CREATE TRIGGER project_update
            BEFORE UPDATE ON projects
            FOR EACH ROW
            WHEN (OLD.code IS DISTINCT FROM NEW.code)
            EXECUTE PROCEDURE project_code_update();
        ');
        $this->data();
    }


    /***
     * 插入路由数据
     */
    private function data()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id' => 0,
            'icon' => 'fa-gamepad',
            'rule' => 'projects',
            'name' => '游戏管理',
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id' => $id,
                'rule' => 'project/index',
                'name' => '投注纪录查询',
            ],
            [
                'parent_id' => $id,
                'rule' => 'project/detail',
                'name' => '投注纪录明细',
            ],
            [
                'parent_id' => $id,
                'rule' => 'project/cancel',
                'name' => '撤单',
            ],
            [
                'parent_id' => $id,
                'rule' => 'project/alert',
                'name' => '大额中奖提醒',
            ],
            [
                'parent_id' => $id,
                'rule' => 'task/index',
                'name' => '追号纪录查询',
            ],
            [
                'parent_id' => $id,
                'rule' => 'task/detail',
                'name' => '追号纪录明细',
            ],
            [
                'parent_id' => $id,
                'rule' => 'task/cancel',
                'name' => '终止追号',
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
            Schema::dropIfExists('projects');
        }
    }
}
