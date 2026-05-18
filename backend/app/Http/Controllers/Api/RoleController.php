<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return $this->successResponse(RoleResource::collection($roles));
    }

    public function show(Role $role): JsonResponse
    {
        return $this->successResponse(new RoleResource($role->loadCount('users')));
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());
        return $this->successResponse(new RoleResource($role), 'Role created successfully.', 201);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role->update($request->validated());
        return $this->successResponse(new RoleResource($role->fresh()), 'Role updated successfully.');
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();
        return $this->successResponse(null, 'Role deleted successfully.');
    }
}
