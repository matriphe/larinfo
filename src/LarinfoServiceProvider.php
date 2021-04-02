<?php

namespace Matriphe\Larinfo;

use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\ServiceProvider;
use Linfo\Linfo;
use Symfony\Component\HttpFoundation\Request;

class LarinfoServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     */
    public function register()
    {
    	$this->app->singleton(LarinfoContract::class, function () {
            $ipinfo = new Ipinfo([
            	'token' => config('services.ipinfo.token')
            ]);

            $dbConfig = config('database.connections.'.config('database.default'));
            $database = new Manager();
            $database->addConnection($dbConfig);

    		return new Larinfo($ipinfo, new Request(), new Linfo(), $database);
        });
    }
}
