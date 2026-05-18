<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportController extends Controller
{
    use ApiResponse;

    /**
     * Get user application access report paginated.
     */
    public function userAccess(): JsonResponse
    {
        $search = request()->query('search');

        $query = User::query()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                DB::raw("
                    COALESCE(
                        json_agg(
                            json_build_object(
                                'application_id', v.application_id,
                                'name', v.name,
                                'url', v.url,
                                'icon', v.icon,
                                'category', v.category
                            )
                        ) FILTER (WHERE v.application_id IS NOT NULL),
                        '[]'
                    ) AS applications
                ")
            ])
            ->leftJoin('view_user_application_access as v', 'users.id', '=', 'v.user_id')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('users.name', 'ilike', "%{$search}%")
                        ->orWhere('users.email', 'ilike', "%{$search}%");
                });
            })
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('users.name')
            ->orderBy('users.id');

        $users = $query->paginate(10);

        $users->getCollection()->transform(function ($user) {
            $user->applications = is_string($user->applications) 
                ? json_decode($user->applications, true) 
                : $user->applications;
            
            return $user;
        });

        return $this->paginatedResponse(JsonResource::collection($users));
    }
}