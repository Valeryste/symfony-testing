<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'route',
        'role_id'
    ];

    public $timestamps = false;

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
