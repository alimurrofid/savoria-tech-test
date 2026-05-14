<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Traits\ApiResponse;
use App\Models\User;
use App\Models\Product;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->successResponse([
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'active_projects' => 42,
            'revenue' => 12000,
        ]);
    }
}
