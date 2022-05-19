<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TasksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Service\Models\Tasks::class, 30)->create();
    }
}
