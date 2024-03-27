<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Faker\FakeTaskProvider;
use Faker\{Factory, Generator};

class FakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Generator::class, function () {
            $faker = Factory::create();
            $faker->addProvider(new FakeTaskProvider($faker));
            return $faker;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
