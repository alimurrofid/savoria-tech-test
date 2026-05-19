<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAccessRuleRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AccessRuleController extends Controller
{
    use ApiResponse;

    /**
     * Valid entity types and their corresponding model + relationship.
     */
    private const ENTITY_MAP = [
        'department' => [Department::class, 'applications'],
        'role'       => [Role::class, 'applications'],
        'user'       => [User::class, 'applications'],
    ];

    /**
     * Fetch the assigned applications for a given entity (department, role, or user).
     *
     * GET /api/access-rules/{type}/{id}
     */
    public function show(string $type, int $id): JsonResponse
    {
        $entity = $this->resolveEntity($type, $id);

        $assigned = $entity->applications()->get();

        return $this->successResponse([
            'entity_type'          => $type,
            'entity_id'            => $id,
            'assigned_applications' => ApplicationResource::collection($assigned),
            'all_applications'     => ApplicationResource::collection(Application::all()),
        ]);
    }

    /**
     * Sync (assign/revoke) applications for a given entity using the `sync()` method.
     * The request body must contain `application_ids` — an array of application IDs to assign.
     * Any IDs not in this array will be detached (revoked).
     *
     * PUT /api/access-rules/{type}/{id}
     */
    public function update(UpdateAccessRuleRequest $request, string $type, int $id): JsonResponse
    {
        $entity = $this->resolveEntity($type, $id);
        $validatedData = $request->validated();

        $entity->applications()->sync($validatedData['application_ids']);

        $updated = $entity->applications()->get();

        return $this->successResponse(
            ApplicationResource::collection($updated),
            "Access rights for {$type} #{$id} updated successfully."
        );
    }

    /**
     * Resolve the target model instance from the entity type string.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    private function resolveEntity(string $type, int $id): Department|Role|User
    {
        if (! array_key_exists($type, self::ENTITY_MAP)) {
            abort(422, "Invalid entity type '{$type}'. Valid types: department, role, user.");
        }

        [$modelClass] = self::ENTITY_MAP[$type];

        return $modelClass::findOrFail($id);
    }
}
