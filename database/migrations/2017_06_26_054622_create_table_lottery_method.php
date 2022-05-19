<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLotteryMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lottery_method', function (Blueprint $table) {
            $table->integer('id')->comment('玩法 ID：第1位数字为玩法分类，1、2级为2位，3级为3位');
            $table->integer('parent_id')->default(0)->comment('父级ID');
            $table->smallInteger('lottery_method_category_id')->comment('玩法分类 ID');
            $table->string('ident', 32)->unique()->comment('英文标识');
            $table->string('name', 32)->default('')->comment('玩法中文名称');
            $table->jsonb('draw_rule')->default('[]')->comment('开奖规则');
            $table->string('lock_table_name', 100)->default('')->comment('封锁表名称');
            $table->string('lock_init_function', 100)->default('')->comment('封锁表初始化函数');
            $table->json('modes')->default('[]')->comment('模式');
            $table->json('prize_level')->default('[]')->comment('奖金级别');
            $table->json('prize_level_name')->default('[]')->comment('奖金级别名称');
            $table->json('layout')->default('[]')->comment('前端渲染内容');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->boolean('status')->default(false)->comment('是否开启');
            $table->integer('max_bet_num')->default(0)->comment('最大投注注数'); //玩法最多可以投注多少注

            $table->primary('id');
            $table->index('parent_id');
            $table->index(['lottery_method_category_id', 'status']);
        });

        $this->data();
        $this->dataPk();
        $this->setStatusFalse();
    }

    private function data()
    {
        $row = DB::table('admin_role_permissions')->where('name', '彩种管理')->where('parent_id', 0)->first();
        if (empty($row)) {
            return ;
        }

        DB::table('admin_role_permissions')->insert([
                [
                        'parent_id' => $row->id,
                        'rule' => 'lotterymethod/index',
                        'name' => '玩法列表',
                ],
                [
                        'parent_id' => $row->id,
                        'rule' => 'lotterymethod/create',
                        'name' => '添加玩法',
                ],
                [
                        'parent_id' => $row->id,
                        'rule' => 'lotterymethod/edit',
                        'name' => '修改玩法',
                ],
                [
                        'parent_id' => $row->id,
                        'rule' => 'lotterymethod/delete',
                        'name' => '删除玩法',
                ],
                [
                    'parent_id' => $row->id,
                    'rule' => 'lotterymethod/editprize',
                    'name' => '修改奖级',
                ],
                [
                    'parent_id' => $row->id,
                    'rule' => 'lotterymethod/editmaxnum',
                    'name' => '修改注数限制',
                ]

        ]);

        DB::table('lottery_method')->insert([

            //================================  lottery_method_category_id: 11 时时彩标准模式  ==================================
            //五星 100100000,
            ['id'=>'100100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5', 'name'=>'五星', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //五星直选 100101000,
            ['id'=>'100101000', 'parent_id'=>'100100000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zhixuan', 'name'=>'五星直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100101001', 'parent_id'=>'100101000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_zhixuan", "tag_check": "n5_zhixuan", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200000]', 'prize_level_name'=>'["五星直选"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中选择一个号码组成一注",
    "help": "从万位、千位、百位、十位、个位中选择一个号码组成一注，所选号码与开奖号码全部相同，且顺序一致，即为中奖。",
    "example": "投注方案：23456；<br />开奖号码：23456，<br />即中五星直选",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 4,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,X,X,X",
    "code_sp": "",
    "if_random": 1,
    "random_cos": 5,
    "random_cos_value": "1|1|1|1|1"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'85000', ],
            ['id'=>'100101002', 'parent_id'=>'100101000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_zhixuan", "tag_check": "n5_zhixuan", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200000]', 'prize_level_name'=>'["五星直选"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个五位数号码组成一注。",
    "help": "手动输入一个5位数号码组成一注，所选号码的万位、千位、百位、十位、个位与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：23456； 开奖号码：23456，即中五星直选",
    "select_area": {
        "type": "input",
        "singletypetips": "三星123,234 五星12345"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100101003', 'parent_id'=>'100101000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zhixuan_zuhe', 'name'=>'组合', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_zuhe", "tag_check": "n5_zuhe", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200000,20000,2000,200,20]', 'prize_level_name'=>'["五星","四星","三星","二星","一星"]', 'layout'=>'
{
    "desc": "从万、千、百、十、个位各选一个号码组成五注。",
    "help": "从万位、千位、百位、十位、个位中至少各选一个号码组成1-5星的组合，共五注，所选号码的个位与开奖号码相同，则中1个一星；所选号码的个位、十位与开奖号码相同，则中1个一星以及1个二星，依此类推，最高可中5个奖。",
    "example": "购买：4+5+6+7+8，该票共10元，由以下5注：45678(五星)、5678(四星)、678(三星)、78(二星)、8(一星)构成。开奖号码：45678，即可中五星、四星、三星、二星、一星各1注。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 4,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100101004', 'parent_id'=>'100101000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zhixuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200000]', 'prize_level_name'=>'["五星和值"]', 'layout'=>'
{
    "desc": "从和值中选一个号码组成1注。",
    "help": "从0-45中任意选择1个或1个以上号码。投注方案：和值1，开奖号码：00001、00010、00100、01000、10000，即为中五星和值。",
    "example": "投注方案：12。开奖号码：14502，即中五星和值。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 23,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //五星组选 100102000,
            ['id'=>'100102000', 'parent_id'=>'100100000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zuxuan', 'name'=>'五星组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100102001', 'parent_id'=>'100102000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zuxuan120', 'name'=>'组选120', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_zuxuan", "tag_check": "n5_zuxuan120", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1666.66666]', 'prize_level_name'=>'["五星组选120"]', 'layout'=>'
{
    "desc": "从0-9中选择5个号码组成一注，顺序不限。",
    "help": "从0-9中任意选择5个号码组成一注，所选号码与开奖号码的万位、千位、百位、十位、个位相同，顺序不限，即为中奖。",
    "example": "投注方案：02568，开奖号码的五个数字只要包含0、2、5、6、8，即可中五星组选120。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 5
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100102002', 'parent_id'=>'100102000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zuxuan60', 'name'=>'组选60', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_zuxuan", "tag_check": "n5_zuxuan60", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3333.33333]', 'prize_level_name'=>'["五星组选60"]', 'layout'=>'
{
    "desc": "从“二重号”选择一个号码，“单号”中选择三个号码组成一注，顺序不限。",
    "help": "选择1个二重号码和3个单号号码组成一注，所选的单号号码与开奖号码相同，且所选二重号码在开奖号码中出现了2次，即为中奖。",
    "example": "投注方案：二重号：8，单号：0、2、5，只要开奖的5个数字包括 0、2、5、8、8，即可中五星组选60。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 3
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100102003', 'parent_id'=>'100102000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zuxuan30', 'name'=>'组选30', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_zuxuan", "tag_check": "n5_zuxuan30", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[6666.66666]', 'prize_level_name'=>'["五星组选30"]', 'layout'=>'
{
    "desc": "从“二重号”选择两个号码，“单号”中选择一个号码组成一注，顺序不限。",
    "help": "选择2个二重号和1个单号号码组成一注，所选的单号号码与开奖号码相同，且所选的2个二重号码分别在开奖号码中出现了2次，即为中奖。",
    "example": "投注方案：二重号：2、8，单号：0，只要开奖的5个数字包括 0、2、2、8、8，即可中五星组选30。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 2
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100102004', 'parent_id'=>'100102000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zuxuan20', 'name'=>'组选20', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_zuxuan", "tag_check": "n5_zuxuan20", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[10000]', 'prize_level_name'=>'["五星组选20"]', 'layout'=>'
{
    "desc": "从“三重号”选择一个号码，“单号”中选择两个号码组成一注，顺序不限。",
    "help": "选择1个三重号码和2个单号号码组成一注，所选的单号号码与开奖号码相同，且所选三重号码在开奖号码中出现了3次，即为中奖。",
    "example": "投注方案：三重号：8，单号：0、2，只要开奖的5个数字包括 0、2、8、8、8，即可中五星组选20。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "三重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100102005', 'parent_id'=>'100102000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zuxuan10', 'name'=>'组选10', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_zuxuan", "tag_check": "n5_zuxuan10", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000]', 'prize_level_name'=>'["五星组选10"]', 'layout'=>'
{
    "desc": "从“三重号”选择一个号码，“二重号”中选择一个号码组成一注，顺序不限。",
    "help": "选择1个三重号码和1个二重号码，所选三重号码在开奖号码中出现3次，并且所选二重号码在开奖号码中出现了2次，即为中奖。",
    "example": "投注方案：三重号：8，二重号：2，只要开奖的5个数字包括 2、2、8、8、8，即可中五星组选10。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "三重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "二重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100102006', 'parent_id'=>'100102000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_zuxuan5', 'name'=>'组选5', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_zuxuan", "tag_check": "n5_zuxuan5", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[40000]', 'prize_level_name'=>'["五星组选5"]', 'layout'=>'
{
    "desc": "从“四重号”选择一个号码，“单号”中选择一个号码组成一注，顺序不限。",
    "help": "选择1个四重号码和1个单号号码组成一注，所选的单号号码与开奖号码相同，且所选四重号码在开奖号码中出现了4次，即为中奖。",
    "example": "投注方案：四重号：8，单号：2，只要开奖的5个数字包括 2、8、8、8、8，即可中五星组选5。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "四重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //五星特殊 100103000,
            ['id'=>'100103000', 'parent_id'=>'100100000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_teshu', 'name'=>'五星特殊', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100103001', 'parent_id'=>'100103000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_yifanfengshun', 'name'=>'一帆风顺', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_teshu", "tag_check": "n5_yffh", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_yffs', 'lock_init_function'=>'initNumberTypeWuXinTeSuLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.883886]', 'prize_level_name'=>'["一帆风顺"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个以上号码。",
    "help": "从0-9中任意选择1个号码组成一注，只要开奖号码的万位、千位、百位、十位、个位中包含所选号码，即为中奖。",
    "example": "投注方案：8；开奖号码：至少出现1个8，即中一帆风顺。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100103002', 'parent_id'=>'100103000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_haoshichengshuang', 'name'=>'好事成双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_teshu", "tag_check": "n5_hscs", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_hscs', 'lock_init_function'=>'initNumberTypeWuXinTeSuLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[24.551927]', 'prize_level_name'=>'["好事成双"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个以上的二重号码。",
    "help": "从0-9中任意选择1个号码组成一注，只要所选号码在开奖号码的万位、千位、百位、十位、个位中出现2次，即为中奖。",
    "example": "投注方案：8；开奖号码：至少出现2个8，即中好事成双。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100103003', 'parent_id'=>'100103000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_sanxingbaoxi', 'name'=>'三星报喜', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_teshu", "tag_check": "n5_sxbx", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_sxbx', 'lock_init_function'=>'initNumberTypeWuXinTeSuLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[233.64486]', 'prize_level_name'=>'["三星报喜"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个以上的三重号码。",
    "help": "从0-9中任意选择1个号码组成一注，只要所选号码在开奖号码的万位、千位、百位、十位、个位中出现3次，即为中奖。",
    "example": "投注方案：8；开奖号码：至少出现3个8，即中三星报喜。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100103004', 'parent_id'=>'100103000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_sijifacai', 'name'=>'四季发财', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_teshu", "tag_check": "n5_sjfc", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_sjfc', 'lock_init_function'=>'initNumberTypeYiWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4347.826087]', 'prize_level_name'=>'["四季发财"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个以上的四重号码。",
    "help": "从0-9中任意选择1个号码组成一注，只要所选号码在开奖号码的万位、千位、百位、十位、个位中出现4次，即为中奖。",
    "example": "投注方案：8；开奖号码：至少出现4个8，即中四季发财。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //四星 100200000,
            ['id'=>'100200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n4', 'name'=>'四星', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //后四直选 100201000,
            ['id'=>'100201000', 'parent_id'=>'100200000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_zhixuan', 'name'=>'后四直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100201001', 'parent_id'=>'100201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n4_zhixuan", "tag_check": "n4_zhixuan", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000]', 'prize_level_name'=>'["直选复式"]', 'layout'=>'
{
    "desc": "从千位、百位、十位、个位各选一个号码组成一注",
    "help": "从千位、百位、十位、个位各选一个号码组成一注，所选号码与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：3456；开奖号码：*3456，即中四星直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,X,X,X,X",
    "code_sp": "",
    "if_random": 1,
    "random_cos": 4,
    "random_cos_value": "1|1|1|1"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100201002', 'parent_id'=>'100201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n4_zhixuan", "tag_check": "n4_zhixuan", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000]', 'prize_level_name'=>'["直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个四位数号码组成一注。",
    "help": "手动输入一个4位数号码组成一注，所选号码的千位、百位、十位、个位与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：3456； 开奖号码：3456，即中四星直选",
    "select_area": {
        "type": "input",
        "singletypetips": "三星123,234 五星12345"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100201003', 'parent_id'=>'100201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_zhixuan_zuhe', 'name'=>'组合', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n4_zuhe", "tag_check": "n4_zuhe", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000,2000,200,20]', 'prize_level_name'=>'["四星","三星","二星","一星"]', 'layout'=>'
{
    "desc": "在千位，百位，十位，个位任意位置上任意选择1个或1个以上号码。",
    "help": "从千位、百位、十位、个位中至少各选一个号码组成1-4星的组合，共四注，所选号码的个位与开奖号码相同，则中1个一星；所选号码的个位、十位与开奖号码相同，则中1个一星以及1个二星，依此类推，最高可中4个奖。",
    "example": "投注方案：5+6+7+8，开奖号码：*5678，即可中四星、三星、二星、一星各1注。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,X,X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //后四组选 100202000,
            ['id'=>'100202000', 'parent_id'=>'100200000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_zuxuan', 'name'=>'后四组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100202001', 'parent_id'=>'100202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_zuxuan24', 'name'=>'组选24', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n4_zuxuan", "tag_check": "n4_zuxuan24", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[833.33333]', 'prize_level_name'=>'["组选24"]', 'layout'=>'
{
    "desc": "从0-9中选择4个号码组成一注，顺序不限。",
    "help": "从0-9中任意选择4个号码组成一注，所选号码与开奖号码的千位、百位、十位、个位相同，且顺序不限，即为中奖。",
    "example": "投注方案：0568，开奖号码的四个数字只要包含0、5、6、8，即可中四星组选24。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 4
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100202002', 'parent_id'=>'100202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_zuxuan12', 'name'=>'组选12', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n4_zuxuan", "tag_check": "n4_zuxuan12", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1666.66666]', 'prize_level_name'=>'["组选12"]', 'layout'=>'
{
    "desc": "从“二重号”选择一个号码，“单号”中选择两个号码组成一注，顺序不限。",
    "help": "选择1个二重号码和2个单号号码组成一注，所选单号号码与开奖号码相同，且所选二重号码在开奖号码中出现了2次，即为中奖。",
    "example": "投注方案：二重号：8，单号：0、6，只要开奖的四个数字包括 0、6、8、8，即可中四星组选12。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100202003', 'parent_id'=>'100202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_zuxuan6', 'name'=>'组选6', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n4_zuxuan", "tag_check": "n4_zuxuan6", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3333.33333]', 'prize_level_name'=>'["组选6"]', 'layout'=>'
{
    "desc": "从“二重号”选择两个号码组成一注，顺序不限。",
    "help": "选择2个二重号码组成一注，所选的2个二重号码在开奖号码中分别出现了2次，即为中奖。",
    "example": "投注方案：二重号：6、8，只要开奖的四个数字排列为 6、6、8、8，即可中四星组选6。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100202004', 'parent_id'=>'100202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_zuxuan4', 'name'=>'组选4', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n4_zuxuan", "tag_check": "n4_zuxuan4", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[5000]', 'prize_level_name'=>'["组选4"]', 'layout'=>'
{
    "desc": "从“三重号”选择一个号码，“单号”中选择两个号码组成一注，顺序不限。",
    "help": "选择1个三重号码和1个单号号码组成一注，所选单号号码与开奖号码相同，且所选三重号码在开奖号码中出现了3次，即为中奖。",
    "example": "投注方案：三重号：8，单号：2，只要开奖的四个数字排列为 2、8、8、8，即可中四星组选4。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "三重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前四直选 100203000,
            ['id'=>'100203000', 'parent_id'=>'100200000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q4_zhixuan', 'name'=>'前四直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100203001', 'parent_id'=>'100203000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q4_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000]', 'prize_level_name'=>'["直选复式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位各选一个号码组成一注",
    "help": "从万位、千位、百位、十位各选一个号码组成一注，所选号码与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：3456；开奖号码：3456*，即中四星直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,X,X,X,X",
    "code_sp": "",
    "if_random": 1,
    "random_cos": 4,
    "random_cos_value": "1|1|1|1"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100203002', 'parent_id'=>'100203000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q4_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000]', 'prize_level_name'=>'["直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个四位数号码组成一注。",
    "help": "手动输入一个4位数号码组成一注，所选号码的万位、千位、百位、十位与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：3456； 开奖号码：3456*，即中四星直选。",
    "select_area": {
        "type": "input",
        "singletypetips": "三星123,234 五星12345"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100203003', 'parent_id'=>'100203000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q4_zhixuan_zuhe', 'name'=>'组合', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000,2000,200,20]', 'prize_level_name'=>'["四星","三星","二星","一星"]', 'layout'=>'
{
    "desc": "在万位，千位，百位，十位任意位置上任意选择1个或1个以上号码。",
    "help": "从万位、千位、百位、十位中至少各选一个号码组成1-4星的组合，共四注，所选号码的十位与开奖号码相同，则中1个一星；所选号码的百位、十位与开奖号码相同，则中1个一星以及1个二星，依此类推，最高可中4个奖。",
    "example": "投注方案：5+6+7+8，开奖号码：5678*，即可中四星、三星、二星、一星各1注。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,X,X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前四组选 100204000,
            ['id'=>'100204000', 'parent_id'=>'100200000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q4_zuxuan', 'name'=>'前四组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100204001', 'parent_id'=>'100204000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q4_zuxuan24', 'name'=>'组选24', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[833.33333]', 'prize_level_name'=>'["组选24"]', 'layout'=>'
{
    "desc": "从0-9中选择4个号码组成一注，顺序不限。",
    "help": "从0-9中任意选择4个号码组成一注，所选号码与开奖号码的万位、千位、百位、十位相同，且顺序不限，即为中奖。",
    "example": "投注方案：0568，开奖号码的四个数字只要包含0、5、6、8，即可中四星组选24。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 4
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100204002', 'parent_id'=>'100204000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q4_zuxuan12', 'name'=>'组选12', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1666.66666]', 'prize_level_name'=>'["组选12"]', 'layout'=>'
{
    "desc": "从“二重号”选择一个号码，“单号”中选择两个号码组成一注，顺序不限。",
    "help": "选择1个二重号码和2个单号号码组成一注，所选单号号码与开奖号码相同，且所选二重号码在开奖号码中出现了2次，即为中奖。",
    "example": "投注方案：二重号：8，单号：0、6，只要开奖的四个数字包括 0、6、8、8，即可中四星组选12。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100204003', 'parent_id'=>'100204000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q4_zuxuan6', 'name'=>'组选6', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3333.33333]', 'prize_level_name'=>'["组选6"]', 'layout'=>'
{
    "desc": "从“二重号”选择两个号码组成一注，顺序不限。",
    "help": "选择2个二重号码组成一注，所选的2个二重号码在开奖号码中分别出现了2次，即为中奖。",
    "example": "投注方案：二重号：6、8，只要开奖的四个数字排列为 6、6、8、8，即可中四星组选6。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100204004', 'parent_id'=>'100204000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q4_zuxuan4', 'name'=>'组选4', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[5000]', 'prize_level_name'=>'["组选4"]', 'layout'=>'
{
    "desc": "从“三重号”选择一个号码，“单号”中选择两个号码组成一注，顺序不限。",
    "help": "选择1个三重号码和1个单号号码组成一注，所选单号号码与开奖号码相同，且所选三重号码在开奖号码中出现了3次，即为中奖。",
    "example": "投注方案：三重号：8，单号：2，只要开奖的四个数字排列为 2、8、8、8，即可中四星组选4。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "三重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //后三码 100300000,
            ['id'=>'100300000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3', 'name'=>'后三码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //后三直选 100301000,
            ['id'=>'100301000', 'parent_id'=>'100300000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zhixuan', 'name'=>'后三直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100301001', 'parent_id'=>'100301000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 2}', 'lock_table_name'=>'lock_hszhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["后三码_直选复式"]', 'layout'=>'
{
    "desc": "从百、十、个位各选一个号码组成一注。",
    "help": "从百位、十位、个位中选择一个3位数号码组成一注，所选号码与开奖号码后3位相同，且顺序一致，即为中奖。",
    "example": "投注方案：345；<br />开奖号码：345，<br />即中后三直选",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,-,X,X,X",
    "code_sp": "",
    "if_random": 1,
    "random_cos": 3,
    "random_cos_value": "1|1|1"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100301002', 'parent_id'=>'100301000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 2}', 'lock_table_name'=>'lock_hszhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["后三码_直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码组成一注。",
    "help": "手动输入一个3位数号码组成一注，所选号码的百位、十位、个位与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：345； 开奖号码：345，即中后三直选",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100301003', 'parent_id'=>'100301000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zhixuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 1, "tag_bonus": "n3_zhixuanhezhi", "tag_check": "n3_zhixuanhezhi", "code_count": 3, "start_position": 2}', 'lock_table_name'=>'lock_hszhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["后三码_直选和值"]', 'layout'=>'
{
    "desc": "从0-27中任意选择1个或1个以上号码",
    "help": "所选数值等于开奖号码的百位、十位、个位三个数字相加之和，即为中奖。",
    "example": "投注方案：和值1；开奖号码后三位：001,010,100,即中后三直选",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "直选和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100301004', 'parent_id'=>'100301000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zhixuan_zuhe', 'name'=>'组合', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000,200,20]', 'prize_level_name'=>'["三星","二星","一星"]', 'layout'=>'
{
    "desc": "从百位、十位、个位各选一个号码组成三注。",
    "help": "从百位、十位、个位各选一个号码组成三注。",
    "example": "投注方案：6+7+8，该票共6元，由以下3注：678（三星）、78（二星）、8（一星）构成，开奖号码：后三位为678，即中三星、二星、一星各1注。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100301005', 'parent_id'=>'100301000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zhixuan_kuadu', 'name'=>'跨度', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选跨度"]', 'layout'=>'
{
    "desc": "从0-9中选择1个以上号码",
    "help": "从0-9中选择1个以上号码。",
    "example": "投注方案：跨度8，开奖号码：后三位 08X，其中X不等于9，后三位19X，其中X不等于0，即可中后三直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "跨度",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //后三组选 100302000,
            ['id'=>'100302000', 'parent_id'=>'100300000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zuxuan', 'name'=>'后三组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100302001', 'parent_id'=>'100302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zuxuan_zusan_fushi', 'name'=>'组三复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zusan", "tag_check": "n3_zusan", "code_count": 3, "start_position": 2}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'initNumberTypeZhuShanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选择2个数字组成两注，所选号码与开奖号码的百位、十位、个位相同，且顺序不限，即为中奖。",
    "example": "投注方案：5,8,8；开奖号码后三位：1个5，2个8 (顺序不限)，即中后三组选三。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组三",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100302002', 'parent_id'=>'100302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zuxuan_zusan_danshi', 'name'=>'组三单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码（三个数字中必须有二个数字相同）。",
    "help": "手动输入号码，至少输入1个三位数号码（三个数字中必须有二个数字相同）。",
    "example": "投注方案：001，开奖号码：后三位010（顺序不限），即可中后三组选三。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100302003', 'parent_id'=>'100302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zuxuan_zuliu_fushi', 'name'=>'组六复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zuliu", "tag_check": "n3_zuliu", "code_count": 3, "start_position": 2}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'initNumberTypeZhuLiuLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择3个或3个以上号码。",
    "help": "从0-9中任意选择3个号码组成一注，所选号码与开奖号码的百位、十位、个位相同，顺序不限，即为中奖。",
    "example": "投注方案：2,5,8；开奖号码后三位：1个2、1个5、1个8 (顺序不限)，即中后三组选六。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组六",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100302004', 'parent_id'=>'100302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zuxuan_zuliu_danshi', 'name'=>'组六单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码（三个数字完全不相同）。",
    "help": "手动输入号码，至少输入1个三位数号码（三个数字完全不相同）。",
    "example": "投注方案：123，开奖号码：后三位321（顺序不限），即可中后三组选六。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100302005', 'parent_id'=>'100302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_hunhe_zuxuan_danshi', 'name'=>'混合单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hunhezuxuan", "tag_check": "n3_hunhezuxuan", "code_count": 3, "start_position": 2}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666,333.33333]', 'prize_level_name'=>'["组三","组六"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码。",
    "help": "键盘手动输入购买号码，3个数字为一注，开奖号码的百位、十位、个位符合后三组三或组六均为中奖。",
    "example": "投注方案：分別投注(0,0,1),以及(1,2,3)，开奖号码后三位包括：(1)0,0,1，顺序不限，即中得组三；或者(2)1,2,3，顺序不限，即中得组六。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100302006', 'parent_id'=>'100302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zuxuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hezhi", "tag_check": "n3_hezhi", "code_count": 3, "start_position": 2}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666,333.33333]', 'prize_level_name'=>'["组三","组六"]', 'layout'=>'
{
    "desc": "从1-26中选择1个号码。",
    "help": "所选数值等于开奖号码百位、十位、个位三个数字相加之和，即为中奖。",
    "example": "投注方案：和值3；开奖号码后三位：(1)开出003号码，顺序不限，即中后三组选三；(2)开出012号码，顺序不限，即中后三组选六",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选和值",
                "no": "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100302007', 'parent_id'=>'100302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_zuxuan_baodan', 'name'=>'包胆', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666,333.33333]', 'prize_level_name'=>'["组三","组六"]', 'layout'=>'
{
    "desc": "从0-9中选择1个号码",
    "help": "从0-9中选择1个号码",
    "example": "投注方案：包胆3，开奖号码：后三位3XX或33X，即中后三组选三、后三位3XY，即中后三组选六。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "包胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //后三特殊 100303000,
            ['id'=>'100303000', 'parent_id'=>'100300000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_teshu', 'name'=>'后三特殊', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100303001', 'parent_id'=>'100303000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_teshu_hezhiweishu', 'name'=>'和值尾数', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["和值尾数"]', 'layout'=>'
{
    "desc": "从0-9中选择1个号码",
    "help": "从0-9中选择1个号码",
    "example": "投注方案：和值尾数8，开奖号码：后三位936，和值位数为8，即中和值尾数。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值尾数",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100303002', 'parent_id'=>'100303000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_teshu_teshuhao', 'name'=>'特殊号', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200,33.33333,7.407407]', 'prize_level_name'=>'["豹子","顺子","对子"]', 'layout'=>'
{
    "desc": "选择一个号码形态",
    "help": "选择一个号码形态",
    "example": "投注方案：豹子顺子对子，开奖号码：后三位888，即中豹子，后三位678，即中顺子，后三位558，即中对子。",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特殊号",
                "no": "豹子|顺子|对子",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前三码 100400000,
            ['id'=>'100400000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3', 'name'=>'前三码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前三直选 100401000,
            ['id'=>'100401000', 'parent_id'=>'100400000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zhixuan', 'name'=>'前三直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100401001', 'parent_id'=>'100401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选复式"]', 'layout'=>'
{
    "desc": "从万、千、百位各选一个号码组成一注。",
    "help": "从万位、千位、百位中选择一个3位数号码组成一注，所选号码与开奖号码的前3位相同，且顺序一致，即为中奖。",
    "example": "投注方案：345； 开奖号码：345，即中前三直选",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,X,-,-",
    "code_sp": "",
    "if_random": 1,
    "random_cos": 3,
    "random_cos_value": "1|1|1"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100401002', 'parent_id'=>'100401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码组成一注。",
    "help": "手动输入一个3位数号码组成一注，所选号码的万位、千位、百位与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：345； 开奖号码：345，即中前三直选",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100401003', 'parent_id'=>'100401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zhixuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 1, "tag_bonus": "n3_zhixuanhezhi", "tag_check": "n3_zhixuanhezhi", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选和值"]', 'layout'=>'
{
    "desc": "从0-27中任意选择1个或1个以上号码",
    "help": "所选数值等于开奖号码的万位、千位、百位三个数字相加之和，即为中奖。",
    "example": "投注方案：和值1；开奖号码前三位：001,010,100,即中前三直选",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "直选和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100401004', 'parent_id'=>'100401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zhixuan_zuhe', 'name'=>'组合', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000,200,20]', 'prize_level_name'=>'["三星","二星","一星"]', 'layout'=>'
{
    "desc": "从万位、千位、百位各选一个号码组成三注。",
    "help": "从万位、千位、百位各选一个号码组成三注。",
    "example": "投注方案：6+7+8，该票共6元，由以下3注：678（三星）、78（二星）、8（一星）构成，开奖号码：前三位为678，即中三星、二星、一星各1注。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100401005', 'parent_id'=>'100401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zhixuan_kuadu', 'name'=>'跨度', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选跨度"]', 'layout'=>'
{
    "desc": "从0-9中选择1个以上号码",
    "help": "从0-9中选择1个以上号码。",
    "example": "投注方案：跨度8，开奖号码：前三位 08X，其中X不等于9，前三位19X，其中X不等于0，即可中前三直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "跨度",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前三组选 100402000,
            ['id'=>'100402000', 'parent_id'=>'100400000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zuxuan', 'name'=>'前三组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100402001', 'parent_id'=>'100402000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zuxuan_zusan_fushi', 'name'=>'组三复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zusan", "tag_check": "n3_zusan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_qszuxuan', 'lock_init_function'=>'initNumberTypeZhuShanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选择2个数字组成两注，所选号码与开奖号码的万位、千位、百位相同，且顺序不限，即为中奖。",
    "example": "投注方案：5,8,8；开奖号码前三位：1个5，2个8 (顺序不限)，即中前三组选三。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组三",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100402002', 'parent_id'=>'100402000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zuxuan_zusan_danshi', 'name'=>'组三单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码（三个数字中必须有二个数字相同）。",
    "help": "手动输入号码，至少输入1个三位数号码（三个数字中必须有二个数字相同）。",
    "example": "投注方案：001，开奖号码：前三位010（顺序不限），即可中前三组选三。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100402003', 'parent_id'=>'100402000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zuxuan_zuliu_fushi', 'name'=>'组六复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zuliu", "tag_check": "n3_zuliu", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_qszuxuan', 'lock_init_function'=>'initNumberTypeZhuLiuLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择3个或3个以上号码。",
    "help": "从0-9中任意选择3个号码组成一注，所选号码与开奖号码的万位、千位、百位相同，顺序不限，即为中奖。",
    "example": "投注方案：2,5,8；开奖号码前三位：1个2、1个5、1个8 (顺序不限)，即中前三组选六。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组六",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100402004', 'parent_id'=>'100402000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zuxuan_zuliu_danshi', 'name'=>'组六单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码（三个数字完全不相同）。",
    "help": "手动输入号码，至少输入1个三位数号码（三个数字完全不相同）。",
    "example": "投注方案：123，开奖号码：前三位321（顺序不限），即可中前三组选六。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100402005', 'parent_id'=>'100402000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_hunhe_zuxuan_danshi', 'name'=>'混合单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hunhezuxuan", "tag_check": "n3_hunhezuxuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_qszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666, 333.33333]', 'prize_level_name'=>'["组三", "组六"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码。",
    "help": "键盘手动输入购买号码，3个数字为一注，开奖号码的万位、千位、百位符合后三组三或组六均为中奖。",
    "example": "投注方案：分別投注(0,0,1),以及(1,2,3)，开奖号码前三位包括：(1)0,0,1，顺序不限，即中得组三；或者(2)1,2,3，顺序不限，即中得组六。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100402006', 'parent_id'=>'100402000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zuxuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hezhi", "tag_check": "n3_hezhi", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_qszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666,333.33333]', 'prize_level_name'=>'["组三","组六"]', 'layout'=>'
{
    "desc": "从1-26中任意选择1个以上号码。",
    "help": "所选数值等于开奖号码万位、千位、百位三个数字相加之和，即为中奖。",
    "example": "投注方案：和值3；<br />开奖号码前三位：<br />(1)开出003号码，顺序不限，即中前三组选三；<br />(2)开出012号码，顺序不限，即中前三组选六",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选和值",
                "no": "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100402007', 'parent_id'=>'100402000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_zuxuan_baodan', 'name'=>'包胆', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666,333.33333]', 'prize_level_name'=>'["组三","组六"]', 'layout'=>'
{
    "desc": "从0-9中选择1个号码",
    "help": "从0-9中选择1个号码",
    "example": "投注方案：包胆3，开奖号码：前三位3XX或33X，即中前三组选三、前三位3XY，即中前三组选六。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "包胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前三特殊 100403000,
            ['id'=>'100403000', 'parent_id'=>'100400000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_teshu', 'name'=>'前三特殊', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100403001', 'parent_id'=>'100403000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_teshu_hezhiweishu', 'name'=>'和值尾数', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["和值尾数"]', 'layout'=>'
{
    "desc": "从0-9中选择1个号码",
    "help": "从0-9中选择1个号码",
    "example": "投注方案：和值尾数8，开奖号码：前三位936，和值位数为8，即中和值尾数。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值尾数",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100403002', 'parent_id'=>'100403000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_teshu_teshuhao', 'name'=>'特殊号', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200,33.33333,7.407407]', 'prize_level_name'=>'["豹子","顺子","对子"]', 'layout'=>'
{
    "desc": "选择一个号码形态",
    "help": "选择一个号码形态",
    "example": "投注方案：豹子顺子对子，开奖号码：前三位888，即中豹子，前三位678，即中顺子，前三位558，即中对子。",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特殊号",
                "no": "豹子|顺子|对子",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //中三码 100500000,
            ['id'=>'100500000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3', 'name'=>'中三码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //中三直选 100501000,
            ['id'=>'100501000', 'parent_id'=>'100500000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zhixuan', 'name'=>'中三直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100501001', 'parent_id'=>'100501000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 1}', 'lock_table_name'=>'lock_zszhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选复式"]', 'layout'=>'
{
    "desc": "从千、百、十位各选一个号码组成一注。",
    "help": "从千位、百位、十位中选择一个3位数号码组成一注，所选号码与开奖号码的中间3位相同，且顺序一致，即为中奖。",
    "example": "投注方案：456； 开奖号码：3456，即中中三直选",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,X,X,X,-",
    "code_sp": "",
    "if_random": 1,
    "random_cos": 3,
    "random_cos_value": "1|1|1"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100501002', 'parent_id'=>'100501000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 1}', 'lock_table_name'=>'lock_zszhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码组成一注。",
    "help": "手动输入一个3位数号码组成一注，所选号码的千位、百位、十位与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：345； 开奖号码：2345，即中中三直选",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100501003', 'parent_id'=>'100501000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zhixuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 1, "tag_bonus": "n3_zhixuanhezhi", "tag_check": "n3_zhixuanhezhi", "code_count": 3, "start_position": 1}', 'lock_table_name'=>'lock_zszhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选和值"]', 'layout'=>'
{
    "desc": "从0-27中任意选择1个或1个以上号码",
    "help": "所选数值等于开奖号码的千位、百位、十位三个数字相加之和，即为中奖。",
    "example": "投注方案：和值1；开奖号码中间三位：01001,00010,00100,即中中三直选",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "直选和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100501004', 'parent_id'=>'100501000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zhixuan_zuhe', 'name'=>'组合', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000,200,20]', 'prize_level_name'=>'["三星","二星","一星"]', 'layout'=>'
{
    "desc": "从千位、百位、十位各选一个号码组成三注。",
    "help": "从千位、百位、十位各选一个号码组成三注。",
    "example": "投注方案：6+7+8，该票共6元，由以下3注：678（三星）、78（二星）、8（一星）构成，开奖号码：中三位为678，即中三星、二星、一星各1注。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100501005', 'parent_id'=>'100501000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zhixuan_kuadu', 'name'=>'跨度', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选跨度"]', 'layout'=>'
{
    "desc": "从0-9中选择1个以上号码",
    "help": "从0-9中选择1个以上号码。",
    "example": "投注方案：跨度8，开奖号码：中三位 08X，其中X不等于9，中三位19X，其中X不等于0，即可中中三直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "跨度",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //中三组选 100502000,
            ['id'=>'100502000', 'parent_id'=>'100500000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zuxuan', 'name'=>'中三组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100502001', 'parent_id'=>'100502000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zuxuan_zusan_fushi', 'name'=>'组三复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zusan", "tag_check": "n3_zusan", "code_count": 3, "start_position": 1}', 'lock_table_name'=>'lock_zszuxuan', 'lock_init_function'=>'initNumberTypeZhuShanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选择2个数字组成两注，所选号码与开奖号码的千位、百位、十位相同，且顺序不限，即为中奖。",
    "example": "投注方案：5,8,8；开奖号码中间三位：1个5，2个8 (顺序不限)，即中中三组选三。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组三",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100502002', 'parent_id'=>'100502000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zuxuan_zusan_danshi', 'name'=>'组三单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码（三个数字中必须有二个数字相同）。",
    "help": "手动输入号码，至少输入1个三位数号码（三个数字中必须有二个数字相同）。",
    "example": "投注方案：001，开奖号码：中三位010（顺序不限），即可中中三组选三。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100502003', 'parent_id'=>'100502000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zuxuan_zuliu_fushi', 'name'=>'组六复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zuliu", "tag_check": "n3_zuliu", "code_count": 3, "start_position": 1}', 'lock_table_name'=>'lock_zszuxuan', 'lock_init_function'=>'initNumberTypeZhuLiuLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六"]', 'layout'=>'
{
    "desc": "从0-9中任意选择3个或3个以上号码。",
    "help": "从0-9中任意选择3个号码组成一注，所选号码与开奖号码的千位、百位、十位相同，顺序不限，即为中奖。",
    "example": "投注方案：2,5,8；开奖号码中间三位：1个2、1个5、1个8 (顺序不限)，即中中三组选六。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组六",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100502004', 'parent_id'=>'100502000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zuxuan_zuliu_danshi', 'name'=>'组六单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码（三个数字完全不相同）。",
    "help": "手动输入号码，至少输入1个三位数号码（三个数字完全不相同）。",
    "example": "投注方案：123，开奖号码：中三位321（顺序不限），即可中中三组选六。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100502005', 'parent_id'=>'100502000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_hunhe_zuxuan_danshi', 'name'=>'混合单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hunhezuxuan", "tag_check": "n3_hunhezuxuan", "code_count": 3, "start_position": 1}', 'lock_table_name'=>'lock_zszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666,333.33333]', 'prize_level_name'=>'["组三","组六"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码。",
    "help": "键盘手动输入购买号码，3个数字为一注，开奖号码的千位、百位、十位符合中三组三或组六均为中奖。",
    "example": "投注方案：分別投注(0,0,1),以及(1,2,3)，开奖号码中间三位包括：(1)0,0,1，顺序不限，即中得组三；或者(2)1,2,3，顺序不限，即中得组六。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100502006', 'parent_id'=>'100502000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zuxuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hezhi", "tag_check": "n3_hezhi", "code_count": 3, "start_position": 1}', 'lock_table_name'=>'lock_zszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666, 333.33333]', 'prize_level_name'=>'["组三", "组六"]', 'layout'=>'
{
    "desc": "从1-26中选择1个号码。",
    "help": "所选数值等于开奖号码千位、百位、十位三个数字相加之和，即为中奖。",
    "example": "投注方案：和值3；开奖号码中间三位：(1)开出003号码，顺序不限，即中中三组选三；(2)开出012号码，顺序不限，即中中三组选六",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100502007', 'parent_id'=>'100502000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_zuxuan_baodan', 'name'=>'包胆', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666,333.33333]', 'prize_level_name'=>'["组三","组六"]', 'layout'=>'
{
    "desc": "从0-9中选择1个号码",
    "help": "从0-9中选择1个号码",
    "example": "投注方案：包胆3，开奖号码：中三位3XX或33X，即中中三组选三、中三位3XY，即中中三组选六。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "包胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //中三特殊 100503000,
            ['id'=>'100503000', 'parent_id'=>'100500000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_teshu', 'name'=>'中三特殊', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100503001', 'parent_id'=>'100503000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_teshu_hezhiweishu', 'name'=>'和值尾数', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["和值尾数"]', 'layout'=>'
{
    "desc": "从0-9中选择1个号码",
    "help": "从0-9中选择1个号码",
    "example": "投注方案：和值尾数8，开奖号码：中三位936，和值位数为8，即中和值尾数。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值尾数",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100503002', 'parent_id'=>'100503000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_teshu_teshuhao', 'name'=>'特殊号', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_hszuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200,33.33333,7.407407]', 'prize_level_name'=>'["豹子","顺子","对子"]', 'layout'=>'
{
    "desc": "选择一个号码形态",
    "help": "选择一个号码形态",
    "example": "投注方案：豹子顺子对子，开奖号码：中三位888，即中豹子，中三位678，即中顺子，中三位558，即中对子。",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特殊号",
                "no": "豹子|顺子|对子",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码 100600000,
            ['id'=>'100600000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2', 'name'=>'二码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //后二直选 100601000,
            ['id'=>'100601000', 'parent_id'=>'100600000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zhixuan', 'name'=>'后二直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100601001', 'parent_id'=>'100601000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["后二直选复式"]', 'layout'=>'
{
    "desc": "从十、个位各选一个号码组成一注。",
    "help": "从十位、个位中选择一个2位数号码组成一注，所选号码与开奖号码的十位、个位相同，且顺序一致，即为中奖。",
    "example": "投注方案：58；开奖号码后二位：58，即中后二直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,-,-,X,X",
    "code_sp": "",
    "if_random": 1,
    "random_cos": 2,
    "random_cos_value": "1|1"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100601002', 'parent_id'=>'100601000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["后二直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入一个2位数号码组成一注，所选号码的十位、个位与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：58；开奖号码后二位：58，即中后二直选。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100601003', 'parent_id'=>'100601000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zhixuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 1, "tag_bonus": "n2_zhixuanhezhi", "tag_check": "n2_zhixuanhezhi", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["后二直选和值"]', 'layout'=>'
{
    "desc": "从0-18中任意选择1个或1个以上的和值号码。",
    "help": "所选数值等于开奖号码的十位、个位二个数字相加之和，即为中奖。",
    "example": "投注方案：和值1；开奖号码后二位：01,10，即中后二直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 9,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100601004', 'parent_id'=>'100601000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zhixuan_kuadu', 'name'=>'跨度', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["后二直选跨度"]', 'layout'=>'
{
    "desc": "从0-9中选择1个以上号码。",
    "help": "从0-9中选择1个以上号码。",
    "example": "投注方案：跨度9，开奖号码：后二位90或09，即可中后二直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "跨度",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //后二组选 100602000,
            ['id'=>'100602000', 'parent_id'=>'100600000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zuxuan', 'name'=>'后二组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100602001', 'parent_id'=>'100602000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zuxuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["后二组选复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选2个号码组成一注，所选号码与开奖号码的十位、个位相同，顺序不限，即中奖。",
    "example": "投注方案：12,58；开奖号码后二位：1个5，1个8 (顺序不限)，即中后二组选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100602002', 'parent_id'=>'100602000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zuxuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["后二组选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入一个2位数号码组成一注，所选号码的十位、个位与开奖号码相同，顺序不限，即为中奖。",
    "example": "投注方案：12,58；开奖号码后二位：1个5，1个8 (顺序不限)，即中后二组选。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100602003', 'parent_id'=>'100602000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zuxuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuanhezhi", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["后二组选和值"]', 'layout'=>'
{
    "desc": "从1-17中任意选择1个或1个以上的和值号码。",
    "help": "所选数值等于开奖号码的十位、个位二个数字相加之和（不含对子号），即为中奖。",
    "example": "投注方案：和值1；开奖号码后二位：10或01(顺序不限，不含对子号)，即中后二组选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 9,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100602004', 'parent_id'=>'100602000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_zuxuan_baodan', 'name'=>'包胆', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["前二组选包胆"]', 'layout'=>'
{
    "desc": "从0-9中选择1个包胆号码。",
    "help": "从0-9中选择1个包胆号码。",
    "example": "投注方案：包胆8，开奖号码：后二位8X，且X不等于8，即中后二组选包胆。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "包胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前二直选 100603000,
            ['id'=>'100603000', 'parent_id'=>'100600000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zhixuan', 'name'=>'前二直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100603001', 'parent_id'=>'100603000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["前二直选复式"]', 'layout'=>'
{
    "desc": "从万、千位各选一个号码组成一注。",
    "help": "从万位、千位中选择一个2位数号码组成一注，所选号码与开奖号码的前2位相同，且顺序一致，即为中奖。",
    "example": "投注方案：58；开奖号码前二位：58，即中前二直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": "",
    "if_random": 1,
    "random_cos": 2,
    "random_cos_value": "1|1"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100603002', 'parent_id'=>'100603000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["前二直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入一个2位数号码组成一注，所选号码的万位、千位与开奖号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：58；开奖号码前二位：58，即中前二直选。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100603003', 'parent_id'=>'100603000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zhixuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 1, "tag_bonus": "n2_zhixuanhezhi", "tag_check": "n2_zhixuanhezhi", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["前二直选和值"]', 'layout'=>'
{
    "desc": "从0-18中任意选择1个或1个以上的和值号码。",
    "help": "所选数值等于开奖号码的万位、千位二个数字相加之和，即为中奖。",
    "example": "投注方案：和值1；开奖号码前二位：01,10，即中前二直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 9,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100603004', 'parent_id'=>'100603000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zhixuan_kuadu', 'name'=>'跨度', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["前二直选跨度"]', 'layout'=>'
{
    "desc": "从0-9中选择1个以上号码。",
    "help": "从0-9中选择1个以上号码。",
    "example": "投注方案：跨度9，开奖号码：前二位90或09，即可中前二直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "跨度",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前二组选 100604000,
            ['id'=>'100604000', 'parent_id'=>'100600000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zuxuan', 'name'=>'前二组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100604001', 'parent_id'=>'100604000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zuxuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["前二组选复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选2个号码组成一注，所选号码与开奖号码的万位、千位相同，顺序不限，即中奖。",
    "example": "投注方案：12,58；开奖号码前二位：1个5，1个8 (顺序不限)，即中前二组选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100604002', 'parent_id'=>'100604000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zuxuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["前二组选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入一个2位数号码组成一注，所选号码的万位、千位与开奖号码相同，顺序不限，即为中奖。",
    "example": "投注方案：12,58；开奖号码前二位：1个5，1个8 (顺序不限)，即中前二组选。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100604003', 'parent_id'=>'100604000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zuxuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuanhezhi", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["前二组选和值"]', 'layout'=>'
{
    "desc": "从1-17中任意选择1个或1个以上号码",
    "help": "所选数值等于开奖号码的万位、千位二个数字相加之和（不含对子号），即为中奖。",
    "example": "投注方案：和值1；开奖号码前二位：10或01 (顺序不限，不含对子号)，即中前二组选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 9,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100604004', 'parent_id'=>'100604000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_zuxuan_baodan', 'name'=>'包胆', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["前二组选包胆"]', 'layout'=>'
{
    "desc": "从0-9中选择1个包胆号码。",
    "help": "从0-9中选择1个包胆号码。",
    "example": "投注方案：包胆8，开奖号码：前二位8X，且X不等于8，即中前二组选包胆。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "包胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 100700000,
            ['id'=>'100700000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_dingweidan_01', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 100701000,
            ['id'=>'100701000', 'parent_id'=>'100700000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_dingweidan_02', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100701001', 'parent_id'=>'100701000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_dingweidan', 'name'=>'定位胆', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name'=>'lock_dwd', 'lock_init_function'=>'initNumberTypeYiWeiLock,1-5', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["定位胆"]', 'layout'=>'
{
    "desc": "在万位，千位，百位，十位，个位任意位置上任意选择1个或1个以上号码。",
    "help": "从万位、千位、百位、十位、个位任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码万位：1，即中定位胆万位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 4,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100701002', 'parent_id'=>'100701000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_dingweidan_wanwei', 'name'=>'万位', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name'=>'lock_dwd', 'lock_init_function'=>'initNumberTypeYiWeiLock,1-5', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["定位胆"]', 'layout'=>'
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码万位：1，即中定位胆万位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100701003', 'parent_id'=>'100701000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_dingweidan_qianwei', 'name'=>'千位', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name'=>'lock_dwd', 'lock_init_function'=>'initNumberTypeYiWeiLock,1-5', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["定位胆"]', 'layout'=>'
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码千位：1，即中定位胆千位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100701004', 'parent_id'=>'100701000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_dingweidan_baiwei', 'name'=>'百位', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name'=>'lock_dwd', 'lock_init_function'=>'initNumberTypeYiWeiLock,1-5', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["定位胆"]', 'layout'=>'
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码百位：1，即中定位胆百位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100701005', 'parent_id'=>'100701000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_dingweidan_shiwei', 'name'=>'十位', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name'=>'lock_dwd', 'lock_init_function'=>'initNumberTypeYiWeiLock,1-5', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["定位胆"]', 'layout'=>'
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码十位：1，即中定位胆十位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100701006', 'parent_id'=>'100701000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_dingweidan_gewei', 'name'=>'个位', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name'=>'lock_dwd', 'lock_init_function'=>'initNumberTypeYiWeiLock,1-5', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["定位胆"]', 'layout'=>'
{
    "desc": "任意选择1个或1个以上号码。",
    "help": "任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案：1；开奖号码个位：1，即中定位胆个位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //不定位 100800000,
            ['id'=>'100800000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_budingwei', 'name'=>'不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三星不定位 100801000,
            ['id'=>'100801000', 'parent_id'=>'100800000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_budingwei', 'name'=>'三星不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100801001', 'parent_id'=>'100801000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_budingwei_yima', 'name'=>'后三一码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_1mbudingwei", "tag_check": "n1_budingwei", "code_count": 3, "start_position": 2}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.38007]', 'prize_level_name'=>'["奖金"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个以上号码。",
    "help": "从0-9中选择1个号码，每注由1个号码组成，只要开奖号码的百位、十位、个位中包含所选号码，即为中奖。",
    "example": "投注方案：1；开奖号码后三位：至少出现1个1，即中后三一码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100801002', 'parent_id'=>'100801000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_budingwei_erma', 'name'=>'后三二码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_2mbudingwei", "tag_check": "n2_budingwei", "code_count": 3, "start_position": 2}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[37.03703]', 'prize_level_name'=>'["后三二码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个以上号码。",
    "help": "从0-9中选择2个号码，每注由2个不同的号码组成，开奖号码的百位、十位、个位中同时包含所选的2个号码，即为中奖。",
    "example": "投注方案：1,2；开奖号码后三位：至少出现1和2各1个，即中后三二码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100801003', 'parent_id'=>'100801000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_budingwei_yima', 'name'=>'前三一码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_1mbudingwei", "tag_check": "n1_budingwei", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_qsbudingwei', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.38007]', 'prize_level_name'=>'["奖金"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个以上号码。",
    "help": "从0-9中选择1个号码，每注由1个号码组成，只要开奖号码的万位、千位、百位中包含所选号码，即为中奖。",
    "example": "投注方案：1；开奖号码前三位：至少出现1个1，即中前三一码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100801004', 'parent_id'=>'100801000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_budingwei_erma', 'name'=>'前三二码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_2mbudingwei", "tag_check": "n2_budingwei", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[37.03703]', 'prize_level_name'=>'["前三二码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个以上号码。",
    "help": "从0-9中选择2个号码，每注由2个不同的号码组成，开奖号码的万位、千位、百位中同时包含所选的2个号码，即为中奖。",
    "example": "投注方案：1,2；开奖号码前三位：至少出现1和2各1个，即中前三二码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100801005', 'parent_id'=>'100801000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_budingwei_yima', 'name'=>'中三一码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_1mbudingwei", "tag_check": "n1_budingwei", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_qsbudingwei', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.38007]', 'prize_level_name'=>'["中三一码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个以上号码。",
    "help": "从0-9中选择1个号码，每注由1个号码组成，只要开奖号码的千位、百位、十位中包含所选号码，即为中奖。",
    "example": "投注方案：1；开奖号码中三位：至少出现1个1，即中中三一码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100801006', 'parent_id'=>'100801000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_z3_budingwei_erma', 'name'=>'中三二码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_2mbudingwei", "tag_check": "n2_budingwei", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[37.03703]', 'prize_level_name'=>'["中三二码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个以上号码。",
    "help": "从0-9中选择2个号码，每注由2个不同的号码组成，开奖号码的千位、百位、十位中同时包含所选的2个号码，即为中奖。",
    "example": "投注方案：1,2；开奖号码中三位：至少出现1和2各1个，即中中三二码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //四星不定位 100802000,
            ['id'=>'100802000', 'parent_id'=>'100800000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_budingwei', 'name'=>'四星不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100802001', 'parent_id'=>'100802000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_budingwei_yima', 'name'=>'后四一码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n4_yimabudingwei", "tag_check": "n4_yimabudingwei", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[5.815644]', 'prize_level_name'=>'["一码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个以上号码。",
    "help": "从0-9中选择1个号码，每注由1个号码组成，只要开奖号码的千位、百位、十位、个位中包含所选号码，即为中奖。",
    "example": "投注方案：1；开奖号码后四位：至少出现1个1，即中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100802002', 'parent_id'=>'100802000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h4_budingwei_erma', 'name'=>'后四二码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n4_ermabudingwei", "tag_check": "n4_ermabudingwei", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20.533881]', 'prize_level_name'=>'["二码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个以上号码。",
    "help": "从0-9中选择2个号码，每注由2个不同的号码组成，开奖号码的千位、百位、十位、个位中同时包含所选的2个号码，即为中奖。",
    "example": "投注方案：1,2；开奖号码后四位：至少出现1和2各1个，即中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //五星不定位 100803000,
            ['id'=>'100803000', 'parent_id'=>'100800000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_budingwei', 'name'=>'五星不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100803001', 'parent_id'=>'100803000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_budingwei_yima', 'name'=>'五星一码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_yimabudingwei", "tag_check": "n5_yimabudingwei", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.88388]', 'prize_level_name'=>'["一码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个以上号码。",
    "help": "从0-9中选择1个号码，每注由1个号码组成，只要开奖号码包含所选号码，即为中奖。",
    "example": "投注方案：1；开奖号码：至少出现1个1，即中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100803002', 'parent_id'=>'100803000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_budingwei_erma', 'name'=>'五星二码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_ermabudingwei", "tag_check": "n5_ermabudingwei", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[13.633265]', 'prize_level_name'=>'["二码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个以上号码。",
    "help": "从0-9中选择2个号码，每注由2个不同的号码组成，开奖号码中同时包含所选的2个号码，即为中奖。",
    "example": "投注方案：1,2；开奖号码至少出现1和2各1个，即中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100803003', 'parent_id'=>'100803000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_budingwei_sanma', 'name'=>'五星三码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n5_sanmabudingwei", "tag_check": "n5_sanmabudingwei", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[45.977011]', 'prize_level_name'=>'["三码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择3个以上号码。",
    "help": "从0-9中选择3个号码，每注由3个不同的号码组成，开奖号码中同时包含所选的3个号码，即为中奖。",
    "example": "投注方案：1,2,3；开奖号码至少出现1、2、3各1个，即中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定胆",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //大小单双 100900000,
            ['id'=>'100900000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_daxiaodanshuang_01', 'name'=>'大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //大小单双 100901000,
            ['id'=>'100901000', 'parent_id'=>'100900000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_daxiaodanshuang_02', 'name'=>'大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100901001', 'parent_id'=>'100901000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h2_daxiaodanshuang', 'name'=>'后二大小单双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_dxds", "tag_check": "n2_dxds", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'lock_hdaxiaodanshuang', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[8]', 'prize_level_name'=>'["后二大小单双"]', 'layout'=>'
{
    "desc": "从十位、个位中的“大、小、单、双”中至少各选一个组成一注。",
    "help": "对十位和个位的“大（56789）小（01234）、单（13579）双（02468）”形态进行购买，所选号码的位置、形态与开奖号码的位置、形态相同，即为中奖。",
    "example": "投注方案：大单；开奖号码十位与个位：大单，即中后二大小单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "十位",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "大|小|单|双",
                "place": 1,
                "cols": 1
            }
        ]
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100901002', 'parent_id'=>'100901000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q2_daxiaodanshuang', 'name'=>'前二大小单双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_dxds", "tag_check": "n2_dxds", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qdaxiaodanshuang', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[8]', 'prize_level_name'=>'["前二大小单双"]', 'layout'=>'
{
    "desc": "从万位、千位中的“大、小、单、双”中至少各选一个组成一注。",
    "help": "对万位、千位的“大（56789）小（01234）、单（13579）双（02468）”形态进行购买，所选号码的位置、形态与开奖号码的位置、形态相同，即为中奖。",
    "example": "投注方案：小双；开奖号码万位与千位：小双，即中前二大小单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "万位",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "大|小|单|双",
                "place": 1,
                "cols": 1
            }
        ]
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100901003', 'parent_id'=>'100901000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_q3_daxiaodanshuang', 'name'=>'前三大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qdaxiaodanshuang', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[16]', 'prize_level_name'=>'["前三大小单双"]', 'layout'=>'
{
    "desc": "从万位、千位、百位中的“大、小、单、双”中至少各选一个组成一注。",
    "help": "从万位、千位、百位中的“大、小、单、双”中至少各选一个组成一注。",
    "example": "投注方案：小双小，开奖号码：万位、千位、百位“小双小”，即中前三大小单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "万位",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "大|小|单|双",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "大|小|单|双",
                "place": 2,
                "cols": 1
            }
        ]
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100901004', 'parent_id'=>'100901000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_h3_daxiaodanshuang', 'name'=>'后三大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qdaxiaodanshuang', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[16]', 'prize_level_name'=>'["后三大小单双"]', 'layout'=>'
{
    "desc": "从百位、十位、个位中的“大、小、单、双”中至少各选一个组成一注。",
    "help": "从百位、十位、个位中的“大、小、单、双”中至少各选一个组成一注。",
    "example": "投注方案：大单大，开奖号码：百位、十位、个位“大单大”，即中后三大小单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "百位",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "大|小|单|双",
                "place": 1,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "大|小|单|双",
                "place": 2,
                "cols": 1
            }
        ]
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'100901005', 'parent_id'=>'100901000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_n5_hezhi_daxiaodanshuang', 'name'=>'总和大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qdaxiaodanshuang', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["总和大小单双"]', 'layout'=>'
{
    "desc": "总和为0~22为小，23~45为大；总和尾数单为单，总和尾数双数为双。",
    "help": "从“大、小、单、双”中至少选一个组成一注。",
    "example": "投注方案：小，开奖号码：12345，即中和值小。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "总和大小单双",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选 101100000,
            ['id'=>'101100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx', 'name'=>'任选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任二直选 101101000,
            ['id'=>'101101000', 'parent_id'=>'101100000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx2_zhixuan', 'name'=>'任二直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101101001', 'parent_id'=>'101101000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx2_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "position": "0,1", "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_rx2_wq', 'lock_init_function'=>'initNumberTypeRXTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["直选复式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少两位上各选1个号码组成1注",
    "help": "从任意两个以上的位置中选择一个号码，所选号码与开奖号码对应位置出现的号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：万位1，百位2；<br />开奖号码：13245，<br />即中任选二直选",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 4,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101101002', 'parent_id'=>'101101000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx2_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "position": "0,1", "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_rx2_wq', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["直选单式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择两个位置,至少手动输入一个两位数的号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择两个位置,至少手动输入一个两位数的号码构成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：位置选择万位、百位，输入号码58,开奖号码：51812，即中任二直选(单式)。",
    "select_area": {
        "type": "input",
        "select": "true"
    },
    "show_str": "X",
    "code_sp": ",",
    "default_position": "00011"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101101003', 'parent_id'=>'101101000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx2_zhixuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 1, "position": "0,2", "tag_bonus": "n2_zhixuanhezhi", "tag_check": "n2_zhixuanhezhi", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_rx2_wb', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["直选和值"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择两个位置,至少选择一个和值号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择两个位置,至少选择一个和值号码构成一注，所选号码与开奖号码的和值相同，即为中奖。",
    "example": "投注方案：位置选择万位、百位，选择和值号码13<br>开奖号码：51812，即中任二直选(单式)。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 9,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X",
    "code_sp": ",",
    "default_position": "00011"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任二组选 101102000,
            ['id'=>'101102000', 'parent_id'=>'101100000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx2_zuxuan', 'name'=>'任二组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101102001', 'parent_id'=>'101102000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx2_zuxuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "position": "0,1", "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_rx2_wq', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["组选复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选2个号码组成一注，所选号码出现在开奖号的对应位上，顺序不限，即中奖。",
    "example": "投注方案：万位5,百8；开奖号码位5*8**或者8*5** (顺序不限)，即中任选二组选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X",
    "code_sp": ",",
    "default_position": "00011"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101102002', 'parent_id'=>'101102000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx2_zuxuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "position": "0,1", "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_rx2_wq', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["组选单式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择两个位置,至少手动输入一个两位数的号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择两个位置,至少手动输入一个两位数的号码构成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序不限，即为中奖。",
    "example": "投注方案：位置选择万位、百位，输入号码85<br>开奖号码：51812或者81512，即中任二组选(单式)。",
    "select_area": {
        "type": "input",
        "select": "true"
    },
    "show_str": "X",
    "code_sp": ",",
    "default_position": "00011"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101102003', 'parent_id'=>'101102000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx2_zuxuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 0, "position": "0,1", "tag_bonus": "n2_zuxuanhezhi", "tag_check": "n2_zuxuanhezhi", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_rx2_wq', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["组选和值"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择两个位置,至少选择一个和值号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择两个位置,至少选择一个和值号码构成一注，所选号码与开奖号码的和值相同，即为中奖。",
    "example": "投注方案：位置选择万位、百位，选择和值号码13<br>开奖号码：51812，即中任二组选和值。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 9,
        "select": "true",
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ",",
    "default_position": "00011"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任三直选 101103000,
            ['id'=>'101103000', 'parent_id'=>'101100000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zhixuan', 'name'=>'任三直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101103001', 'parent_id'=>'101103000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "position": "2,3,4", "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_rx3_bsg', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选复式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少三位上各选1个号码组成一注。",
    "help": "从万位、千位、百位、十位、个位中至少三位上各选1个号码组成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：万位5，百8,个位2<br/>开奖号码：51812，即中任三直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 4,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101103002', 'parent_id'=>'101103000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "position": "2,3,4", "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_rx3_bsg', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选单式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择三个位置,至少手动输入一个三位数的号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择三个位置,至少手动输入一个三位数的号码构成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：位置选择万位、百位,个位，输入号码582<br>开奖号码：51812，即中任三直选(单式)。",
    "select_area": {
        "type": "input",
        "select": "true"
    },
    "show_str": "X",
    "code_sp": " ",
    "default_position": "00111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101103003', 'parent_id'=>'101103000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zhixuan_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 1, "position": "2,3,4", "tag_bonus": "n3_zhixuanhezhi", "tag_check": "n3_zhixuanhezhi", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_rx3_bsg', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选和值"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择三个位置,至少选择一个和值号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择三个位置,至少选择一个和值号码构成一注，所选号码与开奖号码的和值相同，即为中奖。",
    "example": "投注方案：位置选择万位、百位、个位，选择和值号码15<br>开奖号码：51812，即中任二直选(单式)。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X",
    "code_sp": ",",
    "default_position": "00111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任三组选 101104000,
            ['id'=>'101104000', 'parent_id'=>'101100000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zuxuan', 'name'=>'任三组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101104001', 'parent_id'=>'101104000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zuxuan_zusan_fushi', 'name'=>'组三复式', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2", "tag_bonus": "n3_zusan", "tag_check": "n3_zusan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_rx3_wqb', 'lock_init_function'=>'initNumberTypeZhuShanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三复式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择三个位置,号码区至少选择两个号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择三个位置,号码区至少选择两个号码构成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序不限，即为中奖。",
    "example": "投注方案：选择位置万位、十位、个位,选择号码12<br>开奖号码：11812，即中任三组三。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组三",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X",
    "code_sp": "",
    "default_position": "00111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101104002', 'parent_id'=>'101104000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zuxuan_zusan_danshi', 'name'=>'组三单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_rx3_wqb', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三单式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择三个位置，至少手动输入三个号码构成一注（三个数字中必须有二个数字相同）。",
    "help": "三个数字中必须有二个数字相同，所选号码与开奖号码的百位、十位、个位相同，顺序不限，即为中奖。",
    "example": "投注方案：位置选择万位、十位、个位，输入号码112，开奖号码：11812，即中任三组选。",
    "select_area": {
        "type": "input",
        "select": "true"
    },
    "show_str": "X",
    "code_sp": " ",
    "default_position": "00111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101104003', 'parent_id'=>'101104000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zuxuan_zuliu_fushi', 'name'=>'组六复式', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2", "tag_bonus": "n3_zuliu", "tag_check": "n3_zuliu", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_rx3_wqb', 'lock_init_function'=>'initNumberTypeZhuLiuLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六复式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择三个位置,号码区至少选择三个号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择三个位置,号码区至少选择三个号码构成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序不限，即为中奖。",
    "example": "投注方案：选择位置万位、十位、个位,选择号码512<br>开奖号码：51812，即中任三组六。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组六",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X",
    "code_sp": "",
    "default_position": "00111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101104004', 'parent_id'=>'101104000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zuxuan_zuliu_danshi', 'name'=>'组六单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_rx3_wqb', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六单式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择三个位置，至少手动输入三个互不相同号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择三个位置，至少手动输入三个互不相同号码构成一注。",
    "example": "投注方案：位置选择万位、十位、个位，输入号码512，开奖号码：51812，即中任三组六。",
    "select_area": {
        "type": "input",
        "select": "true"
    },
    "show_str": "X",
    "code_sp": " ",
    "default_position": "00111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101104005', 'parent_id'=>'101104000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_hunhe_zuxuan_danshi', 'name'=>'混合组选单式', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2", "tag_bonus": "n3_hunhezuxuan", "tag_check": "n3_hunhezuxuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_rx3_wqb', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666, 333.33333]', 'prize_level_name'=>'["组三", "组六"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择三个位置,手动至少输入三个号码构成一注(不包含豹子号)。",
    "help": "从万位、千位、百位、十位、个位中至少选择三个位置,手动至少输入三个号码构成一注(不包含豹子号)，所选号码与开奖号码的指定位置上的号码相同，且顺序不限，即为中奖。",
    "example": "投注方案：选择位置万位、十位、个位,输入号码512<br>开奖号码：51812，即中任三混合组选。",
    "select_area": {
        "type": "input",
        "select": "true"
    },
    "show_str": "X",
    "code_sp": " ",
    "default_position": "00111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101104006', 'parent_id'=>'101104000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx3_zuxuan_hezhi', 'name'=>'组选和值', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2", "tag_bonus": "n3_hezhi", "tag_check": "n3_hezhi", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_rx3_wqb', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666, 333.33333]', 'prize_level_name'=>'["组三", "组六"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择三个位置,至少选择一个和值号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择三个位置,至少选择一个和值号码构成一注，所选号码与开奖号码的和值(不包含豹子号)相同，即为中奖。",
    "example": "投注方案：选择位置万位、十位、个位,选择和值号码8<br>开奖号码：51812，即中任三组选和值。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X",
    "code_sp": ",",
    "default_position": "00111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任四直选 101105000,
            ['id'=>'101105000', 'parent_id'=>'101100000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx4_zhixuan', 'name'=>'任四直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101105001', 'parent_id'=>'101105000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx4_zhixuan_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2,3", "tag_bonus": "n4_zhixuan", "tag_check": "n4_zhixuan", "code_count": 4, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000]', 'prize_level_name'=>'["直选复式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少四位上各选1个号码组成一注。",
    "help": "从万位、千位、百位、十位、个位中至少四位上各选1个号码组成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：万位5，千位1,百位8,十位1<br>开奖号码：51812，即中任四直选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "万位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "千位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 3,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 4,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101105002', 'parent_id'=>'101105000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx4_zhixuan_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2,3", "tag_bonus": "n4_zhixuan", "tag_check": "n4_zhixuan", "code_count": 4, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000]', 'prize_level_name'=>'["直选单式"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择四个位置,至少手动输入一个四位数的号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择四个位置,至少手动输入一个四位数的号码构成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序一致，即为中奖。",
    "example": "投注方案：选择万位、千位、百位、十位，输入号码5181<br>开奖号码：51812，即中任四直选(单式)。",
    "select_area": {
        "type": "input",
        "select": "true"
    },
    "show_str": "X",
    "code_sp": "",
    "default_position": "01111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任四组选 101106000,
            ['id'=>'101106000', 'parent_id'=>'101100000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx4_zuxuan', 'name'=>'任四组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101106001', 'parent_id'=>'101106000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx4_zuxuan24', 'name'=>'组选24', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2,3", "tag_bonus": "n4_zuxuan", "tag_check": "n4_zuxuan24", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[833.33333]', 'prize_level_name'=>'["组选24"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择四个位置,号码区至少选择四个号码构成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择四个位置,号码区至少选择四个号码构成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序不限，即为中奖。",
    "example": "投注方案：位置选择千位、百位、十位、个位,号码选择0568<br>开奖号码：10568(指定位置号码顺序不限)，即可中任四组选24。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选24",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 4
            }
        ],
        "big_index": 5,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X",
    "code_sp": ",",
    "default_position": "01111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101106002', 'parent_id'=>'101106000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx4_zuxuan12', 'name'=>'组选12', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2,3", "tag_bonus": "n4_zuxuan", "tag_check": "n4_zuxuan12", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1666.66666]', 'prize_level_name'=>'["组选12"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择四个位置,从“二重号”选择一个号码，“单号”中选择两个号码组成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择四个位置,从“二重号”选择一个号码，“单号”中选择两个号码组成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序不限，即为中奖。",
    "example": "投注方案：位置选择千位、百位、十位、个位,二重号：8；单号：06<br>开奖号码：10688(指定位置号码顺序不限)，即可中任四组选12。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 5,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X,X",
    "code_sp": "",
    "default_position": "01111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101106003', 'parent_id'=>'101106000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx4_zuxuan6', 'name'=>'组选6', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2,4", "tag_bonus": "n4_zuxuan", "tag_check": "n4_zuxuan6", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3333.33333]', 'prize_level_name'=>'["组选6"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择四个位置,从“二重号”中选择两个号码组成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择四个位置,从“二重号”中选择两个号码组成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序不限，即为中奖。",
    "example": "投注方案：位置选择千位、百位、十位、个位,二重号：28<br>开奖号码：12288(指定位置号码顺序不限)，即可中任四组选6。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 5,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X",
    "code_sp": ",",
    "default_position": "01111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101106004', 'parent_id'=>'101106000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_rx4_zuxuan4', 'name'=>'组选4', 'draw_rule'=>'{"is_sum": 0, "position": "0,1,2,3", "tag_bonus": "n4_zuxuan", "tag_check": "n4_zuxuan4", "code_count": 4, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[5000]', 'prize_level_name'=>'["组选4"]', 'layout'=>'
{
    "desc": "从万位、千位、百位、十位、个位中至少选择四个位置,从“三重号”中选择一个号码，“单号”中选择一个号码组成一注。",
    "help": "从万位、千位、百位、十位、个位中至少选择四个位置,从“三重号”中选择一个号码，“单号”中选择一个号码组成一注，所选号码与开奖号码的指定位置上的号码相同，且顺序不限，即为中奖。",
    "example": "投注方案：位置选择千位、百位、十位、个位,三重号：8；单号：2<br>开奖号码：18828(指定位置号码顺序不限)，即可中任四组选4。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "三重号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "单　号",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 5,
        "is_button": true,
        "select": "true"
    },
    "show_str": "X,X",
    "code_sp": "",
    "default_position": "01111"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎 101200000,
            ['id'=>'101200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_01', 'name'=>'龙虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和 101201000,
            ['id'=>'101201000', 'parent_id'=>'101200000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_02', 'name'=>'龙虎和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201001', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_wanqian', 'name'=>'万千', 'draw_rule'=>'{"is_sum": 0, "position": "0,1", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从万位、千位上选择一个形态组成一注。",
    "help": "根据万位、千位号码数值比大小，万位号码大于千位号码为龙，万位号码小于千位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于千位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201002', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_wanbai', 'name'=>'万百', 'draw_rule'=>'{"is_sum": 0, "position": "0,2", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从万位、百位上选择一个形态组成一注。",
    "help": "根据万位、百位号码数值比大小，万位号码大于百位号码为龙，万位号码小于百位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于百位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201003', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_wanshi', 'name'=>'万十', 'draw_rule'=>'{"is_sum": 0, "position": "0,3", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,3', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从万位、十位上选择一个形态组成一注。",
    "help": "根据万位、十位号码数值比大小，万位号码大于十位号码为龙，万位号码小于十位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于十位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201004', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_wange', 'name'=>'万个', 'draw_rule'=>'{"is_sum": 0, "position": "0,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,4', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从万位、个位上选择一个形态组成一注。",
    "help": "根据万位、个位号码数值比大小，万位号码大于个位号码为龙，万位号码小于个位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201005', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_qianbai', 'name'=>'千百', 'draw_rule'=>'{"is_sum": 0, "position": "1,2", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,5', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从千位、百位上选择一个形态组成一注。",
    "help": "根据千位、百位号码数值比大小，千位号码大于百位号码为龙，千位号码小于百位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码千位大于百位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201006', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_qianshi', 'name'=>'千十', 'draw_rule'=>'{"is_sum": 0, "position": "1,3", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,6', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从千位、十位上选择一个形态组成一注。",
    "help": "根据千位、十位号码数值比大小，千位号码大于十位号码为龙，千位号码小于十位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码千位大于十位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201007', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_qiange', 'name'=>'千个', 'draw_rule'=>'{"is_sum": 0, "position": "1,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,7', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从千位、个位上选择一个形态组成一注。",
    "help": "根据千位、个位号码数值比大小，千位号码大于个位号码为龙，千位号码小于个位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码千位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201008', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_baishi', 'name'=>'百十', 'draw_rule'=>'{"is_sum": 0, "position": "2,3", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 2}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,8', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从百位、十位上选择一个形态组成一注。",
    "help": "根据百位、十位号码数值比大小，百位号码大于十位号码为龙，百位号码小于十位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码百位大于十位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201009', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_baige', 'name'=>'百个', 'draw_rule'=>'{"is_sum": 0, "position": "2,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 2}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,9', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从百位、个位上选择一个形态组成一注。",
    "help": "根据百位、个位号码数值比大小，百位号码大于个位号码为龙，百位号码小于个位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码百位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101201010', 'parent_id'=>'101201000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_shige', 'name'=>'十个', 'draw_rule'=>'{"is_sum": 0, "position": "3,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,10', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4444444, 4.4444444, 20]', 'prize_level_name'=>'["龙", "虎", "和"]', 'layout'=>'
{
    "desc": "从十位、个位上选择一个形态组成一注。",
    "help": "根据十位、个位号码数值比大小，十位号码大于个位号码为龙，十位号码小于个位号码为虎，号码相同则为和。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码十位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎和",
                "no": "龙|虎|和",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和值 101202000,
            ['id'=>'101202000', 'parent_id'=>'101200000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi', 'name'=>'龙虎和值', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202001', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_wanqian', 'name'=>'万千', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "万千总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "万千总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中万千龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202002', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_wanbai', 'name'=>'万百', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "万百总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "万百总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中万百龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202003', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_wanshi', 'name'=>'万十', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "万十总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "万十总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中万十龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202004', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_wange', 'name'=>'万个', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "万个总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "万个总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中万个龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202005', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_qianbai', 'name'=>'千百', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "千百总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "千百总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中千百龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202006', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_qianshi', 'name'=>'千十', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "千十总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "千十总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中千十龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202007', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_qiange', 'name'=>'千个', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "千个总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "千个总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中千个龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202008', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_baishi', 'name'=>'百十', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "百十总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "百十总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中百十龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202009', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_baige', 'name'=>'百个', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "百个总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "百个总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中百个龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101202010', 'parent_id'=>'101202000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_lhh_hezhi_shige', 'name'=>'十个', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "十个总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "help": "十个总和的个位数1,3,5,7,9时为“单”，0,2,4,6,8时为“双”；5~9为“大”，0~4时为“小”。",
    "example": "投注方案：大，开奖号码：54334，即中十个龙虎和值大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //棋牌 101300000,
            ['id'=>'101300000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_dn_01', 'name'=>'棋牌', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //百家乐 101301000,
            ['id'=>'101301000', 'parent_id'=>'101300000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_bjl', 'name'=>'百家乐', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101301001', 'parent_id'=>'101301000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_bjl_zxh', 'name'=>'庄闲和', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "ssc_zxh", "tag_check": "ssc_zxh", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.34687,4.47053,19.48818]', 'prize_level_name'=>'["庄","闲","和"]', 'layout'=>'
{
    "desc": "选择一个形态组成一注。",
    "help": "庄闲对比，谁最接近9点即为胜方，而相同点数即和局。",
    "example": "投注方案：庄，开奖号码：90125，庄家为9点，闲家为7点，即中庄",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "庄闲和",
                "no": "庄|闲|和",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101301002', 'parent_id'=>'101301000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_bjl_zxd', 'name'=>'庄闲对', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "ssc_zxd", "tag_check": "ssc_zxd", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["庄闲对"]', 'layout'=>'
{
    "desc": "选择一个形态组成一注。",
    "help": "两家首发(第一轮)的两张牌为对子，不含第三张。",
    "example": "投注方案：庄对，开奖号码：55842，投庄对则中奖，投闲对不中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "庄闲对",
                "no": "庄对|闲对",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101301003', 'parent_id'=>'101301000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_bjl_dxdszh', 'name'=>'大小单双质合', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "ssc_dxdszh", "tag_check": "ssc_dxdszh", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.04414,5.8309,3.8835,4.12371,4.5977,3.53982,3.0303,5.88235,4,4,4.7619,3.44828]', 'prize_level_name'=>'["庄大","庄小","庄单","庄双","庄质","庄合","闲大","闲小","闲单","闲双","闲质","闲合"]', 'layout'=>'
{
    "desc": "选择一个形态组成一注。",
    "help": "以庄闲的点数来进行判断结果。 0~4为小，5~9为大; 1、3、5、7、9为单，0、2、4、6、8为双; 1、2、3、5、7为质数，0、4、6、8、9为合数。",
    "example": "投注方案：庄大，开奖号码：90123，即中庄大。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "庄",
                "no": "庄大|庄小|庄单|庄双|庄质|庄合",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            },
            {
                "title": "闲",
                "no": "闲大|闲小|闲单|闲双|闲质|闲合",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //斗牛 101302000,
            ['id'=>'101302000', 'parent_id'=>'101300000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_dn_02', 'name'=>'斗牛', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101302001', 'parent_id'=>'101302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_dn_youniu', 'name'=>'有牛', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "ssc_yn", "tag_check": "ssc_yn", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[30.444]', 'prize_level_name'=>'["有牛"]', 'layout'=>'
{
    "desc": "从一 ~ 牛中任意选择1个以上号码。",
    "help": "从一 ~ 牛中任意选择1个号码，每注由1个号码组成，只要开奖号码如果有牛并且包含所选号码，即为中奖。",
    "example": "根据开奖第一球 ~ 第五球开出的球号数字为基础，任意组合三个号码成0或10的倍数，取剩余两个号码之和为点数（大于10时减去10后的数字作为对奖基数，如：00026为 牛8，02818为牛9，68628、23500皆为牛10俗称牛牛；26378、15286因任意三个号码都无法组合成0或10的倍数，称为没牛，注：当五个号码相同时，只有00000视为牛牛，其它11111，66666等皆视为没牛）。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "牛",
                "no": "一|二|三|四|五|六|七|八|九|牛",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101302002', 'parent_id'=>'101302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_dn_meiniu', 'name'=>'没牛', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "ssc_mein", "tag_check": "ssc_mein", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[5.61]', 'prize_level_name'=>'["没牛"]', 'layout'=>'
{
    "desc": "选择一个形态组成一注。",
    "help": "根据开奖号码计算如果没有牛，即为中奖。",
    "example": "投注方案：没牛；开奖号码11111 没牛，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "牛",
                "no": "没",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101302003', 'parent_id'=>'101302000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_dn_daxiaodanshuang', 'name'=>'大小单双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "ssc_ndxds", "tag_check": "ssc_ndxds", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[6.15839]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "从“大、小、单、双”中至少各选一个成一注。",
    "help": "开奖号码有牛，大小：牛大(牛6,牛7,牛8,牛9,牛牛)，牛小(牛1,牛2,牛3,牛4,牛5)，若开出斗牛结果为没牛，则投注牛大牛小皆为不中奖。单双：牛单(牛1,牛3,牛5,牛7,牛9)，牛双(牛2,牛4,牛6,牛8,牛牛)，若开出斗牛结果为没牛，则投注牛单牛双皆为不中奖。",
    "example": "投注方案：小双；开奖号码00002 牛2 小双中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "牛",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 0
            }
        ]
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //德州扑克 101303000,
            ['id'=>'101303000', 'parent_id'=>'101300000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_dzpk', 'name'=>'德州扑克', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101303001', 'parent_id'=>'101303000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_dzpk_dzpk', 'name'=>'德州扑克', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "ssc_dzpk", "tag_check": "ssc_dzpk", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000,444.44444,222.22222,166.66666,27.77778,18.51852,3.96826,6.94444,833.33334]', 'prize_level_name'=>'["豹子","四张","葫芦","顺子","三张","两对","一对","杂牌","五离"]', 'layout'=>'
{
    "desc": "选择一个形态组成一注。",
    "help": "德州扑克是以开奖五个号码为基准，按德州扑克牌面组合(豹子、四张、葫芦、顺子、三张、两对、一对、杂牌、五离)进行投注的一种玩法！",
    "example": "投注方案：葫芦，开奖号码：:77788，即中葫芦。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "德州扑克",
                "no": "豹子|四张|葫芦|顺子|三张|两对|一对|杂牌|五离",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三公 101304000,
            ['id'=>'101304000', 'parent_id'=>'101300000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_sg', 'name'=>'三公', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101304001', 'parent_id'=>'101304000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_sg_zyh', 'name'=>'左右和', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "ssc_zyh", "tag_check": "ssc_zyh", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.44444,4.44444,20]', 'prize_level_name'=>'["左闲","右闲","和"]', 'layout'=>'
{
    "desc": "选择一个形态组成一注。",
    "help": "三公以开奖结果五个数字为基准，将左闲点数(前三位:万、千、百之和的个位数)与右闲点数(后三位:百、十、个)之和的个位数进行比对的一种玩法！比对的点数中0点为10点视为最大，9点为次大，1点为最小！",
    "example": "投注方案：左闲，开奖号码：4,4,0,6,8，即中左闲。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "左右和",
                "no": "左闲|右闲|和",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101304002', 'parent_id'=>'101304000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_sg_dxdszh', 'name'=>'大小单双质合', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "ssc_sg_dxdszh", "tag_check": "ssc_sg_dxdszh", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双质和"]', 'layout'=>'
{
    "desc": "选择一个形态组成一注。",
    "help": "左闲点数/右闲点数的尾数：大小 0~4为小，5~9为大；单双 1、3、5、7、9为单，0、2、4、6、8为双；质合 1、2、3、5、7为质数，0、4、6、8、9为合数。",
    "example": "投注方案：左闲尾大，开奖号码：4,4,0,6,8，即中左闲尾大",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "左",
                "no": "左闲尾大|左闲尾小|左闲尾单|左闲尾双|左闲尾质|左闲尾合",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            },
            {
                "title": "右",
                "no": "右闲尾大|右闲尾小|右闲尾单|右闲尾双|右闲尾质|右闲尾合",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //新龙虎 101400000,
            ['id'=>'101400000', 'parent_id'=>'0', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_01', 'name'=>'新龙虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //新龙虎 101401000,
            ['id'=>'101401000', 'parent_id'=>'101400000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_02', 'name'=>'新龙虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401001', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_wanqian', 'name'=>'万千', 'draw_rule'=>'{"is_sum": 0, "position": "0,1", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从万位、千位上选择一个形态组成一注。",
    "help": "根据万位、千位号码数值比大小，万位号码大于千位号码为龙，万位号码小于千位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于千位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401002', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_wanbai', 'name'=>'万百', 'draw_rule'=>'{"is_sum": 0, "position": "0,2", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从万位、百位上选择一个形态组成一注。",
    "help": "根据万位、百位号码数值比大小，万位号码大于百位号码为龙，万位号码小于百位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于百位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401003', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_wanshi', 'name'=>'万十', 'draw_rule'=>'{"is_sum": 0, "position": "0,3", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,3', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从万位、十位上选择一个形态组成一注。",
    "help": "根据万位、十位号码数值比大小，万位号码大于十位号码为龙，万位号码小于十位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于十位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401004', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_wange', 'name'=>'万个', 'draw_rule'=>'{"is_sum": 0, "position": "0,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,4', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从万位、个位上选择一个形态组成一注。",
    "help": "根据万位、个位号码数值比大小，万位号码大于个位号码为龙，万位号码小于个位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码万位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401005', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_qianbai', 'name'=>'千百', 'draw_rule'=>'{"is_sum": 0, "position": "1,2", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,5', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从千位、百位上选择一个形态组成一注。",
    "help": "根据千位、百位号码数值比大小，千位号码大于百位号码为龙，千位号码小于百位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码千位大于百位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401006', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_qianshi', 'name'=>'千十', 'draw_rule'=>'{"is_sum": 0, "position": "1,3", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,6', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从千位、十位上选择一个形态组成一注。",
    "help": "根据千位、十位号码数值比大小，千位号码大于十位号码为龙，千位号码小于十位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码千位大于十位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401007', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_qiange', 'name'=>'千个', 'draw_rule'=>'{"is_sum": 0, "position": "1,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,7', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从千位、个位上选择一个形态组成一注。",
    "help": "根据千位、个位号码数值比大小，千位号码大于个位号码为龙，千位号码小于个位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码千位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401008', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_baishi', 'name'=>'百十', 'draw_rule'=>'{"is_sum": 0, "position": "2,3", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 2}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,8', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从百位、十位上选择一个形态组成一注。",
    "help": "根据百位、十位号码数值比大小，百位号码大于十位号码为龙，百位号码小于十位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码百位大于十位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401009', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_baige', 'name'=>'百个', 'draw_rule'=>'{"is_sum": 0, "position": "2,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 2}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,9', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从百位、个位上选择一个形态组成一注。",
    "help": "根据百位、个位号码数值比大小，百位号码大于个位号码为龙，百位号码小于个位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码百位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'101401010', 'parent_id'=>'101401000', 'lottery_method_category_id'=>'11', 'ident'=>'ssc_xlh_shige', 'name'=>'十个', 'draw_rule'=>'{"is_sum": 0, "position": "3,4", "tag_bonus": "n2_lhh", "tag_check": "n2_lhh", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'lock_lhh', 'lock_init_function'=>'initNumberTypeLHH,10', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["新龙虎"]', 'layout'=>'
{
    "desc": "从十位、个位上选择一个形态组成一注。",
    "help": "根据十位、个位号码数值比大小，十位号码大于个位号码为龙，十位号码小于个位号码为虎。二者相同则系统撤单。所选形态与开奖号码形态一致，即为中奖。",
    "example": "投注方案：龙；开奖号码十位大于个位：龙，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "新龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 21 3D 标准模式  ==================================
            //三码 120100000,
            ['id'=>'120100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3', 'name'=>'三码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三码直选 120101000,
            ['id'=>'120101000', 'parent_id'=>'120100000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zhixuan', 'name'=>'三码直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120101001', 'parent_id'=>'120101000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zhixuan_fushi', 'name'=>'直选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选复式"]', 'layout'=>'
{
    "desc": "从百位、十位、个位中至少各选1个号码。",
    "help": "从百位、十位、个位中选择一个3位数号码组成一注，所选号码与开奖号码相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,-,X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120101002', 'parent_id'=>'120101000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zhixuan_danshi', 'name'=>'直选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码。",
    "help": "手动输入一个3位数号码组成一注，所选号码与开奖号码相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120101003', 'parent_id'=>'120101000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zhixuan_hezhi', 'name'=>'直选和值', 'draw_rule'=>'{"is_sum": 1, "tag_bonus": "n3_zhixuanhezhi", "tag_check": "n3_zhixuanhezhi", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选和值"]', 'layout'=>'
{
    "desc": "从0-27中任意选择1个或1个以上号码。",
    "help": "所选数值等于开奖号码的三个数字相加之和，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "直选和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三码组选 120102000,
            ['id'=>'120102000', 'parent_id'=>'120100000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zuxuan', 'name'=>'三码组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120102001', 'parent_id'=>'120102000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zuxuan_zusan_fushi', 'name'=>'组三复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zusan", "tag_check": "n3_zusan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zuxuan', 'lock_init_function'=>'initNumberTypeZhuShanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上的号码。",
    "help": "从0-9中任意选择2个数字组成两注，所选号码与开奖号码相同，且顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组三",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120102002', 'parent_id'=>'120102000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zuxuan_zusan_danshi', 'name'=>'组三单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666]', 'prize_level_name'=>'["组三单式"]', 'layout'=>'
{
    "desc": "手动输入一个3位数号码组成一注。",
    "help": "三个数字中必须有二个数字相同，所选号码与开奖号码的百位、十位、个位相同，顺序不限，即为中奖。",
    "example": "投注方案：001，开奖号码：010（顺序不限），即中三星组选三。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120102003', 'parent_id'=>'120102000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zuxuan_zuliu_fushi', 'name'=>'组六复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zuliu", "tag_check": "n3_zuliu", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zuxuan', 'lock_init_function'=>'initNumberTypeZhuLiuLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择3个或3个以上的号码。",
    "help": "从0-9中任意选择3个号码组成一注，所选号码与开奖号码相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组六",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120102004', 'parent_id'=>'120102000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zuxuan_zuliu_danshi', 'name'=>'组六单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.33333]', 'prize_level_name'=>'["组六单式"]', 'layout'=>'
{
    "desc": "手动输入一个3位数号码组成一注。",
    "help": "三个数字必须互不相同，所选号码与开奖号码的百位、十位、个位相同，且顺序不限，即为中奖。",
    "example": "投注方案：123，开奖号码：321（顺序不限），即中三星组选六。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120102005', 'parent_id'=>'120102000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_hunhe_zuxuan_danshi', 'name'=>'混合组选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hunhezuxuan", "tag_check": "n3_hunhezuxuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666, 333.33333]', 'prize_level_name'=>'["组三", "组六"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码。",
    "help": "手动输入购买号码，3个数字为一注，开奖号码符合组三或组六均为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120102006', 'parent_id'=>'120102000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_zuxuan_hezhi', 'name'=>'组选和值', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hezhi", "tag_check": "n3_hezhi", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.66666, 333.33333]', 'prize_level_name'=>'["组三", "组六"]', 'layout'=>'
{
    "desc": "从1-26中任意选择1个或1个以上号码。",
    "help": "所选数值等于开奖号码的三个数字相加之和，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选和值",
                "no": "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码 120200000,
            ['id'=>'120200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n2_01', 'name'=>'二码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码直选 120201000,
            ['id'=>'120201000', 'parent_id'=>'120200000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n2_02', 'name'=>'二码直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120201001', 'parent_id'=>'120201000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_h2_zhixuan_fushi', 'name'=>'后二直选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["后二直选复式"]', 'layout'=>'
{
    "desc": "从十位和个位上至少各选1个号码。",
    "help": "从十位和个位上至少各选1个号码，所选号码与开奖号码的十位、个位相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120201002', 'parent_id'=>'120201000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_h2_zhixuan_danshi', 'name'=>'后二直选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["后二直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入一个2位数号码组成一注，所选号码与开奖号码的十位、个位相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120201003', 'parent_id'=>'120201000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_q2_zhixuan_fushi', 'name'=>'前二直选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["前二直选复式"]', 'layout'=>'
{
    "desc": "从百位和十位上至少各选1个号码。",
    "help": "从百位和十位上至少各选1个号码，所选号码与开奖号码百位、十位相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,-",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120201004', 'parent_id'=>'120201000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_q2_zhixuan_danshi', 'name'=>'前二直选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["前二直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入一个2位数号码组成一注，所选号码与开奖号码的百位、十位相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码组选 120202000,
            ['id'=>'120202000', 'parent_id'=>'120200000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n2_03', 'name'=>'二码组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120202001', 'parent_id'=>'120202000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_h2_zuxuan_fushi', 'name'=>'后二组选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["后二组选复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选二个号码组成一注，所选号码与开奖号码的十位、个位相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120202002', 'parent_id'=>'120202000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_h2_zuxuan_danshi', 'name'=>'后二组选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["后二组选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入购买号码，2个数字为一注，所选号码与开奖号码的十位、个位相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120202003', 'parent_id'=>'120202000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_q2_zuxuan_fushi', 'name'=>'前二组选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["前二组选复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选2个号码组成一注，所选号码与开奖号码的百位、十位相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120202004', 'parent_id'=>'120202000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_q2_zuxuan_danshi', 'name'=>'前二组选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["前二组选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入购买号码，2个数字为一注，所选号码与开奖号码的百位、十位相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 120300000,
            ['id'=>'120300000', 'parent_id'=>'0', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_dingweidan_01', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 120301000,
            ['id'=>'120301000', 'parent_id'=>'120300000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_dingweidan_02', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120301001', 'parent_id'=>'120301000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_dingweidan', 'name'=>'定位胆', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name'=>'lock_dwd', 'lock_init_function'=>'initNumberTypeYiWeiLock,1-3', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["定位胆"]', 'layout'=>'
{
    "desc": "在百位，十位，个位任意位置上任意选择1个或1个以上号码。",
    "help": "从百位、十位、个位任意1个位置或多个位置上选择1个号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //不定位 120400000,
            ['id'=>'120400000', 'parent_id'=>'0', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_budingwei_01', 'name'=>'不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //不定位 120401000,
            ['id'=>'120401000', 'parent_id'=>'120400000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_budingwei_02', 'name'=>'不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120401001', 'parent_id'=>'120401000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_budingwei_yima', 'name'=>'三星一码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_1mbudingwei", "tag_check": "n1_budingwei", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.38007]', 'prize_level_name'=>'["三星一码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个或1个以上的号码。",
    "help": "从0-9中选择1个号码，每注由1个号码组成，只要开奖结果中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120401002', 'parent_id'=>'120401000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_n3_budingwei_erma', 'name'=>'二码不定位', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[37.03703]', 'prize_level_name'=>'["二码不定位"]', 'layout'=>'
{
    "desc": "从0-9中选择2个号码组成一注。",
    "help": "每注由2个不同的号码组成，只要开奖号码的百位、十位、个位中包含所选的2个号码，即为中奖。",
    "example": "投注方案：12，开奖号码：至少出现1和2各1个，即中二码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //大小单双 120500000,
            ['id'=>'120500000', 'parent_id'=>'0', 'lottery_method_category_id'=>'21', 'ident'=>'3d_daxiaodanshuang_01', 'name'=>'大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码大小单双 120501000,
            ['id'=>'120501000', 'parent_id'=>'120500000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_daxiaodanshuang', 'name'=>'二码大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120501001', 'parent_id'=>'120501000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_daxiaodanshuang_qianer', 'name'=>'前二大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[8]', 'prize_level_name'=>'["前二大小单双"]', 'layout'=>'
{
    "desc": "对百位和十位的“大（56789）小（01234）单（13579）双（02468）”形态进行购买。",
    "help": "所选号码的位置、形态与开奖位置相同，即为中奖。",
    "example": "投注方案：小双，开奖号码：百位与十位“小双”，即中前二大小单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "百位",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "大|小|单|双",
                "place": 1,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120501002', 'parent_id'=>'120501000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_daxiaodanshuang_houer', 'name'=>'后二大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[8]', 'prize_level_name'=>'["后二大小单双"]', 'layout'=>'
{
    "desc": "对十位和个位的“大（56789）小（01234）单（13579）双（02468）”形态进行购买。",
    "help": "所选号码的位置、形态与开奖位置相同，即为中奖。",
    "example": "投注方案：大单，开奖号码：十位与个位“大单”，即中后二大小单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "百位",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "大|小|单|双",
                "place": 1,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //和值大小单双 120502000,
            ['id'=>'120502000', 'parent_id'=>'120500000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_hezhi', 'name'=>'和值大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120502001', 'parent_id'=>'120502000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_hezhi_daxiao', 'name'=>'和值大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["和值大小"]', 'layout'=>'
{
    "desc": "选择大、小进行投注。",
    "help": "开奖号码和值14～27为大，0～13为小。",
    "example": "投注方案：小，开奖号码：01，即中和值大小。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值大小",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120502002', 'parent_id'=>'120502000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_hezhi_danshuang', 'name'=>'和值单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["和值单双"]', 'layout'=>'
{
    "desc": "选择单、双进行投注。",
    "help": "选择单、双进行投注。",
    "example": "投注方案：单，开奖号码：01，即中和值单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值单双",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'120502003', 'parent_id'=>'120502000', 'lottery_method_category_id'=>'21', 'ident'=>'3d_hezhi_daxiaodanshuang', 'name'=>'和值大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.43,8.65,8.65,7.43]', 'prize_level_name'=>'["小单","小双","大单","大双"]', 'layout'=>'
{
    "desc": "选择大、小、单、双进行投注。",
    "help": "开奖号码和值14～27为大，0～13为小。",
    "example": "投注方案：小单，开奖号码：01，即中大小单双。",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "和值大小单双",
                "no": "小单|小双|大单|大双",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 31 11 选 5 标准模式  ==================================
            //三码 130100000,
            ['id'=>'130100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_n3_01', 'name'=>'三码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三码 130101000,
            ['id'=>'130101000', 'parent_id'=>'130100000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_n3_02', 'name'=>'三码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130101001', 'parent_id'=>'130101000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q3_zhixuan_fushi', 'name'=>'前三直选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n3_zhixuan", "tag_check": "lotto_n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_shanma', 'lock_init_function'=>'initLeTouTypeShanZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1980]', 'prize_level_name'=>'["前三直选复式"]', 'layout'=>'
{
    "desc": "从第一位、第二位、第三位中至少各选择1个号码。",
    "help": "从01-11共11个号码中选择3个不重复的号码组成一注，所选号码与当期顺序摇出的5个号码中的前3个号码相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "第一位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            },
            {
                "title": "第二位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1
            },
            {
                "title": "第三位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,X,-,-",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130101002', 'parent_id'=>'130101000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q3_zhixuan_danshi', 'name'=>'前三直选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n3_zhixuan", "tag_check": "lotto_n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_shanma', 'lock_init_function'=>'initLeTouTypeShanZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1980]', 'prize_level_name'=>'["前三直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码组成一注。",
    "help": "手动输入3个号码组成一注，所输入的号码与当期顺序摇出的5个号码中的前3个号码相同，且顺序一致，即为中奖。",
    "example": "投注内容：02 03 11,06 08 10。开奖号码：02 03 11 07 05，即中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ";"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130101003', 'parent_id'=>'130101000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q3_zuxuan_fushi', 'name'=>'前三组选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n3_zuxuan", "tag_check": "lotto_n3_zuxuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_shanma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[330]', 'prize_level_name'=>'["前三组选复式"]', 'layout'=>'
{
    "desc": "从01-11中任意选择3个或3个以上号码。",
    "help": "从01-11中共11个号码中选择3个号码，所选号码与当期顺序摇出的5个号码中的前3个号码相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "前三组选",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130101004', 'parent_id'=>'130101000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q3_zuxuan_danshi', 'name'=>'前三组选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n3_zuxuan", "tag_check": "lotto_n3_zuxuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_shanma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[330]', 'prize_level_name'=>'["前三组选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码组成一注。",
    "help": "手动输入3个号码组成一注，所输入的号码与当期顺序摇出的5个号码中的前3个号码相同，顺序不限，即为中奖。",
    "example": "投注内容：02 03 11,06 08 10。开奖号码：02 11 03 07 05，即中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ";"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130101005', 'parent_id'=>'130101000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q3_zuxuan_dantuo', 'name'=>'前三组选胆拖', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[330]', 'prize_level_name'=>'["前三组选胆拖"]', 'layout'=>'
{
    "desc": "从01-11中，选取3个及以上的号码进行投注，每注需至少包括1个胆码及2个拖码",
    "help": "从01-11中，选取3个及以上的号码进行投注，每注需至少包括1个胆码及2个拖码",
    "example": "投注方案：胆码02、拖码01 06，开奖号码：02 01 06 **（前三顺序不限），即中前三组选胆拖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码 130200000,
            ['id'=>'130200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_n2_01', 'name'=>'二码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码 130201000,
            ['id'=>'130201000', 'parent_id'=>'130200000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_n2_02', 'name'=>'二码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130201001', 'parent_id'=>'130201000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q2_zhixuan_fushi', 'name'=>'前二直选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n2_zhixuan", "tag_check": "lotto_n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'initLeTouTypeErZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[220]', 'prize_level_name'=>'["前二直选复式"]', 'layout'=>'
{
    "desc": "从第一位、第二位中至少各选择1个号码。",
    "help": "从01-11共11个号码中选择2个不重复的号码组成一注，所选号码与当期顺序摇出的5个号码中的前2个号码相同，且顺序一致，即中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "第一位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            },
            {
                "title": "第二位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130201002', 'parent_id'=>'130201000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q2_zhixuan_danshi', 'name'=>'前二直选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n2_zhixuan", "tag_check": "lotto_n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'initLeTouTypeErZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[220]', 'prize_level_name'=>'["前二直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码组成一注。",
    "help": "手动输入2个号码组成一注，所输入的号码与当期顺序摇出的5个号码中的前2个号码相同，且顺序一致，即为中奖。",
    "example": "投注内容：02 03,06 08。开奖号码：02 03 11 07 05，即中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ";"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130201003', 'parent_id'=>'130201000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q2_zuxuan_fushi', 'name'=>'前二组选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n2_zuxuan", "tag_check": "lotto_n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[110]', 'prize_level_name'=>'["前二组选复式"]', 'layout'=>'
{
    "desc": "从01-11中任意选择2个或2个以上号码。",
    "help": "从01-11中共11个号码中选择2个号码，所选号码与当期顺序摇出的5个号码中的前2个号码相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "前二组选",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130201004', 'parent_id'=>'130201000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q2_zuxuan_danshi', 'name'=>'前二组选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n2_zuxuan", "tag_check": "lotto_n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[110]', 'prize_level_name'=>'["前二组选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码组成一注。",
    "help": "手动输入2个号码组成一注，所输入的号码与当期顺序摇出的5个号码中的前2个号码相同，顺序不限，即为中奖。",
    "example": "投注内容：06 08,03 02。开奖号码：02 03 11 07 05，即中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ";"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130201005', 'parent_id'=>'130201000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q2_zuxuan_dantuo', 'name'=>'前二组选胆拖', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[110]', 'prize_level_name'=>'["前二组选胆拖"]', 'layout'=>'
{
    "desc": "从01-11中，选取2个及以上的号码进行投注，每注需至少包括1个胆码及1个拖码",
    "help": "从01-11中，选取2个及以上的号码进行投注，每注需至少包括1个胆码及1个拖码",
    "example": "投注方案：胆码01、拖码06，开奖号码：06 01 ***（前二顺序不限），即中前二组选胆拖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //不定位 130300000,
            ['id'=>'130300000', 'parent_id'=>'0', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_budingwei_01', 'name'=>'不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //不定位 130301000,
            ['id'=>'130301000', 'parent_id'=>'130300000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_budingwei_02', 'name'=>'不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130301001', 'parent_id'=>'130301000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_q3_budingwei_yima', 'name'=>'前三一码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_budingwei", "tag_check": "lotto_budingwei", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_budingwei', 'lock_init_function'=>'initLeTouTypeShanzuxuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.33333]', 'prize_level_name'=>'["前三一码"]', 'layout'=>'
{
    "desc": "从01-11中任意选择1个或1个以上号码。",
    "help": "从01-11中共11个号码中选择1个号码，每注由1个号码组成，只要当期顺序摇出的第一位、第二位、第三位开奖号码中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "前三位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 130400000,
            ['id'=>'130400000', 'parent_id'=>'0', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_dingweidan_01', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 130401000,
            ['id'=>'130401000', 'parent_id'=>'130400000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_dingweidan_02', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130401001', 'parent_id'=>'130401000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_dingweidan', 'name'=>'定位胆', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_dingerwei', 'lock_init_function'=>'initLeTouTypeYiWeiLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[22]', 'prize_level_name'=>'["前五定位胆"]', 'layout'=>'
{
    "desc": "从第一至第五位任意选择1个或1个以上号码",
    "help": "从第一至第五位任意选择1个或1个以上号码",
    "example": "投注方案：第一位 01，开奖号码：01**** ，即中定位胆。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "第一位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            },
            {
                "title": "第二位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1
            },
            {
                "title": "第三位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 2,
                "cols": 1
            },
            {
                "title": "第四位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 3,
                "cols": 1
            },
            {
                "title": "第五位",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 4,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //趣味型 130500000,
            ['id'=>'130500000', 'parent_id'=>'0', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_qw_01', 'name'=>'趣味型', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //趣味型 130501000,
            ['id'=>'130501000', 'parent_id'=>'130500000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_qw_02', 'name'=>'趣味型', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130501001', 'parent_id'=>'130501000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_qw_dingdanshuang', 'name'=>'定单双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_dingdanshuang", "tag_check": "lotto_dingdanshuang", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_danshuang', 'lock_init_function'=>'initLeTouTypeDSLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[924, 154, 30.8, 12.32, 6.16, 4.62]', 'prize_level_name'=>'["5双0单", "0双5单", "4双1单", "1双4单", "3双2单", "2双3单"]', 'layout'=>'
{
    "desc": "从不同的单双组合中任意选择1个或1个以上的组合。",
    "help": "从5种单双个数组合中选择1种组合，当期开奖号码的单双个数与所选单双组合一致，即为中奖。",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "定单双",
                "no": "5单0双|4单1双|3单2双|2单3双|1单4双|0单5双",
                "place": 0,
                "cols": 0,
                "no_bg": "square"
            }
        ]
    },
    "show_str": "X",
    "code_sp": "|"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130501002', 'parent_id'=>'130501000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_qw_caizhongwei', 'name'=>'猜中位', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_zhongwei", "tag_check": "lotto_zhongwei", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_zhongwei', 'lock_init_function'=>'initLeTouTypeZhongWeiLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[33, 14.6666666667, 10.2666666667, 9.24]', 'prize_level_name'=>'["中位03或09", "中位04或08", "中位05或07", "中位06"]', 'layout'=>'
{
    "desc": "从3-9中任意选择1个或1个以上数字。",
    "help": "从3-9中选择1个号码进行购买，所选号码与5个开奖号码按照大小顺序排列后的第3个号码相同，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "猜中位",
                "no": "3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选 130600000,
            ['id'=>'130600000', 'parent_id'=>'0', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx', 'name'=>'任选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选复式 130601000,
            ['id'=>'130601000', 'parent_id'=>'130600000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_fushi', 'name'=>'任选复式', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130601001', 'parent_id'=>'130601000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_1zhong1_fushi', 'name'=>'一中一', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_rx1", "tag_check": "lotto_rx1", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4]', 'prize_level_name'=>'["一中一"]', 'layout'=>'
{
    "desc": "从01-11中任意选择1个或1个以上号码。",
    "help": "从01-11共11个号码中选择1个号码进行购买，只要当期顺序摇出的5个开奖号码中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "选一中1",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130601002', 'parent_id'=>'130601000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_2zhong2_fushi', 'name'=>'二中二', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_rx2", "tag_check": "lotto_rx2", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[11]', 'prize_level_name'=>'["二中二"]', 'layout'=>'
{
    "desc": "从01-11中任意选择2个或2个以上号码。",
    "help": "从01-11共11个号码中选择2个号码进行购买，只要当期顺序摇出的5个开奖号码中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "选二中2",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130601003', 'parent_id'=>'130601000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_3zhong3_fushi', 'name'=>'三中三', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_rx3", "tag_check": "lotto_rx3", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[33]', 'prize_level_name'=>'["三中三"]', 'layout'=>'
{
    "desc": "从01-11中任意选择3个或3个以上号码。",
    "help": "从01-11共11个号码中选择3个号码进行购买，只要当期顺序摇出的5个开奖号码中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "选三中3",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130601004', 'parent_id'=>'130601000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_4zhong4_fushi', 'name'=>'四中四', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_rx4", "tag_check": "lotto_rx4", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[132]', 'prize_level_name'=>'["四中四"]', 'layout'=>'
{
    "desc": "从01-11中任意选择4个或4个以上号码。",
    "help": "从01-11共11个号码中选择4个号码进行购买，只要当期顺序摇出的5个开奖号码中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "选四中4",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130601005', 'parent_id'=>'130601000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_5zhong5_fushi', 'name'=>'五中五', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_rx5", "tag_check": "lotto_rx5", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'initLeTouTypeRXLock,5', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[924]', 'prize_level_name'=>'["五中五"]', 'layout'=>'
{
    "desc": "从01-11中任意选择5个或5个以上号码。",
    "help": "从01-11共11个号码中选择5个号码进行购买，只要当期顺序摇出的5个开奖号码中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "选五中5",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130601006', 'parent_id'=>'130601000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_6zhong5_fushi', 'name'=>'六中五', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_rx6", "tag_check": "lotto_rx6", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[154]', 'prize_level_name'=>'["六中五"]', 'layout'=>'
{
    "desc": "从01-11中任意选择6个或6个以上号码。",
    "help": "从01-11共11个号码中选择6个号码进行购买，只要当期顺序摇出的5个开奖号码中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "选六中5",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130601007', 'parent_id'=>'130601000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_7zhong5_fushi', 'name'=>'七中五', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_rx7", "tag_check": "lotto_rx7", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[44]', 'prize_level_name'=>'["七中五"]', 'layout'=>'
{
    "desc": "从01-11中任意选择7个或7个以上号码。",
    "help": "从01-11共11个号码中选择7个号码进行购买，只要当期顺序摇出的5个开奖号码中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "选七中5",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130601008', 'parent_id'=>'130601000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_8zhong5_fushi', 'name'=>'八中五', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_rx8", "tag_check": "lotto_rx8", "code_count": 5, "start_position": 0}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[16.5]', 'prize_level_name'=>'["八中五"]', 'layout'=>'
{
    "desc": "从01-11中任意选择8个或8个以上号码。",
    "help": "从01-11共11个号码中选择8个号码进行购买，只要当期顺序摇出的5个开奖号码中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "选八中5",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选单式 130602000,
            ['id'=>'130602000', 'parent_id'=>'130600000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_danshi', 'name'=>'任选单式', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130602001', 'parent_id'=>'130602000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_1zhong1_danshi', 'name'=>'一中一', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.4]', 'prize_level_name'=>'["一中一"]', 'layout'=>'
{
    "desc": "手动输入号码，从01-11中任意输入1个号码组成一注",
    "help": "手动输入号码，从01-11中任意输入1个号码组成一注",
    "example": "投注方案：05，开奖号码：08 04 11 05 03，即中任选一中一。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130602002', 'parent_id'=>'130602000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_2zhong2_danshi', 'name'=>'二中二', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[11]', 'prize_level_name'=>'["二中二"]', 'layout'=>'
{
    "desc": "手动输入号码，从01-11中任意输入2个号码组成一注",
    "help": "手动输入号码，从01-11中任意输入2个号码组成一注",
    "example": "投注方案：05 04，开奖号码：08 04 11 05 03，即中任选二中二。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130602003', 'parent_id'=>'130602000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_3zhong3_danshi', 'name'=>'三中三', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[33]', 'prize_level_name'=>'["三中三"]', 'layout'=>'
{
    "desc": "手动输入号码，从01-11中任意输入3个号码组成一注",
    "help": "手动输入号码，从01-11中任意输入3个号码组成一注",
    "example": "投注方案：05 04 11，开奖号码：08 04 11 05 03，即中任选三中三。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130602004', 'parent_id'=>'130602000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_4zhong4_danshi', 'name'=>'四中四', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[132]', 'prize_level_name'=>'["四中四"]', 'layout'=>'
{
    "desc": "手动输入号码，从01-11中任意输入4个号码组成一注",
    "help": "手动输入号码，从01-11中任意输入4个号码组成一注",
    "example": "投注方案：05 04 08 03，开奖号码：08 04 11 05 03，即中任选四中四。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130602005', 'parent_id'=>'130602000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_5zhong5_danshi', 'name'=>'五中五', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[924]', 'prize_level_name'=>'["五中五"]', 'layout'=>'
{
    "desc": "手动输入号码，从01-11中任意输入5个号码组成一注",
    "help": "手动输入号码，从01-11中任意输入5个号码组成一注",
    "example": "投注方案：05 04 08 03，开奖号码：08 04 11 05 03，即中任选四中四。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130602006', 'parent_id'=>'130602000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_6zhong5_danshi', 'name'=>'六中五', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[154]', 'prize_level_name'=>'["六中五"]', 'layout'=>'
{
    "desc": "手动输入号码，从01-11中任意输入6个号码组成一注",
    "help": "手动输入号码，从01-11中任意输入6个号码组成一注",
    "example": "投注方案：05 10 04 11 03 08，开奖号码：08 04 11 05 03，即中任选六中五。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130602007', 'parent_id'=>'130602000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_7zhong5_danshi', 'name'=>'七中五', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[44]', 'prize_level_name'=>'["七中五"]', 'layout'=>'
{
    "desc": "手动输入号码，从01-11中任意输入7个号码组成一注",
    "help": "手动输入号码，从01-11中任意输入7个号码组成一注",
    "example": "投注方案：05 10 04 11 03 08 09，开奖号码：08 04 11 05 03，即中任选七中五。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130602008', 'parent_id'=>'130602000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_8zhong5_danshi', 'name'=>'八中五', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_renxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[16.5]', 'prize_level_name'=>'["八中五"]', 'layout'=>'
{
    "desc": "手动输入号码，从01-11中任意输入8个号码组成一注",
    "help": "手动输入号码，从01-11中任意输入8个号码组成一注",
    "example": "投注方案：05 10 04 11 03 08 09 01，开奖号码：08 04 11 05 03，即中任选八中五。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选胆拖 130603000,
            ['id'=>'130603000', 'parent_id'=>'130600000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_dantuo', 'name'=>'任选胆拖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130603001', 'parent_id'=>'130603000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_2zhong2_dantuo', 'name'=>'二中二', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[11]', 'prize_level_name'=>'["二中二"]', 'layout'=>'
{
    "desc": "从01-11中，选取2个及以上的号码进行投注，每注需至少包括1个胆码及1个拖码",
    "help": "从01-11中，选取2个及以上的号码进行投注，每注需至少包括1个胆码及1个拖码",
    "example": "投注方案：胆码 08、拖码06，开奖号码：06 08 11 09 02，即中任选二中二。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130603002', 'parent_id'=>'130603000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_3zhong3_dantuo', 'name'=>'三中三', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[33]', 'prize_level_name'=>'["三中三"]', 'layout'=>'
{
    "desc": "从01-11中，选取3个及以上的号码进行投注，每注需至少包括1个胆码及2个拖码",
    "help": "从01-11中，选取3个及以上的号码进行投注，每注需至少包括1个胆码及2个拖码",
    "example": "投注方案：胆码 08、拖码06 11，开奖号码：06 08 11 09 02，即中任选三中三。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130603003', 'parent_id'=>'130603000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_4zhong4_dantuo', 'name'=>'四中四', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[132]', 'prize_level_name'=>'["四中四"]', 'layout'=>'
{
    "desc": "从01-11中，选取4个及以上的号码进行投注，每注需至少包括1个胆码及3个拖码",
    "help": "从01-11中，选取4个及以上的号码进行投注，每注需至少包括1个胆码及3个拖码",
    "example": "投注方案：胆码 08、拖码06 09 11，开奖号码：06 08 11 09 02，即中任选四中四。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130603004', 'parent_id'=>'130603000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_5zhong5_dantuo', 'name'=>'五中五', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[924]', 'prize_level_name'=>'["五中五"]', 'layout'=>'
{
    "desc": "从01-11中，选取5个及以上的号码进行投注，每注需至少包括1个胆码及4个拖码",
    "help": "从01-11中，选取5个及以上的号码进行投注，每注需至少包括1个胆码及4个拖码",
    "example": "投注方案：胆码 08、拖码02 06 09 11，开奖号码：06 08 11 09 02，即中任选五中五。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130603005', 'parent_id'=>'130603000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_6zhong5_dantuo', 'name'=>'六中五', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[154]', 'prize_level_name'=>'["六中五"]', 'layout'=>'
{
    "desc": "从01-11中，选取6个及以上的号码进行投注，每注需至少包括1个胆码及5个拖码",
    "help": "从01-11中，选取6个及以上的号码进行投注，每注需至少包括1个胆码及5个拖码",
    "example": "投注方案：胆码 08、拖码02 05 06 09 11，开奖号码：06 08 11 09 02，即中任选六中五。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130603006', 'parent_id'=>'130603000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_7zhong5_dantuo', 'name'=>'七中五', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[44]', 'prize_level_name'=>'["七中五"]', 'layout'=>'
{
    "desc": "从01-11中，选取7个及以上的号码进行投注，每注需至少包括1个胆码及6个拖码",
    "help": "从01-11中，选取7个及以上的号码进行投注，每注需至少包括1个胆码及6个拖码",
    "example": "投注方案：胆码 08、拖码01 02 05 06 09 11，开奖号码：06 08 11 09 02，即中任选七中五。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'130603007', 'parent_id'=>'130603000', 'lottery_method_category_id'=>'31', 'ident'=>'11x5_rx_8zhong5_dantuo', 'name'=>'八中五', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_erma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[16.5]', 'prize_level_name'=>'["八中五"]', 'layout'=>'
{
    "desc": "从01-11中，选取8个及以上的号码进行投注，每注需至少包括1个胆码及7个拖码",
    "help": "从01-11中，选取8个及以上的号码进行投注，每注需至少包括1个胆码及7个拖码",
    "example": "投注方案：胆码 08、拖码01 02 03 05 06 09 11，开奖号码：06 08 11 09 02，即中任选八中五。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,-,-,-",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 41 快乐 8 标准模式  ==================================
            //和值 140100000,
            ['id'=>'140100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_hezhi', 'name'=>'和值', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //趣味型 140101000,
            ['id'=>'140101000', 'parent_id'=>'140100000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_qw', 'name'=>'趣味型', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140101001', 'parent_id'=>'140101000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_hezhi_danshuang', 'name'=>'和值单双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_heds", "tag_check": "bjkl_heds", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'lock_heids', 'lock_init_function'=>'initBJKLHEDSLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.8888, 4.8888]', 'prize_level_name'=>'["和值单", "和值双"]', 'layout'=>'
{
    "desc": "选择20个开奖号码总和值的单双属性",
    "help": "20个开奖号码的总和值为奇数时中“单”，为偶数时中“双”。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值单双",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": true
    },
    "show_str": "X",
    "code_sp": "|"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140101002', 'parent_id'=>'140101000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_hezhi_daxiao', 'name'=>'和值大小', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_hedx", "tag_check": "bjkl_hedx", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'lock_heidx', 'lock_init_function'=>'initBJKLHEPANLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[305, 4.9382, 4.9382]', 'prize_level_name'=>'["和值810", "和值大", "和值小"]', 'layout'=>'
{
    "desc": "选择20个开奖号码总和值的大小属性",
    "help": "选择20个开奖号码总和值的大小属性:小于810为小,等于810为和,大于810为大",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值大小",
                "no": "小|和|大",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": true
    },
    "show_str": "X",
    "code_sp": "|"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140101003', 'parent_id'=>'140101000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_jioupan', 'name'=>'奇偶盘', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_jopan", "tag_check": "bjkl_jopan", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'lock_jopan', 'lock_init_function'=>'initBJKLHEPANLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[10.5, 5.375, 5.375]', 'prize_level_name'=>'["和盘", "奇盘", "偶盘"]', 'layout'=>'
{
    "desc": "选择20个开奖号码中包含奇偶号码个数多少的关系",
    "help": "20个开奖号码中奇数个数大于偶数个数中“奇”盘，偶数个数大于奇数个数中“偶”盘，两盘个数相等时中“和”盘。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "奇偶盘",
                "no": "奇|和|偶",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": true
    },
    "show_str": "X",
    "code_sp": "|"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140101004', 'parent_id'=>'140101000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_shangxiapan', 'name'=>'上下盘', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_sxpan", "tag_check": "bjkl_sxpan", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'lock_sxpan', 'lock_init_function'=>'initBJKLHEPANLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[10.5, 5.375, 5.375]', 'prize_level_name'=>'["中盘", "上盘", "下盘"]', 'layout'=>'
{
    "desc": "选择20个开奖号码中包含上盘(01-40)与下盘(41-80)号码个数多少的关系",
    "help": "20个开奖号码中上盘（01-40）号码个数大余下盘（41-80）号码个数中“上”盘，下盘号码个数大于上盘号码个数中“下”盘，两盘号码个数相等时中“中”盘。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "上下盘",
                "no": "上|中|下",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": true
    },
    "show_str": "X",
    "code_sp": "|"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140101005', 'parent_id'=>'140101000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_hezhi_daxiaodanshuang', 'name'=>'和值大小单双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_hedxds", "tag_check": "bjkl_hedxds", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'lock_hedxds', 'lock_init_function'=>'initBJKLHEDXDSLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[8]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "选择20个开奖号码总和值的大小单双属性",
    "help": "选择20个开奖号码总和值的大小单双属性（和值811～1410为大,和值210~810为小）。",
    "example": "投注方案：和值\"大•单\"<br />开奖结果：20个开奖号码的总和值为大(超过810)<br />　　　　　并且为单数<br />中奖名称：和值大小单双_\"大•单\"",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "大小单双",
                "no": "大单|大双|小单|小双",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": true
    },
    "show_str": "X",
    "code_sp": "|"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选 140200000,
            ['id'=>'140200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_rx', 'name'=>'任选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选型 140201000,
            ['id'=>'140201000', 'parent_id'=>'140200000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_rxx', 'name'=>'任选型', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140201001', 'parent_id'=>'140201000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_renxuan1', 'name'=>'任选一', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_rx1", "tag_check": "bjkl_rx1", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'lock_rx1', 'lock_init_function'=>'initBJKLRX1Lock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[8]', 'prize_level_name'=>'["任选一中一"]', 'layout'=>'
{
    "desc": "从01-80中任选1个以上号码",
    "help": "从01-80中选择1个号码组成一注，当期开奖结果的20个号码中包含所选号码，即可中奖。",
    "example": "投注方案：01<br />开奖号码：01 02 03 04 05 06 07 08 21 22<br />　　　　　71 72 73 74 75 76 77 78 79 80<br />中奖结果：中1个号码",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "上",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40",
                "place": 0,
                "cols": 2
            },
            {
                "title": "下",
                "no": "41|42|43|44|45|46|47|48|49|50|51|52|53|54|55|56|57|58|59|60|61|62|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80",
                "place": 0,
                "cols": 2
            }
        ],
        "title": "任选一",
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140201002', 'parent_id'=>'140201000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_renxuan2', 'name'=>'任选二', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_rx2", "tag_check": "bjkl_rx2", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[31.25]', 'prize_level_name'=>'["任选二中二"]', 'layout'=>'
{
    "desc": "从01-80中任选2-8个号码",
    "help": "从01-80中选择2个号码组成一注，当期开奖结果的20个号码中包含所选号码，即可中奖。",
    "example": "投注方案：01 02<br />开奖号码：01 02 03 04 05 06 07 08 21 22<br />　　　　　71 72 73 74 75 76 77 78 79 80<br />中奖结果：中2个号码",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "上",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40",
                "place": 0,
                "cols": 2
            },
            {
                "title": "下",
                "no": "41|42|43|44|45|46|47|48|49|50|51|52|53|54|55|56|57|58|59|60|61|62|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80",
                "place": 0,
                "cols": 2
            }
        ],
        "title": "任选二",
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140201003', 'parent_id'=>'140201000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_renxuan3', 'name'=>'任选三', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_rx3", "tag_check": "bjkl_rx3", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[57.5, 7.5]', 'prize_level_name'=>'["任选三中三", "任选三中二"]', 'layout'=>'
{
    "desc": "从01-80中任选3-8个号码",
    "help": "从01-80中选择3个号码组成一注，当期开奖结果的20个号码中包含3个或2个所选号码，即可中奖。不兼中兼得。",
    "example": "投注方案：01 02 03<br />开奖号码：01 02 03 04 05 06 07 08 21 22<br />　　　　　71 72 73 74 75 76 77 78 79 80<br />中奖结果：中3个号码",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "上",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40",
                "place": 0,
                "cols": 2
            },
            {
                "title": "下",
                "no": "41|42|43|44|45|46|47|48|49|50|51|52|53|54|55|56|57|58|59|60|61|62|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80",
                "place": 0,
                "cols": 2
            }
        ],
        "title": "任选三",
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140201004', 'parent_id'=>'140201000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_renxuan4', 'name'=>'任选四', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_rx4", "tag_check": "bjkl_rx4", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[137.5, 15, 3.75]', 'prize_level_name'=>'["任选四中四", "任选四中三", "任选四中二"]', 'layout'=>'
{
    "desc": "从01-80中任选4-8个号码",
    "help": "从01-80中选择4个号码组成一注，当期开奖结果的20个号码中包含4个、3个或2个所选号码，即可中奖。不兼中兼得",
    "example": "投注方案：01 02 03 04<br />开奖号码：01 02 03 04 05 06 07 08 21 22<br />　　　　　71 72 73 74 75 76 77 78 79 80<br />中奖结果：中4个号码",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "上",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40",
                "place": 0,
                "cols": 2
            },
            {
                "title": "下",
                "no": "41|42|43|44|45|46|47|48|49|50|51|52|53|54|55|56|57|58|59|60|61|62|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80",
                "place": 0,
                "cols": 2
            }
        ],
        "title": "任选四",
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140201005', 'parent_id'=>'140201000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_renxuan5', 'name'=>'任选五', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_rx5", "tag_check": "bjkl_rx5", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[710, 59, 7.1]', 'prize_level_name'=>'["任选五中五", "任选五中四", "任选五中三"]', 'layout'=>'
{
    "desc": "从01-80中任选5-8个号码",
    "help": "从01-80中选择5个号码组成一注，当期开奖结果的20个号码中包含5个、4个或3个所选号码，即可中奖。不兼中兼得。",
    "example": "投注方案：01 02 03 04 05<br />开奖号码：01 02 03 04 05 06 07 08 21 22<br />　　　　　71 72 73 74 75 76 77 78 79 80<br />中奖结果：中5个号码",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "上",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40",
                "place": 0,
                "cols": 2
            },
            {
                "title": "下",
                "no": "41|42|43|44|45|46|47|48|49|50|51|52|53|54|55|56|57|58|59|60|61|62|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80",
                "place": 0,
                "cols": 2
            }
        ],
        "title": "任选五",
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140201006', 'parent_id'=>'140201000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_renxuan6', 'name'=>'任选六', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_rx6", "tag_check": "bjkl_rx6", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1150.74, 80.55, 11.5, 6.9]', 'prize_level_name'=>'["任选六中六", "任选六中五", "任选六中四", "任选六中三"]', 'layout'=>'
{
    "desc": "从01-80中任选6-8个号码",
    "help": "从01-80中选择6个号码组成一注，当期开奖结果的20个号码中包含6个、5个、4个或3个所选号码，即可中奖。不兼中兼得。",
    "example": "投注方案：01 02 03 04 05 06<br />开奖号码：01 02 03 04 05 06 07 08 21 22<br />　　　　　71 72 73 74 75 76 77 78 79 80<br />中奖结果：中6个号码",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "上",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40",
                "place": 0,
                "cols": 2
            },
            {
                "title": "下",
                "no": "41|42|43|44|45|46|47|48|49|50|51|52|53|54|55|56|57|58|59|60|61|62|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80",
                "place": 0,
                "cols": 2
            }
        ],
        "title": "任选六",
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'140201007', 'parent_id'=>'140201000', 'lottery_method_category_id'=>'41', 'ident'=>'kl8_renxuan7', 'name'=>'任选七', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "bjkl_rx7", "tag_check": "bjkl_rx7", "code_count": 20, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[10834.23, 249.18, 32.5, 6.5, 3.25]', 'prize_level_name'=>'["任选七中七", "任选七中六", "任选七中五", "任选七中四", "任选七中零"]', 'layout'=>'
{
    "desc": "从01-80中任选7-8个号码",
    "help": "从01-80中选择7个号码组成一注，当期开奖结果的20个号码中包含7个、6个、5个、或4个所选号码，或者出现0个所选号码，即可中奖。不兼中兼得。",
    "example": "投注方案：01 02 03 04 05 06 07<br />开奖号码：01 02 03 04 05 06 07 08 21 22<br />　　　　　71 72 73 74 75 76 77 78 79 80<br />中奖结果：中7个号码",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "上",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40",
                "place": 0,
                "cols": 2
            },
            {
                "title": "下",
                "no": "41|42|43|44|45|46|47|48|49|50|51|52|53|54|55|56|57|58|59|60|61|62|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80",
                "place": 0,
                "cols": 2
            }
        ],
        "title": "任选七",
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 51 快 3 标准模式  ==================================
            //二同号 150100000,
            ['id'=>'150100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_ertonghao', 'name'=>'二同号', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二同号单选 150101000,
            ['id'=>'150101000', 'parent_id'=>'150100000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_ertonghao_danxuan', 'name'=>'二同号单选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150101001', 'parent_id'=>'150101000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_ertonghao_danxuan_fushi', 'name'=>'单选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_ethdx", "tag_check": "k3_ethdx", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_ethdx', 'lock_init_function'=>'initNumberTypeK3ETHDXLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[144]', 'prize_level_name'=>'["二同号单选复式"]', 'layout'=>'
{
    "desc": "选择1个对子（11,22,33,44,55,66）和1个不同号码(1,2,3,4,5,6)投注。",
    "help": "选择1个对子（11,22,33,44,55,66）和1个不同号码(1,2,3,4,5,6)投注，选号与开奖号码一致即中奖。",
    "example": "投注方案：112；开奖号码112,121,211，即中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二同号",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-ertonghao k3-ertonghao-1",
                    "2": "k3-ertonghao k3-ertonghao-2",
                    "3": "k3-ertonghao k3-ertonghao-3",
                    "4": "k3-ertonghao k3-ertonghao-4",
                    "5": "k3-ertonghao k3-ertonghao-5",
                    "6": "k3-ertonghao k3-ertonghao-6"
                },
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "不同号",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150101002', 'parent_id'=>'150101000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_ertonghao_danxuan_danshi', 'name'=>'单选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_ethdx", "tag_check": "k3_ethdx", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_ethdx', 'lock_init_function'=>'initNumberTypeK3ETHDXLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[144]', 'prize_level_name'=>'["二同号单选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个对子号（11,22,33,44,55,66）和1个不同号码(1,2,3,4,5,6)，组成三位数号码为一注",
    "help": "输入 1 个对子号（11,22,33,44,55,66）和1个不同号码(1,2,3,4,5,6),组成三位数号码为一注(顺序不限）",
    "example": "投注方案：112；开奖号码112,121,211，即中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二同号复选 150102000,
            ['id'=>'150102000', 'parent_id'=>'150100000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_ertonghao_fx', 'name'=>'二同号复选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150102001', 'parent_id'=>'150102000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_ertonghao_fuxuan', 'name'=>'复选', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_ethfx", "tag_check": "k3_ethfx", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_ethfx', 'lock_init_function'=>'initNumberTypeK3ETHFXLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[28.8]', 'prize_level_name'=>'["二同号复选"]', 'layout'=>'
{
    "desc": "选择对子（1，2，3，4，5，6）进行投注",
    "help": "选择对子（1，2，3，4，5，6）投注，开奖号码中包含选择的对子即中奖（不含豹子号）。",
    "example": "投注方案：1；开奖号码为：112，211，121，即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二同号",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-ertonghao k3-ertonghao-1",
                    "2": "k3-ertonghao k3-ertonghao-2",
                    "3": "k3-ertonghao k3-ertonghao-3",
                    "4": "k3-ertonghao k3-ertonghao-4",
                    "5": "k3-ertonghao k3-ertonghao-5",
                    "6": "k3-ertonghao k3-ertonghao-6"
                },
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二不同号 150200000,
            ['id'=>'150200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_erbutonghao_01', 'name'=>'二不同号', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二不同号 150201000,
            ['id'=>'150201000', 'parent_id'=>'150200000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_erbutonghao_02', 'name'=>'二不同号', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150201001', 'parent_id'=>'150201000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_erbutonghao_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_2mbudingwei", "tag_check": "n2_budingwei", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[14.4]', 'prize_level_name'=>'["二不同号标准复式"]', 'layout'=>'
{
    "desc": "从1-6中任意选择2个或2个以上号码。",
    "help": "从1-6中任意选择2个号码组成一注，顺序不限。",
    "example": "投注方案：2,5；开奖号码中出现：1个2、1个5 (顺序不限)，即中二不同号。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150201002', 'parent_id'=>'150201000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_erbutonghao_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_2mbudingwei", "tag_check": "n2_budingwei", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[14.4]', 'prize_level_name'=>'["二不同号标准单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1-6中两个不同的数字组成一注号码",
    "help": "开奖号码中至少包含所输入的两个数字即为中奖",
    "example": "投注方案：58；开奖号码：568，即中二不同号。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150201003', 'parent_id'=>'150201000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_erbutonghao_dantuo', 'name'=>'胆拖', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_ebthdt", "tag_check": "k3_ebthdt", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[14.4]', 'prize_level_name'=>'["二不同号胆拖"]', 'layout'=>'
{
    "desc": "从1-6中任意选择1个胆码以及1个以上的号码作为拖码。",
    "help": "从1-6中选择一个胆码和一个拖码组成一注，如果开奖号码中包含所选号码即为中奖",
    "example": "投注方案：胆码 3、拖码 4，开奖号码：34* （顺序不限），即中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三连号通选 150300000,
            ['id'=>'150300000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_sanlianhao_tongxuan_01', 'name'=>'三连号通选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三连号通选 150301000,
            ['id'=>'150301000', 'parent_id'=>'150300000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_sanlianhao_tongxuan_02', 'name'=>'三连号通选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150301001', 'parent_id'=>'150301000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_sanlianhao_tongxuan', 'name'=>'三连号通选', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_sbth", "tag_check": "k3_sbthtx", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_sbth', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[18]', 'prize_level_name'=>'["三连号通选"]', 'layout'=>'
{
    "desc": "对所有三个相连的号码进行投注。",
    "help": "开奖号码为三连号（123,234,345,456）即为中奖",
    "example": "投注方案：三连号通选，开奖号码123或234或345或456 即为中奖",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "号码",
                "no": "通选",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三不同号 150400000,
            ['id'=>'150400000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_sanbutonghao_01', 'name'=>'三不同号', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三不同号 150401000,
            ['id'=>'150401000', 'parent_id'=>'150400000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_sanbutonghao_02', 'name'=>'三不同号', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150401001', 'parent_id'=>'150401000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_sanbutonghao_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_sbth", "tag_check": "n3_zuliu", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_sbth', 'lock_init_function'=>'initNumberTypeSBTHLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[72]', 'prize_level_name'=>'["三不同号复式"]', 'layout'=>'
{
    "desc": "选择任意三个或以上的号码进行投注。",
    "help": "从1-6中任意选择3个号码组成一注，顺序不限，即为中奖。",
    "example": "投注方案：2,5,6；开奖号码中出现：1个2、1个5、1个6 (顺序不限)，即中三不同号。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150401002', 'parent_id'=>'150401000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_sanbutonghao_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_sbth", "tag_check": "n3_zuliu", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_sbth', 'lock_init_function'=>'initNumberTypeSBTHLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[72]', 'prize_level_name'=>'["三不同号单式"]', 'layout'=>'
{
    "desc": "手动输入号码，从 1 ~ 6 中任意输入三个不相同的号码组成一注",
    "help": "从 1 ~ 6 中任意输入三个不相同的号码组成一注，顺序不限，即为中奖。",
    "example": "投注方案：256；开奖号码中出现：1个2、1个5、1个6 (顺序不限)，即中三不同号。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150401003', 'parent_id'=>'150401000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_sanbutonghao_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_sbthhz", "tag_check": "k3_sbthhz", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_sbth', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[72]', 'prize_level_name'=>'["和值"]', 'layout'=>'
{
    "desc": "从6-15中任意选择1个或1个以上号码",
    "help": "所选数值等于开奖号码相加之和，且开奖号码为三个不同号码，即为中奖。",
    "example": "投注方案：和值6；开奖号码123,即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "6|7|8|9|10|11|12|13|14|15",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150401004', 'parent_id'=>'150401000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_sanbutonghao_dantuo', 'name'=>'胆拖', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[72]', 'prize_level_name'=>'["三不同号胆拖"]', 'layout'=>'
{
    "desc": "从1~6中，选取3个及以上的号码进行投注。",
    "help": "每注需包括1个胆码及2个拖码",
    "example": "投注方案：胆码2、拖码1 6，开奖号码：216，即中三不同号胆拖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "拖码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三同号 150500000,
            ['id'=>'150500000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_santonghao', 'name'=>'三同号', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三同号单选 150501000,
            ['id'=>'150501000', 'parent_id'=>'150500000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_santonghao_danxuan_02', 'name'=>'三同号单选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150501001', 'parent_id'=>'150501000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_santonghao_danxuan', 'name'=>'三同号单选', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_sthdx", "tag_check": "k3_sthdx", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_sthdx', 'lock_init_function'=>'initNumberTypeSTHDXLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[432]', 'prize_level_name'=>'["三同号单选"]', 'layout'=>'
{
    "desc": "选择任意一组以上三位相同的号码",
    "help": "从 1 ~ 6中选择任意一个或多个号码，若与开奖号相同即为中奖。",
    "example": "投注方案：2；开奖号码：222 ，即中三同号。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-santonghao k3-santonghao-1",
                    "2": "k3-santonghao k3-santonghao-2",
                    "3": "k3-santonghao k3-santonghao-3",
                    "4": "k3-santonghao k3-santonghao-4",
                    "5": "k3-santonghao k3-santonghao-5",
                    "6": "k3-santonghao k3-santonghao-6"
                },
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三同号通选 150502000,
            ['id'=>'150502000', 'parent_id'=>'150500000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_santonghao_tongxuan_02', 'name'=>'三同号通选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150502001', 'parent_id'=>'150502000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_santonghao_tongxuan', 'name'=>'三同号通选', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_sthdx", "tag_check": "k3_sthdx", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_sthdx', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[72]', 'prize_level_name'=>'["三同号通选"]', 'layout'=>'
{
    "desc": "对所有三同号（111,222,333,444,555,666）进行投注",
    "help": "投注后，开奖号码为任意数字的三重号，即为中奖",
    "example": "投注方案：通选；开奖号码中出现 3 个相同数字，即中三同号。",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "号码",
                "no": "通选",
                "place": 0,
                "cols": 1,
                "no_bg": "square"
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //和值 150600000,
            ['id'=>'150600000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_hezhi_01', 'name'=>'和值', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //和值 150601000,
            ['id'=>'150601000', 'parent_id'=>'150600000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_hezhi_02', 'name'=>'和值', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150601001', 'parent_id'=>'150601000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_hezhi', 'name'=>'和值', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_k3hz", "tag_check": "k3_k3hz", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeKSHZLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[432,144,72,43.2,28.8,20.571429,17.28,16]', 'prize_level_name'=>'["3,18", "4,17", "5,16", "6,15", "7,14", "8,13", "9,12", "10,11"]', 'layout'=>'
{
    "desc": "从3-18中任意选择1个或1个以上号码",
    "help": "所选数值等于开奖号码相加之和，即为中奖。",
    "example": "投注方案：4；开奖号码：112，开奖号码相加为 4，即中快三和值",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150601002', 'parent_id'=>'150601000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_hezhi_daxiaodanshuang', 'name'=>'大小单双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_k3hz", "tag_check": "k3_k3hz", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeKSHZLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "从“大、小、单、双”中任选一个成一注。",
    "help": "和值为 3 ~ 10 为小，11 ~ 18 为大；和值尾数单数为单，和值尾数双数为双。",
    "example": "投注方案：小，开奖号码：234，开奖号码相加为 9，即中和值：小。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "和值",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜必出号码 150700000,
            ['id'=>'150700000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_caibichuhaoma_01', 'name'=>'猜必出号码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜必出号码 150701000,
            ['id'=>'150701000', 'parent_id'=>'150700000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_caibichuhaoma_02', 'name'=>'猜必出号码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150701001', 'parent_id'=>'150701000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_caibichuhaoma', 'name'=>'猜必出号码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_cbchm", "tag_check": "k3_cbchm", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.747253]', 'prize_level_name'=>'["猜必出号码"]', 'layout'=>'
{
    "desc": "从1-6中任意选择1个或1个以上号码进行投注。",
    "help": "从1-6中任意选择1个号码组成一注，顺序不限，开奖号码包含此号码，即为中奖。",
    "example": "投注方案：2；开奖号码中包含2(顺序不限)，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜不出号码 150800000,
            ['id'=>'150800000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_caibuchuhaoma_01', 'name'=>'猜不出号码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜不出号码 150801000,
            ['id'=>'150801000', 'parent_id'=>'150800000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_caibuchuhaoma_02', 'name'=>'猜不出号码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150801001', 'parent_id'=>'150801000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_caibuchuhaoma', 'name'=>'猜不出号码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_cnchm", "tag_check": "k3_cnchm", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.456]', 'prize_level_name'=>'["猜不出号码"]', 'layout'=>'
{
    "desc": "从1-6中任意选择1个或1个以上号码进行投注。",
    "help": "从1-6中任意选择1个号码组成一注，顺序不限，开奖号码不包含此号码，即为中奖。",
    "example": "投注方案：2；开奖号码中不包含2，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //跨度 150900000,
            ['id'=>'150900000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_kuadu_01', 'name'=>'跨度', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //跨度 150901000,
            ['id'=>'150901000', 'parent_id'=>'150900000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_kuadu_02', 'name'=>'跨度', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150901001', 'parent_id'=>'150901000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_kuadu_0to5', 'name'=>'跨度', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_kuadu_0to5", "tag_check": "k3_kuadu_0to5", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[72, 14.4, 9, 8]', 'prize_level_name'=>'["跨度0", "跨度1,5", "跨度2,4", "跨度3"]', 'layout'=>'
{
    "desc": "从0-5中任意选择1个或1个以上号码",
    "help": "开奖号码中的数字最大数减去最小数结果为所选跨度，即中奖。",
    "example": "投注方案：5；开奖号码：126，最大数 6 减去最小数 1 等于 5，即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "跨度",
                "no": "0|1|2|3|4|5",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 3,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'150901002', 'parent_id'=>'150901000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_kuadu_dxds', 'name'=>'大小单双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_kuadudxds", "tag_check": "k3_kuadudxds", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.272727, 5.142857, 3.789474, 4.235294]', 'prize_level_name'=>'["大", "小", "单", "双"]', 'layout'=>'
{
    "desc": "“大、小、单、双”选1个或以上。",
    "help": "跨度0,1,2为小，3,4,5为大，1,3,5为单，0,2,4为双",
    "example": "投注方案：大；开奖号码：126，最大数 6 减去最小数 1 等于 5 为大，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "大小单双",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //牌点 151000000,
            ['id'=>'151000000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_paidian_01', 'name'=>'牌点', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //牌点 151001000,
            ['id'=>'151001000', 'parent_id'=>'151000000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_paidian_02', 'name'=>'牌点', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'151001001', 'parent_id'=>'151001000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_paidian_1to10', 'name'=>'牌点', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_paidian_1to10", "tag_check": "k3_paidian_1to10", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[16, 17.28, 19.636364, 24, 27]', 'prize_level_name'=>'["牌点1,10", "牌点2,9", "牌点3,8", "牌点4,7", "牌点5,6"]', 'layout'=>'
{
    "desc": "从1-10中任意选择1个或1个以上号码",
    "help": "开奖号码中的数字相加，若个位为所选号码（个位0则代表10点），即中奖。",
    "example": "投注方案：4；开奖号码：112，开奖数字相加为 4，即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "牌点",
                "no": "1|2|3|4|5|6|7|8|9|10",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'151001002', 'parent_id'=>'151001000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_paidian_dxds', 'name'=>'大小单双', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "k3_paidiandxds", "tag_check": "k3_paidiandxds", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{
    "desc": "“大、小、单、双”选一个组成一注。",
    "help": "牌点1,2,3,4,5为小，6,7,8,9,10为大，1,3,5,7,9为单，2,4,6,8,10为双",
    "example": "投注方案：单；开奖号码 126，开奖号码相加为 9，即中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "大小单双",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号 151100000,
            ['id'=>'151100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'51', 'ident'=>'k3_danhao_01', 'name'=>'单号', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号 151101000,
            ['id'=>'151101000', 'parent_id'=>'151100000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_danhao_02', 'name'=>'单号', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'151101001', 'parent_id'=>'151101000', 'lottery_method_category_id'=>'51', 'ident'=>'k3_danhao', 'name'=>'单号复式', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.747253]', 'prize_level_name'=>'["单号复式"]', 'layout'=>'
{
    "desc": "从1~6中任意选择1个以上号码。",
    "help": "从1~6中任意选择1个以上号码。",
    "example": "投注方案：1，开奖号码：1**，即中单号复选。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "1|2|3|4|5|6",
                "no_bg": {
                    "1": "k3-number k3-number-1",
                    "2": "k3-number k3-number-2",
                    "3": "k3-number k3-number-3",
                    "4": "k3-number k3-number-4",
                    "5": "k3-number k3-number-5",
                    "6": "k3-number k3-number-6"
                },
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 61 六合彩标准模式  ==================================
            //特码 160100000,
            ['id'=>'160100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tema_01', 'name'=>'特码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码 160101000,
            ['id'=>'160101000', 'parent_id'=>'160100000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tema_02', 'name'=>'特码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160101001', 'parent_id'=>'160101000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tema', 'name'=>'特码', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[98]', 'prize_level_name'=>'["特码"]', 'layout'=>'
{
    "desc": "由1~49中选择一个以上的数字。投注方案：36开奖号码：36，即中特码。",
    "help": "由1~49中选择一个以上的数字。投注方案：36开奖号码：36，即中特码。",
    "example": "投注方案：01；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特码01",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "特码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49",
                "no_bg": {
                    "01": "lhc-red",
                    "02": "lhc-red",
                    "03": "lhc-blue",
                    "04": "lhc-blue",
                    "05": "lhc-green",
                    "06": "lhc-green",
                    "07": "lhc-red",
                    "08": "lhc-red",
                    "09": "lhc-blue",
                    "10": "lhc-blue",
                    "11": "lhc-green",
                    "12": "lhc-red",
                    "13": "lhc-red",
                    "14": "lhc-blue",
                    "15": "lhc-blue",
                    "16": "lhc-green",
                    "17": "lhc-green",
                    "18": "lhc-red",
                    "19": "lhc-red",
                    "20": "lhc-blue",
                    "21": "lhc-green",
                    "22": "lhc-green",
                    "23": "lhc-red",
                    "24": "lhc-red",
                    "25": "lhc-blue",
                    "26": "lhc-blue",
                    "27": "lhc-green",
                    "28": "lhc-green",
                    "29": "lhc-red",
                    "30": "lhc-red",
                    "31": "lhc-blue",
                    "32": "lhc-green",
                    "33": "lhc-green",
                    "34": "lhc-red",
                    "35": "lhc-red",
                    "36": "lhc-blue",
                    "37": "lhc-blue",
                    "38": "lhc-green",
                    "39": "lhc-green",
                    "40": "lhc-red",
                    "41": "lhc-blue",
                    "42": "lhc-blue",
                    "43": "lhc-green",
                    "44": "lhc-green",
                    "45": "lhc-red",
                    "46": "lhc-red",
                    "47": "lhc-blue",
                    "48": "lhc-blue",
                    "49": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "big_index": 25,
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码大小单双 160200000,
            ['id'=>'160200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_daxiao_01', 'name'=>'特码大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码大小单双 160201000,
            ['id'=>'160201000', 'parent_id'=>'160200000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_daxiao_02', 'name'=>'特码大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160201001', 'parent_id'=>'160201000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_daxiao', 'name'=>'特码大小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.92,4.083333]', 'prize_level_name'=>'["特大","特小"]', 'layout'=>'
{
    "desc": "开出的特码大于或等于25为特大；小于等于24为特小。",
    "help": "开出的特码大于或等于25为特大；小于等于24为特小。",
    "example": "投注方案：小；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特码大小-小",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "特码大小",
                "no": "大|小",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160201002', 'parent_id'=>'160201000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_danshuang', 'name'=>'特码单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.92,4.083333]', 'prize_level_name'=>'["特单","特双"]', 'layout'=>'
{
    "desc": "特码为单数为特单；为双数叫特双。",
    "help": "特码为单数为特单；为双数叫特双。",
    "example": "投注方案：单；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特码单双-单",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "特码单双",
                "no": "单|双",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160201003', 'parent_id'=>'160201000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_daxiaodanshuang', 'name'=>'特码大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.538462,8.166667,8.166667,8.166667]', 'prize_level_name'=>'["特大单","特大双","特小单","特小双"]', 'layout'=>'
{
    "desc": "特码大于或等于25为特大；小于等于24为特小；单数为特单；双数叫特双。",
    "help": "特码大于或等于25为特大；小于等于24为特小；单数为特单；双数叫特双。",
    "example": "投注方案：特小单；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特码大小单双-特小单",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特码单双",
                "no": "特大单|特小单|特大双|特小双",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160201004', 'parent_id'=>'160201000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_hedaxiao', 'name'=>'特合大小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.92,4.083333]', 'prize_level_name'=>'["特合大","特合小"]', 'layout'=>'
{
    "desc": "以特码个位和十位数之和来决定大小，合数大于或等于7为大，小于或等于6为小。",
    "help": "以特码个位和十位数之和来决定大小，合数大于或等于7为大，小于或等于6为小。",
    "example": "投注方案：特合小；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特合大小-特合小",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特合大小",
                "no": "特合大|特合小",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160201005', 'parent_id'=>'160201000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_hedanshuang', 'name'=>'特合单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.92,4.083333]', 'prize_level_name'=>'["特合单","特合双"]', 'layout'=>'
{
    "desc": "以特码个位和十位数之和来决定单双，如01、12、32为合单；如02、11、33为合双。",
    "help": "以特码个位和十位数之和来决定单双，如01、12、32为合单；如02、11、33为合双。",
    "example": "投注方案：特合单；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特合单双-特合单",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特合单双",
                "no": "特合单|特合双",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160201006', 'parent_id'=>'160201000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_weidaxiao', 'name'=>'特尾大小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.92,4.083333]', 'prize_level_name'=>'["特尾大","特尾小"]', 'layout'=>'
{
    "desc": "以特码尾数若0尾-4尾为小、5尾-9尾为大；如01、32、44为特尾小；如05、18、19为特尾大。",
    "help": "以特码尾数若0尾-4尾为小、5尾-9尾为大；如01、32、44为特尾小；如05、18、19为特尾大。",
    "example": "投注方案：特尾小；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特尾大小-特合小",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特尾大小",
                "no": "特尾大|特尾小",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码生肖 160300000,
            ['id'=>'160300000', 'parent_id'=>'0', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_shengxiao_01', 'name'=>'特码生肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码生肖 160301000,
            ['id'=>'160301000', 'parent_id'=>'160300000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_shengxiao_02', 'name'=>'特码生肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160301001', 'parent_id'=>'160301000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_shengxiao', 'name'=>'特码生肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[19.6,24.5]', 'prize_level_name'=>'["当年生肖","其它生肖"]', 'layout'=>'
{
    "desc": "若开出的特码坐落于投注的生肖所属号码内，视为中奖，其余情形视为不中奖。",
    "help": "生肖为鼠、牛、虎、兔、龙、蛇、马、羊、猴、鸡、狗、猪，每一个生肖都会有各自所属的号码，再以生肖投注。",
    "example": "投注方案：鸡；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特码生肖-鸡",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "特码生肖",
                "no": "鼠|牛|虎|兔|龙|蛇|马|羊|猴|鸡|狗|猪",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码波色 160400000,
            ['id'=>'160400000', 'parent_id'=>'0', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_bose_01', 'name'=>'特码波色', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码波色 160401000,
            ['id'=>'160401000', 'parent_id'=>'160400000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_bose_02', 'name'=>'特码波色', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160401001', 'parent_id'=>'160401000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_bose', 'name'=>'特波', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[5.764706,6.125,6.125]', 'prize_level_name'=>'["红波","蓝波","绿波"]', 'layout'=>'
{
    "desc": "以特码的波色下注，开奖的球色与下注的颜色相同，视为中奖。",
    "help": "以特码的波色下注，开奖的球色与下注的颜色相同，视为中奖。",
    "example": "投注方案：红波；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特波-红波",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特波",
                "no": "红波|蓝波|绿波",
                "no_bg": {
                    "红波": "lhc-red",
                    "蓝波": "lhc-blue",
                    "绿波": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160401002', 'parent_id'=>'160401000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_bosedaxiao', 'name'=>'特波大小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[14,9.8,10.888889,14,10.888889,14]', 'prize_level_name'=>'["红大","红小","蓝大","蓝小","绿大","绿小"]', 'layout'=>'
{
    "desc": "以特码的波色、大小为一个投注组合，当期特码开出符合投注组合，即视为中奖。",
    "help": "以特码的波色、大小为一个投注组合，当期特码开出符合投注组合，即视为中奖。",
    "example": "投注方案：红小；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特波大小-红小",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特波大小",
                "no": "红大|红小|蓝大|蓝小|绿大|绿小",
                "no_bg": {
                    "红大": "lhc-red",
                    "红小": "lhc-red",
                    "蓝大": "lhc-blue",
                    "蓝小": "lhc-blue",
                    "绿大": "lhc-green",
                    "绿小": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160401003', 'parent_id'=>'160401000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_bosedanshuang', 'name'=>'特波单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[12.25,10.888889,12.25,12.25,10.888889,14]', 'prize_level_name'=>'["红单","红双","蓝单","蓝双","绿单","绿双"]', 'layout'=>'
{
    "desc": "以特码的波色、单双为一个投注组合，当期特码开出符合投注组合，即视为中奖。",
    "help": "以特码的波色、单双为一个投注组合，当期特码开出符合投注组合，即视为中奖。",
    "example": "投注方案：红单；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特波单双-红单",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "特波大小",
                "no": "红单|红双|蓝单|蓝双|绿单|绿双",
                "no_bg": {
                    "红单": "lhc-red",
                    "红双": "lhc-red",
                    "蓝单": "lhc-blue",
                    "蓝双": "lhc-blue",
                    "绿单": "lhc-green",
                    "绿双": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160401004', 'parent_id'=>'160401000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_tm_bosedaxiaodanshuang', 'name'=>'特波大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[32.666667,19.6,24.5,19.6,19.6,32.666667,24.5,24.5,19.6,24.5,24.5,32.666667]', 'prize_level_name'=>'["红大单","红小单","红大双","红小双","蓝大单","蓝小单","蓝大双","蓝小双","绿大单","绿小单","绿大双","绿小双"]', 'layout'=>'
{
    "desc": "以特码的波色、单双及大小为一个投注组合，当期特码开出符合投注组合，即视为中奖。",
    "help": "以特码的波色、单双及大小为一个投注组合，当期特码开出符合投注组合，即视为中奖。",
    "example": "投注方案：红小单；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中特波大小单双-红小单",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "红",
                "no": "红大单|红小单|红大双|红小双",
                "no_bg": {
                    "红大单": "lhc-red",
                    "红小单": "lhc-red",
                    "红大双": "lhc-red",
                    "红小双": "lhc-red"
                },
                "place": 0,
                "cols": 0
            },
            {
                "title": "蓝",
                "no": "蓝大单|蓝小单|蓝大双|蓝小双",
                "no_bg": {
                    "蓝大单": "lhc-blue",
                    "蓝小单": "lhc-blue",
                    "蓝大双": "lhc-blue",
                    "蓝小双": "lhc-blue"
                },
                "place": 0,
                "cols": 0
            },
            {
                "title": "绿",
                "no": "绿大单|绿小单|绿大双|绿小双",
                "no_bg": {
                    "绿大单": "lhc-green",
                    "绿小单": "lhc-green",
                    "绿大双": "lhc-green",
                    "绿小双": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //正码 160500000,
            ['id'=>'160500000', 'parent_id'=>'0', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_zhengma_01', 'name'=>'正码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //正码 160501000,
            ['id'=>'160501000', 'parent_id'=>'160500000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_zhengma_02', 'name'=>'正码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160501001', 'parent_id'=>'160501000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_zhengma', 'name'=>'正码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[16.333333]', 'prize_level_name'=>'["正码"]', 'layout'=>'
{
    "desc": "由1~49中选择一个以上的数字。投注方案：36开奖号码：36，即中正码。",
    "help": "由1~49中选择一个以上的数字。投注方案：36开奖号码：36，即中正码。",
    "example": "投注方案：02；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中正码-02",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "正码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49",
                "no_bg": {
                    "01": "lhc-red",
                    "02": "lhc-red",
                    "03": "lhc-blue",
                    "04": "lhc-blue",
                    "05": "lhc-green",
                    "06": "lhc-green",
                    "07": "lhc-red",
                    "08": "lhc-red",
                    "09": "lhc-blue",
                    "10": "lhc-blue",
                    "11": "lhc-green",
                    "12": "lhc-red",
                    "13": "lhc-red",
                    "14": "lhc-blue",
                    "15": "lhc-blue",
                    "16": "lhc-green",
                    "17": "lhc-green",
                    "18": "lhc-red",
                    "19": "lhc-red",
                    "20": "lhc-blue",
                    "21": "lhc-green",
                    "22": "lhc-green",
                    "23": "lhc-red",
                    "24": "lhc-red",
                    "25": "lhc-blue",
                    "26": "lhc-blue",
                    "27": "lhc-green",
                    "28": "lhc-green",
                    "29": "lhc-red",
                    "30": "lhc-red",
                    "31": "lhc-blue",
                    "32": "lhc-green",
                    "33": "lhc-green",
                    "34": "lhc-red",
                    "35": "lhc-red",
                    "36": "lhc-blue",
                    "37": "lhc-blue",
                    "38": "lhc-green",
                    "39": "lhc-green",
                    "40": "lhc-red",
                    "41": "lhc-blue",
                    "42": "lhc-blue",
                    "43": "lhc-green",
                    "44": "lhc-green",
                    "45": "lhc-red",
                    "46": "lhc-red",
                    "47": "lhc-blue",
                    "48": "lhc-blue",
                    "49": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "big_index": 25,
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //连肖连尾
            ['id'=>'160600000', 'parent_id'=>'0', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianxiao_lianwei', 'name'=>'连肖连尾', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //连肖
            ['id'=>'160601000', 'parent_id'=>'160600000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianxiao', 'name'=>'连肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160601001', 'parent_id'=>'160601000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianxiao_yixiao', 'name'=>'一肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.4,3.8]', 'prize_level_name'=>'["当年生肖","其它生肖"]', 'layout'=>'
{
    "desc": "从鼠至猪12生肖中任选一个生肖为一注。",
    "help": "每注的每个生肖中如果都有当期开奖的所有7个号码中对应的生肖，则视为中奖。如果其中每个生肖没有当期开奖的所有7个号码对应的生肖，则视为不中奖。",
    "example": "投注方案：鼠；<br />开奖生肖：鼠 牛 虎 兔 龙 蛇 + 马，<br />即中奖",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "一肖",
                "no": "鼠|牛|虎|兔|龙|蛇|马|羊|猴|鸡|狗|猪",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160601002', 'parent_id'=>'160601000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianxiao_erxiao', 'name'=>'二肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[5.916,7.5]', 'prize_level_name'=>'["当年生肖","其它生肖"]', 'layout'=>'
{
    "desc": "从鼠至猪12生肖中任选两个生肖为一注。",
    "help": "每注的每个生肖中如果都有当期开奖的所有7个号码中对应的生肖，则视为中奖。如果其中每个生肖没有当期开奖的所有7个号码对应的生肖，则视为不中奖。",
    "example": "投注方案：鼠牛；<br />开奖生肖：鼠 牛 虎 兔 龙 蛇 + 马，<br />即中奖",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "二肖",
                "no": "鼠|牛|虎|兔|龙|蛇|马|羊|猴|鸡|狗|猪",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160601003', 'parent_id'=>'160601000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianxiao_sanxiao', 'name'=>'三肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[16.134,20.7]', 'prize_level_name'=>'["当年生肖","其它生肖"]', 'layout'=>'
{
    "desc": "从鼠至猪12生肖中任选三个生肖为一注。",
    "help": "每注的每个生肖中如果都有当期开奖的所有7个号码中对应的生肖，则视为中奖。如果其中每个生肖没有当期开奖的所有7个号码对应的生肖，则视为不中奖。",
    "example": "投注方案：鼠牛虎；<br />开奖生肖：鼠 牛 虎 兔 龙 蛇 + 马，<br />即中奖",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "三肖",
                "no": "鼠|牛|虎|兔|龙|蛇|马|羊|猴|鸡|狗|猪",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160601004', 'parent_id'=>'160601000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianxiao_sixiao', 'name'=>'四肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[45.6,60]', 'prize_level_name'=>'["当年生肖","其它生肖"]', 'layout'=>'
{
    "desc": "从鼠至猪12生肖中任选四个个生肖为一注。",
    "help": "每注的每个生肖中如果都有当期开奖的所有7个号码中对应的生肖，则视为中奖。如果其中每个生肖没有当期开奖的所有7个号码对应的生肖，则视为不中奖。",
    "example": "投注方案：鼠牛虎兔；<br />开奖生肖：鼠 牛 虎 兔 龙 蛇 + 马，<br />即中奖",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "四肖",
                "no": "鼠|牛|虎|兔|龙|蛇|马|羊|猴|鸡|狗|猪",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160601005', 'parent_id'=>'160601000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianxiao_wuxiao', 'name'=>'五肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[156.92,195.16]', 'prize_level_name'=>'["当年生肖","其它生肖"]', 'layout'=>'
{
    "desc": "从鼠至猪12生肖中任选五个生肖为一注。",
    "help": "每注的每个生肖中如果都有当期开奖的所有7个号码中对应的生肖，则视为中奖。如果其中每个生肖没有当期开奖的所有7个号码对应的生肖，则视为不中奖。",
    "example": "投注方案：鼠牛虎兔龙；<br />开奖生肖：鼠 牛 虎 兔 龙 蛇 + 马，<br />即中奖",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "五肖",
                "no": "鼠|牛|虎|兔|龙|蛇|马|羊|猴|鸡|狗|猪",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //连尾
            ['id'=>'160602000', 'parent_id'=>'160600000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianwei', 'name'=>'连尾', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160602001', 'parent_id'=>'160602000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianwei_yiwei', 'name'=>'一尾', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.14,3.54]', 'prize_level_name'=>'["0尾","1-9尾"]', 'layout'=>'
{
    "desc": "从0至9十个数字中任选一个数字为一注。",
    "help": "一个尾数对应多个号码，不论同尾数的号码出现一个或多个，派彩只派一次。每个尾数都有自己的赔率，下注组合的总赔率，取该组合尾数的最低赔率为总赔率。",
    "example": "投注方案：1；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "一尾",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160602002', 'parent_id'=>'160602000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianwei_erwei', 'name'=>'二尾', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.1,6]', 'prize_level_name'=>'["0尾","1-9尾"]', 'layout'=>'
{
    "desc": "从0至9十个数字中任选二个数字为一注。",
    "help": "每注的每个尾数中如果都有当期开奖的所有7个号码中对应的尾数，则视为中奖。如果其中一个或多个尾数没有当期开奖的所有7个号码对应的尾数，则视为不中奖。",
    "example": "投注方案：12；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "二尾",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160602003', 'parent_id'=>'160602000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianwei_sanwei', 'name'=>'三尾', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[15,12.6]', 'prize_level_name'=>'["0尾","1-9尾"]', 'layout'=>'
{
    "desc": "从0至9十个数字中任选三个数字为一注。",
    "help": "每注的每个尾数中如果都有当期开奖的所有7个号码中对应的尾数，则视为中奖。如果其中一个或多个尾数没有当期开奖的所有7个号码对应的尾数，则视为不中奖。",
    "example": "投注方案：012；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "三尾",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160602004', 'parent_id'=>'160602000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianwei_siwei', 'name'=>'四尾', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[36,30]', 'prize_level_name'=>'["0尾","1-9尾"]', 'layout'=>'
{
    "desc": "从0至9十个数字中任选四个数字为一注。",
    "help": "每注的每个尾数中如果都有当期开奖的所有7个号码中对应的尾数，则视为中奖。如果其中一个或多个尾数没有当期开奖的所有7个号码对应的尾数，则视为不中奖。",
    "example": "投注方案：0126；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "四尾",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160602005', 'parent_id'=>'160602000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianwei_wuwei', 'name'=>'五尾', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[102,84]', 'prize_level_name'=>'["0尾","1-9尾"]', 'layout'=>'
{
    "desc": "从0至9十个数字中任选五个数字为一注。",
    "help": "每注的每个尾数中如果都有当期开奖的所有7个号码中对应的尾数，则视为中奖。如果其中一个或多个尾数没有当期开奖的所有7个号码对应的尾数，则视为不中奖。",
    "example": "投注方案：01267；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "五尾",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //连码
            ['id'=>'160700000', 'parent_id'=>'0', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianma_01', 'name'=>'连码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //连码
            ['id'=>'160701000', 'parent_id'=>'160700000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianma', 'name'=>'连码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160701001', 'parent_id'=>'160701000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianma_erzhongte', 'name'=>'二中特', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[62,102]', 'prize_level_name'=>'["二正码","一正一特"]', 'layout'=>'
{
    "desc": "从1至49，四十九个数字中任选两个数字为一注。",
    "help": "若其中一个是正码，一个是特别号码，即为中奖。",
    "example": "投注方案：01 02；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49",
                "no_bg": {
                    "01": "lhc-red",
                    "02": "lhc-red",
                    "03": "lhc-blue",
                    "04": "lhc-blue",
                    "05": "lhc-green",
                    "06": "lhc-green",
                    "07": "lhc-red",
                    "08": "lhc-red",
                    "09": "lhc-blue",
                    "10": "lhc-blue",
                    "11": "lhc-green",
                    "12": "lhc-red",
                    "13": "lhc-red",
                    "14": "lhc-blue",
                    "15": "lhc-blue",
                    "16": "lhc-green",
                    "17": "lhc-green",
                    "18": "lhc-red",
                    "19": "lhc-red",
                    "20": "lhc-blue",
                    "21": "lhc-green",
                    "22": "lhc-green",
                    "23": "lhc-red",
                    "24": "lhc-red",
                    "25": "lhc-blue",
                    "26": "lhc-blue",
                    "27": "lhc-green",
                    "28": "lhc-green",
                    "29": "lhc-red",
                    "30": "lhc-red",
                    "31": "lhc-blue",
                    "32": "lhc-green",
                    "33": "lhc-green",
                    "34": "lhc-red",
                    "35": "lhc-red",
                    "36": "lhc-blue",
                    "37": "lhc-blue",
                    "38": "lhc-green",
                    "39": "lhc-green",
                    "40": "lhc-red",
                    "41": "lhc-blue",
                    "42": "lhc-blue",
                    "43": "lhc-green",
                    "44": "lhc-green",
                    "45": "lhc-red",
                    "46": "lhc-red",
                    "47": "lhc-blue",
                    "48": "lhc-blue",
                    "49": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160701002', 'parent_id'=>'160701000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianma_erquanzhong', 'name'=>'二全中', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[126]', 'prize_level_name'=>'["二全中"]', 'layout'=>'
{
    "desc": "从1至49，四十九个数字中任选两个数字为一注。",
    "help": "二个号码都是开奖号码之正码，视为中奖，其余情形视为不中奖（含一个正码加一个特别号码之情形）",
    "example": "投注方案：02 21；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49",
                "no_bg": {
                    "01": "lhc-red",
                    "02": "lhc-red",
                    "03": "lhc-blue",
                    "04": "lhc-blue",
                    "05": "lhc-green",
                    "06": "lhc-green",
                    "07": "lhc-red",
                    "08": "lhc-red",
                    "09": "lhc-blue",
                    "10": "lhc-blue",
                    "11": "lhc-green",
                    "12": "lhc-red",
                    "13": "lhc-red",
                    "14": "lhc-blue",
                    "15": "lhc-blue",
                    "16": "lhc-green",
                    "17": "lhc-green",
                    "18": "lhc-red",
                    "19": "lhc-red",
                    "20": "lhc-blue",
                    "21": "lhc-green",
                    "22": "lhc-green",
                    "23": "lhc-red",
                    "24": "lhc-red",
                    "25": "lhc-blue",
                    "26": "lhc-blue",
                    "27": "lhc-green",
                    "28": "lhc-green",
                    "29": "lhc-red",
                    "30": "lhc-red",
                    "31": "lhc-blue",
                    "32": "lhc-green",
                    "33": "lhc-green",
                    "34": "lhc-red",
                    "35": "lhc-red",
                    "36": "lhc-blue",
                    "37": "lhc-blue",
                    "38": "lhc-green",
                    "39": "lhc-green",
                    "40": "lhc-red",
                    "41": "lhc-blue",
                    "42": "lhc-blue",
                    "43": "lhc-green",
                    "44": "lhc-green",
                    "45": "lhc-red",
                    "46": "lhc-red",
                    "47": "lhc-blue",
                    "48": "lhc-blue",
                    "49": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160701003', 'parent_id'=>'160701000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianma_sanzhongersan', 'name'=>'三中二三', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[40,200]', 'prize_level_name'=>'["中二","中三"]', 'layout'=>'
{
    "desc": "从1至49，四十九个数字中任选三个数字为一注。",
    "help": "选号三个号为一组，若其中两个号是开奖号码的正码，中奖三中二。若其中三个号是开奖号码的正码，中奖三中三",
    "example": "投注方案：02 21 27；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49",
                "no_bg": {
                    "01": "lhc-red",
                    "02": "lhc-red",
                    "03": "lhc-blue",
                    "04": "lhc-blue",
                    "05": "lhc-green",
                    "06": "lhc-green",
                    "07": "lhc-red",
                    "08": "lhc-red",
                    "09": "lhc-blue",
                    "10": "lhc-blue",
                    "11": "lhc-green",
                    "12": "lhc-red",
                    "13": "lhc-red",
                    "14": "lhc-blue",
                    "15": "lhc-blue",
                    "16": "lhc-green",
                    "17": "lhc-green",
                    "18": "lhc-red",
                    "19": "lhc-red",
                    "20": "lhc-blue",
                    "21": "lhc-green",
                    "22": "lhc-green",
                    "23": "lhc-red",
                    "24": "lhc-red",
                    "25": "lhc-blue",
                    "26": "lhc-blue",
                    "27": "lhc-green",
                    "28": "lhc-green",
                    "29": "lhc-red",
                    "30": "lhc-red",
                    "31": "lhc-blue",
                    "32": "lhc-green",
                    "33": "lhc-green",
                    "34": "lhc-red",
                    "35": "lhc-red",
                    "36": "lhc-blue",
                    "37": "lhc-blue",
                    "38": "lhc-green",
                    "39": "lhc-green",
                    "40": "lhc-red",
                    "41": "lhc-blue",
                    "42": "lhc-blue",
                    "43": "lhc-green",
                    "44": "lhc-green",
                    "45": "lhc-red",
                    "46": "lhc-red",
                    "47": "lhc-blue",
                    "48": "lhc-blue",
                    "49": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160701004', 'parent_id'=>'160701000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianma_sanquanzhong', 'name'=>'三全中', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1300]', 'prize_level_name'=>'["三全中"]', 'layout'=>'
{
    "desc": "从1至49，四十九个数字中任选三个数字为一注。",
    "help": "所投注的每三个号码为一组合，若三个号码都是开奖号码之正码，视为中奖，其余情形视为 不中奖。",
    "example": "投注方案：02 21 27；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49",
                "no_bg": {
                    "01": "lhc-red",
                    "02": "lhc-red",
                    "03": "lhc-blue",
                    "04": "lhc-blue",
                    "05": "lhc-green",
                    "06": "lhc-green",
                    "07": "lhc-red",
                    "08": "lhc-red",
                    "09": "lhc-blue",
                    "10": "lhc-blue",
                    "11": "lhc-green",
                    "12": "lhc-red",
                    "13": "lhc-red",
                    "14": "lhc-blue",
                    "15": "lhc-blue",
                    "16": "lhc-green",
                    "17": "lhc-green",
                    "18": "lhc-red",
                    "19": "lhc-red",
                    "20": "lhc-blue",
                    "21": "lhc-green",
                    "22": "lhc-green",
                    "23": "lhc-red",
                    "24": "lhc-red",
                    "25": "lhc-blue",
                    "26": "lhc-blue",
                    "27": "lhc-green",
                    "28": "lhc-green",
                    "29": "lhc-red",
                    "30": "lhc-red",
                    "31": "lhc-blue",
                    "32": "lhc-green",
                    "33": "lhc-green",
                    "34": "lhc-red",
                    "35": "lhc-red",
                    "36": "lhc-blue",
                    "37": "lhc-blue",
                    "38": "lhc-green",
                    "39": "lhc-green",
                    "40": "lhc-red",
                    "41": "lhc-blue",
                    "42": "lhc-blue",
                    "43": "lhc-green",
                    "44": "lhc-green",
                    "45": "lhc-red",
                    "46": "lhc-red",
                    "47": "lhc-blue",
                    "48": "lhc-blue",
                    "49": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'160701005', 'parent_id'=>'160701000', 'lottery_method_category_id'=>'61', 'ident'=>'lhc_lianma_siquanzhong', 'name'=>'四全中', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20000]', 'prize_level_name'=>'["四全中"]', 'layout'=>'
{
    "desc": "从1至49，四十九个数字中任选四个数字为一注。",
    "help": "所投注的每四个号码为一组合，若四个号码都是开奖号码之正码，视为中奖，其余情形视为 不中奖。",
    "example": "投注方案：02 21 27 30；<br />开奖号码：02 21 27 30 36 37 + 01，<br />即中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "号码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49",
                "no_bg": {
                    "01": "lhc-red",
                    "02": "lhc-red",
                    "03": "lhc-blue",
                    "04": "lhc-blue",
                    "05": "lhc-green",
                    "06": "lhc-green",
                    "07": "lhc-red",
                    "08": "lhc-red",
                    "09": "lhc-blue",
                    "10": "lhc-blue",
                    "11": "lhc-green",
                    "12": "lhc-red",
                    "13": "lhc-red",
                    "14": "lhc-blue",
                    "15": "lhc-blue",
                    "16": "lhc-green",
                    "17": "lhc-green",
                    "18": "lhc-red",
                    "19": "lhc-red",
                    "20": "lhc-blue",
                    "21": "lhc-green",
                    "22": "lhc-green",
                    "23": "lhc-red",
                    "24": "lhc-red",
                    "25": "lhc-blue",
                    "26": "lhc-blue",
                    "27": "lhc-green",
                    "28": "lhc-green",
                    "29": "lhc-red",
                    "30": "lhc-red",
                    "31": "lhc-blue",
                    "32": "lhc-green",
                    "33": "lhc-green",
                    "34": "lhc-red",
                    "35": "lhc-red",
                    "36": "lhc-blue",
                    "37": "lhc-blue",
                    "38": "lhc-green",
                    "39": "lhc-green",
                    "40": "lhc-red",
                    "41": "lhc-blue",
                    "42": "lhc-blue",
                    "43": "lhc-green",
                    "44": "lhc-green",
                    "45": "lhc-red",
                    "46": "lhc-red",
                    "47": "lhc-blue",
                    "48": "lhc-blue",
                    "49": "lhc-green"
                },
                "place": 0,
                "cols": 0
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 71 PK10 标准模式  ==================================
            //猜冠军 170100000,
            ['id'=>'170100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanjun_01', 'name'=>'猜冠军', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜冠军 170101000,
            ['id'=>'170101000', 'parent_id'=>'170100000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanjun_02', 'name'=>'猜冠军', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170101001', 'parent_id'=>'170101000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanjun', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_guanjun", "tag_check": "pk10_guanjun", "code_count": 1, "start_position": 0}', 'lock_table_name'=>'lock_guanjun', 'lock_init_function'=>'initPk10GuanjunLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["猜冠军"]', 'layout'=>'
{
    "desc": "从01-10中任意选择1个以上号码。",
    "help": "从01-10中选择一个号码，只要开奖的冠军车号与所选号码一致即中奖。如：选择05，开奖冠军车号为05，即为中奖",
    "example": "投注方案：01；开奖冠军车号是01，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "冠军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜冠亚军 170200000,
            ['id'=>'170200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanyajun_01', 'name'=>'猜冠亚军', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜冠亚军 170201000,
            ['id'=>'170201000', 'parent_id'=>'170200000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanyajun_02', 'name'=>'猜冠亚军', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170201001', 'parent_id'=>'170201000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanyajun_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_guanyajun", "tag_check": "pk10_guanyajun", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_guanyajun', 'lock_init_function'=>'initPk10GuanyajunLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[180]', 'prize_level_name'=>'["猜冠亚军_复式"]', 'layout'=>'
{
    "desc": "从01-10选择2个号码组成一注。",
    "help": "从01-10选择2个号码组成一注，只要开奖的冠军车号、亚军车号与所选号码相同且顺序一致，即为中奖。",
    "example": "投注方案：05 08；开奖冠军车号是05，亚军车号是08，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "冠军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 0,
                "cols": 1
            },
            {
                "title": "亚军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 1,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170201002', 'parent_id'=>'170201000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanyajun_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_guanyajun", "tag_check": "pk10_guanyajun", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_guanyajun', 'lock_init_function'=>'initPk10GuanyajunLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[180]', 'prize_level_name'=>'["猜冠亚军_单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入2个两位数号码。",
    "help": "手动输入2个两位数号码组成一注，所选号码与开奖冠军、亚军相同，且顺序一致，即为中奖。",
    "example": "投注方案：01 02,03 04,05 06； 开奖冠亚军的车号是01 02或者03 04或者05 06，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": ";"
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //冠亚和值 170202000,
            ['id'=>'170202000', 'parent_id'=>'170200000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanyajun_03', 'name'=>'冠亚和值', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170202001', 'parent_id'=>'170202000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanyahezhi_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_danshuang', 'lock_init_function'=>'initPk10DanshuangLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.6,4.5]', 'prize_level_name'=>'["单","双"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应冠亚和值的单双与所选项一致即中奖。",
    "example": "“冠亚和值”为单视为投注“单”的注单视为中奖，为双视为投注“双”的注单视为中奖，其余视为不中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "冠亚和单双",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170202002', 'parent_id'=>'170202000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanyahezhi_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_danshuang', 'lock_init_function'=>'initPk10DanshuangLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4.5,3.6]', 'prize_level_name'=>'["大","小"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应冠亚和值的大小与所选项一致即中奖。",
    "example": "“冠亚和值”大于11时投注“大”的注单视为中奖，小于或等于11时投注“小”的注单视为中奖，其余视为不中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "冠亚和大小",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170202003', 'parent_id'=>'170202000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_guanyahezhi_zhiding', 'name'=>'指定', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_danshuang', 'lock_init_function'=>'initPk10DanshuangLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[90,45,30,22.5,18]', 'prize_level_name'=>'["和值3,4,18,19","和值5,6,16,17","和值7,8,14,15","和值9,10,12,13","和值11"]', 'layout'=>'
{
    "desc": "选择应“冠亚和值”数字组成一注。",
    "help": "选择应“冠亚和值”数字组成一注。",
    "example": "“冠亚和值”可能出现的结果为3～19， 投中对应“冠亚和值”数字的视为中奖，其余视为不中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "冠亚和值",
                "no": "3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜前三名 170300000,
            ['id'=>'170300000', 'parent_id'=>'0', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qiansanming_01', 'name'=>'猜前三名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜前三名 170301000,
            ['id'=>'170301000', 'parent_id'=>'170300000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qiansanming_02', 'name'=>'猜前三名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170301001', 'parent_id'=>'170301000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qiansanming_fushi', 'name'=>'复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_qiansanming", "tag_check": "pk10_qiansanming", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_qiansanming', 'lock_init_function'=>'initPk10QiansanmingLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1440]', 'prize_level_name'=>'["猜前三名_复式"]', 'layout'=>'
{
    "desc": "从01-10选择3个号码组成一注。",
    "help": "从01-10中选择三个号码，只要开奖冠军、亚军、季军的车号与所选号码相同且顺序一致即中奖。如：冠军选择01，亚军选择02，季军选择03，开奖的冠军车号01、亚军02、季军03，即为中奖。",
    "example": "投注方案：03 04 05；开奖号码：03 04 05，即为中奖",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "冠军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 0,
                "cols": 1
            },
            {
                "title": "亚军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 1,
                "cols": 1
            },
            {
                "title": "季军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170301002', 'parent_id'=>'170301000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qiansanming_danshi', 'name'=>'单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_qiansanming", "tag_check": "pk10_qiansanming", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_qiansanming', 'lock_init_function'=>'initPk10QiansanmingLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[1440]', 'prize_level_name'=>'["猜前三名_单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入3个两位数号码组成一注。",
    "help": "手动输入3个两位数号码组成一注，所选号码与开奖冠亚季军的车号相同，且顺序一致，即为中奖。",
    "example": "投注方案：01 02 03,04 05 06,07 08 09； 开奖冠亚季军的车号是01 02 03或者04 05 06或者07 08 09，即为中奖",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜前四名 170400000,
            ['id'=>'170400000', 'parent_id'=>'0', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qiansiming_01', 'name'=>'猜前四名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜前四名 170401000,
            ['id'=>'170401000', 'parent_id'=>'170400000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qiansiming_02', 'name'=>'猜前四名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170401001', 'parent_id'=>'170401000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qiansiming_fushi', 'name'=>'复式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qiansanming', 'lock_init_function'=>'initPk10QiansanmingLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[10080]', 'prize_level_name'=>'["复式"]', 'layout'=>'
{
    "desc": "从01-10选择4个号码组成一注。",
    "help": "从冠军、亚军、第三名、第四名01-10中各选择各1个以上号码。",
    "example": "投注方案：01、02、03、04，开奖号码：01、02、03、04 **，即中猜前四名_复式。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "冠军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 0,
                "cols": 1
            },
            {
                "title": "亚军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 1,
                "cols": 1
            },
            {
                "title": "第三名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 2,
                "cols": 1
            },
            {
                "title": "第四名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 3,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170401002', 'parent_id'=>'170401000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qiansiming_danshi', 'name'=>'单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qiansanming', 'lock_init_function'=>'initPk10QiansanmingLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[10080]', 'prize_level_name'=>'["单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入4个两位数号码组成一注。",
    "help": "手动输入号码，至少输入4个两位数号码组成一注。",
    "example": "投注方案：01、02、03、04，开奖号码：01、02、03、04 **，即中猜前四名_单式。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜前五名 170500000,
            ['id'=>'170500000', 'parent_id'=>'0', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qianwuming_01', 'name'=>'猜前五名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //猜前五名 170501000,
            ['id'=>'170501000', 'parent_id'=>'170500000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qianwuming_02', 'name'=>'猜前五名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170501001', 'parent_id'=>'170501000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qianwuming_fushi', 'name'=>'复式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qiansanming', 'lock_init_function'=>'initPk10QiansanmingLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[60480]', 'prize_level_name'=>'["复式"]', 'layout'=>'
{
    "desc": "从01-10选择4个号码组成一注。",
    "help": "从冠军、亚军、第三名、第四名、第五名01-10中各选择各1个以上号码。",
    "example": "投注方案：01、02、03、04、05，开奖号码：01、02、03、04、05 **，即中猜前五名_复式。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "冠军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 0,
                "cols": 1
            },
            {
                "title": "亚军",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 1,
                "cols": 1
            },
            {
                "title": "第三名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 2,
                "cols": 1
            },
            {
                "title": "第四名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 3,
                "cols": 1
            },
            {
                "title": "第五名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 4,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170501002', 'parent_id'=>'170501000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_qianwuming_danshi', 'name'=>'单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_qiansanming', 'lock_init_function'=>'initPk10QiansanmingLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[60480]', 'prize_level_name'=>'["单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入5个两位数号码组成一注。",
    "help": "手动输入号码，至少输入5个两位数号码组成一注。",
    "example": "投注方案：01、02、03、04、05，开奖号码：01、02、03、04、05 **，即中猜前五名_单式。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 170600000,
            ['id'=>'170600000', 'parent_id'=>'0', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_dingweidan_01', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 170601000,
            ['id'=>'170601000', 'parent_id'=>'170600000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_dingweidan_02', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170601001', 'parent_id'=>'170601000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_dingweidan', 'name'=>'定位胆', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_dingweidan", "tag_check": "pk10_dingweidan", "code_count": 10, "start_position": 0}', 'lock_table_name'=>'lock_dingweidan', 'lock_init_function'=>'initPk10DingweidanLock,1to10', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["定位胆"]', 'layout'=>'
{
    "desc": "在第1至10名任意位置上任意选择1个或1个以上号码。",
    "help": "从第1至10名任意位置上至少选择1个以上号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "example": "投注方案第1名：01；开奖第1名车号：01，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "第一名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 0,
                "cols": 1
            },
            {
                "title": "第二名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 1,
                "cols": 1
            },
            {
                "title": "第三名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 2,
                "cols": 1
            },
            {
                "title": "第四名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 3,
                "cols": 1
            },
            {
                "title": "第五名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 4,
                "cols": 1
            },
            {
                "title": "第六名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 5,
                "cols": 1
            },
            {
                "title": "第七名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 6,
                "cols": 1
            },
            {
                "title": "第八名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 7,
                "cols": 1
            },
            {
                "title": "第九名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 8,
                "cols": 1
            },
            {
                "title": "第十名",
                "no": "01|02|03|04|05|06|07|08|09|10",
                "place": 9,
                "cols": 1
            }
        ],
        "big_index": 6,
        "is_button": true
    },
    "show_str": "X,X,X,X,X,X,X,X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //大小 170700000,
            ['id'=>'170700000', 'parent_id'=>'0', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao_01', 'name'=>'大小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //大小 170701000,
            ['id'=>'170701000', 'parent_id'=>'170700000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao_02', 'name'=>'大小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701001', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao1', 'name'=>'第一名', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_daxiao1", "tag_check": "pk10_daxiao1", "code_count": 1, "start_position": 0}', 'lock_table_name'=>'lock_daxiao', 'lock_init_function'=>'initPk10DaxiaoLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第一名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖的名次对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第一名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第一名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701002', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao2', 'name'=>'第二名', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_daxiao2", "tag_check": "pk10_daxiao2", "code_count": 1, "start_position": 1}', 'lock_table_name'=>'lock_daxiao', 'lock_init_function'=>'initPk10DaxiaoLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第二名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第二名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第二名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701003', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao3', 'name'=>'第三名', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_daxiao3", "tag_check": "pk10_daxiao3", "code_count": 1, "start_position": 2}', 'lock_table_name'=>'lock_daxiao', 'lock_init_function'=>'initPk10DaxiaoLock,3', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第三名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第三名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第三名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701004', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao4', 'name'=>'第四名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第四名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第四名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第四名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701005', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao5', 'name'=>'第五名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第五名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第五名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第五名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701006', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao6', 'name'=>'第六名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第六名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第六名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第六名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701007', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao7', 'name'=>'第七名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第七名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第七名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第七名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701008', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao8', 'name'=>'第八名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第八名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第八名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第八名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701009', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao9', 'name'=>'第九名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第九名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第九名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第九名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170701010', 'parent_id'=>'170701000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_daxiao10', 'name'=>'第十名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第十名"]', 'layout'=>'
{
    "desc": "选择大或小为一注。",
    "help": "选择大或小进行投注，只要开奖对应车号的大小(注：01,02,03,04,05为小；06,07,08,09,10为大)与所选项一致即中奖。",
    "example": "例如：第十名选择：大，开奖号码为07，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第十名",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单双 170800000,
            ['id'=>'170800000', 'parent_id'=>'0', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang_01', 'name'=>'单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单双 170801000,
            ['id'=>'170801000', 'parent_id'=>'170800000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang_02', 'name'=>'单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801001', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang1', 'name'=>'第一名', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_danshuang1", "tag_check": "pk10_danshuang1", "code_count": 1, "start_position": 0}', 'lock_table_name'=>'lock_danshuang', 'lock_init_function'=>'initPk10DanshuangLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第一名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第一名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第一名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801002', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang2', 'name'=>'第二名', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_danshuang2", "tag_check": "pk10_danshuang2", "code_count": 1, "start_position": 1}', 'lock_table_name'=>'lock_danshuang', 'lock_init_function'=>'initPk10DanshuangLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第二名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第二名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第二名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801003', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang3', 'name'=>'第三名', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "pk10_danshuang3", "tag_check": "pk10_danshuang3", "code_count": 1, "start_position": 2}', 'lock_table_name'=>'lock_danshuang', 'lock_init_function'=>'initPk10DanshuangLock,3', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第三名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第三名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第三名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801004', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang4', 'name'=>'第四名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第四名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第四名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第四名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801005', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang5', 'name'=>'第五名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第五名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第五名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第五名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801006', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang6', 'name'=>'第六名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第六名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第六名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第六名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801007', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang7', 'name'=>'第七名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第七名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第七名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第七名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801008', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang8', 'name'=>'第八名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第八名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第八名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第八名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801009', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang9', 'name'=>'第九名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第九名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第九名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第九名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170801010', 'parent_id'=>'170801000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_danshuang10', 'name'=>'第十名', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["第十名"]', 'layout'=>'
{
    "desc": "选择单或双为一注。",
    "help": "选择单或双进行投注，只要开奖对应车号的单双(注：01,03,05,07,09为单；02,04,06,08,10为双)与所选项一致即中奖。",
    "example": "例如：第十名选择 双，开奖号码为08，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第十名",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎斗 170900000,
            ['id'=>'170900000', 'parent_id'=>'0', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_lhd_01', 'name'=>'龙虎斗', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎斗 170901000,
            ['id'=>'170901000', 'parent_id'=>'170900000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_lhd_02', 'name'=>'龙虎斗', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170901001', 'parent_id'=>'170901000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_lhd_1vs10', 'name'=>'冠军VS第十名', 'draw_rule'=>'{"is_sum": 0, "position": "0,9", "tag_bonus": "pk10_lhd", "tag_check": "pk10_lhd", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{
    "desc": "选择龙或虎为一注。",
    "help": "根据冠军、第十名号码数值比大小，冠军号码大于第十名号码为龙，反之为虎。所选形态与开奖号码形态一致，即为中奖。",
    "example": "例如：选择 龙，开奖号码冠军为08，第十名为01，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "冠军VS第十名",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170901002', 'parent_id'=>'170901000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_lhd_2vs9', 'name'=>'亚军VS第九名', 'draw_rule'=>'{"is_sum": 0, "position": "1,8", "tag_bonus": "pk10_lhd", "tag_check": "pk10_lhd", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{
    "desc": "选择龙或虎为一注。",
    "help": "根据亚军、第九名号码数值比大小，亚军号码大于第九名号码为龙，反之为虎。所选形态与开奖号码形态一致，即为中奖。",
    "example": "例如：选择 龙，开奖号码亚军为08，第九名为01，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "亚军VS第九名",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170901003', 'parent_id'=>'170901000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_lhd_3vs8', 'name'=>'季军VS第八名', 'draw_rule'=>'{"is_sum": 0, "position": "2,7", "tag_bonus": "pk10_lhd", "tag_check": "pk10_lhd", "code_count": 2, "start_position": 2}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{
    "desc": "选择龙或虎为一注。",
    "help": "根据季军、第八名号码数值比大小，季军号码大于第八名号码为龙，反之为虎。所选形态与开奖号码形态一致，即为中奖。",
    "example": "例如：选择 龙，开奖号码季军为08，第八名为01，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "季军VS第八名",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170901004', 'parent_id'=>'170901000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_lhd_4vs7', 'name'=>'第四名VS第七名', 'draw_rule'=>'{"is_sum": 0, "position": "3,6", "tag_bonus": "pk10_lhd", "tag_check": "pk10_lhd", "code_count": 2, "start_position": 3}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{
    "desc": "选择龙或虎为一注。",
    "help": "根据第四名、第七名号码数值比大小，第四名号码大于第七名号码为龙，反之为虎。所选形态与开奖号码形态一致，即为中奖。",
    "example": "例如：选择 龙，开奖号码第四名为08，第七名为01，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第四名VS第七名",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'170901005', 'parent_id'=>'170901000', 'lottery_method_category_id'=>'71', 'ident'=>'pk10_lhd_5vs6', 'name'=>'第五名VS第六名', 'draw_rule'=>'{"is_sum": 0, "position": "4,5", "tag_bonus": "pk10_lhd", "tag_check": "pk10_lhd", "code_count": 2, "start_position": 4}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{
    "desc": "选择龙或虎为一注。",
    "help": "根据第五名、第六名号码数值比大小，第五名号码大于第六名号码为龙，反之为虎。所选形态与开奖号码形态一致，即为中奖。",
    "example": "例如：选择 龙，开奖号码第五名为08，第六名为01，即为中奖。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第五名VS第六名",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ]
    },
    "show_str": "X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 81 快乐10分 标准模式  ==================================
            //任选一 180100000,
            ['id'=>'180100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan1_01', 'name'=>'任选一', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选一 180101000,
            ['id'=>'180101000', 'parent_id'=>'180100000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan1_02', 'name'=>'任选一', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180101001', 'parent_id'=>'180101000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan1_shutou', 'name'=>'数投', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx1hongtou", "tag_check": "kls_rx1hongtou", "code_count": 1, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[40]', 'prize_level_name'=>'["数投"]', 'layout'=>'
{
    "desc": "从01至18中任意选择1个数字号码，对开奖号码中按开奖顺序出现的第一个位置的投注。",
    "help": "投注号码与开奖号码中按开奖顺序出现的第一个位置数字号码相符，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "数投",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 10,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180101002', 'parent_id'=>'180101000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan1_hongtou', 'name'=>'红投', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx1shutou", "tag_check": "kls_rx1shutou", "code_count": 1, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[40]', 'prize_level_name'=>'["红投"]', 'layout'=>'
{
    "desc": "从19和20两个红色号码中任意选择1个红色号码，对开奖号码中按开奖顺序出现的第一个位置的投注。",
    "help": "投注号码与开奖号码中按开奖顺序出现的第一个位置为红色号码，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "红投",
                "no": "19|20",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180101003', 'parent_id'=>'180101000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan1', 'name'=>'任选投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx1", "tag_check": "kls_rx1", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[5]', 'prize_level_name'=>'["任选一"]', 'layout'=>'
{
    "desc": "从01至20中任意选择1个数字或多个数字号码，对开奖号码，顺序不计。",
    "help": "投注号码与开奖号码相符，（顺序不限），即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "任选一",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选二 180200000,
            ['id'=>'180200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan2_01', 'name'=>'任选二', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选二 180201000,
            ['id'=>'180201000', 'parent_id'=>'180200000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan2_02', 'name'=>'任选二', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180201001', 'parent_id'=>'180201000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan2', 'name'=>'任选投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx2", "tag_check": "kls_rx2", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[13.571429]', 'prize_level_name'=>'["任选二"]', 'layout'=>'
{
    "desc": "从01至20中任意选择2个号码对开奖号码中任意2个位置的投注。",
    "help": "投注号码与开奖号码中任意2个位置的号码相符，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "任选二",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180201002', 'parent_id'=>'180201000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan2_dantuo', 'name'=>'任选胆拖', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx2dantuo", "tag_check": "kls_rx2dantuo", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[13.571429]', 'prize_level_name'=>'["任选胆拖"]', 'layout'=>'
{
    "desc": "从01-20中，选取2个及以上的号码进行投注，每注需至少包括1个胆码及1个拖码。",
    "help": "投注号码与开奖号码中任意2个位置的号码相符，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "unique": true,
                "max_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 1,
                "cols": 1,
                "unique": true
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180201003', 'parent_id'=>'180201000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan2_lianzu', 'name'=>'前组投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n2_zuxuan", "tag_check": "lotto_n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[380]', 'prize_level_name'=>'["前二"]', 'layout'=>'
{
    "desc": "指从01至20中任意选择2个号码对开奖号码中按开奖顺序出现的2个连续位置的投注。",
    "help": "投注号码与开奖号码中按开奖顺序出现的2个连续位置的号码相符（顺序不限），即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "前二",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180201004', 'parent_id'=>'180201000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan2_lianzu_dantuo', 'name'=>'前组胆拖', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n2_zuxuan_dantuo", "tag_check": "lotto_n2_zuxuan_dantuo", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[380]', 'prize_level_name'=>'["胆拖"]', 'layout'=>'
{
    "desc": "从01-20中，选取2个及以上的号码进行投注，每注需至少包括1个胆码及1个拖码。",
    "help": "投注号码与开奖号码中按开奖顺序出现的2个连续位置的号码相符（顺序不限），即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "unique": true,
                "max_chosen": 1
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 1,
                "unique": true,
                "cols": 1
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180201005', 'parent_id'=>'180201000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan2_lianzhi', 'name'=>'前直投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n2_zhixuan", "tag_check": "lotto_n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[760]', 'prize_level_name'=>'["前直投注"]', 'layout'=>'
{
    "desc": "指从01至20中任意选择2个号码对开奖号码中按开奖顺序出现的2个连续位置按位相符的投注。",
    "help": "投注号码与开奖号码中按开奖顺序出现的2个连续位置的号码按位相符，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "第一",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1
            },
            {
                "title": "第二",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 1,
                "cols": 1
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选三 180300000,
            ['id'=>'180300000', 'parent_id'=>'0', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan3_01', 'name'=>'任选三', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选三 180301000,
            ['id'=>'180301000', 'parent_id'=>'180300000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan3_02', 'name'=>'任选三', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180301001', 'parent_id'=>'180301000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan3', 'name'=>'任选投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx3", "tag_check": "kls_rx3", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[40.714286]', 'prize_level_name'=>'["任选"]', 'layout'=>'
{
    "desc": "指从01至20中任意选择3个号码对开奖号码中任意3个位置的投注。",
    "help": "投注号码与开奖号码中任意3个位置的号码相符，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "任选三",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "min_chosen": 3
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180301002', 'parent_id'=>'180301000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan3_dantuo', 'name'=>'任选胆拖', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx3dantuo", "tag_check": "kls_rx3dantuo", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[40.714286]', 'prize_level_name'=>'["任选胆拖"]', 'layout'=>'
{
    "desc": "从20个号码中任选3个,投注号码与开奖号码任意三位相同即中奖",
    "help": "从20个号码中任选3个,投注号码与开奖号码任意三位相同即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "unique": true,
                "max_chosen": 2
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 1,
                "cols": 1,
                "unique": true
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180301003', 'parent_id'=>'180301000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan3_qianzu', 'name'=>'前组投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n3_zuxuan", "tag_check": "lotto_n3_zuxuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2280]', 'prize_level_name'=>'["前组"]', 'layout'=>'
{
    "desc": "指从01至20中任意选择3个号码对开奖号码中按开奖顺序出现的前3个连续位置的投注。",
    "help": "投注号码与开奖号码中按开奖顺序出现的前3个位置的号码相符（顺序不限），即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "前三",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "min_chosen": 3
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180301004', 'parent_id'=>'180301000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan3_qianzu_dantuo', 'name'=>'前组胆拖', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n3_zuxuan_dantuo", "tag_check": "lotto_n3_zuxuan_dantuo", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2280]', 'prize_level_name'=>'["胆拖"]', 'layout'=>'
{
    "desc": "从01 ~ 11中，选取3个及以上的号码进行投注，每注需至少包括1个胆码及2个拖码。",
    "help": "投注号码与开奖号码中按开奖顺序出现的前3个位置的号码相符（顺序不限），即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "unique": true,
                "max_chosen": 2
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 1,
                "cols": 1,
                "unique": true,
                "min_chosen": 1
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180301005', 'parent_id'=>'180301000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan3_qianzhi', 'name'=>'前直投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "lotto_n3_zhixuan", "tag_check": "lotto_n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[13680]', 'prize_level_name'=>'["前直投注"]', 'layout'=>'
{
    "desc": "指从01至20中任意选择3个号码对开奖号码中按开奖顺序出现的前3个连续位置按位相符的投注。",
    "help": "投注号码与开奖号码中按开奖顺序出现的前3个位置的号码按位相符，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "第一",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "第二",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 1,
                "cols": 1,
                "min_chosen": 1
            },
            {
                "title": "第三",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 2,
                "cols": 1,
                "min_chosen": 1
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选四 180400000,
            ['id'=>'180400000', 'parent_id'=>'0', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan4_01', 'name'=>'任选四', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选四 180401000,
            ['id'=>'180401000', 'parent_id'=>'180400000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan4_02', 'name'=>'任选四', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180401001', 'parent_id'=>'180401000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan4', 'name'=>'任选投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx4", "tag_check": "kls_rx4", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[138.428571]', 'prize_level_name'=>'["任选"]', 'layout'=>'
{
    "desc": "指从01至20中任意选择4个号码，对开奖号码中任意4个位置的投注。",
    "help": "投注号码与开奖号码中任意4个位置的号码相符，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "任选四",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "min_chosen": 4
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180401002', 'parent_id'=>'180401000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan4_dantuo', 'name'=>'胆拖投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx4dantuo", "tag_check": "kls_rx4dantuo", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[138.428571]', 'prize_level_name'=>'["任选胆拖"]', 'layout'=>'
{
    "desc": "从01 ~ 20中，选取4个及以上的号码进行投注，每注需至少包括1个胆码及3个拖码。",
    "help": "投注号码与开奖号码中任意4个位置的号码相符，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "unique": true,
                "max_chosen": 3
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 1,
                "cols": 1,
                "unique": true
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选五 180500000,
            ['id'=>'180500000', 'parent_id'=>'0', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan5_01', 'name'=>'任选五', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选五 180501000,
            ['id'=>'180501000', 'parent_id'=>'180500000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan5_02', 'name'=>'任选五', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180501001', 'parent_id'=>'180501000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan5', 'name'=>'任选投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx5", "tag_check": "kls_rx5", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[553.714286]', 'prize_level_name'=>'["任选5"]', 'layout'=>'
{
    "desc": "指从01至20中任意选择5个号码，对开奖号码中任意5个位置的投注。",
    "help": "投注号码与开奖号码中任意5个位置的号码相符，即中奖。",
    "example": "投注号码与开奖号码中任意5个位置的号码相符，即中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "任选五",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "min_chosen": 5
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180501002', 'parent_id'=>'180501000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_renxuan5_dantuo', 'name'=>'胆拖投注', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_rx5dantuo", "tag_check": "kls_rx5dantuo", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[553.714286]', 'prize_level_name'=>'["任选胆拖"]', 'layout'=>'
{
    "desc": "从01 ~ 20中，选取5个及以上的号码进行投注，每注需至少包括1个胆码及4个拖码。",
    "help": "投注号码与开奖号码中任意5个位置的号码相符，即中奖。",
    "example": "",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "胆码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 0,
                "cols": 1,
                "unique": true,
                "max_chosen": 4
            },
            {
                "title": "拖码",
                "no": "01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20",
                "place": 1,
                "cols": 1,
                "unique": true
            }
        ],
        "big_index": 11,
        "is_button": true
    },
    "show_str": "X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //趣味 180600000,
            ['id'=>'180600000', 'parent_id'=>'0', 'lottery_method_category_id'=>'81', 'ident'=>'kls_quwei_01', 'name'=>'趣味', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //趣味 180601000,
            ['id'=>'180601000', 'parent_id'=>'180600000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_quwei_02', 'name'=>'趣味', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180601001', 'parent_id'=>'180601000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_lh', 'name'=>'龙虎', 'draw_rule'=>'{"is_sum": 0, "position": "0,1", "tag_bonus": "pk10_lhd", "tag_check": "pk10_lhd", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{
    "desc": "开奖号码第一球大于第二球为龙、第二球大于第一球为虎。",
    "help": "龙：开出之号码第一球的中奖号码大于第二球的中奖号码。如 第一球开出14 第二球开出09，中奖为龙。虎：开出之号码第一球的中奖号码小于第二球的中奖号码。如 第一球开出14 第二球开出16，中奖为虎。",
    "example": "",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "龙虎",
                "no": "龙|虎",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180601002', 'parent_id'=>'180601000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_fw', 'name'=>'方位', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_fw", "tag_check": "kls_fw", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[8]', 'prize_level_name'=>'["方位"]', 'layout'=>'
{
    "desc": "投注号码与开奖号码相符，即中奖。",
    "help": "东：开出之号码为01、05、09、13、17南：开出之号码为02、06、10、14、18西：开出之号码为03、07、11、15、19北：开出之号码为04、08、12、16、20",
    "example": "",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第一球",
                "no": "东|南|西|北",
                "place": 0,
                "cols": 1
            },
            {
                "title": "第二球",
                "no": "东|南|西|北",
                "place": 1,
                "cols": 1
            },
            {
                "title": "第三球",
                "no": "东|南|西|北",
                "place": 2,
                "cols": 1
            },
            {
                "title": "第四球",
                "no": "东|南|西|北",
                "place": 3,
                "cols": 1
            },
            {
                "title": "第五球",
                "no": "东|南|西|北",
                "place": 4,
                "cols": 1
            },
            {
                "title": "第六球",
                "no": "东|南|西|北",
                "place": 5,
                "cols": 1
            },
            {
                "title": "第七球",
                "no": "东|南|西|北",
                "place": 6,
                "cols": 1
            },
            {
                "title": "第八球",
                "no": "东|南|西|北",
                "place": 7,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X,X,X,X,X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180601003', 'parent_id'=>'180601000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_zfb', 'name'=>'中发白', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_zfb", "tag_check": "kls_zfb", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[5.714286,6.666667]', 'prize_level_name'=>'["中，发","白"]', 'layout'=>'
{
    "desc": "投注号码与开奖号码相符，即中奖。",
    "help": "中：开出之号码为01、02、03、04、05、06、07 发：开出之号码为08、09、10、11、12、13、14 白：开出之号码为15、16、17、18、19、20",
    "example": "",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第一球",
                "no": "中|发|白",
                "place": 0,
                "cols": 1
            },
            {
                "title": "第二球",
                "no": "中|发|白",
                "place": 1,
                "cols": 1
            },
            {
                "title": "第三球",
                "no": "中|发|白",
                "place": 2,
                "cols": 1
            },
            {
                "title": "第四球",
                "no": "中|发|白",
                "place": 3,
                "cols": 1
            },
            {
                "title": "第五球",
                "no": "中|发|白",
                "place": 4,
                "cols": 1
            },
            {
                "title": "第六球",
                "no": "中|发|白",
                "place": 5,
                "cols": 1
            },
            {
                "title": "第七球",
                "no": "中|发|白",
                "place": 6,
                "cols": 1
            },
            {
                "title": "第八球",
                "no": "中|发|白",
                "place": 7,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X,X,X,X,X,X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180601004', 'parent_id'=>'180601000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_wx', 'name'=>'五行', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_wx", "tag_check": "kls_wx", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[10]', 'prize_level_name'=>'["五行"]', 'layout'=>'
{
    "desc": "投注号码与开奖号码相符，即中奖。",
    "help": "金：开出之号码为01、06、11、16木：开出之号码为02、07、12、17水：开出之号码为03、08、13、18火：开出之号码为04、09、14、19土：开出之号码为05、10、15、20",
    "example": "",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "第一球",
                "no": "金|木|水|火|土",
                "place": 0,
                "cols": 1
            },
            {
                "title": "第二球",
                "no": "金|木|水|火|土",
                "place": 1,
                "cols": 1
            },
            {
                "title": "第三球",
                "no": "金|木|水|火|土",
                "place": 2,
                "cols": 1
            },
            {
                "title": "第四球",
                "no": "金|木|水|火|土",
                "place": 3,
                "cols": 1
            },
            {
                "title": "第五球",
                "no": "金|木|水|火|土",
                "place": 4,
                "cols": 1
            },
            {
                "title": "第六球",
                "no": "金|木|水|火|土",
                "place": 5,
                "cols": 1
            },
            {
                "title": "第七球",
                "no": "金|木|水|火|土",
                "place": 6,
                "cols": 1
            },
            {
                "title": "第八球",
                "no": "金|木|水|火|土",
                "place": 7,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X,X,X,X,X,X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'180601005', 'parent_id'=>'180601000', 'lottery_method_category_id'=>'81', 'ident'=>'kls_ncbb', 'name'=>'农场宝贝', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "kls_ncbb", "tag_check": "kls_ncbb", "code_count": 8, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[3.076923,8,20]', 'prize_level_name'=>'["水果","蔬菜","动物"]', 'layout'=>'
{
    "desc": "投注号码与开奖号码相符，即中奖。",
    "help": "水果：开出之号码为01、02、03、04、05、06、07、08、09、11、12、13、14蔬菜：开出之号码为10、15、16、17、18动物：开出之号码为19、20",
    "example": "",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "第一球",
                "no": "水果|蔬菜|动物",
                "place": 0,
                "cols": 1
            },
            {
                "title": "第二球",
                "no": "水果|蔬菜|动物",
                "place": 1,
                "cols": 1
            },
            {
                "title": "第三球",
                "no": "水果|蔬菜|动物",
                "place": 2,
                "cols": 1
            },
            {
                "title": "第四球",
                "no": "水果|蔬菜|动物",
                "place": 3,
                "cols": 1
            },
            {
                "title": "第五球",
                "no": "水果|蔬菜|动物",
                "place": 4,
                "cols": 1
            },
            {
                "title": "第六球",
                "no": "水果|蔬菜|动物",
                "place": 5,
                "cols": 1
            },
            {
                "title": "第七球",
                "no": "水果|蔬菜|动物",
                "place": 6,
                "cols": 1
            },
            {
                "title": "第八球",
                "no": "水果|蔬菜|动物",
                "place": 7,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X,X,X,X,X,X,X,X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 91 PC蛋蛋 标准模式  ==================================
            //三码 190100000,
            ['id'=>'190100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3', 'name'=>'三码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三码直选 190101000,
            ['id'=>'190101000', 'parent_id'=>'190100000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zhixuan', 'name'=>'三码直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190101001', 'parent_id'=>'190101000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zhixuan_fushi', 'name'=>'直选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选复式"]', 'layout'=>'
{
    "desc": "从百位、十位、个位中至少各选1个号码。",
    "help": "从百位、十位、个位中选择一个3位数号码组成一注，所选号码与开奖号码相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,-,X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190101002', 'parent_id'=>'190101000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zhixuan_danshi', 'name'=>'直选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zhixuan", "tag_check": "n3_zhixuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码。",
    "help": "手动输入一个3位数号码组成一注，所选号码与开奖号码相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190101003', 'parent_id'=>'190101000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zhixuan_hezhi', 'name'=>'直选和值', 'draw_rule'=>'{"is_sum": 1, "tag_bonus": "n3_zhixuanhezhi", "tag_check": "n3_zhixuanhezhi", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[2000]', 'prize_level_name'=>'["直选和值"]', 'layout'=>'
{
    "desc": "从0-27中任意选择1个或1个以上号码。",
    "help": "所选数值等于开奖号码的三个数字相加之和，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "直选和值",
                "no": "0|1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三码组选 190102000,
            ['id'=>'190102000', 'parent_id'=>'190100000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zuxuan', 'name'=>'三码组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190102001', 'parent_id'=>'190102000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zuxuan_zusan_fushi', 'name'=>'组三复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zusan", "tag_check": "n3_zusan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zuxuan', 'lock_init_function'=>'initNumberTypeZhuShanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.6666666]', 'prize_level_name'=>'["组三复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上的号码。",
    "help": "从0-9中任意选择2个数字组成两注，所选号码与开奖号码相同，且顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组三",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190102002', 'parent_id'=>'190102000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zuxuan_zusan_danshi', 'name'=>'组三单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.6666666]', 'prize_level_name'=>'["组三单式"]', 'layout'=>'
{
    "desc": "手动输入一个3位数号码组成一注。",
    "help": "三个数字中必须有二个数字相同，所选号码与开奖号码的百位、十位、个位相同，顺序不限，即为中奖。",
    "example": "投注方案：001，开奖号码：010（顺序不限），即中三星组选三。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190102003', 'parent_id'=>'190102000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zuxuan_zuliu_fushi', 'name'=>'组六复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_zuliu", "tag_check": "n3_zuliu", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zuxuan', 'lock_init_function'=>'initNumberTypeZhuLiuLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.3333333]', 'prize_level_name'=>'["组六复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择3个或3个以上的号码。",
    "help": "从0-9中任意选择3个号码组成一注，所选号码与开奖号码相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组六",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190102004', 'parent_id'=>'190102000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zuxuan_zuliu_danshi', 'name'=>'组六单式', 'draw_rule'=>'{}', 'lock_table_name'=>'lock_zhixuan', 'lock_init_function'=>'initNumberTypeThreeZhiXuanLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[333.3333333]', 'prize_level_name'=>'["组六单式"]', 'layout'=>'
{
    "desc": "手动输入一个3位数号码组成一注。",
    "help": "三个数字必须互不相同，所选号码与开奖号码的百位、十位、个位相同，且顺序不限，即为中奖。",
    "example": "投注方案：123，开奖号码：321（顺序不限），即中三星组选六。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190102005', 'parent_id'=>'190102000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_hunhe_zuxuan_danshi', 'name'=>'混合组选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hunhezuxuan", "tag_check": "n3_hunhezuxuan", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.6666666, 333.3333333]', 'prize_level_name'=>'["组三", "组六"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个三位数号码。",
    "help": "手动输入购买号码，3个数字为一注，开奖号码符合组三或组六均为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190102006', 'parent_id'=>'190102000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_zuxuan_hezhi', 'name'=>'组选和值', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_hezhi", "tag_check": "n3_hezhi", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'lock_zuxuan', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[666.6666666, 333.3333333]', 'prize_level_name'=>'["组三", "组六"]', 'layout'=>'
{
    "desc": "从1-26中任意选择1个或1个以上号码。",
    "help": "所选数值等于开奖号码的三个数字相加之和，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选和值",
                "no": "1|2|3|4|5|6|7|8|9|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 14,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码 190200000,
            ['id'=>'190200000', 'parent_id'=>'0', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n2_01', 'name'=>'二码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码直选 190201000,
            ['id'=>'190201000', 'parent_id'=>'190200000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n2_02', 'name'=>'二码直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190201001', 'parent_id'=>'190201000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_h2_zhixuan_fushi', 'name'=>'后二直选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["后二直选复式"]', 'layout'=>'
{
    "desc": "从十位和个位上至少各选1个号码。",
    "help": "从十位和个位上至少各选1个号码，所选号码与开奖号码的十位、个位相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "-,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190201002', 'parent_id'=>'190201000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_h2_zhixuan_danshi', 'name'=>'后二直选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,2', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["后二直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入一个2位数号码组成一注，所选号码与开奖号码的十位、个位相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190201003', 'parent_id'=>'190201000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_q2_zhixuan_fushi', 'name'=>'前二直选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["前二直选复式"]', 'layout'=>'
{
    "desc": "从百位和十位上至少各选1个号码。",
    "help": "从百位和十位上至少各选1个号码，所选号码与开奖号码百位、十位相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,-",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190201004', 'parent_id'=>'190201000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_q2_zhixuan_danshi', 'name'=>'前二直选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zhixuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'initNumberTypeTwoZhiXuanLock,1', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[200]', 'prize_level_name'=>'["前二直选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入一个2位数号码组成一注，所选号码与开奖号码的百位、十位相同，且顺序一致，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码组选 190202000,
            ['id'=>'190202000', 'parent_id'=>'190200000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n2_03', 'name'=>'二码组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190202001', 'parent_id'=>'190202000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_h2_zuxuan_fushi', 'name'=>'后二组选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["后二组选复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选二个号码组成一注，所选号码与开奖号码的十位、个位相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190202002', 'parent_id'=>'190202000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_h2_zuxuan_danshi', 'name'=>'后二组选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 1}', 'lock_table_name'=>'lock_herma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["后二组选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入购买号码，2个数字为一注，所选号码与开奖号码的十位、个位相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190202003', 'parent_id'=>'190202000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_q2_zuxuan_fushi', 'name'=>'前二组选复式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["前二组选复式"]', 'layout'=>'
{
    "desc": "从0-9中任意选择2个或2个以上号码。",
    "help": "从0-9中选2个号码组成一注，所选号码与开奖号码的百位、十位相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "组选",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190202004', 'parent_id'=>'190202000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_q2_zuxuan_danshi', 'name'=>'前二组选单式', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n2_common", "tag_check": "n2_zuxuan", "code_count": 2, "start_position": 0}', 'lock_table_name'=>'lock_qerma', 'lock_init_function'=>'', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[100]', 'prize_level_name'=>'["前二组选单式"]', 'layout'=>'
{
    "desc": "手动输入号码，至少输入1个两位数号码。",
    "help": "手动输入购买号码，2个数字为一注，所选号码与开奖号码的百位、十位相同，顺序不限，即为中奖。",
    "select_area": {
        "type": "input"
    },
    "show_str": "X",
    "code_sp": " "
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 190300000,
            ['id'=>'190300000', 'parent_id'=>'0', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_dingweidan_01', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //定位胆 190301000,
            ['id'=>'190301000', 'parent_id'=>'190300000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_dingweidan_02', 'name'=>'定位胆', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190301001', 'parent_id'=>'190301000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_dingweidan', 'name'=>'定位胆', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n1_dingwei", "tag_check": "n1_dan", "code_count": 0, "start_position": 0}', 'lock_table_name'=>'lock_dwd', 'lock_init_function'=>'initNumberTypeYiWeiLock,1-3', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[20]', 'prize_level_name'=>'["定位胆"]', 'layout'=>'
{
    "desc": "在百位，十位，个位任意位置上任意选择1个或1个以上号码。",
    "help": "从百位、十位、个位任意1个位置或多个位置上选择1个号码，所选号码与相同位置上的开奖号码一致，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "百位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 1,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 2,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X,X,X",
    "code_sp": ""
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //不定位 190400000,
            ['id'=>'190400000', 'parent_id'=>'0', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_budingwei_01', 'name'=>'不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //不定位 190401000,
            ['id'=>'190401000', 'parent_id'=>'190400000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_budingwei_02', 'name'=>'不定位', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190401001', 'parent_id'=>'190401000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_budingwei_yima', 'name'=>'三星一码', 'draw_rule'=>'{"is_sum": 0, "tag_bonus": "n3_1mbudingwei", "tag_check": "n1_budingwei", "code_count": 3, "start_position": 0}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.3800738]', 'prize_level_name'=>'["三星一码"]', 'layout'=>'
{
    "desc": "从0-9中任意选择1个或1个以上的号码。",
    "help": "从0-9中选择1个号码，每注由1个号码组成，只要开奖结果中包含所选号码，即为中奖。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190401002', 'parent_id'=>'190401000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_n3_budingwei_erma', 'name'=>'二码不定位', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[37.0370370]', 'prize_level_name'=>'["二码不定位"]', 'layout'=>'
{
    "desc": "从0-9中选择2个号码组成一注。",
    "help": "每注由2个不同的号码组成，只要开奖号码的百位、十位、个位中包含所选的2个号码，即为中奖。",
    "example": "投注方案：12，开奖号码：至少出现1和2各1个，即中二码不定位。",
    "select_area": {
        "type": "digital",
        "layout": [
            {
                "title": "不定位",
                "no": "0|1|2|3|4|5|6|7|8|9",
                "place": 0,
                "cols": 1,
                "min_chosen": 2
            }
        ],
        "big_index": 5,
        "is_button": true
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //大小单双 190500000,
            ['id'=>'190500000', 'parent_id'=>'0', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_daxiaodanshuang_01', 'name'=>'大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //二码大小单双 190501000,
            ['id'=>'190501000', 'parent_id'=>'190500000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_daxiaodanshuang', 'name'=>'二码大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190501001', 'parent_id'=>'190501000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_daxiaodanshuang_qianer', 'name'=>'前二大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[8]', 'prize_level_name'=>'["前二大小单双"]', 'layout'=>'
{
    "desc": "对百位和十位的“大（56789）小（01234）单（13579）双（02468）”形态进行购买。",
    "help": "所选号码的位置、形态与开奖位置相同，即为中奖。",
    "example": "投注方案：小双，开奖号码：百位与十位“小双”，即中前二大小单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "百位",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            },
            {
                "title": "十位",
                "no": "大|小|单|双",
                "place": 1,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190501002', 'parent_id'=>'190501000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_daxiaodanshuang_houer', 'name'=>'后二大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[8]', 'prize_level_name'=>'["后二大小单双"]', 'layout'=>'
{
    "desc": "对十位和个位的“大（56789）小（01234）单（13579）双（02468）”形态进行购买。",
    "help": "所选号码的位置、形态与开奖位置相同，即为中奖。",
    "example": "投注方案：大单，开奖号码：十位与个位“大单”，即中后二大小单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "十位",
                "no": "大|小|单|双",
                "place": 0,
                "cols": 1
            },
            {
                "title": "个位",
                "no": "大|小|单|双",
                "place": 1,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //和值大小单双 190502000,
            ['id'=>'190502000', 'parent_id'=>'190500000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_hezhi', 'name'=>'和值大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190502001', 'parent_id'=>'190502000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_hezhi_daxiao', 'name'=>'和值大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["和值大小"]', 'layout'=>'
{
    "desc": "选择大、小进行投注。",
    "help": "开奖号码和值14～27为大，0～13为小。",
    "example": "投注方案：小，开奖号码：01，即中和值大小。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值大小",
                "no": "大|小",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190502002', 'parent_id'=>'190502000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_hezhi_danshuang', 'name'=>'和值单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[4]', 'prize_level_name'=>'["和值单双"]', 'layout'=>'
{
    "desc": "选择单、双进行投注。",
    "help": "选择单、双进行投注。",
    "example": "投注方案：单，开奖号码：01，即中和值单双。",
    "select_area": {
        "type": "dxds",
        "layout": [
            {
                "title": "和值单双",
                "no": "单|双",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'190502003', 'parent_id'=>'190502000', 'lottery_method_category_id'=>'91', 'ident'=>'pcdd_hezhi_daxiaodanshuang', 'name'=>'和值大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'initNumberTypeBudingWeiLock', 'modes'=>'[1,2,3,4,5,6,7,8]', 'prize_level'=>'[7.4349442,8.6580087,8.6580087,7.4349442]', 'prize_level_name'=>'["小单","小双","大单","大双"]', 'layout'=>'
{
    "desc": "选择大、小、单、双进行投注。",
    "help": "开奖号码和值14～27为大，0～13为小。",
    "example": "投注方案：小单，开奖号码：01，即中大小单双。",
    "select_area": {
        "type": "dds",
        "layout": [
            {
                "title": "和值大小单双",
                "no": "小单|小双|大单|大双",
                "place": 0,
                "cols": 1
            }
        ],
        "is_button": false
    },
    "show_str": "X",
    "code_sp": ","
}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

        ]);
    }

    public function dataPk()
    {

        DB::table('lottery_method')->insert([

            //================================  lottery_method_category_id: 12 时时彩盘口模式  ==================================
            //整合 102000000,
            ['id'=>'102000000', 'parent_id'=>'0', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zhenghe', 'name'=>'整合', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //总和 102101000,
            ['id'=>'102101000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zonghe', 'name'=>'总和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102101001', 'parent_id'=>'102101000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zonghe_da', 'name'=>'大', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102101002', 'parent_id'=>'102101000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zonghe_xiao', 'name'=>'小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102101003', 'parent_id'=>'102101000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zonghe_dan', 'name'=>'单', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102101004', 'parent_id'=>'102101000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zonghe_shuang', 'name'=>'双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102101005', 'parent_id'=>'102101000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zonghe_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102101006', 'parent_id'=>'102101000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zonghe_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102101007', 'parent_id'=>'102101000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zonghe_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第一球 102102000,
            ['id'=>'102102000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu', 'name'=>'第一球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102001', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_da', 'name'=>'大', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102002', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_xiao', 'name'=>'小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102003', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_dan', 'name'=>'单', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102004', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_shuang', 'name'=>'双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102005', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_0', 'name'=>'0', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["0"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102006', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_1', 'name'=>'1', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102007', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_2', 'name'=>'2', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["2"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102008', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_3', 'name'=>'3', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["3"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102009', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_4', 'name'=>'4', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["4"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102010', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_5', 'name'=>'5', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["5"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102011', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_6', 'name'=>'6', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["6"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102012', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_7', 'name'=>'7', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["7"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102013', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_8', 'name'=>'8', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["8"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102102014', 'parent_id'=>'102102000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diyiqiu_9', 'name'=>'9', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["9"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第二球 102103000,
            ['id'=>'102103000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu', 'name'=>'第二球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103001', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_da', 'name'=>'大', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103002', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_xiao', 'name'=>'小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103003', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_dan', 'name'=>'单', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103004', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_shuang', 'name'=>'双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103005', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_0', 'name'=>'0', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["0"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103006', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_1', 'name'=>'1', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103007', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_2', 'name'=>'2', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["2"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103008', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_3', 'name'=>'3', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["3"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103009', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_4', 'name'=>'4', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["4"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103010', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_5', 'name'=>'5', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["5"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103011', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_6', 'name'=>'6', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["6"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103012', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_7', 'name'=>'7', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["7"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103013', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_8', 'name'=>'8', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["8"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102103014', 'parent_id'=>'102103000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_dierqiu_9', 'name'=>'9', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["9"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第三球 102104000,
            ['id'=>'102104000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu', 'name'=>'第三球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104001', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_da', 'name'=>'大', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104002', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_xiao', 'name'=>'小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104003', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_dan', 'name'=>'单', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104004', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_shuang', 'name'=>'双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104005', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_0', 'name'=>'0', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["0"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104006', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_1', 'name'=>'1', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104007', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_2', 'name'=>'2', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["2"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104008', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_3', 'name'=>'3', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["3"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104009', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_4', 'name'=>'4', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["4"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104010', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_5', 'name'=>'5', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["5"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104011', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_6', 'name'=>'6', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["6"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104012', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_7', 'name'=>'7', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["7"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104013', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_8', 'name'=>'8', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["8"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102104014', 'parent_id'=>'102104000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disanqiu_9', 'name'=>'9', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["9"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第四球 102105000,
            ['id'=>'102105000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu', 'name'=>'第四球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105001', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_da', 'name'=>'大', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105002', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_xiao', 'name'=>'小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105003', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_dan', 'name'=>'单', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105004', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_shuang', 'name'=>'双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105005', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_0', 'name'=>'0', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["0"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105006', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_1', 'name'=>'1', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105007', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_2', 'name'=>'2', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["2"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105008', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_3', 'name'=>'3', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["3"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105009', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_4', 'name'=>'4', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["4"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105010', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_5', 'name'=>'5', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["5"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105011', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_6', 'name'=>'6', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["6"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105012', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_7', 'name'=>'7', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["7"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105013', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_8', 'name'=>'8', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["8"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102105014', 'parent_id'=>'102105000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_disiqiu_9', 'name'=>'9', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["9"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第五球 102106000,
            ['id'=>'102106000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu', 'name'=>'第五球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106001', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_da', 'name'=>'大', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106002', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_xiao', 'name'=>'小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106003', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_dan', 'name'=>'单', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106004', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_shuang', 'name'=>'双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106005', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_0', 'name'=>'0', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["0"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106006', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_1', 'name'=>'1', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106007', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_2', 'name'=>'2', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["2"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106008', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_3', 'name'=>'3', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["3"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106009', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_4', 'name'=>'4', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["4"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106010', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_5', 'name'=>'5', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["5"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106011', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_6', 'name'=>'6', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["6"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106012', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_7', 'name'=>'7', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["7"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106013', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_8', 'name'=>'8', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["8"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102106014', 'parent_id'=>'102106000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_diwuqiu_9', 'name'=>'9', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["9"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前三 102107000,
            ['id'=>'102107000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_qiansan', 'name'=>'前三', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102107001', 'parent_id'=>'102107000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_qiansan_baozi', 'name'=>'豹子', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[100]', 'prize_level_name'=>'["豹子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102107002', 'parent_id'=>'102107000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_qiansan_shunzi', 'name'=>'顺子', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[16.6666666]', 'prize_level_name'=>'["顺子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102107003', 'parent_id'=>'102107000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_qiansan_duizi', 'name'=>'对子', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.7037037]', 'prize_level_name'=>'["对子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102107004', 'parent_id'=>'102107000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_qiansan_banshun', 'name'=>'半顺', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.7777777]', 'prize_level_name'=>'["半顺"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102107005', 'parent_id'=>'102107000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_qiansan_zaliu', 'name'=>'杂六', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.3333333]', 'prize_level_name'=>'["杂六"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //中三 102108000,
            ['id'=>'102108000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zhongsan', 'name'=>'中三', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102108001', 'parent_id'=>'102108000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zhongsan_baozi', 'name'=>'豹子', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[100]', 'prize_level_name'=>'["豹子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102108002', 'parent_id'=>'102108000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zhongsan_shunzi', 'name'=>'顺子', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[16.6666666]', 'prize_level_name'=>'["顺子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102108003', 'parent_id'=>'102108000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zhongsan_duizi', 'name'=>'对子', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.7037037]', 'prize_level_name'=>'["对子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102108004', 'parent_id'=>'102108000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zhongsan_banshun', 'name'=>'半顺', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.7777777]', 'prize_level_name'=>'["半顺"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102108005', 'parent_id'=>'102108000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_zhongsan_zaliu', 'name'=>'杂六', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.3333333]', 'prize_level_name'=>'["杂六"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //后三 102109000,
            ['id'=>'102109000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_housan', 'name'=>'后三', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102109001', 'parent_id'=>'102109000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_housan_baozi', 'name'=>'豹子', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[100]', 'prize_level_name'=>'["豹子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102109002', 'parent_id'=>'102109000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_housan_shunzi', 'name'=>'顺子', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[16.6666666]', 'prize_level_name'=>'["顺子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102109003', 'parent_id'=>'102109000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_housan_duizi', 'name'=>'对子', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.7037037]', 'prize_level_name'=>'["对子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102109004', 'parent_id'=>'102109000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_housan_banshun', 'name'=>'半顺', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.7777777]', 'prize_level_name'=>'["半顺"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102109005', 'parent_id'=>'102109000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_housan_zaliu', 'name'=>'杂六', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.3333333]', 'prize_level_name'=>'["杂六"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-万千 102110000,
            ['id'=>'102110000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanqian', 'name'=>'龙虎万千', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102110001', 'parent_id'=>'102110000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanqian_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102110002', 'parent_id'=>'102110000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanqian_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102110003', 'parent_id'=>'102110000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanqian_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-万百 102111000,
            ['id'=>'102111000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanbai', 'name'=>'龙虎万百', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102111001', 'parent_id'=>'102111000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanbai_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102111002', 'parent_id'=>'102111000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanbai_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102111003', 'parent_id'=>'102111000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanbai_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-万十 102112000,
            ['id'=>'102112000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanshi', 'name'=>'龙虎万十', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102112001', 'parent_id'=>'102112000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanshi_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102112002', 'parent_id'=>'102112000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanshi_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102112003', 'parent_id'=>'102112000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wanshi_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-万个 102113000,
            ['id'=>'102113000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wange', 'name'=>'龙虎万个', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102113001', 'parent_id'=>'102113000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wange_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102113002', 'parent_id'=>'102113000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wange_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102113003', 'parent_id'=>'102113000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_wange_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-千百 102114000,
            ['id'=>'102114000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qianbai', 'name'=>'龙虎千百', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102114001', 'parent_id'=>'102114000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qianbai_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102114002', 'parent_id'=>'102114000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qianbai_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102114003', 'parent_id'=>'102114000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qianbai_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-千十 102115000,
            ['id'=>'102115000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qianshi', 'name'=>'龙虎千十', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102115001', 'parent_id'=>'102115000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qianshi_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102115002', 'parent_id'=>'102115000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qianshi_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102115003', 'parent_id'=>'102115000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qianshi_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-千个 102116000,
            ['id'=>'102116000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qiange', 'name'=>'龙虎千个', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102116001', 'parent_id'=>'102116000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qiange_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102116002', 'parent_id'=>'102116000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qiange_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102116003', 'parent_id'=>'102116000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_qiange_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-百十 102117000,
            ['id'=>'102117000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_baishi', 'name'=>'龙虎百十', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102117001', 'parent_id'=>'102117000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_baishi_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102117002', 'parent_id'=>'102117000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_baishi_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102117003', 'parent_id'=>'102117000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_baishi_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-百个 102118000,
            ['id'=>'102118000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_baige', 'name'=>'龙虎百个', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102118001', 'parent_id'=>'102118000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_baige_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102118002', 'parent_id'=>'102118000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_baige_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102118003', 'parent_id'=>'102118000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_baige_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //龙虎和-十个 102119000,
            ['id'=>'102119000', 'parent_id'=>'102000000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_shige', 'name'=>'龙虎十个', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102119001', 'parent_id'=>'102119000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_shige_long', 'name'=>'龙', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102119002', 'parent_id'=>'102119000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_shige_hu', 'name'=>'虎', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2222222]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'102119003', 'parent_id'=>'102119000', 'lottery_method_category_id'=>'12', 'ident'=>'ssc_pk_lhh_shige_he', 'name'=>'和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["和"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],


            //================================  lottery_method_category_id: 32 11 选 5 盘口模式  ==================================
            //整合 132100000,
            ['id'=>'132100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_zhenghe', 'name'=>'整合', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //总和 132101000,
            ['id'=>'132101000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_zonghe_01', 'name'=>'总和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132101001', 'parent_id'=>'132101000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_zonghe_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132101002', 'parent_id'=>'132101000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_zonghe_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[1.908686441]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132101003', 'parent_id'=>'132101000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_zonghe_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[1.993141593]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132101004', 'parent_id'=>'132101000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_zonghe_longhu', 'name'=>'龙虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132101005', 'parent_id'=>'132101000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_zonghe_weida', 'name'=>'尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[1.9913793]', 'prize_level_name'=>'["尾大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132101006', 'parent_id'=>'132101000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_zonghe_weixiao', 'name'=>'尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.0086957]', 'prize_level_name'=>'["尾小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第一球 132102000,
            ['id'=>'132102000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_diyiqiu_01', 'name'=>'第一球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132102001', 'parent_id'=>'132102000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_diyiqiu_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132102002', 'parent_id'=>'132102000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_diyiqiu_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第二球 132103000,
            ['id'=>'132103000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_dierqiu_01', 'name'=>'第二球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132103001', 'parent_id'=>'132103000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_dierqiu_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132103002', 'parent_id'=>'132103000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_dierqiu_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第三球 132104000,
            ['id'=>'132104000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_disanqiu_01', 'name'=>'第三球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132104001', 'parent_id'=>'132104000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_disanqiu_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132104002', 'parent_id'=>'132104000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_disanqiu_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第四球 132105000,
            ['id'=>'132105000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_disiqiu_01', 'name'=>'第四球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132105001', 'parent_id'=>'132105000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_disiqiu_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132105002', 'parent_id'=>'132105000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_disiqiu_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第五球 132106000,
            ['id'=>'132106000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_diwuqiu_01', 'name'=>'第五球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132106001', 'parent_id'=>'132106000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_diwuqiu_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132106002', 'parent_id'=>'132106000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_diwuqiu_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //一中一 132107000,
            ['id'=>'132107000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_yizhongyi_01', 'name'=>'一中一', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132107001', 'parent_id'=>'132107000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_yizhongyi', 'name'=>'一中一', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.2]', 'prize_level_name'=>'["一中一"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第一球 132108000,
            ['id'=>'132108000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_diyiqiu_01', 'name'=>'单号第一球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132108001', 'parent_id'=>'132108000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_diyiqiu', 'name'=>'第一球', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[11]', 'prize_level_name'=>'["第一球"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第二球 132109000,
            ['id'=>'132109000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_dierqiu_01', 'name'=>'单号第二球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132109001', 'parent_id'=>'132109000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_dierqiu', 'name'=>'第二球', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[11]', 'prize_level_name'=>'["第二球"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第三球 132110000,
            ['id'=>'132110000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_disanqiu_01', 'name'=>'单号第三球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132110001', 'parent_id'=>'132110000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_disanqiu', 'name'=>'第三球', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[11]', 'prize_level_name'=>'["第三球"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第四球 132120000,
            ['id'=>'132120000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_disiqiu_01', 'name'=>'单号第四球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132120001', 'parent_id'=>'132120000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_disiqiu', 'name'=>'第四球', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[11]', 'prize_level_name'=>'["第四球"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第五球 132130000,
            ['id'=>'132130000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_diwuqiu_01', 'name'=>'单号第五球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132130001', 'parent_id'=>'132130000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_danhao_diwuqiu', 'name'=>'第五球', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[11]', 'prize_level_name'=>'["第五球"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选二中二 132140000,
            ['id'=>'132140000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_erzhonger_01', 'name'=>'任选二中二', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132140001', 'parent_id'=>'132140000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_erzhonger', 'name'=>'二中二', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[5.5]', 'prize_level_name'=>'["二中二"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选三中三 132150000,
            ['id'=>'132150000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_sanzhongsan_01', 'name'=>'任选三中三', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132150001', 'parent_id'=>'132150000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_sanzhongsan', 'name'=>'三中三', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[16.5]', 'prize_level_name'=>'["三中三"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选四中四 132160000,
            ['id'=>'132160000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_sizhongsi_01', 'name'=>'任选四中四', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132160001', 'parent_id'=>'132160000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_sizhongsi', 'name'=>'四中四', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[66]', 'prize_level_name'=>'["四中四"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选五中五 132170000,
            ['id'=>'132170000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_wuzhongwu_01', 'name'=>'任选五中五', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132170001', 'parent_id'=>'132170000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_wuzhongwu', 'name'=>'五中五', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[462]', 'prize_level_name'=>'["五中五"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选六中五 132180000,
            ['id'=>'132180000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_liuzhongwu_01', 'name'=>'任选六中五', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132180001', 'parent_id'=>'132180000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_liuzhongwu', 'name'=>'六中五', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[77]', 'prize_level_name'=>'["六中五"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选七中五 132190000,
            ['id'=>'132190000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_qizhongwu_01', 'name'=>'任选七中五', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132190001', 'parent_id'=>'132190000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_qizhongwu', 'name'=>'七中五', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[22]', 'prize_level_name'=>'["七中五"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //任选八中五 132200000,
            ['id'=>'132200000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_bazhongwu_01', 'name'=>'任选八中五', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132200001', 'parent_id'=>'132200000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_bazhongwu', 'name'=>'八中五', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[8.25]', 'prize_level_name'=>'["八中五"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前二组选 132210000,
            ['id'=>'132210000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_qianerzuxuan_01', 'name'=>'前二组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132210001', 'parent_id'=>'132210000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_qianerzuxuan', 'name'=>'前二组选', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[55]', 'prize_level_name'=>'["前二组选"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前三组选 132220000,
            ['id'=>'132220000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_qiansanzuxuan_01', 'name'=>'前三组选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132220001', 'parent_id'=>'132220000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_renxuan_qiansanzuxuan', 'name'=>'前三组选', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[165]', 'prize_level_name'=>'["前三组选"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前二直选 132230000,
            ['id'=>'132230000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_qianerzhixuan_01', 'name'=>'前二直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132230001', 'parent_id'=>'132230000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_qianerzhixuan', 'name'=>'前二直选', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[110]', 'prize_level_name'=>'["前二直选"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前三直选 132240000,
            ['id'=>'132240000', 'parent_id'=>'132100000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_qiansanzhixuan_01', 'name'=>'前三直选', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'132240001', 'parent_id'=>'132240000', 'lottery_method_category_id'=>'32', 'ident'=>'11x5_pk_qiansanzhixuan', 'name'=>'前三直选', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[990]', 'prize_level_name'=>'["前三直选"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 42 快乐 8 盘口模式  ==================================
            //整合 142000000,
            ['id'=>'142000000', 'parent_id'=>'0', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zhenghe', 'name'=>'整合', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //总和、总和过关 142001000,
            ['id'=>'142001000', 'parent_id'=>'142000000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe', 'name'=>'总和、总和过关', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142001001', 'parent_id'=>'142001000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe_da', 'name'=>'总和大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.1375]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142001002', 'parent_id'=>'142001000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe_xiao', 'name'=>'总和小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.1375]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142001003', 'parent_id'=>'142001000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe_dan', 'name'=>'总和单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.1375]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142001004', 'parent_id'=>'142001000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe_shuang', 'name'=>'总和双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.1375]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142001005', 'parent_id'=>'142001000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe_810', 'name'=>'总和810', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[126.875]', 'prize_level_name'=>'["810"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142001006', 'parent_id'=>'142001000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe_dadan', 'name'=>'总大单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4.25]', 'prize_level_name'=>'["大单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142001007', 'parent_id'=>'142001000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe_dashuang', 'name'=>'总大双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4.25]', 'prize_level_name'=>'["大双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142001008', 'parent_id'=>'142001000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe_xiaodan', 'name'=>'总小单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4.25]', 'prize_level_name'=>'["小单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142001009', 'parent_id'=>'142001000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zonghe_xiaoshuang', 'name'=>'总小双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4.25]', 'prize_level_name'=>'["小双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //前后和 142002000,
            ['id'=>'142002000', 'parent_id'=>'142000000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_qianhouhe', 'name'=>'前后和', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142002001', 'parent_id'=>'142002000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_qianhouhe_qianduo', 'name'=>'前(多)', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[2.6875]', 'prize_level'=>'[2]', 'prize_level_name'=>'["前(多)"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142002002', 'parent_id'=>'142002000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_qianhouhe_houduo', 'name'=>'后(多)', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[2.6875]', 'prize_level'=>'[2]', 'prize_level_name'=>'["后(多)"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142002003', 'parent_id'=>'142002000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_qianhouhe_qianhouhe', 'name'=>'前后(和)', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[5.25]', 'prize_level'=>'[4]', 'prize_level_name'=>'["前后(和)"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //五行 142003000,
            ['id'=>'142003000', 'parent_id'=>'142000000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_wuxing', 'name'=>'五行', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142003001', 'parent_id'=>'142003000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_wuxing_jin', 'name'=>'金', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[5.375]', 'prize_level_name'=>'["金"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142003002', 'parent_id'=>'142003000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_wuxing_mu', 'name'=>'木', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.75]', 'prize_level_name'=>'["木"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142003003', 'parent_id'=>'142003000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_wuxing_shui', 'name'=>'水', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[1.4375]', 'prize_level_name'=>'["水"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142003004', 'parent_id'=>'142003000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_wuxing_huo', 'name'=>'火', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.75]', 'prize_level_name'=>'["火"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142003005', 'parent_id'=>'142003000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_wuxing_tu', 'name'=>'土', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[5.375]', 'prize_level_name'=>'["土"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单双和 142004000,
            ['id'=>'142004000', 'parent_id'=>'142000000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_danshuanghe', 'name'=>'单双和', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142004001', 'parent_id'=>'142004000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_danshuanghe_danduo', 'name'=>'单(多)', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.6875]', 'prize_level_name'=>'["前(多)"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142004002', 'parent_id'=>'142004000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_danshuanghe_shuangduo', 'name'=>'双(多)', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.6875]', 'prize_level_name'=>'["后(多)"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142004003', 'parent_id'=>'142004000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_danshuanghe_danshuanghe', 'name'=>'单双(和)', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[5.25]', 'prize_level_name'=>'["前后(和)"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //正码 142100000,
            ['id'=>'142100000', 'parent_id'=>'142000000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zhengma_01', 'name'=>'正码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'142100001', 'parent_id'=>'142100000', 'lottery_method_category_id'=>'42', 'ident'=>'kl8_pk_zhengma', 'name'=>'正码', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["正码"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 52 快 3 盘口模式  ==================================
            //整合 152100000,
            ['id'=>'152100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_zhenghe', 'name'=>'整合', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //三军大小 152101000,
            ['id'=>'152101000', 'parent_id'=>'152100000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_sanjundaxiao_01', 'name'=>'三军大小', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152101001', 'parent_id'=>'152101000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_sanjundaxiao_sanjun', 'name'=>'三军', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.3736264]', 'prize_level_name'=>'["三军"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152101002', 'parent_id'=>'152101000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_sanjundaxiao_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.0571429]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //围骰全骰 152102000,
            ['id'=>'152102000', 'parent_id'=>'152100000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_weitouquantou_01', 'name'=>'围骰全骰', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152102001', 'parent_id'=>'152102000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_weitouquantou_weitou', 'name'=>'围骰', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[216]', 'prize_level_name'=>'["围骰"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152102002', 'parent_id'=>'152102000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_weitouquantou_quantou', 'name'=>'全骰', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[36]', 'prize_level_name'=>'["全骰"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //点数 152103000,
            ['id'=>'152103000', 'parent_id'=>'152100000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_01', 'name'=>'点数', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103001', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_4', 'name'=>'4', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[72]', 'prize_level_name'=>'["4"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103002', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_5', 'name'=>'5', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[36]', 'prize_level_name'=>'["5"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103003', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_6', 'name'=>'6', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[21.6]', 'prize_level_name'=>'["6"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103004', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_7', 'name'=>'7', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[14.4]', 'prize_level_name'=>'["7"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103005', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_8', 'name'=>'8', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10.2857142]', 'prize_level_name'=>'["8"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103006', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_9', 'name'=>'9', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[8.64]', 'prize_level_name'=>'["9"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103007', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_10', 'name'=>'10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[8]', 'prize_level_name'=>'["10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103008', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_11', 'name'=>'11', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[8]', 'prize_level_name'=>'["11"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103009', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_12', 'name'=>'12', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[8.64]', 'prize_level_name'=>'["12"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103010', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_13', 'name'=>'13', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10.2857142]', 'prize_level_name'=>'["13"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103011', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_14', 'name'=>'14', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[14.4]', 'prize_level_name'=>'["14"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103012', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_15', 'name'=>'15', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[21.6]', 'prize_level_name'=>'["15"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103013', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_16', 'name'=>'16', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[36]', 'prize_level_name'=>'["16"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152103014', 'parent_id'=>'152103000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_dianshu_17', 'name'=>'17', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[72]', 'prize_level_name'=>'["17"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //长牌 152104000,
            ['id'=>'152104000', 'parent_id'=>'152100000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_changpai_01', 'name'=>'长牌', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152104001', 'parent_id'=>'152104000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_changpai', 'name'=>'长牌', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[7.2]', 'prize_level_name'=>'["长牌"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //短牌 152105000,
            ['id'=>'152105000', 'parent_id'=>'152100000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_duanpai_01', 'name'=>'短牌', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152105001', 'parent_id'=>'152105000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_duanpai', 'name'=>'短牌', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[14.4]', 'prize_level_name'=>'["短牌"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //和值 152106000,
            ['id'=>'152106000', 'parent_id'=>'152100000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_hezhi_01', 'name'=>'和值', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'152106001', 'parent_id'=>'152106000', 'lottery_method_category_id'=>'52', 'ident'=>'k3_pk_hezhi_daxiaodanshuang', 'name'=>'大小单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 62 六合彩盘口模式  ==================================
            //整合 162100000,
            ['id'=>'162100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_zhenghe', 'name'=>'整合', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码 162101000,
            ['id'=>'162101000', 'parent_id'=>'162100000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_01', 'name'=>'特码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162101001', 'parent_id'=>'162101000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema', 'name'=>'特码', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[49]', 'prize_level_name'=>'["特码"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码大小单双 162102000,
            ['id'=>'162102000', 'parent_id'=>'162100000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_daxiao_01', 'name'=>'特码大小单双', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162102001', 'parent_id'=>'162102000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162102002', 'parent_id'=>'162102000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162102003', 'parent_id'=>'162102000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_daxiaodanshuang', 'name'=>'大小&单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["大小&单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162102004', 'parent_id'=>'162102000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hedaxiao', 'name'=>'合大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["合大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162102005', 'parent_id'=>'162102000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hedanshuang', 'name'=>'合单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["合单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162102006', 'parent_id'=>'162102000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_weidaxiao', 'name'=>'尾大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["尾大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码生肖 162103000,
            ['id'=>'162103000', 'parent_id'=>'162100000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_shengxiao_01', 'name'=>'特码生肖', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162103001', 'parent_id'=>'162103000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_shengxiao', 'name'=>'生肖', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[9.8,12.25]', 'prize_level_name'=>'["当年生肖","其它生肖"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码波色 162104000,
            ['id'=>'162104000', 'parent_id'=>'162100000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_bose_01', 'name'=>'特码波色', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104001', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hongbo', 'name'=>'红波', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.8823529]', 'prize_level_name'=>'["红波"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104002', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lanbo', 'name'=>'蓝波', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.0625]', 'prize_level_name'=>'["蓝波"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104003', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lvbo', 'name'=>'绿波', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.0625]', 'prize_level_name'=>'["绿波"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104004', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hongda', 'name'=>'红大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[7]', 'prize_level_name'=>'["红大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104005', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hongxiao', 'name'=>'红小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4.9]', 'prize_level_name'=>'["红小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104006', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_landa', 'name'=>'蓝大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[5.4444444]', 'prize_level_name'=>'["蓝大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104007', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lanxiao', 'name'=>'蓝小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[7]', 'prize_level_name'=>'["蓝小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104008', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lvda', 'name'=>'绿大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[5.4444444]', 'prize_level_name'=>'["绿大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104009', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lvxiao', 'name'=>'绿小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[7]', 'prize_level_name'=>'["绿小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104010', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hongdan', 'name'=>'红单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[6.125]', 'prize_level_name'=>'["红单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104011', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hongshuang', 'name'=>'红双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[5.4444444]', 'prize_level_name'=>'["红双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104012', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_landan', 'name'=>'蓝单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[6.125]', 'prize_level_name'=>'["蓝单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104013', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lanshuang', 'name'=>'蓝双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[6.125]', 'prize_level_name'=>'["蓝双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104014', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lvdan', 'name'=>'绿单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[5.4444444]', 'prize_level_name'=>'["绿单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104015', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lvshuang', 'name'=>'绿双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[7]', 'prize_level_name'=>'["绿双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104016', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hongdadan', 'name'=>'红大单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[16.3333333]', 'prize_level_name'=>'["红大单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104017', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hongxiaodan', 'name'=>'红小单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[9.8]', 'prize_level_name'=>'["红小单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104018', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hongdashuang', 'name'=>'红大双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[12.25]', 'prize_level_name'=>'["红大双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104019', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_hongxiaoshuang', 'name'=>'红小双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[9.8]', 'prize_level_name'=>'["红小双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104020', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_landadan', 'name'=>'蓝大单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[9.8]', 'prize_level_name'=>'["蓝大单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104021', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lanxiaodan', 'name'=>'蓝小单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[16.3333333]', 'prize_level_name'=>'["蓝小单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104022', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_landashuang', 'name'=>'蓝大双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[12.25]', 'prize_level_name'=>'["蓝大双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104023', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lanxiaoshuang', 'name'=>'蓝小双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[12.25]', 'prize_level_name'=>'["蓝小双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104024', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lvdadan', 'name'=>'绿大单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[9.8]', 'prize_level_name'=>'["绿大单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104025', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lvxiaodan', 'name'=>'绿小单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[12.25]', 'prize_level_name'=>'["绿小单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104026', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lvdashuang', 'name'=>'绿大双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[12.25]', 'prize_level_name'=>'["绿大双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162104027', 'parent_id'=>'162104000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_tema_lvxiaoshuang', 'name'=>'绿小双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[16.3333333]', 'prize_level_name'=>'["绿小双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //正码 162105000,
            ['id'=>'162105000', 'parent_id'=>'162100000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_zhengma_01', 'name'=>'正码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'162105001', 'parent_id'=>'162105000', 'lottery_method_category_id'=>'62', 'ident'=>'lhc_pk_zhengma', 'name'=>'正码', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[8.1666667]', 'prize_level_name'=>'["正码"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 72 PK10 盘口模式  ==================================
            //整合 172100000,
            ['id'=>'172100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_zhenghe', 'name'=>'整合', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //冠亚军和 172101000,
            ['id'=>'172101000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_01', 'name'=>'冠亚军和', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101001', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_da', 'name'=>'大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.25]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101002', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_xiao', 'name'=>'小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[1.8]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101003', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[1.8]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101004', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.25]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101005', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_3', 'name'=>'3', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[45]', 'prize_level_name'=>'["3"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101006', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_4', 'name'=>'4', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[45]', 'prize_level_name'=>'["4"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101007', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_5', 'name'=>'5', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[22.5]', 'prize_level_name'=>'["5"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101008', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_6', 'name'=>'6', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[22.5]', 'prize_level_name'=>'["6"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101009', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_7', 'name'=>'7', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[15]', 'prize_level_name'=>'["7"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101010', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_8', 'name'=>'8', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[15]', 'prize_level_name'=>'["8"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101011', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_9', 'name'=>'9', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[11.25]', 'prize_level_name'=>'["9"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101012', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_10', 'name'=>'10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[11.25]', 'prize_level_name'=>'["10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101013', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_11', 'name'=>'11', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[9]', 'prize_level_name'=>'["11"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101014', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_12', 'name'=>'12', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[11.25]', 'prize_level_name'=>'["12"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101015', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_13', 'name'=>'13', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[11.25]', 'prize_level_name'=>'["13"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101016', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_14', 'name'=>'14', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[15]', 'prize_level_name'=>'["14"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101017', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_15', 'name'=>'15', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[15]', 'prize_level_name'=>'["15"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101018', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_16', 'name'=>'16', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[22.5]', 'prize_level_name'=>'["16"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101019', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_17', 'name'=>'17', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[22.5]', 'prize_level_name'=>'["17"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101020', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_18', 'name'=>'18', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[45]', 'prize_level_name'=>'["18"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172101021', 'parent_id'=>'172101000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanyajunhe_19', 'name'=>'19', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[45]', 'prize_level_name'=>'["19"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //冠军 172102000,
            ['id'=>'172102000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanjun_01', 'name'=>'冠军', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172102001', 'parent_id'=>'172102000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanjun_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172102002', 'parent_id'=>'172102000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanjun_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172102003', 'parent_id'=>'172102000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_guanjun_longhu', 'name'=>'龙虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //亚军 172103000,
            ['id'=>'172103000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_yajun_01', 'name'=>'亚军', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172103001', 'parent_id'=>'172103000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_yajun_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172103002', 'parent_id'=>'172103000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_yajun_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172103003', 'parent_id'=>'172103000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_yajun_longhu', 'name'=>'龙虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第四名 172104000,
            ['id'=>'172104000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_disiming_01', 'name'=>'第四名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172104001', 'parent_id'=>'172104000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_disiming_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172104002', 'parent_id'=>'172104000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_disiming_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172104003', 'parent_id'=>'172104000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_disiming_longhu', 'name'=>'龙虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第五名 172105000,
            ['id'=>'172105000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diwuming_01', 'name'=>'第五名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172105001', 'parent_id'=>'172105000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diwuming_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172105002', 'parent_id'=>'172105000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diwuming_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172105003', 'parent_id'=>'172105000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diwuming_longhu', 'name'=>'龙虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第六名 172106000,
            ['id'=>'172106000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diliuming_01', 'name'=>'第六名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172106001', 'parent_id'=>'172106000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diliuming_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172106002', 'parent_id'=>'172106000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diliuming_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第七名 172107000,
            ['id'=>'172107000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diqiming_01', 'name'=>'第七名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172107001', 'parent_id'=>'172107000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diqiming_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172107002', 'parent_id'=>'172107000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_diqiming_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第八名 172108000,
            ['id'=>'172108000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_dibaming_01', 'name'=>'第八名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172108001', 'parent_id'=>'172108000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_dibaming_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172108002', 'parent_id'=>'172108000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_dibaming_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第九名 172109000,
            ['id'=>'172109000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_dijiuming_01', 'name'=>'第九名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172109001', 'parent_id'=>'172109000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_dijiuming_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172109002', 'parent_id'=>'172109000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_dijiuming_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第十名 172110000,
            ['id'=>'172110000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_dishiming_01', 'name'=>'第十名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172110001', 'parent_id'=>'172110000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_dishiming_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172110002', 'parent_id'=>'172110000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_dishiming_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第三名 172111000,
            ['id'=>'172111000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_disanming_01', 'name'=>'第三名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172111001', 'parent_id'=>'172111000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_disanming_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172111002', 'parent_id'=>'172111000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_disanming_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172111003', 'parent_id'=>'172111000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_disanming_longhu', 'name'=>'龙虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号冠军 172112000,
            ['id'=>'172112000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaoguanjun_01', 'name'=>'单号冠军', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172112001', 'parent_id'=>'172112000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaoguanjun', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号亚军 172113000,
            ['id'=>'172113000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaoyajun_01', 'name'=>'单号亚军', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172113001', 'parent_id'=>'172113000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaoyajun', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第三名 172114000,
            ['id'=>'172114000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodisanming_01', 'name'=>'单号第三名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172114001', 'parent_id'=>'172114000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodisanming', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第四名 172115000,
            ['id'=>'172115000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodisiming_01', 'name'=>'单号第四名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172115001', 'parent_id'=>'172115000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodisiming', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第五名 172116000,
            ['id'=>'172116000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodiwuming_01', 'name'=>'单号第五名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172116001', 'parent_id'=>'172116000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodiwuming', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第六名 172117000,
            ['id'=>'172117000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodiliuming_01', 'name'=>'单号第六名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172117001', 'parent_id'=>'172117000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodiliuming', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第七名 172118000,
            ['id'=>'172118000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodiqiming_01', 'name'=>'单号第七名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172118001', 'parent_id'=>'172118000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodiqiming', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第八名 172119000,
            ['id'=>'172119000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodibaming_01', 'name'=>'单号第八名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172119001', 'parent_id'=>'172119000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodibaming', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第九名 172120000,
            ['id'=>'172120000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodijiuming_01', 'name'=>'单号第九名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172120001', 'parent_id'=>'172120000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodijiuming', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //单号第十名 172121000,
            ['id'=>'172121000', 'parent_id'=>'172100000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodishiming_01', 'name'=>'单号第十名', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'172121001', 'parent_id'=>'172121000', 'lottery_method_category_id'=>'72', 'ident'=>'pk10_pk_danhaodishiming', 'name'=>'1-10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[10]', 'prize_level_name'=>'["1-10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 82 快乐10分 盘口模式  ==================================
            //两面盘 182000000,
            ['id'=>'182000000', 'parent_id'=>'0', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zhenghe', 'name'=>'两面盘', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //总和 182001000,
            ['id'=>'182001000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zonghe', 'name'=>'总和', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182001001', 'parent_id'=>'182001000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zonghe_da', 'name'=>'总和大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.06200586]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182001002', 'parent_id'=>'182001000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zonghe_xiao', 'name'=>'总和小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.06200586]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182001003', 'parent_id'=>'182001000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zonghe_dan', 'name'=>'总和单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.00333969]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182001004', 'parent_id'=>'182001000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zonghe_shuang', 'name'=>'总和双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.12421166]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182001005', 'parent_id'=>'182001000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zonghe_weida', 'name'=>'总和尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.00066705]', 'prize_level_name'=>'["尾大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182001006', 'parent_id'=>'182001000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zonghe_weixiao', 'name'=>'总和尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[1.99933340]', 'prize_level_name'=>'["尾小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第一球 182100000,
            ['id'=>'182100000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao_1', 'name'=>'第一球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100001', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1', 'name'=>'单号', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[20]', 'prize_level_name'=>'["单号"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100002', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100003', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100004', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_da', 'name'=>'大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100005', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_xiao', 'name'=>'小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100006', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_weida', 'name'=>'尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100007', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_weixiao', 'name'=>'尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100008', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_heshudan', 'name'=>'合数单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100009', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_heshushuang', 'name'=>'合数双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100010', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_long', 'name'=>'龙', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100011', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_hu', 'name'=>'虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100012', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_dong', 'name'=>'东', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["东"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100013', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_nan', 'name'=>'南', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["南"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100014', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_xi', 'name'=>'西', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["西"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100015', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_bei', 'name'=>'北', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["北"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100016', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_zhong', 'name'=>'中', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["中"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100017', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_fa', 'name'=>'发', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["发"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182100018', 'parent_id'=>'182100000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao1_bai', 'name'=>'白', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.33333333]', 'prize_level_name'=>'["白"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第二球 182200000,
            ['id'=>'182200000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao_2', 'name'=>'第二球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200001', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2', 'name'=>'单号', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[20]', 'prize_level_name'=>'["单号"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200002', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200003', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200004', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_da', 'name'=>'大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200005', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_xiao', 'name'=>'小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200006', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_weida', 'name'=>'尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200007', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_weixiao', 'name'=>'尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200008', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_heshudan', 'name'=>'合数单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200009', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_heshushuang', 'name'=>'合数双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200010', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_long', 'name'=>'龙', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200011', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_hu', 'name'=>'虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200012', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_dong', 'name'=>'东', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["东"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200013', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_nan', 'name'=>'南', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["南"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200014', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_xi', 'name'=>'西', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["西"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200015', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_bei', 'name'=>'北', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["北"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200016', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_zhong', 'name'=>'中', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["中"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200017', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_fa', 'name'=>'发', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["发"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182200018', 'parent_id'=>'182200000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao2_bai', 'name'=>'白', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.33333333]', 'prize_level_name'=>'["白"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第三球 182300000,
            ['id'=>'182300000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao_3', 'name'=>'第三球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300001', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3', 'name'=>'单号', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[20]', 'prize_level_name'=>'["单号"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300002', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300003', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300004', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_da', 'name'=>'大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300005', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_xiao', 'name'=>'小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300006', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_weida', 'name'=>'尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300007', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_weixiao', 'name'=>'尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300008', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_heshudan', 'name'=>'合数单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300009', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_heshushuang', 'name'=>'合数双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300010', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_long', 'name'=>'龙', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300011', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_hu', 'name'=>'虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300012', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_dong', 'name'=>'东', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["东"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300013', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_nan', 'name'=>'南', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["南"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300014', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_xi', 'name'=>'西', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["西"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300015', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_bei', 'name'=>'北', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["北"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300016', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_zhong', 'name'=>'中', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["中"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300017', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_fa', 'name'=>'发', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["发"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182300018', 'parent_id'=>'182300000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao3_bai', 'name'=>'白', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.33333333]', 'prize_level_name'=>'["白"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第四球 182400000,
            ['id'=>'182400000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao_4', 'name'=>'第四球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400001', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4', 'name'=>'单号', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[20]', 'prize_level_name'=>'["单号"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400002', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400003', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400004', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_da', 'name'=>'大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400005', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_xiao', 'name'=>'小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400006', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_weida', 'name'=>'尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400007', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_weixiao', 'name'=>'尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400008', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_heshudan', 'name'=>'合数单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400009', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_heshushuang', 'name'=>'合数双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400010', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_long', 'name'=>'龙', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400011', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_hu', 'name'=>'虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400012', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_dong', 'name'=>'东', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["东"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400013', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_nan', 'name'=>'南', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["南"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400014', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_xi', 'name'=>'西', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["西"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400015', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_bei', 'name'=>'北', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["北"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400016', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_zhong', 'name'=>'中', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["中"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400017', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_fa', 'name'=>'发', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["发"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182400018', 'parent_id'=>'182400000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao4_bai', 'name'=>'白', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.33333333]', 'prize_level_name'=>'["白"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第五球 182500000,
            ['id'=>'182500000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao_5', 'name'=>'第五球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500001', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5', 'name'=>'单号', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[20]', 'prize_level_name'=>'["单号"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500002', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500003', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500004', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_da', 'name'=>'大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500005', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_xiao', 'name'=>'小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500006', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_weida', 'name'=>'尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500007', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_weixiao', 'name'=>'尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500008', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_heshudan', 'name'=>'合数单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500009', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_heshushuang', 'name'=>'合数双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500010', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_long', 'name'=>'龙', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500011', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_hu', 'name'=>'虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500012', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_dong', 'name'=>'东', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["东"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500013', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_nan', 'name'=>'南', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["南"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500014', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_xi', 'name'=>'西', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["西"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500015', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_bei', 'name'=>'北', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["北"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500016', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_zhong', 'name'=>'中', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["中"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500017', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_fa', 'name'=>'发', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["发"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182500018', 'parent_id'=>'182500000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao5_bai', 'name'=>'白', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.33333333]', 'prize_level_name'=>'["白"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第六球 182600000,
            ['id'=>'182600000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao_6', 'name'=>'第六球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600001', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6', 'name'=>'单号', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[20]', 'prize_level_name'=>'["单号"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600002', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600003', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600004', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_da', 'name'=>'大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600005', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_xiao', 'name'=>'小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600006', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_weida', 'name'=>'尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600007', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_weixiao', 'name'=>'尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600008', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_heshudan', 'name'=>'合数单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600009', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_heshushuang', 'name'=>'合数双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600010', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_long', 'name'=>'龙', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600011', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_hu', 'name'=>'虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600012', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_dong', 'name'=>'东', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["东"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600013', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_nan', 'name'=>'南', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["南"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600014', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_xi', 'name'=>'西', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["西"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600015', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_bei', 'name'=>'北', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["北"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600016', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_zhong', 'name'=>'中', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["中"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600017', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_fa', 'name'=>'发', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["发"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182600018', 'parent_id'=>'182600000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao6_bai', 'name'=>'白', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.33333333]', 'prize_level_name'=>'["白"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第七球 182700000,
            ['id'=>'182700000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao_7', 'name'=>'第七球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700001', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7', 'name'=>'单号', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[20]', 'prize_level_name'=>'["单号"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700002', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700003', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700004', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_da', 'name'=>'大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700005', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_xiao', 'name'=>'小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700006', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_weida', 'name'=>'尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700007', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_weixiao', 'name'=>'尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700008', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_heshudan', 'name'=>'合数单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700009', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_heshushuang', 'name'=>'合数双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700010', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_long', 'name'=>'龙', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700011', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_hu', 'name'=>'虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700012', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_dong', 'name'=>'东', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["东"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700013', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_nan', 'name'=>'南', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["南"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700014', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_xi', 'name'=>'西', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["西"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700015', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_bei', 'name'=>'北', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["北"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700016', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_zhong', 'name'=>'中', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["中"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700017', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_fa', 'name'=>'发', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["发"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182700018', 'parent_id'=>'182700000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao7_bai', 'name'=>'白', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.33333333]', 'prize_level_name'=>'["白"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //第八球 182800000,
            ['id'=>'182800000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao_8', 'name'=>'第八球', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800001', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8', 'name'=>'单号', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[20]', 'prize_level_name'=>'["单号"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800002', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_dan', 'name'=>'单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800003', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_shuang', 'name'=>'双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800004', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_da', 'name'=>'大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800005', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_xiao', 'name'=>'小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800006', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_weida', 'name'=>'尾大', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800007', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_weixiao', 'name'=>'尾小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800008', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_heshudan', 'name'=>'合数单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800009', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_heshushuang', 'name'=>'合数双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800010', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_long', 'name'=>'龙', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["龙"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800011', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_hu', 'name'=>'虎', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["虎"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800012', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_dong', 'name'=>'东', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["东"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800013', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_nan', 'name'=>'南', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["南"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800014', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_xi', 'name'=>'西', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["西"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800015', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_bei', 'name'=>'北', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4]', 'prize_level_name'=>'["北"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800016', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_zhong', 'name'=>'中', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["中"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800017', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_fa', 'name'=>'发', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.85714286]', 'prize_level_name'=>'["发"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182800018', 'parent_id'=>'182800000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_danhao8_bai', 'name'=>'白', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.33333333]', 'prize_level_name'=>'["白"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //正码 182900000,
            ['id'=>'182900000', 'parent_id'=>'182000000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zhengma_01', 'name'=>'正码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'182900001', 'parent_id'=>'182900000', 'lottery_method_category_id'=>'82', 'ident'=>'kls_pk_zhengma', 'name'=>'正码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2.5]', 'prize_level_name'=>'["正码"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

            //================================  lottery_method_category_id: 92 PC蛋蛋 盘口模式  ==================================
            //整合 191100000,
            ['id'=>'191100000', 'parent_id'=>'0', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zhenghe', 'name'=>'整合', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //特码 191101000,
            ['id'=>'191101000', 'parent_id'=>'191100000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_title', 'name'=>'特码', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101001', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_0', 'name'=>'特码0', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[1000]', 'prize_level_name'=>'["特码0"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101002', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_1', 'name'=>'特码1', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[333.3333333]', 'prize_level_name'=>'["特码1"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101003', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_2', 'name'=>'特码2', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[166.6666667]', 'prize_level_name'=>'["特码2"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101004', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_3', 'name'=>'特码3', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[100]', 'prize_level_name'=>'["特码3"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101005', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_4', 'name'=>'特码4', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[66.6666667]', 'prize_level_name'=>'["特码4"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101006', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_5', 'name'=>'特码5', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[47.6190476]', 'prize_level_name'=>'["特码5"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101007', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_6', 'name'=>'特码6', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[35.7142857]', 'prize_level_name'=>'["特码6"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101008', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_7', 'name'=>'特码7', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[27.7777778]', 'prize_level_name'=>'["特码7"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101009', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_8', 'name'=>'特码8', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[22.2222222]', 'prize_level_name'=>'["特码8"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101010', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_9', 'name'=>'特码9', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[18.1818182]', 'prize_level_name'=>'["特码9"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101011', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_10', 'name'=>'特码10', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[15.8730159]', 'prize_level_name'=>'["特码10"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101012', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_11', 'name'=>'特码11', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[14.4927536]', 'prize_level_name'=>'["特码11"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101013', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_12', 'name'=>'特码12', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[13.6986301]', 'prize_level_name'=>'["特码12"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101014', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_13', 'name'=>'特码13', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[13.3333333]', 'prize_level_name'=>'["特码13"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101015', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_14', 'name'=>'特码14', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[13.3333333]', 'prize_level_name'=>'["特码14"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101016', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_15', 'name'=>'特码15', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[13.6986301]', 'prize_level_name'=>'["特码15"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101017', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_16', 'name'=>'特码16', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[14.4927536]', 'prize_level_name'=>'["特码16"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101018', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_17', 'name'=>'特码17', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[15.8730159]', 'prize_level_name'=>'["特码17"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101019', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_18', 'name'=>'特码18', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[18.181818]', 'prize_level_name'=>'["特码18"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101020', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_19', 'name'=>'特码19', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[22.2222222]', 'prize_level_name'=>'["特码19"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101021', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_20', 'name'=>'特码20', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[27.7777778]', 'prize_level_name'=>'["特码20"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101022', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_21', 'name'=>'特码21', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[35.7142857]', 'prize_level_name'=>'["特码21"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101023', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_22', 'name'=>'特码22', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[47.6190476]', 'prize_level_name'=>'["特码22"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101024', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_23', 'name'=>'特码23', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[66.6666667]', 'prize_level_name'=>'["特码23"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101025', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_24', 'name'=>'特码24', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[100]', 'prize_level_name'=>'["特码24"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101026', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_25', 'name'=>'特码25', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[166.66666667]', 'prize_level_name'=>'["特码25"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101027', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_26', 'name'=>'特码26', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[333.33333333]', 'prize_level_name'=>'["特码26"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191101028', 'parent_id'=>'191101000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_tema_27', 'name'=>'特码27', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[1000]', 'prize_level_name'=>'["特码27"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //组合（混合） 191102000,
            ['id'=>'191102000', 'parent_id'=>'191100000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_hunhe', 'name'=>'组合（混合）', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191102001', 'parent_id'=>'191102000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_daxiao', 'name'=>'大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191102002', 'parent_id'=>'191102000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_danshuang', 'name'=>'单双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[2]', 'prize_level_name'=>'["单双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191102003', 'parent_id'=>'191102000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_dadan', 'name'=>'大单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4.3290043]', 'prize_level_name'=>'["大单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191102004', 'parent_id'=>'191102000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_xiaodan', 'name'=>'小单', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.7174721]', 'prize_level_name'=>'["小单"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191102005', 'parent_id'=>'191102000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_dashuang', 'name'=>'大双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.7174721]', 'prize_level_name'=>'["大双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191102006', 'parent_id'=>'191102000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_xiaoshuang', 'name'=>'小双', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[4.3290043]', 'prize_level_name'=>'["小双"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191102007', 'parent_id'=>'191102000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_jidaxiao', 'name'=>'极大小', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[28.5714286]', 'prize_level_name'=>'["极大小"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //组合（波色） 191103000,
            ['id'=>'191103000', 'parent_id'=>'191100000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_bose', 'name'=>'组合（波色）', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191103001', 'parent_id'=>'191103000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_hongbo', 'name'=>'红波', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.0120482]', 'prize_level_name'=>'["红波"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191103002', 'parent_id'=>'191103000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_lvbo', 'name'=>'绿波', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.8759690]', 'prize_level_name'=>'["绿波"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191103003', 'parent_id'=>'191103000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_lanbo', 'name'=>'蓝波', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[3.8759690]', 'prize_level_name'=>'["蓝波"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            //组合（豹子） 191104000,
            ['id'=>'191104000', 'parent_id'=>'191100000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_baozi_title', 'name'=>'组合（豹子）', 'draw_rule'=>'[]', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[]', 'prize_level'=>'[]', 'prize_level_name'=>'[]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],
            ['id'=>'191104001', 'parent_id'=>'191104000', 'lottery_method_category_id'=>'92', 'ident'=>'pcdd_pk_zuhe_baozi', 'name'=>'豹子', 'draw_rule'=>'{}', 'lock_table_name'=>'', 'lock_init_function'=>'', 'modes'=>'[9]', 'prize_level'=>'[100]', 'prize_level_name'=>'["豹子"]', 'layout'=>'
{}', 'sort'=>'0', 'status'=>true, 'max_bet_num'=>'0', ],

        ]);
    }

    public function setStatusFalse()
    {
        $idents_array = [
            //时时彩定位胆 当个位置
            'ssc_n5_dingweidan_wanwei', 'ssc_n5_dingweidan_qianwei', 'ssc_n5_dingweidan_baiwei', 'ssc_n5_dingweidan_shiwei', 'ssc_n5_dingweidan_gewei',
        ];

        DB::table('lottery_method')->whereIn('ident', $idents_array)->update(['status'=>'f']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->environment() != 'production') {
            Schema::dropIfExists('lottery_method');
        }
    }
}
