<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Issue\Generator;

class AutoGenerateXglhcIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoGenerateXglhcIssue:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '香港六合彩自动生成奖期';

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
        $issue->generateXglhcIssue();
    }
}
