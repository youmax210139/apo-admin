<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Import\Cron;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Import:run {fun} {platform} {additional?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '第三平台数据导入程序，例：php artisan Import:run all Jiucheng';

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
        $platform = $this->argument('platform');
        $additional = $this->argument('additional');
        $fun = $this->argument('fun');
        Cron::$fun($platform, $additional === null ? '' : json_decode($additional, true));
    }
}
