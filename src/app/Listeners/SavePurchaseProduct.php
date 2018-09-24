<?php

namespace Solunes\Store\App\Listeners;

class SavePurchaseProduct {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){
            if(!$event->investment){
                $event->investment = $event->product->product_cost * $event->quantity;
            }
            if(!$event->transport_investment){
                $event->transport_investment = $event->product->transport_cost * $event->quantity;
            }
            if($event->quantity==0&&$event->status=='holding'){
                $event->status = 'finished';
                $partner = $event->partner;
                $partner->capital = $partner->capital + $event->return + $event->profit;
                $partner->save();
                $event->load('partner_transport');
                $partner_transport = $event->partner_transport;
                $partner_transport->capital = $partner_transport->capital + $event->transport_return;
                $partner_transport->save();
            }
            return $event;
    	}

    }

}
