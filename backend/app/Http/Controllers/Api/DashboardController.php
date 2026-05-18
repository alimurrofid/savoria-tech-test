<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * Return all applications accessible by the authenticated user,
     * sourced from the view_user_application_access PostgreSQL view.
     */
    public function index()
    {
        $userId = Auth::id();

        $applications = DB::table('view_user_application_access')
            ->where('user_id', $userId)
            ->get(['application_id', 'name', 'url', 'icon', 'category']);

        return $this->successResponse($applications);
    }
}
