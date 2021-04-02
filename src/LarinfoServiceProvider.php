<?php

namespace Matriphe\Larinfo;

use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Linfo\Linfo;
use Matriphe\Larinfo\Entities\IpAddressChecker;

class LarinfoServiceProvider extends ServiceProvider
{
    private const CONFIG_FILEPATH = '/../config/larinfo.php';

    /**
     * Bootstrap any package services.
     *
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.self::CONFIG_FILEPATH => config_path('larinfo.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.self::CONFIG_FILEPATH, 'larinfo'
        );

        $this->app->singleton(LarinfoContract::class, function () {
            $ipinfo = new Ipinfo([
                'token' => config('services.ipinfo.token'),
            ]);

            $request = Request::capture();

            $linfo = new Linfo(config('larinfo.linfo'));

            $dbConfig = config('database.connections.'.config('database.default'));
            $database = new Manager();
            $database->addConnection($dbConfig);

            $ipAddressChecker = new IpAddressChecker();

            return new Larinfo($ipinfo, $request, $linfo, $database, $ipAddressChecker);
        });
    }
}
