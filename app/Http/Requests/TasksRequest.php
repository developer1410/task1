<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TasksRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Pagination
            'per_page' => 'int',
            // Order
            'order.column' => 'filled|string',
            'order.type' => 'filled|string',
            // Columns
            'status' => 'string',
            'creator_id' => 'int',
            'assigned_user_id' => 'int',
            'title' => 'string|max:250',
            'description' => 'string|max:1000',
            'status_id' => 'integer',
            'estimation_date' => 'date',
            'started_date' => 'date'
        ];
    }
}
