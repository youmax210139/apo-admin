<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Pump\Cron as PumpCron;

/**
 * 返水发放在这里
 * 至于抽水计算/扣除/返水计算写在SendPrize后台
 * 其实返水大多数在抽水的时候已经发放，但是以后不知道会不会有按小时或天发放的需求
 * 也有存在发放关闭后，重新打开补发的情况
 * @package App\Console\Commands
 */
class PumpOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PumpOrder:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '彩票抽返水帐变';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (get_config('pump_inlet_order_enabled') == 1 || get_config('pump_outlet_order_enable') == 1) {
            PumpCron::Order();
        }
    }
}
