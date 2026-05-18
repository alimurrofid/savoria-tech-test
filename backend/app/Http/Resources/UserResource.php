<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'is_admin'      => $this->is_admin,
            'department_id' => $this->department_id,
            'role_id'       => $this->role_id,
            'department'    => new DepartmentResource($this->whenLoaded('department')),
            'role'          => new RoleResource($this->whenLoaded('role')),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
