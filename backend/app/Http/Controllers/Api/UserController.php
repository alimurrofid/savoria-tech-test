<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $users = User::with(['department:id,name', 'role:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'is_admin', 'department_id', 'role_id', 'created_at']);

        return $this->successResponse(UserResource::collection($users));
    }

    public function show(User $user): JsonResponse
    {
        return $this->successResponse(new UserResource($user->load(['department:id,name', 'role:id,name'])));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return $this->successResponse(
            new UserResource($user->load(['department:id,name', 'role:id,name'])),
            'User created successfully.',
            201
        );
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $this->successResponse(
            new UserResource($user->fresh()->load(['department:id,name', 'role:id,name'])),
            'User updated successfully.'
        );
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return $this->successResponse(null, 'User deleted successfully.');
    }
}
