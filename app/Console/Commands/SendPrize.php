<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\SendPrize\SendPrize as SendPrizeAPI;
use Service\API\Pump\Cron as PumpCron;

class SendPrize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendPrize:run {fun} {ident} {issue?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '派奖程序，参数：{fun} {ident} {issue?}';

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
        $ident = $this->argument('ident');
        $issue = $this->argument('issue');
        $fun = $this->argument('fun');
        SendPrizeAPI::$fun($ident, $issue === null ? '' : $issue);
        //为了即时性，抽水代码不写在开奖代码里，只能跟在这里
        if (get_config('pump_inlet_enabled', 0) == 1) {
            PumpCron::calculate($ident);
        }
    }
}
