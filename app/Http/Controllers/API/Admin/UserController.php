<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

    /*
        Get Method
        GET /api/admin/users
    */
    public function index()
    {
        $users = User::latest()->paginate(10);
        if ($users)
        {
            return response()->json($users, 200);
        }
        return response()->json([
            'message' => 'No users found'
        ], 404);
    }

    /**
     * GET /api/admin/users/{id}
     */
    public function show($id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return response()->json([
                "msg"=>"User not Found"
            ], 404);
        }
        return response()->json($user,200);
    }
    
    /**
     * PUT /api/admin/users/{id}
     */
    public function update(Request $request,$id)
    {
        $user = User::find($id);
        $validate = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'required|string',
            'role' => 'sometimes|string|in:user,admin',
        ]);
        $user->update($validate);
        return response()->json(["msg"=>"User updates Success"],200);
    }

    /**
     * DELETE /api/admin/users/{id}
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return response()->json([
                "msg"=>"User not Found"
            ], 404);
        }
        $user->delete();
        return response()->json(["msg"=>"User deleted Success"],200);
    }


    // post to change pass
    /**
     * POST /api/admin/users/{id}/updatePassword
     */
    public function updatePassword(Request $request, $id)
    {
        $user = User::find($id);
        if(!$user)
        {
            return response()->json([
                "msg"=>"User not Found"
            ], 404);
        }
        $validate = $request->validate([
            'password' => 'required|string|min:6',
        ]);
        $user->update($validate);
        return response()->json(["msg"=>"User password updated Success"],200);
    }

    public function create(Request $request)
    {
        // check if user is already found
        $user = User::where('email', $request->email)->first();
        if($user)
        {
            return response()->json([
                "msg"=>"User already exists"
            ], 404);
        }
        $validate = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|string|in:user,admin',
        ]);
        $user = User::create($validate);
        return response()->json(["msg"=>"User created Success"],200);
    }
}
