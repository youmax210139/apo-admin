<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\Service\Models\Projects::class, 30)->create();
    }
}
