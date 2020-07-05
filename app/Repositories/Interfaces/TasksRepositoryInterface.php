<?php

namespace App\Repositories\Interfaces;


interface TasksRepositoryInterface
{
    /**
     * Get tasks by filter, order, page
     * @param array $filters
     * @param array $order
     * @param int|null $per_page
     * @return array
     */
    public function all(array $filters = [], array $order = [], $per_page = null);

}