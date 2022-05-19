<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Service\Models\Admin\AdminUser::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'usernick' => $faker->unique()->firstName,
        'username' => $faker->unique()->word,
        'password' => $password ?: $password = bcrypt('admin123'),
        'remember_token' => \Illuminate\Support\Str::random(10),
    ];
});

$factory->define(\Service\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'usernick' => $faker->unique()->word,
        'username' => $faker->unique()->word,
        'password' => $password ?: $password = bcrypt('admin123'),
        'remember_token' => \Illuminate\Support\Str::random(10),
    ];
});

//增加账变列表测试数据
$factory->define(\Service\Models\Orders::class, function (Faker\Generator $faker) {
    $times = function () {
        return date('Y-m-d H:i:s', strtotime('-' . mt_rand(0, 10) . ' days -' . mt_rand(1, 24) . ' hours'));
    };
    $r = mt_rand(1, 4);
    $lottery_method = [
        1 => ['lottery' => 1, 'methodid' => "100{$r}0{$r}00{$r}"],
        2 => ['lottery' => 2, 'methodid' => "120{$r}0{$r}00{$r}"],
        3 => ['lottery' => 3, 'methodid' => "130{$r}0{$r}00{$r}"]
    ];

    $lm = $lottery_method[mt_rand(1, 3)];
    return [
        'lottery_id' => $lm['lottery'],
        'lottery_method_id' => $lm['methodid'],
        'package_id' => mt_rand(1, 10000),
        'task_id' => mt_rand(1, 10000),
        'project_id' => mt_rand(1, 1000),
        'mode' => mt_rand(1, 4),
        'from_user_id' => mt_rand(1, 5),
        'to_user_id' => mt_rand(1, 5),
        'admin_user_id' => mt_rand(1, 6),
        'order_type_id' => mt_rand(1, 8),
        'amount' => mt_rand(800, 20000),
        'pre_balance' => mt_rand(1, 99),
        'pre_hold_balance' => mt_rand(1, 99),
        'balance' => mt_rand(800, 20000),
        'hold_balance' => mt_rand(1, 99),
        'comment' => implode(' ', $faker->words),
        'ip' => mt_rand(10, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255),
        'client_type' => mt_rand(0, 5),
        'created_at' => $times
    ];
});



//增加游戏投注纪录测试数据
$factory->define(\Service\Models\Projects::class, function (Faker\Generator $faker) {
    $times = function () {
        return date('Y-m-d H:i:s', strtotime('-' . rand(0, 10) . ' days -' . rand(1, 24) . ' hours'));
    };

    $single_price = 2;
    $multiple = rand(400, 9000);

    $r = mt_rand(1, 4);
    $lottery_method = [
        1 => ['lottery' => 1, 'methodid' => "100{$r}0{$r}00{$r}"],
        2 => ['lottery' => 2, 'methodid' => "120{$r}0{$r}00{$r}"],
        3 => ['lottery' => 3, 'methodid' => "130{$r}0{$r}00{$r}"]
    ];
    $lm = $lottery_method[mt_rand(1, 3)];
    $a =  [
        'user_id' => rand(1, 5),
        'package_id' => rand(100000, 999999),
        'task_id' => rand(100000, 999999),
        'lottery_id' => $lm['lottery'],
        'lottery_method_id' => $lm['methodid'],
        'issue' => rand(1, 10000),
        'bonus' => rand(999, 10000),
        'code' => rand(1, 1000),
        'single_price' => $single_price,
        'multiple' => $multiple,
        'total_price' => ($single_price * $multiple),
        'top_id' => rand(1, 4),
        //'top_point' => 0.1300,
        'created_at' => $times,
        'deduct_at' => $times,
        'send_prize_at' => $times,
        'is_deduct' => 1,
        'is_cancel' => 0,
        'is_get_prize' => 1,
        'prize_status' => 1,
        'ip' => '127.0.0.1',
        'mode' => 1,
        'rebate' => 0.1,
        'client_type' => 1,
        'is_send_credit' => 1,
    ];

    return $a;
});


//增加游戏投注纪录测试数据
$factory->define(\Service\Models\Tasks::class, function (Faker\Generator $faker) {
    $times = function () {
        return date('Y-m-d H:i:s', strtotime('-' . rand(0, 10) . ' days -' . rand(1, 24) . ' hours'));
    };

    $issue_count = rand(10, 30);
    $finished_count = rand(10, 20);
    $win_count = rand(1, 5);
    $r = mt_rand(1, 4);
    $lottery_method = [
        1 => ['lottery' => 1, 'methodid' => "100{$r}0{$r}00{$r}"],
        2 => ['lottery' => 2, 'methodid' => "120{$r}0{$r}00{$r}"],
        3 => ['lottery' => 3, 'methodid' => "130{$r}0{$r}00{$r}"]
    ];
    $lm = $lottery_method[mt_rand(1, 3)];
    $a =  [
        'user_id' => rand(1, 5),
        'lottery_id' => $lm['lottery'],
        'lottery_method_id' => $lm['methodid'],
        'package_id' => rand(10000, 99999),
        'title' => '后 4 星 追号 ' . $issue_count . ' 期',
        'code' =>  implode('|', array(rand(1, 9), rand(1, 9), rand(1, 9), rand(1, 9))),
        'issue_count' => $issue_count,
        'finished_count' => $finished_count,
        'cancel_count' => 0,
        'single_price' => 2000,
        'task_price' => 2000 * $issue_count,
        'finish_price' => 2000 * $finished_count,
        'cancel_price' => 2000 * 1,
        'begin_issue' => date('Ymd-') . (110 - $issue_count),
        'win_count' => $win_count,
        'updated_at' => $times,
        'top_id' => rand(1, 4),
        //'top_point' => 0.130,
        'status' => 0,
        'stop_on_win' => 1,
        'ip' => '192.168.203.' . rand(1, 254),
        'mode' => rand(1, 3),
        'rebate' => 0.2,
        'client_type' => rand(1, 3),

    ];

    return $a;
});


//增加游戏投注纪录测试数据
$factory->define(\Service\Models\Taskdetails::class, function (Faker\Generator $faker) {
    $timestamp = time() - rand(1000, 9999);
    $issue_count = rand(10, 30);
    $a =  [
        'task_id' => rand(23, 60),
        'project_id' => rand(1, 30),
        'multiple' => rand(1, 6),
        'issue' => '后4星 追号' . date('Ymd-', $timestamp) . (110 - $issue_count) . '期',
        'status' =>  rand(0, 2),
    ];
    return $a;
});

$factory->define(\Service\Models\ReportLottery::class, function (Faker\Generator $faker) {
    static $users = [2, 40, 41, 400, 401, 4000, 4001];
    static $method = [100101001, 100101002, 100101003];

    static $user = null;

    if (empty($user)) {
        $user = array_pop($users);
    }

    if (empty($users)) {
        $users = [1, 20, 21, 200, 201, 2000, 2001];
    }

    $data = [
        'price'             => rand(1, 10000),
        'bonus'             => rand(1, 10000),
        'rebate'            => rand(1, 10000),
        'user_id'           => $user,
        'lottery_id'        => 1,
        'lottery_method_id' => array_pop($method),
        'issue'             => 3333
    ];

    if (empty($method)) {
        $user = array_pop($users);
        $method = [100101001, 100101002, 100101003];
    }

    return $data;
});
