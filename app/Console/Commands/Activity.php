<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Activity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Activity:run {fun} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '活动相关计划任务，例：php artisan Activity:run all xxxx-xx-xx';

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
        \Service\API\Activity\Cron::$fun($date);
    }
}
