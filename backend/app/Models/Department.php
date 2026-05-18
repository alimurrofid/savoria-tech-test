<?php

namespace App\Models;

use App\Models\Application;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Department extends Model
{
    /**
     * Applications assigned to this department.
     */
    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_department');
    }

    /**
     * Users belonging to this department.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
