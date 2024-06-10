<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agency extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'nom',
        'adresse',
        'phone',
        'ville',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function nom(): Attribute
    {
        return new Attribute(
            get: fn ($value) => strtoupper($value),
        );
    }
}
