<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request) {
        // validation
        $request->validate([
            'per_page' => 'int',
            'page' => 'int',
            // Order
            'order.column' => 'filled|string',
            'order.type' => 'filled|string',
            // Filters
            'status' => 'string',
            'creator_id' => 'int',
            'assigned_user_id' => 'int'
        ]);

        $tasks_sql = DB::table('tasks')
            ->leftJoin(DB::raw('task_statuses as ts'), 'ts.id', '=', 'tasks.status_id')
            ->leftJoin(DB::raw('users as creator'), 'creator.id', '=', 'tasks.creator_id')
            ->leftJoin(DB::raw('users as assigned_user'), 'assigned_user.id', '=', 'tasks.assigned_user_id')
            ->select([
                'tasks.id',
                DB::raw('creator.id as creator_id'),
                DB::raw('CONCAT_WS(" ", creator.first_name, creator.last_name) as creator_name'),
                DB::raw('assigned_user.id as assigned_user_id'),
                DB::raw('CONCAT_WS(" ", assigned_user.first_name, assigned_user.last_name) as assigned_user_name'),
                'tasks.title',
                'tasks.description',
                DB::raw('ts.name as status'),
                'tasks.estimation_date',
                'tasks.started_date',
                'tasks.created_at',
                'tasks.updated_at'

            ]);

        $orders = [
            'status' => 'ts.name',
            'tasks_title' => 'tasks.title',
            'creator_id' => 'creator.id',
            'creator_name' => DB::raw('CONCAT_WS(" ", creator.first_name, creator.last_name)'),
            'creator_created_at' => 'creator.created_at',
            'assigned_user_name' => DB::raw('CONCAT_WS(" ", assigned_user.first_name, assigned_user.last_name)'),
            'created_at' => 'tasks.created_at',
        ];

        // Add order
        if(
            $request->has('order')
            && array_key_exists($request->order['column'], $orders)
        ) {
            $tasks_sql->orderBy($orders[$request->order['column']], $request->order['type'] ?? 'asc');
        }

        // Filters [
        // 'name' => ['column' => '', 'condition'=>''(optional)(default is =)]]
        $filters = [
            'status' => ['column' => 'ts.name']
        ];

        foreach($filters as $name => $filter) {
            if($request->has($name)) {
                $tasks_sql->where(
                    $filter['column'],
                    $filter['condition'] ?? '=',
                    $request->$name
                );
            }
        }

        $per_page = $request->per_page
            ? intval($request->per_page)
            : Task::DEFAULT_PER_PAGE; // By default 10 entries by request


        $tasks = $tasks_sql->paginate($per_page);

        return response()->json([
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'from' => $tasks->firstItem(),
                'to' => $tasks->lastItem(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total()
            ]
        ]);
    }

    /**
     * Create new task
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {

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

        abort_if(empty($task), 500, 'Can not create new task!');

        return response()->json([
            'task_id' => $task->id
        ], 201);
    }

    /**
     * Update task
     * @param Task $task
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Task $task, Request $request) {

        $request->validate([
            'title' => 'string|max:250',
            'description' => 'string|max:1000',
            'status_id' => 'integer',
            'assigned_user_id' => 'integer',
            'estimation_date' => 'date',
            'started_date' => 'date'
        ]);

        $update = $request->only(['assigned_user_id', 'title', 'description',
            'status_id', 'estimation_date', 'started_date']);

        abort_if(empty($update), 400, 'Empty request');

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
            ], $status);

    }


}
