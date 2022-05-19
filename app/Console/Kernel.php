<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Cache;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\AutoWithdrawalThird::class,
        \App\Console\Commands\SendPrize::class,
        \App\Console\Commands\IssueError::class,
        \App\Console\Commands\ThirdGame::class,
        \App\Console\Commands\Report::class,
        \App\Console\Commands\DailyWage::class,
        \App\Console\Commands\Cleanup::class,
        \App\Console\Commands\Activity::class,
        \App\Console\Commands\UserFundBackup::class,
        \App\Console\Commands\ThirdGameUserFundBackup::class,
        \App\Console\Commands\AutoPrevDraw::class,
        \App\Console\Commands\Import::class,
        \App\Console\Commands\AutoGenerateIssue::class,
        \App\Console\Commands\AutoGenerateXglhcIssue::class,
        \App\Console\Commands\AutoGenerateJndkl8Issue::class,
        \App\Console\Commands\Dividend::class,
        \App\Console\Commands\ClearUserData::class,
        \App\Console\Commands\ExecuteSql::class,
        \App\Console\Commands\HourlyCoupon::class,
        \App\Console\Commands\ThirdGameDs::class,
        \App\Console\Commands\PrivateReturn::class,
        \App\Console\Commands\AutoRbcxRate::class,
        \App\Console\Commands\AutoWithdrawalRisk::class,
        \App\Console\Commands\KillCodeWin::class,
        \App\Console\Commands\UserMigration::class,
        \App\Console\Commands\AutoOtc365Rate::class,
        \App\Console\Commands\PumpOrder::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        try {
            $lottery_rows = Cache::store('redis')->remember(
                'cronLotteryRows',  //LotteryController.php 也可以刷新此缓存
                1440,
                function () {
                    $rows = \Service\Models\Lottery::select(['ident', 'cron','id','issue_rule'])
                        ->where('status', true)
                        ->where('special', '<', 2)
                        ->orderBy('id', 'asc')
                        ->get();
                    return $rows;
                }
            );
            foreach ($lottery_rows as $lottery) {
                $schedule->command('SendPrize:run all ' . $lottery->ident)
                    ->name($lottery->ident)
                    ->runInBackground()
                    ->cron($lottery->cron);

                if (in_array($lottery->ident, ["hl30s", "z75ssc", "z75pk10"])) {
                    $schedule->command('SendPrize:run all ' . $lottery->ident)
                        ->name($lottery->ident . '_2ND')
                        ->runInBackground()
                        ->everyMinute()
                        ->when(function () {
                            sleep(30);
                            return true;
                        });
                }
            }
        } catch (\Exception $e) {
        }

        /*
        $schedule->command('SendPrize:run all cqssc')
            ->name('cqssc')                             // 重庆时时彩
            ->runInBackground()
            ->everyMinute()
            ->unlessBetween('04:00', '07:00');          // 凌晨 04:00 到早上 07:00 之间不执行

        $schedule->command('SendPrize:run all xjssc')
            ->name('xjssc')                             // 新疆时时彩
            ->runInBackground()
            ->everyMinute()
            ->unlessBetween('03:00', '10:00');          // 凌晨 03:00 到早上 10:00 之间不执行

        $schedule->command('SendPrize:run all tjssc')
            ->name('tjssc')                             // 天津时时彩
            ->runInBackground()
            ->everyMinute()
            ->between('09:00', '23:30');                // 早上 09:00 到晚上 23:30 之间执行

        $schedule->command('SendPrize:run all 5fssc')
            ->name('5fssc')                             // 五分时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 3fssc')
            ->name('3fssc')                             // 三分时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 2fssc')
            ->name('2fssc')                             // 二分时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 1fssc')
            ->name('1fssc')                             // 分分时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all txffc')
            ->name('txffc')                             // 腾讯分分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqutxffssc')
            ->name('qiqutxffssc')                       // 奇趣腾讯分分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqutx3fssc')
            ->name('qiqutx3fssc')                       // 奇趣腾讯三分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqutx5fssc')
            ->name('qiqutx5fssc')                       // 奇趣腾讯五分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqutx10fssc')
            ->name('qiqutx10fssc')                      // 奇趣腾讯十分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqutxffpk10')
            ->name('qiqutxffpk10')                      // 奇趣腾讯分分PK10
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqutx5fpk10')
            ->name('qiqutx5fpk10')                      // 奇趣腾讯五分PK10
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all tx5fssc')
            ->name('tx5fssc')                           // 腾讯5分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all bjssc')
            ->name('bjssc')                             // 北京时时彩
            ->runInBackground()
            ->everyMinute()
            ->unlessBetween('00:30', '09:00');          // 早上 00:30 到早上 09:00 之间不执行

        $schedule->command('SendPrize:run all lg1d5fssc')
            ->name('lg1d5fssc')                         // LG 1.5 分时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all llssc')
            ->name('llssc')                             // 乐利时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all btcffc')
            ->name('btcffc')                            // 比特币分分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all jlffc')
            ->name('jlffc')                             // 吉利分分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all fhjlssc')
            ->name('fhjlssc')                           // 凤凰吉利时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all jsssc')
            ->name('jsssc')                             // 极速时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段
        $schedule->command('SendPrize:run all jsftpk10')
            ->name('jsftpk10')                          // 极速飞艇
            ->runInBackground()
            ->everyMinute();

        $schedule->command('SendPrize:run all xglhc')
            ->name('xglhc')                             // 香港六合彩
            ->runInBackground()
            ->everyMinute()
            ->between('21:40', '23:59');                // 晚上 21:40 到 23:59 之间执行

        $schedule->command('SendPrize:run all gd11x5')
            ->name('gd11x5')                            // 广东 11 选 5
            ->runInBackground()
            ->everyMinute()
            ->between('09:00', '23:50');                // 早上 09:00 到晚上 23:50 之间执行

        $schedule->command('SendPrize:run all sd11x5')
            ->name('sd11x5')                            // 山东 11 选 5
            ->runInBackground()
            ->everyMinute()
            ->between('09:00', '23:50');                // 早上 09:00 到晚上 23:50 之间执行

        $schedule->command('SendPrize:run all jx11x5')
            ->name('jx11x5')                            // 江西 11 选 5
            ->runInBackground()
            ->everyMinute()
            ->between('09:00', '23:50');                // 早上 09:00 到晚上 23:50 之间执行

        $schedule->command('SendPrize:run all ah11x5')
            ->name('ah11x5')                            // 安徽 11 选 5
            ->runInBackground()
            ->everyMinute()
            ->between('08:40', '22:30');                // 早上 08:40 到晚上 22:30 之间执行

        $schedule->command('SendPrize:run all sh11x5')
            ->name('sh11x5')                            // 上海 11 选 5
            ->runInBackground()
            ->everyMinute()
            ->unlessBetween('01:00', '08:50');          // 凌晨 01:00 到早上 08:50 之间不执行

        $schedule->command('SendPrize:run all zj11x5')
            ->name('zj11x5')                            // 浙江 11 选 5
            ->runInBackground()
            ->everyMinute()
            ->between('08:20', '23:50');                // 早上 08:20 到晚上 23:50 之间执行

        $schedule->command('SendPrize:run all 5f11x5')
            ->name('5f11x5')                            // 五分 11 选 5
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 3f11x5')
            ->name('3f11x5')                            // 三分 11 选 5
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 2f11x5')
            ->name('2f11x5')                            // 二分 11 选 5
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 1f11x5')
            ->name('1f11x5')                            // 分分 11 选 5
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all bjpk10')
            ->name('bjpk10')                            // 北京 PK 10
            ->runInBackground()
            ->everyMinute()
            ->unlessBetween('00:30', '09:00');          // 早上 00:30 到早上 09:00 之间不执行

        $schedule->command('SendPrize:run all jsk3')
            ->name('jsk3')                              // 江苏快 3
            ->runInBackground()
            ->everyMinute()
            ->between('08:30', '23:00');                // 早上 08:30 到晚上 23:00 之间执行

        $schedule->command('SendPrize:run all hbk3')
            ->name('hbk3')                              // 湖北快 3
            ->runInBackground()
            ->everyMinute()
            ->between('09:00', '23:00');                // 早上 08:30 到晚上 23:00 之间执行

        $schedule->command('SendPrize:run all ahk3')
            ->name('ahk3')                              // 安徽快 3
            ->runInBackground()
            ->everyMinute()
            ->between('08:30', '23:00');                // 早上 08:30 到晚上 23:00 之间执行

        $schedule->command('SendPrize:run all fucai3d')
            ->name('fucai3d')                           // 福彩 3D
            ->runInBackground()
            ->everyMinute()
            ->between('21:00', '22:00');                // 晚上 21:00 到晚上 22:00 之间执行

        $schedule->command('SendPrize:run all 5f3d')
            ->name('5f3d')                              // 五分 11 选 5
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all pl3pl5')
            ->name('pl3pl5')                            // 排列三五
            ->runInBackground()
            ->everyMinute()
            ->between('20:00', '23:00');                // 早上 08:30 到晚上 21:00 之间执行

        $schedule->command('SendPrize:run all bjkl8')
            ->name('bjkl8')                             // 北京快乐 8
            ->runInBackground()
            ->everyMinute()
            ->between('09:00', '23:59');                // 早上 09:00 到晚上 23:59 之间执行

        $schedule->command('SendPrize:run all shssl3d')
            ->name('shssl3d')                           // 上海时时乐
            ->runInBackground()
            ->everyMinute()
            ->between('10:00', '22:30');                // 早上 10:00 到晚上 23:30 之间执行

        $schedule->command('SendPrize:run all hnkls')
            ->name('hnkls')                             // 湖南快乐 10 分
            ->runInBackground()
            ->everyMinute()
            ->between('09:00', '23:10');                // 早上 09:00 到晚上 23:10 之间执行

        $schedule->command('SendPrize:run all tjkls')
            ->name('tjkls')                             // 天津快乐 10 分
            ->runInBackground()
            ->everyMinute()
            ->between('09:00', '23:10');                // 早上 09:00 到晚上 23:10 之间执行

        $schedule->command('SendPrize:run all gdkls')
            ->name('gdkls')                             // 广东快乐 10 分
            ->runInBackground()
            ->everyMinute()
            ->between('09:00', '23:10');                // 早上 09:00 到晚上 23:10 之间执行

        $schedule->command('SendPrize:run all cqkls')
            ->name('cqkls')                             // 重庆快乐 10 分
            ->runInBackground()
            ->everyMinute()
            ->unlessBetween('02:30', '10:00');          // 早上 02:30 到早上 10:00 之间不执行

        $schedule->command('SendPrize:run all pcdd')
            ->name('pcdd')                              // PC 蛋蛋
            ->runInBackground()
            ->everyMinute()
            ->unlessBetween('00:10', '09:00');          // 早上 00:10 到早上 09:00 之间不执行

        $schedule->command('SendPrize:run all 5fpk10')
            ->name('5fpk10')                            // 五分 PK10
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all jsscpk10')
            ->name('jsscpk10')                          // 极速赛车 PK10
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all xyftpk10')
            ->name('xyftpk10')                          // 幸运飞艇 PK10
            ->runInBackground()
            ->everyMinute()
            ->unlessBetween('05:00', '13:00');          // 早上 05:00 到早上 13:00 之间不执行

        $schedule->command('SendPrize:run all xyffc')
            ->name('xyffc')                             // 幸运分分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all xy3fc')
            ->name('xy3fc')                             // 幸运三分彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all xyssc')
            ->name('xyssc')                             // 幸运时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all xyffpk10')
            ->name('xyffpk10')                          // 幸运分分PK10
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all xy3fpk10')
            ->name('xy3fpk10')                          // 幸运三分PK10
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all xyscpk10')
            ->name('xyscpk10')                          // 幸运赛车PK10
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all ffk3')
            ->name('ffk3')                              // 分分快三
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 3fk3')
            ->name('3fk3')                              // 三分快三
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 5fk3')
            ->name('5fk3')                              // 五分快三
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all ffkls')
            ->name('ffkls')                             // 分分快乐10分
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 3fkls')
            ->name('3fkls')                             // 三分快乐10分
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 5fkls')
            ->name('5fkls')                             // 五分快乐10分
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all fff11x5')
            ->name('fff11x5')                           // 分分11选5
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 3ff11x5')
            ->name('3ff11x5')                           // 三分11选5
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all 5ff11x5')
            ->name('5ff11x5')                           // 五分11选5
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all fkscpk10')
            ->name('fkscpk10')                           // 疯狂赛车
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all fkssc')
            ->name('fkssc')                              //  疯狂时时彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all tx5fpk10')
            ->name('tx5fpk10')                           //  腾讯PK10
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all jssmpk10')
            ->name('jssmpk10')                           // 极速赛马PK10
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all hn5fssc')
            ->name('hn5fssc')                            // 河内五分彩LJ
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all hn1fssc')
            ->name('hn1fssc')                            // 河内分分彩LJ
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqualyffssc')
            ->name('qiqualyffssc')                       // 奇趣阿里云分分彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqualy5fssc')
            ->name('qiqualy5fssc')                       // 奇趣阿里云五分彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqualy10fssc')
            ->name('qiqualy10fssc')                      // 奇趣阿里云十分彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqu360ffssc')
            ->name('qiqu360ffssc')                       // 奇趣360分分彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqu3605fssc')
            ->name('qiqu3605fssc')                       // 奇趣360五分彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all qiqu36010fssc')
            ->name('qiqu36010fssc')                      // 奇趣360十分彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all hlj11x5')
            ->name('hlj11x5')                            // 黑龙江11选5
            ->runInBackground()
            ->everyMinute()
            ->between('08:30', '23:30');                 // 早上 08:30 到晚上 23:30 之间执行

        $schedule->command('SendPrize:run all js11x5')
            ->name('js11x5')                             // 江苏11选5
            ->runInBackground()
            ->everyMinute()
            ->between('08:30', '23:00');                 // 早上 08:30 到晚上 23:00 之间执行

        $schedule->command('SendPrize:run all hne1fssc')
            ->name('hne1fssc')                           // 河内分分彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all hne5fssc')
            ->name('hne5fssc')                           // 河内五分彩
            ->runInBackground()
            ->everyMinute();

        $schedule->command('SendPrize:run all jndkl8')
            ->name('jndkl8')                             // 加拿大快乐8
            ->runInBackground()
            ->everyMinute();

        $schedule->command('SendPrize:run all jndpcdd')
            ->name('jndpcdd')                            // 加拿大PC28
            ->runInBackground()
            ->everyMinute();

        $schedule->command('SendPrize:run all xcqssc')
            ->name('xcqssc')                             // 新重庆时时彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all xxjssc')
            ->name('xxjssc')                             // 新新疆时时彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all xbjpk10')
            ->name('xbjpk10')                            // 新北京PK10
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all gjtxffssc')
            ->name('gjtxffssc')                          // 国际腾讯分分彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all gjtxffssc')
            ->name('gjtxffssc')                          // 国际腾讯分分彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all jdcqssc')
            ->name('jdcqssc')                            // 经典重庆时时彩
            ->runInBackground()
            ->everyMinute();                             // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all jdxjssc')
            ->name('jdxjssc')                           // 经典重庆时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all jdbjpk10')
            ->name('jdbjpk10')                          // 经典北京PK10
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all lcqssc')
            ->name('lcqssc')                            // 老重庆时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all lxjssc')
            ->name('lxjssc')                            // 老新疆时时彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all fflhc')
            ->name('fflhc')                             // 极速分分六合彩
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all ztx10fssc')
            ->name('ztx10fssc')                         // 腾讯10分彩(自开彩)
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all zqhk3')
            ->name('zqhk3')                             // 青海快三(自开彩)
            ->runInBackground()
            ->everyMinute();                            // 每分钟执行一次，不限时间段

        $schedule->command('SendPrize:run all hl30s')
            ->name('hl30s')                           // 新加破-欢乐30秒
            ->runInBackground()
            ->everyMinute();
        */
