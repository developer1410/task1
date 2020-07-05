<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class UsersController
{
    /**
     * Get list of user with pagination
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'int'
        ]);

        $per_page = $request->per_page
            ? intval($request->per_page)
            : User::DEFAULT_PER_PAGE; // By default 10 entries by request

        $user_sql = DB::table('users')
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'email_verified_at',
                'created_at'
            ]);

        $users = $user_sql->paginate($per_page);

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total()
            ]
        ]);
    }

}