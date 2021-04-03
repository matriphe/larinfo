<?php

namespace Matriphe\Larinfo;

use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Linfo\Exceptions\FatalException;
use Linfo\Linfo;
use Matriphe\Larinfo\Entities\IpAddressChecker;
use Matriphe\Larinfo\Windows\WindowsNoComNet;
use Matriphe\Larinfo\Wrapper\LinfoWrapper;
use Matriphe\Larinfo\Wrapper\WindowsWrapper;

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
            $ipinfoConfig = ['token' => config('services.ipinfo.token')];
            $linfoConfig = config('larinfo.linfo');
            $dbConfig = config('database.connections.'.config('database.default'));

            return new Larinfo(
                new Ipinfo($ipinfoConfig),
                Request::capture(),
                $this->linfoWrapperFactory($linfoConfig),
                $this->getDatabase($dbConfig),
                new IpAddressChecker()
            );
        });
    }

    /**
     * @param  array        $linfoConfig
     * @return LinfoWrapper
     */
    private function linfoWrapperFactory(array $linfoConfig): LinfoWrapper
    {
        try {
            $linfo = new Linfo($linfoConfig);

            return new LinfoWrapper($linfo);
        } catch (FatalException $exception) {
            $windows = new WindowsNoComNet();

            return new WindowsWrapper($windows);
        }
    }

    /**
     * @param  array   $dbConfig
     * @return Manager
     */
    private function getDatabase(array $dbConfig): Manager
    {
        $database = new Manager();
        $database->addConnection($dbConfig);

        return $database;
    }
}
