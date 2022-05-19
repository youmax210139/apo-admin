<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TaskdetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Service\Models\Taskdetails::class, 1200)->create();
    }
}
