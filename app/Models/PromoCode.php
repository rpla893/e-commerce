<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasCustomId;

class PromoCode extends Model
{
    use HasFactory, SoftDeletes, HasCustomId;

    protected $table = 'promo_codes'; // Menentukan nama tabel secara eksplisit

    protected $fillable = [
        'code',
        'discount_amount',
        'custom_id',
        'expiry_date',
    ];

    protected static function boot()
    {
        parent::boot();

        // Otomatis mengisi slug saat nama berubah
        static::saving(function ($code) {
            $code->custom_id = now()->format('dmY') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Relasi ke transaksi yang menggunakan kode promo ini
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(ProductTransaction::class, 'promo_code_id');
    }

    /**
     * Mutator untuk memastikan kode promo selalu tersimpan dalam format UPPERCASE
     */
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    public function isValid()
    {
        return !$this->expiry_date || $this->expiry_date >= now();
    }

}
