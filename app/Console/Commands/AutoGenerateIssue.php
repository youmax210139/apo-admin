<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Issue\Generator;

class AutoGenerateIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoGenerateIssue:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动生成奖期';

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
        $issue = new Generator();
        $issue->run();
    }
}
