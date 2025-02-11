<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasCustomId
{
    protected static function bootHasCustomId()
    {
        static::creating(function ($model) {
            $model->custom_id = self::generateCustomId($model);
        });
    }

    protected static function generateCustomId($model)
    {
        $date = now()->format('dmY'); // Format: 07022025
        $lastId = DB::table($model->getTable())
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;

        return $date . str_pad($lastId, 4, '0', STR_PAD_LEFT);
    }
}
