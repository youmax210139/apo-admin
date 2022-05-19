<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\PushThing\Cron;

class KillCodeWin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'KillCodeWin:run {third_lottery?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '多进程模拟盈亏计算【WIN】，例：php artisan KillCodeWin:run cqssc';

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
        $third_lottery = $this->argument('third_lottery');
        $sec = intval(date('s'));
        if($sec < 40){
            $this->line('sleep '.(40-$sec));
            sleep(40-$sec);
        }
        $cron = new Cron();
        $cron->win($third_lottery);
    }
}
