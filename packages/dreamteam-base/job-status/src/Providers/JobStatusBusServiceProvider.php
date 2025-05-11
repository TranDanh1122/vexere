<?php

namespace DreamTeam\JobStatus\Providers;

use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Bus\QueueingDispatcher as QueueingDispatcherContract;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use Illuminate\Support\ServiceProvider;
use DreamTeam\JobStatus\JobStatusUpdater;
use DreamTeam\JobStatus\Dispatcher;
use DreamTeam\JobStatus\Services\Interfaces\JobStatusServiceInterface;
use DreamTeam\JobStatus\Services\JobStatusService;
use DreamTeam\JobStatus\Repositories\Eloquent\JobStatusRepository;

class JobStatusBusServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(Dispatcher::class, function ($app) {
            return new Dispatcher($app, function ($connection = null) use ($app) {
                return $app[QueueFactoryContract::class]->connection($connection);
            }, app(JobStatusUpdater::class));
        });
        $this->app->alias(
            Dispatcher::class,
            DispatcherContract::class
        );
        $this->app->alias(
            Dispatcher::class,
            QueueingDispatcherContract::class
        );

        $this->app->bind(JobStatusServiceInterface::class, function($app){
            return new JobStatusService(
                new JobStatusRepository()
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Dispatcher::class,
            DispatcherContract::class,
            QueueingDispatcherContract::class,
        ];
    }
}
