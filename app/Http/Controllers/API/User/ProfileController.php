<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json($user,200);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'email'         => 'sometimes|email|unique:users,email,' . $user->id,
            'profile_image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $validated['profile_image'] = $path;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user'    => $user
        ],200);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        // validate input
        $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        //  check old password
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'message' => 'Old password is incorrect',
            ], 400);
        }

        // 3 update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password changed successfully',
        ], 200);
    }
}