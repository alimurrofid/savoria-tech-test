<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a paginated listing of all categories.
     * Supports an optional ?search= query parameter for filtering by name.
     */
    public function index()
    {
        $search = request()->query('search');
        $limit = request()->query('limit');

        $query = Category::query()
            ->when($search, fn ($q) => $q->where('name', 'ilike', "%{$search}%"))
            ->latest();

        if ($limit === 'all') {
            return $this->successResponse(CategoryResource::collection($query->get()));
        }

        $categories = $query->paginate(10);

        return $this->paginatedResponse(CategoryResource::collection($categories));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return $this->successResponse(
            new CategoryResource($category),
            'Category created successfully.',
            201
        );
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        return $this->successResponse(new CategoryResource($category));
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return $this->successResponse(
            new CategoryResource($category->fresh()),
            'Category updated successfully.'
        );
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->successResponse(null, 'Category deleted successfully.');
    }
}
