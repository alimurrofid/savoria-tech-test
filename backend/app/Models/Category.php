<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description'])]
class Category extends Model
{
    use SoftDeletes;

    /**
     * Applications belonging to this category.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
