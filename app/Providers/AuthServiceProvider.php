<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'Medicare\Model' => 'Medicare\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::personalAccessTokensExpireIn(Carbon::now()->addDays(1));
        Passport::$ignoreCsrfToken = true;

        // Mandatory to define Scope
        Passport::tokensCan([
            'admin'             =>  'Admin',
            'editor'            =>  'Editor',
            'driver'            =>  'Driver',
            'driver_manager'    =>  'Driver Manager',
            'customer'          =>  'Customer'
        ]);
    }
}
