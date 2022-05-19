<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Report\Report as Rollup;

class Report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Report:run {type} {params?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '报表汇总';

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
        Rollup::run($this->argument('type'), $this->argument('params'));
    }
}
