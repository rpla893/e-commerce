<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCustomId;

class Shoes extends Model
{
    use HasFactory, SoftDeletes, HasCustomId;

    protected $table = 'shoes';

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'about',
        'price',
        'diskon',
        'stock',
        'diskon',
        'barcode',
        'is_expense',
        'is_popular',
        'category_id',
        'brand_id',
        'custom_id',
        'status',
    ];

    /**
     * Ensure name is Title Case and generate slug only if empty
     */

     protected static function boot()
     {
         parent::boot();

         // Otomatis mengisi slug saat nama berubah
         static::saving(function ($brand) {
             $brand->slug = Str::slug($brand->name);
             $brand->custom_id = now()->format('dmY') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
         });
     }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);

        if (!$this->exists || empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Relasi ke Brand
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Relasi ke Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Relasi ke Photos
     */
    public function photos(): HasMany
    {
        return $this->hasMany(ShoesPhoto::class);
    }

    /**
     * Relasi ke Sizes
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(ShoesSize::class);
    }

    /**
     * Relasi ke ProductTransaction
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(ProductTransaction::class, 'shoes_id');
    }

    // Di dalam model Shoes

    public static function stock()
    {
        // Mengambil jumlah total stok dari semua sepatu
        return self::sum('stock'); // Menjumlahkan seluruh nilai kolom 'stock' di tabel shoes
    }

}
