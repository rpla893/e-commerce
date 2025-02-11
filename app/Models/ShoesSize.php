<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShoesSize extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shoes_sizes'; // Menentukan nama tabel secara eksplisit

    protected $fillable = [
        'size',
        'shoes_id',
    ];

    protected $casts = [
        'shoes_id' => 'integer',
    ];

    /**
     * Relasi ke Shoes
     */
    public function shoes(): BelongsTo
    {
        return $this->belongsTo(Shoes::class, 'shoes_id');
    }
}
