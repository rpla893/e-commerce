<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ProductTransaction;
use App\Models\Shoes;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Menghitung total pemasukan dengan menggunakan scope atau langsung dari model
        $pemasukan = ProductTransaction::grandTotalAmount(); // Atau menggunakan sum('grand_total_amount')
        $stok = Shoes::stock();

        return [
            Stat::make('Pemasukan', $pemasukan),
            Stat::make('Stok', $stok), // Ini perlu disesuaikan jika Anda ingin menampilkan stok produk
        ];
    }
}
