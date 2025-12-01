<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define Passport token scopes (no routesAreCached check)
        Passport::tokensCan([
            'student-only'   => 'Access read-only student resources.',
            'faculty-access' => 'Access faculty-only resources (e.g., check registrations, manage papers).',
        ]);

        // Add more Passport configurations if required:
        // Passport::setDefaultScope(['student-only']);
    }
}
