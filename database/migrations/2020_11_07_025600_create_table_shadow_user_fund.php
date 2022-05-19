<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableShadowUserFund extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shadow_user_fund', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->comment('用户ID');
            $table->decimal('balance', 14, 4)->default(0)->comment('帐户余额');
            $table->decimal('hold_balance', 14, 4)->default(0)->comment('冻结金额');
            $table->integer('points')->default(0)->comment('用户积分');
            $table->decimal('balance_diff', 14, 4)->default(0)->comment('帐户余额变化');
            $table->decimal('hold_balance_diff', 14, 4)->default(0)->comment('冻结金额变化');
            $table->integer('points_diff')->default(0)->comment('用户积分变化');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('时间');
            $table->index(['user_id', 'created_at']);
            $table->index(['created_at']);
        });

        DB::statement("
        CREATE OR REPLACE FUNCTION shadow_user_fund_add_fun() RETURNS trigger AS $$
            DECLARE
                balance_diff DECIMAL;
                hold_balance_diff DECIMAL;
                points_diff INT;
            BEGIN
                balance_diff := NEW.balance - OLD.balance;
                hold_balance_diff := NEW.hold_balance - OLD.hold_balance;
                points_diff := NEW.points - OLD.points;

                INSERT INTO shadow_user_fund (
                    user_id,
                    balance,
                    hold_balance,
                    points,
                    balance_diff,
                    hold_balance_diff,
                    points_diff
                ) VALUES (
                    NEW.user_id,
                    NEW.balance,
                    NEW.hold_balance,
                    NEW.points,
                    balance_diff,
                    hold_balance_diff,
                    points_diff
                );
                RETURN NEW;
            END;
            $$
            language plpgsql;
        ");

        DB::statement("
        CREATE TRIGGER user_fund_update_trigger AFTER UPDATE ON user_fund
            FOR EACH ROW
            EXECUTE PROCEDURE shadow_user_fund_add_fun();
        ");

        DB::statement("
        CREATE OR REPLACE FUNCTION shadow_user_fund_update_fun() RETURNS trigger AS $$
            BEGIN
                RETURN NULL;
            END;
            $$
            language plpgsql;
        ");
        DB::statement("
        CREATE TRIGGER shadow_user_fund_update_trigger BEFORE UPDATE ON shadow_user_fund
            FOR EACH ROW
            EXECUTE PROCEDURE shadow_user_fund_update_fun();
        ");


        $this->data();
    }

    public function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '账变管理')->where('parent_id', 0)->first();
        if(empty($row)) {
            return ;
        }
        DB::table('admin_role_permissions')->insert([
            'parent_id' => $row->id,
            'icon' => '',
            'rule' => 'shadowfund/index',
            'name' => '自动账变',
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
            Schema::dropIfExists('shadow_user_fund');
        }
    }
}
