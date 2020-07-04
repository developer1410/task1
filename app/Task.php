<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    const DEFAULT_PER_PAGE = 5;

    // All fields that can be updated
    protected $fillable = [
        'creator_id',
        'assigned_user_id',
        'title',
        'description',
        'status_id',
        'estimation_date',
        'started_date',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'estimation_date' => 'datetime',
        'started_date' => 'datetime'
    ];

    /**
     * Get status
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status() {
        return $this->belongsTo(
            TaskStatus::class,
            'status_id',
            'id'
        );
    }

    /**
     * Get creator
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator() {
        return $this->belongsTo(
            User::class,
            'creator_id',
            'id'
        );
    }

    /**
     * Get assigned user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedUser() {
        return $this->belongsTo(
            User::class,
            'assigned_user_id',
            'id'
        );
    }
}
