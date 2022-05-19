<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableThirdGameExtend extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_game_extend', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('ID');
            $table->integer('third_game_id')->unsigned()->comment('所属平台ID');
            $table->string('ident', 32)->comment('英文标识');
            $table->string('name', 32)->comment('中文名称');
            $table->string('value', 250)->comment('值');

            $table->unique(['third_game_id', 'ident']);
        });

        $this->data();
    }

    private function data()
    {
        $vr_row = DB::table('third_game')->where('ident', 'Vr')->first();
        $ciaAg_row = DB::table('third_game')->where('ident', 'CiaAg')->first();
        $ciaEbet_row = DB::table('third_game')->where('ident', 'CiaEbet')->first();
        $ciaWmc_row = DB::table('third_game')->where('ident', 'CiaWmc')->first();
        $ciaAvCloud_row = DB::table('third_game')->where('ident', 'CiaAvCloud')->first();
        $fhleli_row = DB::table('third_game')->where('ident', 'FhLeli')->first();

        DB::table('third_game_extend')->insert([
            //Vr
            [
                'third_game_id' => $vr_row->id,
                'ident' => 'api_white_list',
                'name' => '调用本平台的第三方ip白名单',
                'value' => '',
            ],
            //CiaAg
            [
                'third_game_id' => $ciaAg_row->id,
                'ident' => 'rebate_day_limit',
                'name' => '每日用户返水限额',
                'value' => 50000
            ],
            //FhLeli
            [
                'third_game_id' => $fhleli_row->id,
                'ident' => 'api_white_list',
                'name' => '调用本平台的第三方ip白名单',
                'value' => '',
            ],
            //FhLeli
            [
                'third_game_id' => $fhleli_row->id,
                'ident' => 'drop_point',
                'name' => '奖金组降点 设定20表示降低20奖金组',
                'value' => '0',
            ],
            //CiaEbet
            [
                'third_game_id' => $ciaEbet_row->id,
                'ident' => 'api_white_list',
                'name' => '调用本平台的第三方ip白名单',
                'value' => '',
            ],
            //CiaWmc
            [
                'third_game_id' => $ciaWmc_row->id,
                'ident' => 'api_white_list',
                'name' => '调用本平台的第三方ip白名单',
                'value' => '',
            ],
            [
                'third_game_id' => $ciaWmc_row->id,
                'ident' => 'member_limit_switch',
                'name' => '限制条件开关(1开启)',
                'value' => '1',
            ],
            [
                'third_game_id' => $ciaWmc_row->id,
                'ident' => 'member_level',
                'name' => '会员等级配置',
                'value' => '{"bet":888,"deposit":1,"level":3,"history_days":7}',
            ],
            [
                'third_game_id' => $ciaWmc_row->id,
                'ident' => 'member_white_list',
                'name' => '会员白名单',
                'value' => '',
            ],
            //CiaAvCloud
            [
                'third_game_id' => $ciaAvCloud_row->id,
                'ident' => 'api_white_list',
                'name' => '调用本平台的第三方ip白名单',
                'value' => '',
            ],
            [
                'third_game_id' => $ciaAvCloud_row->id,
                'ident' => 'member_limit_switch',
                'name' => '限制条件开关(1开启)',
                'value' => '1',
            ],
            [
                'third_game_id' => $ciaAvCloud_row->id,
                'ident' => 'member_level',
                'name' => '会员等级配置',
                'value' => '{"bet":888,"deposit":1,"level":3,"history_days":7}',
            ],
            [
                'third_game_id' => $ciaAvCloud_row->id,
                'ident' => 'member_white_list',
                'name' => '会员白名单',
                'value' => '',
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
            Schema::dropIfExists('third_game_extend');
        }
    }
}
