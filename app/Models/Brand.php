<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasCustomId;

class Brand extends Model
{
    use HasFactory, SoftDeletes, HasCustomId;

    protected $table = 'brands'; // Menentukan tabel secara eksplisit

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'custom_id'
    ];

    protected static function boot()
    {
        parent::boot();

        // Otomatis mengisi slug saat nama berubah
        static::saving(function ($brand) {
            $brand->slug = Str::slug($brand->name);
            $brand->custom_id = now()->format('dmY') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        });
    }

    public function shoes(): HasMany
    {
        return $this->hasMany(Shoes::class);
    }
}
