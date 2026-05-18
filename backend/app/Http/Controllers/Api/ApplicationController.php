<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\UpdateApplicationRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Traits\ApiResponse;

class ApplicationController extends Controller
{
    use ApiResponse;

    /**
     * Display a paginated listing of all applications.
     * Supports an optional ?search= query parameter for filtering by name or category.
     */
    public function index()
    {
        $search = request()->query('search');

        $applications = Application::with('category')
            ->when($search, fn ($q) => $q
                ->where('name', 'ilike', "%{$search}%")
                ->orWhereHas('category', fn ($q2) => $q2->where('name', 'ilike', "%{$search}%"))
            )
            ->latest()
            ->paginate(10);

        return $this->paginatedResponse(ApplicationResource::collection($applications));
    }

    /**
     * Store a newly created application.
     */
    public function store(StoreApplicationRequest $request)
    {
        $application = Application::create($request->validated());

        return $this->successResponse(
            new ApplicationResource($application),
            'Application created successfully.',
            201
        );
    }

    /**
     * Display the specified application.
     */
    public function show(Application $application)
    {
        return $this->successResponse(new ApplicationResource($application->load('category')));
    }

    /**
     * Update the specified application.
     */
    public function update(UpdateApplicationRequest $request, Application $application)
    {
        $application->update($request->validated());

        return $this->successResponse(
            new ApplicationResource($application->fresh()),
            'Application updated successfully.'
        );
    }

    /**
     * Remove the specified application.
     */
    public function destroy(Application $application)
    {
        $application->delete();

        return $this->successResponse(null, 'Application deleted successfully.');
    }
}
