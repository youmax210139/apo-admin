<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoPrevDraw extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoPrevDraw:run {lottery_ident?} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自主彩种提前生成号码';

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
        $lottery_ident = $this->argument('lottery_ident');
        $date = $this->argument('date');

        $issue = new \Service\API\SendPrize\Draw\AutoPrevDraw();
        $issue->run($lottery_ident, $date);
    }
}
