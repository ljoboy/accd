<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesUniqueCode
{
    protected static function bootGeneratesUniqueCode(): void
    {
        static::creating(function ($model) {
            $model->qr_code = self::generateUniqueCode();
        });
    }

    protected static function generateUniqueCode(): string
    {
        return Str::random(40).uniqid();
    }
}
