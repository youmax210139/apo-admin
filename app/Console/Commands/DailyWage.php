<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\DailyWage\Cron;

class DailyWage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DailyWage:run {fun} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '日工资计算与发放。例：php artisan DailyWage:run all xxxx-xx-xx';

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
        $date = $this->argument('date');

        Cron::$fun($date);
    }
}
