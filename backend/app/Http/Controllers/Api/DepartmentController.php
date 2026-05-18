<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $departments = Department::withCount('users')->orderBy('name')->get();
        return $this->successResponse($departments);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = Department::create($request->validated());
        return $this->successResponse($department, 'Department created successfully.', 201);
    }

    public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        $department->update($request->validated());
        return $this->successResponse($department->fresh(), 'Department updated successfully.');
    }

    public function destroy(Department $department): JsonResponse
    {
        $department->delete();
        return $this->successResponse(null, 'Department deleted successfully.');
    }
}
