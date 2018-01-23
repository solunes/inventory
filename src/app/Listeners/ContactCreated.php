<?php

namespace Solunes\Inventory\App\Listeners;

class ContactCreated {

    public function handle($event) {
    	// Revisar que no esté de manera externa
    	if($event&&!$event->external_code){
            $event = \Solunes\Inventory\App\Controllers\Integrations\HubspotController::exportContactCreated($event);
            return $event;
    	}
    }

}
