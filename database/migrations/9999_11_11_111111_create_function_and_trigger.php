<?php

use Illuminate\Database\Migrations\Migration;

class CreateFunctionAndTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
          CREATE OR REPLACE FUNCTION refuse_delete_common_fun() RETURNS trigger AS $$
                DECLARE
                     log_user_id INT;
                     action_name VARCHAR;
                     query_sql TEXT;
                     allow_special_del_tables VARCHAR[] := array['report_lottery', 'report_lottery_compressed', 'report_lottery_total', 'report_lottery_total_compressed'];
                     no_user_id_tables VARCHAR[] :=  array['lottery', 'user_request_log', 'user_bet_request_log', 'admin_request_log'];
                     no_created_at_tables VARCHAR[] :=  array['user_fund'];
                     forbid_delete_tables VARCHAR[] := array['users', 'user_fund', 'lottery'];

                BEGIN
                     IF TG_ARGV[0] IS NULL THEN
                          action_name := '未定义';
                     ELSE
                          action_name := TG_ARGV[0];
                     END IF;

                     IF TG_TABLE_NAME = 'users' THEN
                          log_user_id := OLD.id;
                     ELSEIF TG_TABLE_NAME = 'orders' THEN
                          log_user_id := OLD.from_user_id;
                     ELSEIF TG_TABLE_NAME=ANY(no_user_id_tables) THEN
                          log_user_id := 0;
                     ELSEIF OLD.user_id IS NOT NULL THEN
                          log_user_id := OLD.user_id;
                     ELSE
                          log_user_id := 0;
                     END IF;

                     query_sql := current_query();

                     IF query_sql ~ E'AnD  \'apl_special\'=\'apl_special\'' AND TG_TABLE_NAME=ANY(allow_special_del_tables) THEN
                          RETURN OLD;
                     ELSEIF TG_TABLE_NAME=ANY(forbid_delete_tables) THEN
                          INSERT INTO user_behavior_log (
                                user_id,
                                db_user,
                                level,
                                action,
                                description
                          ) VALUES (
                                log_user_id,
                                user,
                                1,
                                action_name,
                                '尝试删除被拒绝，执行语句：' || current_query() || '  ，数据记录： ' || OLD
                          );
                          RETURN NULL;
                     ELSIF TG_TABLE_NAME=ANY(allow_special_del_tables) AND OLD.created_at IS NOT NULL AND OLD.created_at < LOCALTIMESTAMP - interval '60 days' THEN
                          RETURN OLD;
                     ELSE
                          INSERT INTO user_behavior_log (
                                user_id,
                                db_user,
                                level,
                                action,
                                description
                          ) VALUES (
                                log_user_id,
                                user,
                                1,
                                action_name,
                                '尝试删除被拒绝，执行语句：' || current_query() || '  ，数据记录： ' || OLD
                          );
                          RETURN NULL;
                     END IF;
                END;
                $$
                language plpgsql;
          ");

        //users 永久
        DB::statement("
          CREATE TRIGGER users_delete_trigger BEFORE DELETE ON users
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除用户');
          ");

        //user_fund 永久
        DB::statement("
          CREATE TRIGGER user_fund_delete_trigger BEFORE DELETE ON user_fund
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除资金');
          ");

        //lottery 永久
        DB::statement("
          CREATE TRIGGER lottery_delete_trigger BEFORE DELETE ON lottery
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除彩种');
          ");

        //orders  60天
        DB::statement("
          CREATE TRIGGER orders_delete_trigger BEFORE DELETE ON orders
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除账变');
          ");

        //projects 60天
        DB::statement("
          CREATE TRIGGER projects_delete_trigger BEFORE DELETE ON projects
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除注单');
          ");

        //withdrawals 60天
        DB::statement("
          CREATE TRIGGER withdrawals_delete_trigger BEFORE DELETE ON withdrawals
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除提款');
          ");

        //deposits  60天
        DB::statement("
          CREATE TRIGGER deposits_delete_trigger BEFORE DELETE ON deposits
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除充值');
          ");

        //user_bet_request_log 30天
        DB::statement("
          CREATE TRIGGER user_bet_request_log_delete_trigger BEFORE DELETE ON user_bet_request_log
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '30 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除用户投注日志');
          ");

        //user_login_log 30天
        DB::statement("
          CREATE TRIGGER user_login_log_delete_trigger BEFORE DELETE ON user_login_log
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '30 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除用户登录日志');
          ");

        //user_request_log 30天
        DB::statement("
          CREATE TRIGGER user_request_log_delete_trigger BEFORE DELETE ON user_request_log
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '30 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除用户非投注日志');
          ");

        //admin_request_log 30天
        DB::statement("
          CREATE TRIGGER admin_request_log_delete_trigger BEFORE DELETE ON admin_request_log
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '30 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除管理员日志');
          ");

        //user_behavior_log 30天
        DB::statement("
          CREATE TRIGGER user_behavior_log_delete_trigger BEFORE DELETE ON user_behavior_log
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '30 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除异常行为日志');
          ");

        //report_lottery 60天 在fun判断
        DB::statement("
          CREATE TRIGGER report_lottery_delete_trigger BEFORE DELETE ON report_lottery
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除报表rl');
          ");

        //report_lottery_compressed 60天 在fun判断
        DB::statement("
          CREATE TRIGGER report_lottery_compressed_delete_trigger BEFORE DELETE ON report_lottery_compressed
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除报表rlc');
          ");

        //report_lottery_total 60天 在fun判断
        DB::statement("
          CREATE TRIGGER report_lottery_total_delete_trigger BEFORE DELETE ON report_lottery_total
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除报表rlt');
          ");

        //report_lottery_total_compressed 60天 在fun判断
        DB::statement("
          CREATE TRIGGER report_lottery_total_compressed_delete_trigger BEFORE DELETE ON report_lottery_total_compressed
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_delete_common_fun('删除报表rltc');
          ");

        //shadow_user_fund 60天
        DB::statement("
          CREATE TRIGGER shadow_user_fund_delete_trigger BEFORE DELETE ON shadow_user_fund
                FOR EACH ROW
                WHEN(OLD.created_at > LOCALTIMESTAMP - interval '60 days')
                EXECUTE PROCEDURE refuse_delete_common_fun('删除自动账变');
          ");


        $this->update_created_at_trigger();
    }

    public function update_created_at_trigger()
    {
        DB::statement("
          CREATE OR REPLACE FUNCTION refuse_update_created_common_fun() RETURNS trigger AS $$
                DECLARE
                     log_user_id INT;
                     action_name VARCHAR;
                     no_user_id_tables VARCHAR[] :=  array['lottery', 'user_request_log', 'user_bet_request_log', 'admin_request_log'];

                BEGIN
                     IF TG_ARGV[0] IS NULL THEN
                          action_name := '未定义';
                     ELSE
                          action_name := TG_ARGV[0];
                     END IF;

                     IF TG_TABLE_NAME = 'users' THEN
                          log_user_id := OLD.id;
                     ELSEIF TG_TABLE_NAME = 'orders' THEN
                          log_user_id := OLD.from_user_id;
                     ELSEIF TG_TABLE_NAME = ANY(no_user_id_tables) THEN
                          log_user_id := 0;
                     ELSEIF OLD.user_id IS NOT NULL THEN
                          log_user_id := OLD.user_id;
                     ELSE
                          log_user_id := 0;
                     END IF;

                     IF NEW.created_at = OLD.created_at THEN
                          RETURN NEW;
                     ELSE
                          INSERT INTO user_behavior_log (
                                user_id,
                                db_user,
                                level,
                                action,
                                description
                          ) VALUES (
                                log_user_id,
                                user,
                                1,
                                action_name,
                                '修改创建时间被拒绝，执行语句：' || current_query() || '  ，原数据： ' || OLD
                          );
                          RETURN NULL;
                     END IF;
                END;
                $$
                language plpgsql;
          ");

        //orders
        DB::statement("
          CREATE TRIGGER orders_update_created_trigger BEFORE UPDATE ON orders
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改账变创建时间');
          ");

        //projects
        DB::statement("
          CREATE TRIGGER projects_update_created_trigger BEFORE UPDATE ON projects
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改注单创建时间');
          ");

        //withdrawals
        DB::statement("
          CREATE TRIGGER withdrawals_update_created_trigger BEFORE UPDATE ON withdrawals
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改提款创建时间');
          ");

        //deposits
        DB::statement("
          CREATE TRIGGER deposits_update_created_trigger BEFORE UPDATE ON deposits
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改充值创建时间');
          ");

        //user_bet_request_log
        DB::statement("
          CREATE TRIGGER user_bet_request_log_update_created_trigger BEFORE UPDATE ON user_bet_request_log
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改用户投注日志时间');
          ");

        //user_login_log
        DB::statement("
          CREATE TRIGGER user_login_log_update_created_trigger BEFORE UPDATE ON user_login_log
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改用户登录日志时间');
          ");

        //user_request_log
        DB::statement("
          CREATE TRIGGER user_request_log_update_created_trigger BEFORE UPDATE ON user_request_log
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改用户非投注日志时间');
          ");

        //admin_request_log
        DB::statement("
          CREATE TRIGGER admin_request_log_update_created_trigger BEFORE UPDATE ON admin_request_log
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改管理员日志时间');
          ");

        //user_behavior_log
        DB::statement("
          CREATE TRIGGER user_behavior_log_update_created_trigger BEFORE UPDATE ON user_behavior_log
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改异常行为日志时间');
          ");

        //report_lottery
        DB::statement("
          CREATE TRIGGER report_lottery_update_created_trigger BEFORE UPDATE ON report_lottery
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改报表rl时间');
          ");

        //report_lottery_compressed
        DB::statement("
          CREATE TRIGGER report_lottery_compressed_update_created_trigger BEFORE UPDATE ON report_lottery_compressed
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改报表rlc时间');
          ");

        //report_lottery_total
        DB::statement("
          CREATE TRIGGER report_lottery_total_update_created_trigger BEFORE UPDATE ON report_lottery_total
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改报表rlt时间');
          ");

        //report_lottery_total_compressed
        DB::statement("
          CREATE TRIGGER report_lottery_total_compressed_update_created_trigger BEFORE UPDATE ON report_lottery_total_compressed
                FOR EACH ROW
                EXECUTE PROCEDURE refuse_update_created_common_fun('修改报表rltc时间');
          ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
