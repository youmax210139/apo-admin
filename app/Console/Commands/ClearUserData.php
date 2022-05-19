<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\ClearUserData as ClearUserDataApi;

class ClearUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ClearUserData:run {fun}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用户数据清理 参数 {fun} check:查看 clear:清理 例如：：php artisan ClearUserData:run check';

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

        $api = new ClearUserDataApi();
        $api->$fun();
    }
}
