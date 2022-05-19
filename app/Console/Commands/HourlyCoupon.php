<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Service\API\Coupon;

class HourlyCoupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'HourlyCoupon:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '小时红包';

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
        Coupon::hourlyCoupon();
    }
}
