<?php

namespace App\Providers;

use App\Billing\PaymentGatewayInterface;
use App\Billing\StripePaymentGateway;
use App\OrderConfirmationNumberGeneratorInterface;
use App\RandomOrderConfirmationNumberGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(StripePaymentGateway::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        $this->app->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);

        $this->app->bind(OrderConfirmationNumberGeneratorInterface::class, RandomOrderConfirmationNumberGenerator::class);
    }

    public function boot()
    {
        //
    }
}
