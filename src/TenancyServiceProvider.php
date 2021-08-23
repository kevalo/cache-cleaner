<?php

namespace Placetopay\Cerberus;

use Illuminate\Support\Facades\Event;
use Placetopay\Cerberus\Commands\TenantsArtisanCommand;
use Placetopay\Cerberus\Commands\TenantsListCommand;
use Placetopay\Cerberus\Commands\TenantsSkeletonStorageCommand;
use Placetopay\Cerberus\Listeners\SetLoggerContext;
use Spatie\Multitenancy\Events\MadeTenantCurrentEvent;
use Spatie\Multitenancy\MultitenancyServiceProvider;

class TenancyServiceProvider extends MultitenancyServiceProvider
{
    public function boot()
    {
        parent::boot();

        Event::listen(
            MadeTenantCurrentEvent::class,
            [SetLoggerContext::class, 'handle']
        );
    }

    protected function bootCommands(): self
    {
        $this->commands([
            TenantsArtisanCommand::class,
            TenantsListCommand::class,
            TenantsSkeletonStorageCommand::class,
        ]);

        return $this;
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/multitenancy.php', 'multitenancy');
    }

    protected function registerPublishables(): self
    {
        $this->publishes([
            __DIR__ . '/../config/multitenancy.php' => config_path('multitenancy.php'),
        ], 'config');

        if (!class_exists('CreateLandlordTenantsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/landlord/create_landlord_tenants_table.php.stub' => database_path('migrations/landlord/' . date('Y_m_d_His', time()) . '_create_landlord_tenants_table.php'),
                __DIR__ . '/../database/migrations/landlord/create_landlord_jobs_table.php.stub' => database_path('migrations/landlord/' . date('Y_m_d_His', time()) . '_create_landlord_jobs_table.php'),
                __DIR__ . '/../database/migrations/landlord/create_landlord_failed_jobs_table.php.stub' => database_path('migrations/landlord/' . date('Y_m_d_His', time()) . '_create_landlord_failed_jobs_table.php'),
            ], 'migrations');
        }

        return $this;
    }
}
