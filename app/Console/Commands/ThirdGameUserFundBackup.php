<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Backup\ThirdGameUserFund;

class ThirdGameUserFundBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ThirdGameUserFundBackup:run {fun} {additional?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '第三方余额备份程序，例：php artisan ThirdGameUserFundBackup:run backup';

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
        $additional = $this->argument('additional');
        $fun = $this->argument('fun');
        $user_fund = new ThirdGameUserFund();
        $user_fund->$fun($additional === null ? '' : json_decode($additional, true));
    }
}
