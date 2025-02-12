<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Traits\HasCustomId;

class ProductTransaction extends Model
{
    use HasFactory, SoftDeletes, HasCustomId;

    protected $table = 'product_transactions'; // Menentukan nama tabel secara eksplisit

    protected $fillable = [
        'name',
        'slug',
        'barcode',
        'phone',
        'email',
        'booking_trx_id',
        'city',
        'post_code',
        'address',
        'quantity',
        'sub_total_amount',
        'discount_amount',
        'grand_total_amount',
        'is_paid',
        'shoes_id',
        'shoes_size',
        'promo_code_id',
        'proof',
        'custom_id',
        'is_verified',
        'payment_method',
        'payment_provider',
        'shipping_method',
    ];

    /**
     * Boot method untuk menangani event model
     */
    protected static function boot()
    {
        parent::boot();

        // Otomatis mengisi slug dan custom_id sebelum menyimpan data
        static::saving(function ($transaction) {
            $transaction->slug = Str::slug($transaction->name);
            $transaction->custom_id = now()->format('dmY') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        });

        // Event setelah transaksi dibuat (mengurangi stok)
        static::created(function ($transaction) {
            $shoes = $transaction->shoes;
            if ($shoes) {
                $shoes->decrement('stock', $transaction->quantity);
            }
        });

        // Event jika transaksi dihapus (menambah stok)
        static::deleted(function ($transaction) {
            $shoes = $transaction->shoes;
            if ($shoes) {
                $shoes->increment('stock', $transaction->quantity);
            }
        });
    }


    /**
     * Generate a unique transaction ID
     *
     * @return string
     */
    public static function generateUniqueTrxId(): string
    {
        $prefix = 'SS';
        do {
            $randomString = $prefix . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('booking_trx_id', $randomString)->exists());

        return $randomString;
    }

    /**
     * Get the associated Shoes model
     */
    public function shoes(): BelongsTo
    {
        return $this->belongsTo(Shoes::class);
    }

    /**
     * Get the associated PromoCode model
     */
    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function getVerificationStatusAttribute()
    {
        return $this->is_verified ? 'Verified' : 'Pending';
    }

    public function scopeGrandTotalAmount($query)
    {
        return $query->sum('grand_total_amount');
    }

}
