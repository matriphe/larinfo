<?php

namespace Matriphe\Larinfo;

use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Support\ServiceProvider;
use Linfo\Linfo;
use Symfony\Component\HttpFoundation\Request;

class LarinfoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     */
    public function register()
    {
        $this->app->singleton('larinfo', function ($app) {
            $larinfo = new Larinfo(new Ipinfo(), new Request(), new Linfo());

            $token = config('services.ipinfo.token');

            if (! empty($token)) {
                return $larinfo->setIpinfoConfig($token);
            }

            return $larinfo;
        });
    }
}
