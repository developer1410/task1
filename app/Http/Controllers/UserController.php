<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserData;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Register new user
     * @param UserData $request
     * @return Response
     */
    public function register(UserData $request) {
        $validatedData = $request->validationData();

        $user = User::create($validatedData);

        return response()->json([
            'user_id' => $user->id
        ], 201);
    }

    public function login(Request $request) {
        $validateData = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|max:50',
        ]);

        $user = User::where(['email' => $validateData['email']])
            ->first();

        if(empty($user) || !Hash::check($validateData['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Check your email',
                'password' => 'Check your password'
            ]);
        }

        // GET current date and time
        $now = now();
        // If user has unexpired token return it in other case create new
        if(
            empty($user->bearer_token)
            || (
                $user->bearer_token
                && $now >= $user->bearer_token_expire_at
            )
        ) {
            // PicVTROyXhIYixRd24Lcephu6I5R0XjT
            $bearer_token = Str::random(User::TOKEN_LENGTH);

            $expiration_date = $now->addMinutes(User::MINUTES_TOKEN_EXPIRE_IN)->format('Y-m-d H:i:s');

            $user->bearer_token = $bearer_token;
            $user->bearer_token_expire_at = $expiration_date;

            // Update changes
            $user->save();
        }

        return response()->json([
            'bearer_token' => $user->bearer_token,
            'bearer_token_expire_at' => $user->bearer_token_expire_at
        ], 201);
    }

    /**
     * Get information about current user.
     * Also can be done for getting information by id in request /user/{id}
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request) {
       $user = auth()->user();
       return response()->json([
           'id' => $user->id,
           'first_name' => $user->first_name,
           'last_name' => $user->last_name,
           'email' => $user->email,
           'is_email_verified' => $user->is_email_verified,
           'created_at' => $user->created_at

       ]);
    }

    /**
     * Update current user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request) {
        $validData = $request->validate([
            'first_name' => 'filled|string|max:100',
            'last_name' => 'filled|string|max:100',
            'email' => 'filled|email|max:150',
            'password' => 'filled|string|max:50'
        ]);

        if(empty($validData)) {
            abort(400, 'Invalid parameters!');
        }

        $user = auth()->user();

        $user->update($validData);

        return response()->json([
            'updated_user_id' => $user->id
        ]);

    }

    /**
     * Delete current user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy() {
        $user = auth()->user();

        if($user->delete()) {
            $status = 200;
            $message = 'Success';
        } else {
            $status = 500;
            $message = 'Failed';
        }

        return response()->json([
            'status' => $message
        ], $status);
    }
}
