<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\ThirdGame\Cron;

class ThirdGameDs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ThirdGameDs:run {ident} {additional?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '第三方游戏记录抓取打赏，例：php artisan ThirdGameDs:run Wml \'{"date":"2020-01-01"}\'';

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
        $ident = $this->argument('ident'); // 第三方游戏标识
        $additional = $this->argument('additional'); // 参数，例如 {\"do\":\"balance\"}
        Cron::ds($ident, $additional === null ? '' : json_decode($additional, true));
    }
}
