<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShoesPhoto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'photo',
        'shoes_id',
    ];
}
