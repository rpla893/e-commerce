<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\CategoryRepositoryInterface;
use Illuminate\Support\ShoesRepositoryInterface;
use Illuminate\Support\OrderRepositoryInterface;
use Illuminate\Support\PomoCodeRepositoryInterface;
use Illuminate\Support\CategoryRepository;
use Illuminate\Support\ShoesRepository;
use Illuminate\Support\OrderRepository;
use Illuminate\Support\PromoCodeRepository;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CategoryRepositoryInterface::class, CategoryRepository::class);

        $this->app->singleton(ShoesRepositoryInterface::class, ShoesRepository::class);

        $this->app->singleton(OrderRepositoryInterface::class, OrderRepository::class);

        $this->app->singleton(PromoCodeRepositoryInterface::class, PromoCodeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
