<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Sellers\SellerRepository;
use App\Infrastructure\Repositories\SellerEloquentRepository;
use App\Domain\Sales\SaleRepository;
use App\Infrastructure\Repositories\SaleEloquentRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SaleRepository::class, SaleEloquentRepository::class);
        $this->app->bind(SellerRepository::class, SellerEloquentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
