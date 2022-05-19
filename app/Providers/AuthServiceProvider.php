<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Service\Models\Admin\AdminRolePermission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(Gate $gate)
    {
        $gate->before(function ($user, $ability) use ($gate) {
            if ($user->id == 1
                    || $ability == '/index'
                    || $ability == 'profile/password'
                    || $ability == 'profile/googlekey'
                    || $ability == 'index/dashboard') {
                return true;
            }

            $permission = AdminRolePermission::where('rule', '=', $ability)->first();

            if ($permission && !$gate->has($ability)) {
                // 对访问权限定义 Gate
                $gate->define($ability, function ($user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            }
        });

        $this->registerPolicies();
    }
}
