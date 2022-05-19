<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Issue\Generator;

class AutoGenerateJndkl8Issue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoGenerateJndkl8Issue:run {date?} {force?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '加拿大快乐8 和 加拿大PC28 自动生成奖期';

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
        $date = $this->argument('date');
        $force = $this->argument('force'); // 0 或 1
        $issue = new Generator();
        $issue->generateJndkl8Issue($date, $force);
    }
}
