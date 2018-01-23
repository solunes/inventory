<?php

namespace Solunes\Inventory;

use Illuminate\Support\ServiceProvider;

class InventoryServiceProvider extends ServiceProvider {

    protected $defer = false;

    public function boot() {
        /* Publicar Elementos */
        $this->publishes([
            __DIR__ . '/config' => config_path()
        ], 'config');
        $this->publishes([
            __DIR__.'/assets' => public_path('assets/inventory'),
        ], 'assets');

        /* Cargar Traducciones */
        $this->loadTranslationsFrom(__DIR__.'/lang', 'inventory');

        /* Cargar Vistas */
        $this->loadViewsFrom(__DIR__ . '/views', 'inventory');
    }


    public function register() {
        /* Registrar ServiceProvider Internos */

        /* Registrar Alias */
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();

        $loader->alias('Inventory', '\Solunes\Inventory\App\Helpers\Inventory');
        $loader->alias('CustomInventory', '\Solunes\Inventory\App\Helpers\CustomInventory');

        /* Comandos de Consola */
        $this->commands([
            //\Solunes\Inventory\App\Console\AccountCheck::class,
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/config/inventory.php', 'inventory'
        );
    }
    
}
