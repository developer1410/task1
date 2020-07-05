<?php

namespace App\Http\Controllers;

use App\Http\Requests\TasksRequest;
use App\Task;
use Illuminate\Http\Request;
use App\Repositories\TasksRepository;

class TasksController extends Controller
{
    protected $taskRepository = null;

    public function __construct(TasksRepository $tasksRepository)
    {
        $this->taskRepository = $tasksRepository;
    }

    /**
     * @param TasksRequest $request
     * @return mixed
     */
    public function index(TasksRequest $request) {
        return $this->taskRepository->all(
            $request->only(['status']),
            $request->order ?? [],
            $request->per_page
        );
    }

    /**
     * Create new task
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

        $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string|max:1000',
            'status_id' => 'required|integer',
            'assigned_user_id' => 'filled|integer'
        ]);

        $user = auth()->user();

        $task = Task::create([
            'creator_id' => $user->id,
            'assigned_user_id' => $request->assigned_user_id ?? $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'status_id' => $request->status_id
        ]);

        if(empty($task))
            return response()->json(['message' => 'Can not create new task!'], 500);


        return response()->json([
            'task_id' => $task->id
        ], 201);
    }

    /**
     * Update task
     * @param Task $task
     * @param TasksRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Task $task, TasksRequest $request) {

        $update = $request->only(['assigned_user_id', 'title', 'description',
            'status_id', 'estimation_date', 'started_date']);

        if(empty($update))
            return response()->json(['message' => 'Empty request'], 400);

        if($task->update($update)) {
            $status =200;
            $message = 'Successfully update';
        } else {
            $status = 500;
            $message = 'Error during updating!';
        }

        return response()->json(
            [
                'task_id' => $task->id,
                'message' => $message
            ], $status);
    }

    /**
     * Delete task
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Task $task) {
        if($task->delete()) {
            $status =200;
            $message = 'Successfully deleted';
        } else {
            $status = 500;
            $message = 'Error during deleting. Try again later.';
        }
        return response()->json(
            [
                'task_id' => $task->id,
                'message' => $message
            ],
            $status
        );
    }


}