#######################################################################################################

        $schedule->command('Report:run Activity')
            ->name('ReportActivity')                    // 活动费用报表
            ->runInBackground()
            ->everyTenMinutes();

        $schedule->command('Report:run Deposit')
            ->name('ReportDeposit')                     // 充值报表
            ->runInBackground()
            ->everyTenMinutes();

        $schedule->command('Report:run Withdrawal')
            ->name('ReportWithdrawal')                  // 提现报表
            ->runInBackground()
            ->everyTenMinutes();

        $schedule->command('Report:run DailyWage')
            ->name('ReportDailyWage')                   // 日工资报表
            ->runInBackground()
            ->everyTenMinutes();

        //凤鸣实时工资统计需要 单号 APFM-1
        $schedule->command('Report:run Sales')
            ->name('ReportSales')                       // 用户5天，15天，销量，活跃人数数据报表
            ->runInBackground()
            ->withoutOverlapping()
            ->dailyAt("04:00");                         // 每天 04:00 进行统计与发放

        $schedule->command('IssueError:run')
            ->name('IssueError')                        // 奖期异常处理
            ->runInBackground()
            ->everyMinute();

        $schedule->command('AutoWithdrawalThird:run')
            ->name('AutoWithdrawalThird')               // 自动出款
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                            // 提现时间是 9：00-02:30，但是中间要进行出款状态确认，所以 24 小时进行

        $schedule->command('AutoWithdrawalRisk:run')
            ->name('AutoWithdrawalRisk')               // 提现自动风控审核
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();

        $schedule->command('Cleanup:run')
            ->name('Cleanup')                          // 数据清理
            ->runInBackground()
            ->dailyAt('03:30');                        // 每天凌晨 03:30


        $schedule->command('UserFundBackup:run backup')
            ->name('UserFundBackup')                  // 彩票余额备份
            ->runInBackground()
            ->dailyAt('00:00');                       // 每天凌晨 00:00

        $schedule->command('ThirdGameUserFundBackup:run backup')
            ->name('ThirdGameUserFundBackup')         // 第三方余额备份
            ->runInBackground()
            ->dailyAt('00:00');                       // 每天凌晨 00:00

        //自动生成号码
        $schedule->command('AutoPrevDraw:run')
            ->name('AutoPrevDraw')                    // 自主彩种提前生成号码
            ->runInBackground()
            ->dailyAt('02:30');                       // 每天凌晨 02:30

        //自动生成奖期
        $schedule->command('AutoGenerateIssue:run')
            ->name('AutoGenerateIssue')               // 自动生成奖期
            ->runInBackground()
            ->dailyAt('05:00');                       // 每天凌晨 05:00

        //自动生成、修正香港六合彩奖期
        $schedule->command('AutoGenerateXglhcIssue:run')
            ->name('AutoGenerateXglhcIssue')          // 自动生成、修正香港六合彩奖期
            ->runInBackground()
            ->twiceDaily(3, 7);                       // 每两天凌晨 3点、7点

        //自动生成 加拿大快乐8、加拿大PC28 奖期
        $schedule->command('AutoGenerateJndkl8Issue:run')
            ->name('AutoGenerateJndkl8Issue')         // 自动生成 加拿大快乐8、加拿大PC28 奖期
            ->runInBackground()
            ->everyMinute()
            ->between('21:00', '22:30');              // 晚上 21:00 到晚上 22:30 之间执行

        $schedule->command('Dividend:run calculate')
            ->name('dividendCaculate')                // 分红计算
            ->runInBackground()
            ->dailyAt('04:00');                       // 每天凌晨 04:00

        $schedule->command('Dividend:run send')
            ->name('dividendSend')                    // 分红发送
            ->runInBackground()
            ->everyTenMinutes();                      // 每分钟检查是否有已确认分红，自动派发

        //每日用户报表
        $schedule->command('Report:run DateUser')
            ->name('ReportDateUser')                  // 每日用户报表
            ->runInBackground()
            ->dailyAt('00:30');                       // 每天凌晨 00:30

        //第三方游戏计划任务
        $this->_scheduleThird($schedule);

        //活动相关计划任务
        $this->_scheduleActivity($schedule);

        // 工资相关
        $this->_scheduleWage($schedule);

        // WIN杀号
        $this->_scheduleKillCodeWin($schedule);

        //私返
        $this->_schedulePrivateReturn($schedule);

        //彩票抽水返水帐变
        $schedule->command('PumpOrder:run')
            ->name('PumpOrder')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyFiveMinutes();                            // 每五分钟运行一次

        //定时红包任务
        if (get_config('hourly_coupon_enabled', 0)) {
            $schedule->command('HourlyCoupon:run')
                ->name('HourlyCoupon')
                ->runInBackground()
                ->hourly();                                // 每小时发一次
        }
        //平台日报表
        $schedule->command('Report:run LotteryBonus')
            ->name('LotteryBonusDaily')
            ->runInBackground()
            ->dailyAt('06:00');

        //三方每日报表
        $schedule->command('Report:run ThirdGame')
            ->name('ThirdGameDaily')
            ->runInBackground()
            ->dailyAt('06:10');

        //设备盈亏日报表
        $schedule->command('Report:run DailyDevice')
            ->name('DailyDevice')
            ->runInBackground()
            ->dailyAt('06:15');

        //彩票玩法分析报表
        $schedule->command('Report:run MethodAnalyse')
            ->name('MethodAnalyse')
            ->runInBackground()
            ->withoutOverlapping()
            ->hourlyAt(5);

        //团队日报表
        $schedule->command('Report:run TeamTotalDaily')
            ->name('TeamTotalDaily')
            ->runInBackground()
            ->dailyAt('06:00');

        //自动更新虚拟货币银行汇率[RBCX]
        $schedule->command('AutoRbcxRate:run')
            ->name('AutoRbcxRate')
            ->runInBackground()
            ->everyMinute();

        //自动更新虚拟货币银行汇率[OTC365]
        $schedule->command('AutoOtc365Rate:run')
            ->name('AutoOtc365Rate')
            ->runInBackground()
            ->everyMinute();

        //个人三方日报表
        $schedule->command('Report:run ThirdGameUserProfit')
            ->name('ThirdGameUserProfit')
            ->runInBackground()
            ->dailyAt('06:10');
    }

    /**
     * 活动相关计划任务
     * @param Schedule $schedule
     */
    protected function _scheduleActivity(Schedule $schedule)
    {
        $schedule->command('Activity:run reglink')
            ->name('ActivityReglink')        // 注册链接邀请码活动
            ->runInBackground()
            ->withoutOverlapping()
            ->weekly()->mondays()->at('03:00');   // 周一3 点进行统计上周数据

        $schedule->command('Activity:run prizepool')
            ->name('ActivityPrizepool')      // 奖池活动
            ->runInBackground()
            ->withoutOverlapping()
            ->hourly();

        $schedule->command('Activity:run jackpot runCron')
            ->name('ActivityJackpotRunCron') // 幸运大奖池
            ->runInBackground()
            ->withoutOverlapping()
            ->hourly();

        //亿博专用活动：主管充值奖励活动
        if (get_config('app_ident', '') === 'YB') {
            $schedule->command('Activity:run yibo_rechargemanage runCron')
                ->name('ActivityYiboRechargemanageRunCron')
                ->runInBackground()
                ->dailyAt('03:30');            // 每天 03:30 进行统计与发放
        }

        //创赢专用活动：取款佣金
        if (get_config('app_ident', '') === 'cy') {
            $schedule->command('Activity:run cy_withdrawcommission runCron')
                ->name('ActivityCYWithdrawCommissionRunCron')
                ->runInBackground()
                ->dailyAt('03:30');            // 每天 03:30 进行统计与发放
        }
    }

    protected function _scheduleThird(Schedule $schedule)
    {
        $schedule->command('ThirdGame:run all Ag')
            ->name('ThirdGameAg')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Ebet')
            ->name('ThirdGameEbet')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Sb')
            ->name('ThirdGameSb')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Sunbet')
            ->name('ThirdGameSunbet')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Vr')
            ->name('ThirdGameVr')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Ky')
            ->name('ThirdGameKy')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Pt')
            ->name('ThirdGamePt')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Im')
            ->name('ThirdGamIm')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Ab')
            ->name('ThirdGameAb')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all FhLeli')
            ->name('ThirdGameFhLeli')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all GgQiPai')
            ->name('ThirdGameGgQiPai')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all GgDy')
            ->name('ThirdGameGgDy')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Sbo')
            ->name('ThirdGameSbo')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Ug')
            ->name('ThirdGameUg')
            ->runInBackground()
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all LgQp')
            ->name('ThirdGameLgQp')
            ->runInBackground()                     //幸运棋牌
            ->withoutOverlapping()
            ->everyMinute();
        $schedule->command('ThirdGame:run all LcQp')
            ->name('ThirdGameLcQp')
            ->runInBackground()                     //龙城棋牌
            ->withoutOverlapping()
            ->everyMinute();
        $schedule->command('ThirdGame:run all VgQp')
            ->name('ThirdGameVgQp')
            ->runInBackground()                     //财神棋牌
            ->withoutOverlapping()
            ->everyMinute();
        $schedule->command('ThirdGame:run all MgDy')
            ->name('ThirdGameMgDy')
            ->runInBackground()                     //MG电游
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段
        $schedule->command('ThirdGame:run all BngDy')
            ->name('ThirdGameBngDy')
            ->runInBackground()                     //BNG电游
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Tcg')
            ->name('ThirdGameTcg')
            ->runInBackground()                     //TCG体育
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Aio')
            ->name('ThirdGameAio')
            ->runInBackground()                     //AIO沙巴体育(CIA)
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Wml')
            ->name('ThirdGameWml')
            ->runInBackground()                     // WML完美真人
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGameDs:run Wml')
            ->name('ThirdGameDsWml')
            ->runInBackground()                     // WML完美真人打赏
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Leg')
            ->name('ThirdGameLeg')
            ->runInBackground()                     // LEG乐游棋牌
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Avia')
            ->name('ThirdGameAvia')
            ->runInBackground()                     // AVIA泛亚电竞
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Mg2')
            ->name('ThirdGameMg2')
            ->runInBackground()                     //MG2电游
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Ds')
            ->name('ThirdGameDs')
            ->runInBackground()                      // DS电竞
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段

        $schedule->command('ThirdGame:run all Bg')
            ->name('ThirdGameBg')
            ->runInBackground()                      // BG大游
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段
        
        $schedule->command('ThirdGame:run all Sea')
            ->name('ThirdGameSea')
            ->runInBackground()                     // SEA电游
            ->withoutOverlapping()
            ->everyMinute();                        // 每1分钟执行一次，不限时间段
    }

    /**
     * 工资计划任务
     * @param Schedule $schedule
     */
    protected function _scheduleWage(Schedule $schedule)
    {
        if (get_config('crontab_DailyWageAll', 0)) {
            $schedule->command('DailyWage:run all')
                ->name('DailyWageAll')                 // 日工资
                ->runInBackground()
                ->withoutOverlapping()
                ->dailyAt("02:10");                    // 修改成每天 02:10 进行统计与发放，避免与DailyWageSend同时执行
        }

        if (get_config('crontab_DailyWageSend', 0)) {
            $schedule->command('DailyWage:run send')
                ->name('DailyWageSend')                // 日工资发放
                ->runInBackground()
                ->withoutOverlapping()
                ->everyThirtyMinutes();                // 每30分钟检查一次是否有需要发放的记录（有时需要等运营确认发放）
        }

        //中挂单日工资计算
        if (get_config('crontab_winLossDailyWageCalculate', 0)) {
            $schedule->command('DailyWage:run winLossDailyWageCalculate')
                ->name('winLossDailyWageCalculate')    // 中挂单日工资计算
                ->runInBackground()
                ->withoutOverlapping()
                ->dailyAt("02:00");                    // 每天 02:00 进行统计
        }

        //中挂单日工资发放
        if (get_config('crontab_winLossDailyWageSend', 0)) {
            $schedule->command('DailyWage:run winLossDailyWageSend')
                ->name('winLossDailyWageSend')         // 中挂单日工资发放
                ->runInBackground()
                ->withoutOverlapping()
                ->everyThirtyMinutes();                // 每30分钟检查一次是否有需要发放的记录（有时需要等运营确认发放）
        }

        if (get_config('crontab_RealtimeWage', 0)) {
            $schedule->command('DailyWage:run realtime')
                ->name('RealtimeWage')                 // 实时工资发放
                ->runInBackground()
                ->withoutOverlapping()
                ->everyMinute();                       // 每分钟运行一次
        }

        if (get_config('crontab_IssueWageA', 0)) {
            $schedule->command('DailyWage:run issueWageA')
                ->name('IssueWage')                    //中挂单奖期工资
                ->runInBackground()
                ->withoutOverlapping()
                ->everyFiveMinutes();                  // 每五分钟运行一次
        }

        if (get_config('crontab_hourCalculate', 0)) {
            $schedule->command('DailyWage:run hourCalculate')
                ->name('hourCalculate')                // 小时工资计算
                ->runInBackground()
                ->withoutOverlapping()
                ->cron('10 * * * *');                  // 每小时10分运行一次
        }

        if (get_config('crontab_hourSend', 0)) {
            $schedule->command('DailyWage:run hourSend')
                ->name('hourSend')                     // 小时工资发放
                ->runInBackground()
                ->withoutOverlapping()
                ->everyMinute();                       // 每分钟运行一次
        }

        if (get_config('crontab_hourBCalculate', 0)) {
            $schedule->command('DailyWage:run hourBCalculate')
                ->name('hourBCalculate')               // 小时工资普通计算
                ->runInBackground()
                ->withoutOverlapping()
                ->cron('10 * * * *');                  // 每小时运行一次
        }

        if (get_config('crontab_hourBSend', 0)) {
            $schedule->command('DailyWage:run hourBSend')
                ->name('hourBSend')                    // 小时工资普通
                ->runInBackground()
                ->withoutOverlapping(40)//单位 分种
                ->everyMinute();                       // 每小时运行一次
        }

        // 一彩主管工资计算
        if (get_config('crontab_zhuguanWage', 0)) {
            $schedule->command('DailyWage:run zhuguanWageCalculate')
                ->name('zhuguanWage')                  // 每天凌晨03：00计算前一天工资
                ->runInBackground()
                ->withoutOverlapping()
                ->dailyAt('03:00');                    // 每天凌晨03：00运行一次
        }

        // 浮动工资计算
        if (get_config('crontab_floatCalculate', 0)) {
            $schedule->command('DailyWage:run floatWageCalculate')
                ->name('floatWageCalculate')           // 浮动工资计算
                ->runInBackground()
                ->withoutOverlapping()
                ->dailyAt('03:00');                    // 每天凌晨03：00运行一次
        }

        // 浮动工资发放
        if (get_config('crontab_floatSend', 0)) {
            $schedule->command('DailyWage:run floatWageSend')
                ->name('floatWageSend')                // 浮动工资发放
                ->runInBackground()
                ->withoutOverlapping()
                ->everyMinute();                       // 每分钟运行一次
        }
    }

    /**
     * 私返计划任务
     * @param Schedule $schedule
     */
    protected function _schedulePrivateReturn(Schedule $schedule)
    {
        //计算日私返
        if (get_config('crontab_private_return_calculate_daily', 0)) {
            $schedule->command('PrivateReturn:run daily')
                ->name('PrivateReturnDaily')           //日私返
                ->runInBackground()
                ->withoutOverlapping()
                ->dailyAt("02:15");                    //每天 02:15 进行统计与发放
        }

        //计算小时私返
        if (get_config('crontab_private_return_calculate_hourly', 0)) {
            $schedule->command('PrivateReturn:run hourly')
                ->name('PrivateReturnHourly')          //小时私返
                ->runInBackground()
                ->withoutOverlapping()
                ->cron('13 * * * *');                  // 每小时运行一次
        }

        //发送私返
        if (get_config('crontab_private_return_send', 0)) {
            $schedule->command('PrivateReturn:run send')
                ->name('PrivateReturnSend')            //发放私返
                ->runInBackground()
                ->withoutOverlapping()
                ->everyFiveMinutes();                  //每5分钟运行一次
        }
    }

    /**
     * 杀号
     * @param Schedule $schedule
     */
    protected function _scheduleKillCodeWin(Schedule $schedule)
    {
        if (!get_config('win_codes_enabled', 1)) { //默认开启
            return true;
        }
        //计算WIN开奖号码
        $win_codes_cli_lotteries = [];
        if ($win_codes_cli = get_config('win_codes_cli', '')) {
            $win_codes_cli_lotteries = explode(',', $win_codes_cli);
        }
        if ($win_codes_cli_lotteries) {
            foreach ($win_codes_cli_lotteries as $third_lottery) {
                $schedule->command('KillCodeWin:run ' . $third_lottery)
                    ->name('KillCodeWin_' . $third_lottery)
                    ->runInBackground()
                    ->withoutOverlapping(5)
                    ->everyMinute();
            }
        }
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
