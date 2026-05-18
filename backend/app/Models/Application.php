<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'url', 'icon', 'category', 'description'])]
class Application extends Model
{
    /**
     * Departments that have access to this application.
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'application_department');
    }

    /**
     * Roles that have access to this application.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'application_role');
    }

    /**
     * Users that have direct access to this application.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'application_user');
    }
}
