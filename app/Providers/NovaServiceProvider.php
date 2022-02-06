<?php

namespace App\Providers;

use App\Nova\Metrics\AccountantUsers;
use App\Nova\Metrics\CEOUsers;
use App\Nova\Metrics\ClientUsers;
use App\Nova\Metrics\Complaints;
use App\Nova\Metrics\Notices;
use App\Nova\Metrics\PMUsers;
use App\Nova\Metrics\Requests;
use App\Nova\Metrics\SPMUsers;
use App\Nova\Metrics\TenantUsers;
use App\Policies\PermissionPolicy;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Vyuldashev\NovaPermission\RolePolicy;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
                ->withAuthenticationRoutes()
                ->withPasswordResetRoutes()
                ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return $user->hasRole('administrator');
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new ClientUsers,
            new TenantUsers,
            new AccountantUsers,
            new PMUsers,
            new SPMUsers,
            new CEOUsers,
            new Complaints,
            new Notices,
            new Requests,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            \Vyuldashev\NovaPermission\NovaPermissionTool::make()
                ->rolePolicy(RolePolicy::class)
                ->permissionPolicy(PermissionPolicy::class),
            new \Czemu\NovaCalendarTool\NovaCalendarTool,
            new \Spatie\BackupTool\BackupTool(),
            new \KABBOUCHI\LogsTool\LogsTool(),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
