<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class usersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        $users = app('db')->table('users')->get();
        return response()->json($users);
    }

    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                'username' => 'required|min:3',
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
        
        try {
            $id = app('db')->table('users')->insertGetId([
                'username' => strtolower(trim($request->input('username'))),
                'email'    => strtolower(trim($request->input('email'))),
                'password' => app('hash')->make($request->input('password')),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $user = app('db')->table('users')->select('username', 'email')->where('id', $id)->first();

            return response()->json([
                'id'       => $id,
                'username' => $user->username,
                'email'    => $user->email
            ], 201);
        } catch (\PDOException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    //
}
