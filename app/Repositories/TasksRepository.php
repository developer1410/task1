<?php

namespace App\Repositories;

use App\Repositories\Interfaces\TasksRepositoryInterface;
use App\Task;
use Illuminate\Support\Facades\DB;

class TasksRepository implements TasksRepositoryInterface
{
    private $available_orders = [];
    /**
     * @var array ['name' => ['column' => '', 'condition'=>''(optional)(default is `=`)]]
     */
    private $available_filters = [
        'status' => ['column' => 'ts.name']
    ];

    public function __construct()
    {
        $this->available_orders = [
            'status' => 'ts.name',
            'tasks_title' => 'tasks.title',
            'creator_id' => 'creator.id',
            'creator_name' => DB::raw('CONCAT_WS(" ", creator.first_name, creator.last_name)'),
            'creator_created_at' => 'creator.created_at',
            'assigned_user_name' => DB::raw('CONCAT_WS(" ", assigned_user.first_name, assigned_user.last_name)'),
            'created_at' => 'tasks.created_at',
        ];
    }

    /**
     * @param array $filters
     * @param array $order
     * @param int|null $per_page
     * @return array
     */
    public function all(array $filters = [], array $order = [], $per_page = null) {
        $tasks_sql = Task::leftJoin(DB::raw('task_statuses as ts'), 'ts.id', '=', 'tasks.status_id')
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

        // Add order
        if(
            !empty($order)
            && array_key_exists($order['column'], $this->available_orders)
        ) {
            $tasks_sql->orderBy($this->available_orders[$order['column']], $order['type'] ?? 'asc');
        }

        foreach ($filters as $name => $value) {
            if(array_key_exists($name, $this->available_filters)) {
                $tasks_sql->where(
                    $this->available_filters[$name]['column'],
                    $this->available_filters[$name]['condition'] ?? '=',
                    $value
                );
            }
        }

        $per_page = $per_page
            ? intval($per_page)
            : Task::DEFAULT_PER_PAGE; // By default 10 entries by request

        $tasks = $tasks_sql->paginate($per_page);

        return [
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'from' => $tasks->firstItem(),
                'to' => $tasks->lastItem(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total()
            ]
        ];
    }



}