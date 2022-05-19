<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $only_user_tree = $this->command->askWithCompletion('Seeding....Only User tree(yes/no)', ['yes', 'no'], 'yes');

        $this->call([
            UsersSeeder::class,
            UserFundSeeder::class,
            UserBanksSeeder::class,
            UserRebatesSeeder::class,
            UsersPrizelevelSeeder::class
        ]);
        if ($only_user_tree != 'yes') {
            $this->call([
                AdminUsersTableSeeder::class,
                OrderSeeder::class,
                PaymentSeeder::class,
                ProjectsSeeder::class,
                TasksSeeder::class,
                TaskdetailsSeeder::class,
                DespositSeeder::class,
                WithdrawalsSeeder::class,
                ThirdGameSeeder::class,
                ReportLotterySeeder::class,
                UserBehaviorLogSeeder::class,
                MonitorSeeder::class,
            ]);
        }
    }
}
