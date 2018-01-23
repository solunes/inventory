<?php

namespace Solunes\Inventory\App\Providers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Solunes\Master\App\Providers\ComposerServiceProvider as ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{

    public function boot(ViewFactory $view)
    {
        view()->composer(['layouts.master'], function ($view) {
            if($cart = \Solunes\Inventory\App\Cart::checkOwner()->checkCart()->status('holding')->with('cart_items','cart_items.product')->first()){
                $array['cart_items'] = $cart->cart_items;
            } else {
                $array['cart_items'] = [];
            }
            $view->with($array);
        });
        parent::boot($view);
    }

    public function register()
    {
        //
    }

}