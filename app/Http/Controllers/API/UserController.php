<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => UserResource::collection(
                User::with(['stores.currentSubscription.plan'])->get()
            )
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:'.User::class],
            'password' => ['required', 'string', Password::defaults()],
            'role' => ['required', 'string', 'in:super_admin,owner,cashier']
        ]);

        $user = User::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['stores.currentSubscription.plan']);

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['nullable', 'string', Password::defaults()],
            'role' => ['required', 'string', 'in:super_admin,owner,cashier'],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => new UserResource($user)
        ]);
    }
}
