<?php

namespace Solunes\Inventory\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Asset;

class ReportController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
    $this->middleware('auth');
    $this->middleware('permission:dashboard');
	  $this->prev = $url->previous();
	}

  public function getSalesReport() {
    $model = \Solunes\Inventory\App\Sale::where('status','!=','holding');
    $array = \Inventory::check_report_header($model);
    $array['show_place'] = true;

    $codes_array = ['income_sale', 'income_sale_credit', 'expense_refund'];
    $account_array = \Solunes\Inventory\App\Account::whereIn('code', $codes_array)->lists('id')->toArray();
    $accounts = \Solunes\Inventory\App\PlaceAccountability::whereIn('account_id', $account_array)->where('created_at', '>=', $array['i_date'])->where('created_at', '<=', $array['e_date']);
    if($array['place']!='all'){
      $accounts = $accounts->where('parent_id', $array['place']);
    }
    $accounts = $accounts->get();
    $inventory = 0;
    $cash = 0;
    $pos = 0;
    $web = 0;
    $online = 0;
    $pending_total = 0;
    $sales_total = 0;
    $refund_total = 0;
    $currency = \Solunes\Inventory\App\Currency::find(1);
    foreach($accounts as $item){
      $new_total = \Inventory::calculate_currency($item->amount, $array['currency'], $item->currency);
      if($item->account->code=='expense_refund'){
        $refund_total -= $new_total;
      } else if($item->account->code=='income_sale_credit') {
        $pending_total += $new_total;
      } else {
        $inventory += $new_total;
        $sales_total += $new_total;
        foreach($item->other_accounts as $other){
          $other_amount = \Inventory::calculate_currency($other->real_amount, $array['currency'], $currency);
          if($other->account->concept->code=='asset_cash'){
            $cash += $other_amount;
          } else if($other->account->concept->code=='asset_bank'){
            $pos += $other_amount;
          }
        }
        /*if($item->type=='normal'){
          $inventory += $paid;
          if($item->pos_bob>0){
            $new_total -= $item->pos_bob;
            $paid -= $item->pos_bob;
            $pos += $item->pos_bob;
          } 
          $cash += $paid;
        } else if($item->type=='web'){
          $web += $paid;
        } else if($item->type=='online'){
          $online += $paid;
        }*/
      }
    }
    $array = $array + ['total'=>$sales_total, 'inventory'=>$inventory, 'cash'=>$cash,'pos'=>$pos, 'web'=>$web, 'online'=>$online, 'pending'=>$pending_total, 'refund_total'=>$refund_total];
    // Gráficos
    $type_items = [['type'=>'paid','total'=>round($inventory)], ['type'=>'web','total'=>round($web)], ['type'=>'online','total'=>round($online)], ['type'=>'pending','total'=>round($pending_total)]];
    $type_items = json_decode(json_encode($type_items));
    $type_field_names = ['paid'=>'Ventas en Tienda '.$array['currency']->name, 'web'=>'Ventas Web '.$array['currency']->name, 'online'=>'Ventas Online '.$array['currency']->name, 'pending'=>'Ventas no Cobradas '.$array['currency']->name];
    $array['graphs']['type'] = ['type'=>'pie', 'graph_name'=>'type', 'name'=>'type', 'label'=>'Tipo de Ventas', 'items'=>$type_items, 'subitems'=>[], 'field_names'=>$type_field_names];
    return \Inventory::check_report_view('inventory::list.sales-report', $array);
  }

  public function getStockReport() {
    $agencies = \Solunes\Business\App\Agency::where('id','>=',1);
    $products = \Solunes\Business\App\ProductBridge::where('stockable',1);
    $user = auth()->user();
    $agencies_list = \Solunes\Business\App\Agency::where('id','>=',1);
    $agency = $user->agency;
    if(!$agency){
      $agency = \Solunes\Business\App\Agency::first();
    }
    if($agency->type=='office'){
      $subagencies = \Solunes\Business\App\Agency::whereIn('type', ['office','store'])->where('region_id', $agency->region_id)->lists('id')->toArray();
      $agencies = $agencies->whereIn('id', $subagencies);
      $agencies_list = $agencies_list->whereIn('id', $subagencies);
    } else if($agency->type=='store'){
      $agencies = $agencies->where('id', $agency->id);
      $agencies_list = $agencies_list->where('id', $agency->id);
    }
    $agencies_list = $agencies_list->get()->lists('name','id')->toArray();
    if(request()->has('filter')){
      if(request()->has('agencies')){
        $agencies = $agencies->whereIn('id', request()->input('agencies')); 
      }
      if(config('solunes.product')&&request()->has('categories')){
        $product_category_ids = \Solunes\Product\App\Product::whereIn('category_id', request()->input('categories'))->lists('id')->toArray();
        $products = $products->whereIn('product_id', $product_category_ids);  
      }
      if(request()->has('variation_options')){
        $products = $products->whereIn('variation_option_id', request()->input('variation_options')); 
      }
    }
    $agencies = $agencies->get();
    $products = $products->get();
    if(config('solunes.product')){
      $categories_list = \Solunes\Business\App\Category::get()->lists('name','id')->toArray();
    }
    $variation_list = \Solunes\Business\App\Variation::where('stockable',1)->lists('id')->toArray();
    $variation_options_list = \Solunes\Business\App\VariationOption::whereIn('parent_id', $variation_list)->get()->lists('name','id')->toArray();
    $stock = [];
    $graph_items = [];
    foreach($agencies as $agency){
      foreach($products as $product_bridge){
        $product_stock = $product_bridge->product_bridge_stocks()->where('agency_id', $agency->id)->first();
        if($product_stock){
          $stock[$agency->id.'-'.$product_bridge->id] = $product_stock->quantity;
        } else {
          $stock[$agency->id.'-'.$product_bridge->id] = 0;
        }
        if(count($graph_items)<20){
          $graph_items[$agency->name.' - '.$product_bridge->name] = $stock[$agency->id.'-'.$product_bridge->id];
        }
      }
    }
    $array['agencies_list'] = $agencies_list;
    if(config('solunes.product')){
      $array['categories_list'] = $categories_list;
    }
    $array['variation_options_list'] = $variation_options_list;
    $array['agencies'] = $agencies;
    $array['products'] = $products;
    $array['stock'] = $stock;
    $array['graph_items'] = $graph_items;
    return view('inventory::content.stock-report', $array);
  }

}