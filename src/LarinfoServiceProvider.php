<?php

namespace Matriphe\Larinfo;

use DavidePastore\Ipinfo\Ipinfo;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Matriphe\Larinfo\Commands\LarinfoCommand;
use Matriphe\Larinfo\Converters\StorageSizeConverter;
use Matriphe\Larinfo\Entities\IpAddressChecker;
use Matriphe\Larinfo\Wrapper\WrapperFactory;

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

        if ($this->app->runningInConsole()) {
            $this->commands([LarinfoCommand::class]);
        }
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
            $linfoConfig = config('larinfo.linfo', []);
            $dbConfig = config('database.connections.'.config('database.default'));

            return new Larinfo(
                new Ipinfo($ipinfoConfig),
                Request::capture(),
                (new WrapperFactory($linfoConfig))->getWrapper(),
                $this->getDatabase($dbConfig),
                new IpAddressChecker(),
                new StorageSizeConverter(),
                config('larinfo.converter.precision', 1),
                config('larinfo.converter.use_binary', true)
            );
        });
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
