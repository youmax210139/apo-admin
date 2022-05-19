<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Cleanup\Cleanup as DatabaseClean;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Cleanup:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '数据清理';

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
        DatabaseClean::run();
    }
}
