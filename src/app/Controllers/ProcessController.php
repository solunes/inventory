<?php

namespace Solunes\Inventory\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use Validator;
use Asset;
use AdminList;
use AdminItem;
use PDF;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProcessController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
	  $this->prev = $url->previous();
	}

  public function getCalculateShipping($shipping_id, $city_id, $weight) {
    $shipping_array = \Inventory::calculate_shipping_cost($shipping_id, $city_id, $weight);
    return $shipping_array;
  }

}