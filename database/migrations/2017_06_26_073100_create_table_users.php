<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('top_id')->default(0)->comment('总代用户 ID，总代为 0');
            $table->integer('parent_id')->default(0)->comment('父级用户 ID，总代为 0');
            $table->jsonb('parent_tree')->default('[]')->comment('父级树');
            $table->smallInteger('user_type_id')->comment('用户类型 ID');
            $table->smallInteger('user_group_id')->comment('用户组 ID');
            $table->string('username', 20)->unique()->comment('用户帐号');
            $table->string('usernick', 20)->comment('用户昵称');
            $table->string('password', 128)->comment('密码');
            $table->string('security_password', 128)->default('')->comment('安全密码');
            $table->string('remember_token', 64)->default('')->comment('记住我');
            $table->smallInteger('withdrawal_num')->default(0)->comment('用户每天提款次数');
            $table->boolean('is_pay_whitelist')->default(false)->comment('是否充值白名单');
            $table->tinyInteger('sub_recharge_status')->default(0)->comment('下级充值权限，0 没权限，1 允许直属下级，2 所有下级');
            $table->smallInteger('frozen')->default(0)->comment('冻结类型：1. 不可登录; 2. 可登录,不可投注,不可充提; 3. 可登录,不可投注,可充提');
            $table->timestamp('frozen_at')->nullable()->comment('禁用时间');
            $table->string('frozen_reason', 64)->default('')->comment('冻结原因');
            $table->string('unfrozen_reason', 64)->default('')->comment('解冻原因');
            $table->timestamp('deleted_at')->nullable()->comment('软删除删除时间');
            $table->ipAddress('created_ip')->nullable()->comment('创建IP');
            $table->ipAddress('last_ip')->nullable()->comment('最后一次登录IP');
            $table->timestamp('last_time')->nullable()->comment('最后登录时间');
            $table->timestamp('last_active')->nullable()->comment('最后活跃时间');
            $table->string('last_session', 64)->default('')->comment('最近登陆产生的随机数，用于挤掉登陆用户');
            $table->timestamp('created_at')->comment('创建时间');
            $table->timestamp('updated_at')->comment('修改时间');

            $table->index('top_id');
            $table->index(['parent_id', 'deleted_at']);
            $table->index('last_active');
            $table->index('created_at');
            $table->index('deleted_at');
            $table->index(['user_group_id', 'id']);
        });

        DB::statement('ALTER SEQUENCE IF EXISTS "users_id_seq" RESTART WITH 20000');
        DB::statement('CREATE INDEX "users_parent_tree_index" ON "users" USING GIN ("parent_tree" jsonb_path_ops)');

        DB::statement('
        CREATE OR REPLACE FUNCTION users_group_update() RETURNS trigger AS $$
            BEGIN

                INSERT INTO user_behavior_log (
                    user_id,
                    db_user,
                    level,
                    action,
                    description
                ) VALUES (
                    OLD.id,
                    user,
                    1,
                    \'用户组变更\',
                    \'用户尝试修改\' || OLD.username || \' 权限 \' || OLD.user_group_id || \' 为\' || NEW.user_group_id || \'，执行语句：\' || current_query()
                );
                RETURN NULL;
            END;
        $$ LANGUAGE plpgsql
        ');

        DB::statement('
        CREATE OR REPLACE FUNCTION users_name_update() RETURNS trigger AS $$
            BEGIN

                INSERT INTO user_behavior_log (
                    user_id,
                    db_user,
                    level,
                    action,
                    description
                ) VALUES (
                    OLD.id,
                    user,
                    1,
                    \'用户名变更\',
                    \'用户尝试修改用户名 \' || OLD.username || \' 为\' || NEW.username || \'，执行语句：\' || current_query()
                );
                RETURN NULL;
            END;
        $$ LANGUAGE plpgsql
        ');

        DB::statement('
        CREATE OR REPLACE FUNCTION users_id_update() RETURNS trigger AS $$
            BEGIN

                INSERT INTO user_behavior_log (
                    user_id,
                    db_user,
                    level,
                    action,
                    description
                ) VALUES (
                    OLD.id,
                    user,
                    1,
                    \'用户ID变更\',
                    \'用户尝试修改用户ID \' || OLD.id || \' 为\' || NEW.id || \'，执行语句：\' || current_query()
                );
                RETURN NULL;
            END;
        $$ LANGUAGE plpgsql
        ');

        DB::statement('
        CREATE TRIGGER users_group_update

            BEFORE UPDATE ON users
            FOR EACH ROW
            WHEN (OLD.user_group_id IS DISTINCT FROM NEW.user_group_id)
            EXECUTE PROCEDURE users_group_update();
        ');

        DB::statement('
        CREATE TRIGGER users_name_update
            BEFORE UPDATE ON users
            FOR EACH ROW
            WHEN (OLD.username IS DISTINCT FROM NEW.username)
            EXECUTE PROCEDURE users_name_update();
        ');

        DB::statement('
        CREATE TRIGGER users_id_update
            BEFORE UPDATE ON users
            FOR EACH ROW
            WHEN (OLD.id IS DISTINCT FROM NEW.id)
            EXECUTE PROCEDURE users_id_update();
        ');
        $this->data();
    }

    private function data()
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
                'icon' => '',
                'rule' => 'user/index',
                'name' => '用户列表',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/detail',
                'name' => '查看用户信息',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/banks',
                'name' => '查看银行卡信息',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/unbundlebank',
                'name' => '解绑用户银行卡',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/editbank',
                'name' => '修改银行卡',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/delbank',
                'name' => '删除银行卡',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/setadduser',
                'name' => '开户权限',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/showfreeze',
                'name' => '冻结用户',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/freeze',
                'name' => '冻结用户执行',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/unfreeze',
                'name' => '解冻用户执行',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/recharge',
                'name' => '充值',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/deduct',
                'name' => '扣款',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/changepass',
                'name' => '修改密码',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/securityquestion',
                'name' => '密保',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/rebates',
                'name' => '返点',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/quota',
                'name' => '配额管理',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/delete',
                'name' => '删除',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/points',
                'name' => '积分',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/withdrawallimit',
                'name' => '提款次数',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'createtopuser/index',
                'name' => '总代开户',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'domain/index',
                'name' => '域名管理',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'domain/create',
                'name' => '添加域名',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'domain/assignanddel',
                'name' => '分配或者删除域名',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'domain/recovery',
                'name' => '回收域名',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'registerurl/index',
                'name' => '推广链接',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'registerurl/del',
                'name' => '删除推广链接',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/sendmsg',
                'name' => '站内信',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/depositwhitelist',
                'name' => '充值白名单',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/subrechargewhitelist',
                'name' => '下级充值白名单',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/googlekey',
                'name' => '清除谷歌验证',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/userlevel',
                'name' => '用户分层',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/kickout',
                'name' => '踢用户下线',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/userobserve',
                'name' => '设置重点观察',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/prizelevel',
                'name' => '奖级调整',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/dividendlock',
                'name' => '分红锁',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'userip/index',
                'name' => '用户IP反查',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/unbindtelephone',
                'name' => '解绑手机',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'userbetip/index',
                'name' => '同IP投注反查',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/unbindemail',
                'name' => '解绑email',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/unbindweixin',
                'name' => '解绑微信号',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/unbindqq',
                'name' => '解绑qq号码',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'message/index',
                'name' => '消息查询',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/adduser',
                'name' => '添加下级',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/issuelimitbet',
                'name' => '单期投注限制',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/bantransfer',
                'name' => '禁止转账',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/banwithdrawal',
                'name' => '禁止提款',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/adduserlimit',
                'name' => '开户限额',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'chatmessage/index',
                'name' => '上下级聊天查询',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'chatmessage/delete',
                'name' => '删除上下级聊天',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/skip_diff_ip_verify',
                'name' => '异地验证',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/editremark',
                'name' => '用户备注',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/clearteamthirdrebate',
                'name' => '清理团队三方返水',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/allowtransfertoparent',
                'name' => '向上级转账',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/changetoagent',
                'name' => '身份变更',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'user/recythirdbalance',
                'name' => '收回三方余额',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'userloginip/index',
                'name' => '同ip统计表',
            ],
            [
                'parent_id' => $row->id,
                'icon' => '',
                'rule' => 'userloginip/detail',
                'name' => '同ip统计表详情页',
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
            Schema::dropIfExists('users');
        }
    }
}
