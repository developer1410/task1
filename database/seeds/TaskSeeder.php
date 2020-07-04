<?php

use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Task::whereNotNull('id')->delete();

        factory(\App\Task::class, 10)->create();
    }
}
