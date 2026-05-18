<?php

namespace App\Models;

use App\Models\Application;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name'])]
class Role extends Model
{
    use SoftDeletes;
    /**
     * Applications assigned to this role.
     */
    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_role');
    }

    /**
     * Users belonging to this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
