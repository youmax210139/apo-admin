<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Dividend\Cron;

class Dividend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Dividend:run {fun} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '分红计算和派发，参数 {fun} {date?} 例如：php artisan Dividend:run all xxxx-xx-xx';

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
        $fun = $this->argument('fun');
        $date = $this->argument('date')??date('Y-m-d', strtotime('-1 days'));

        Cron::$fun($date);
    }
}
