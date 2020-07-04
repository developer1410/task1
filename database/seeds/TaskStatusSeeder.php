<?php

use Illuminate\Database\Seeder;

use App\TaskStatus;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TaskStatus::whereNotNull('id')->delete();

        TaskStatus::insert([
            [
                'id' => 1,
                'name' => 'View'
            ], [
                'id' => 2,
                'name' => 'In Progress'
            ], [
                'id' => 3,
                'name' => 'Done'
            ]
        ]);

    }
}
