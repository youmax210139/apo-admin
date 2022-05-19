<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\ThirdGame\Cron;

class ThirdGame extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */

    /**
     * 第三方游戏运行命令
     * @var string
     */
    protected $signature = 'ThirdGame:run {fun} {ident} {additional?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '第三方记录抓取与返点程序，例：php artisan ThirdGame:run fetch Ag \'{"date":"2019-01-01"}\'';

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
        $fun = $this->argument('fun');//函数名
        $ident = $this->argument('ident');//第三方游戏标识
        $additional = $this->argument('additional');//参数，例如 {\"do\":\"balance\"}
        Cron::$fun($ident, $additional === null ? '' : json_decode($additional, true));
    }
}
