<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\PrivateReturn\Cron;

class PrivateReturn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PrivateReturn:run {fun} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '私返计算与发放，例：php artisan PrivateReturn:run daily xxxx-xx-xx';

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
