<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Withdrawal\Risk;

class AutoWithdrawalRisk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AutoWithdrawalRisk:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '提现自动风控审核';

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
        $risk = new Risk();
        $risk->process();
    }
}
