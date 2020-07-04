<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Task;
use Faker\Generator as Faker;

$factory->define(Task::class, function (Faker $faker) {
    $users = \App\User::all();
    $statuses = \App\TaskStatus::all();
    // Add 10 row to table
    $creator = $users->random(1)->first();
    $assigned_user = $users->random(1)->first();
    $status = $statuses->random(1)->first();

    return [
        'creator_id' => $creator->id,
        'assigned_user_id' => $assigned_user->id,
        'title' => $faker->text(150),
        'description' => $faker->text(250),
        'status_id' => $status->id
    ];
});
