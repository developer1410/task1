<?php

namespace App\Http\Controllers;

use App\TaskStatus;

class TasksStatusesController extends Controller
{
    /**
     * Get list of task's statuses
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return response()->json(TaskStatus::get([
            'id',
            'name'
        ]));
    }
}