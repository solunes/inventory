<?php

namespace Solunes\Store\App\Listeners;

class SavePurchase {

    public function handle($event) {
    	// Revisar que tenga una sesión y sea un modelo del sitio web.
    	if($event){
            if(!$event->user_id){
                $event->user_id = auth()->user()->id;
            }
            if($event->status=='paid'){
                $currency_id = $event->currency_id;
                $account = \Solunes\Store\App\Account::where('place_id', $event->place_id)->first();
                $account_detail = new \Solunes\Store\App\AccountDetail;
                $account_detail->parent_id = $account->id;
                $account_detail->type = 'debit';
                $account_detail->concept_id = 3;
                $account_detail->name = 'Compra de mercancía';
                $account_detail->currency_id = $currency_id;
                $account_detail->amount = $event->total;
                $account_detail->save();
            }
            return $event;
    	}

    }

}
