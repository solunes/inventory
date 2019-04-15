<?php

namespace Solunes\Inventory\App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Solunes\Master\App\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        //
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        
        // Módulo de Proyectos
        $events->listen('eloquent.creating: Solunes\Inventory\App\InventoryMovement', '\Solunes\Inventory\App\Listeners\RegisteringInventoryMovement');
        $events->listen('eloquent.created: Solunes\Inventory\App\StockAddition', '\Solunes\Inventory\App\Listeners\StockAdditionCreated');
        $events->listen('eloquent.created: Solunes\Inventory\App\StockTransfer', '\Solunes\Inventory\App\Listeners\StockTransferCreated');
        $events->listen('eloquent.created: Solunes\Inventory\App\StockRemoval', '\Solunes\Inventory\App\Listeners\StockRemovalCreated');

        parent::boot($events);
    }
}
