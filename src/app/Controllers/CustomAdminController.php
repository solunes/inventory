<?php

namespace Solunes\Inventory\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Asset;

class CustomAdminController extends Controller {

	protected $request;
	protected $url;

	public function __construct(UrlGenerator $url) {
	  $this->middleware('auth');
	  $this->middleware('permission:dashboard');
	  $this->prev = $url->previous();
	  $this->module = 'admin';
	}

	public function getIndex() {
		$user = auth()->user();
		//$array['tasks'] = $user->active_inventory_tasks;
		$array['tasks'] = \Solunes\Inventory\App\InventoryTask::limit(2)->get();
		$array['active_issues_inventorys'] = \Solunes\Inventory\App\Inventory::has('active_inventory_issues')->with('active_inventory_issues')->get();
      	return view('inventory::list.dashboard', $array);
	}

	/* Módulo de Proyectos */

	public function allInventorys() {
		$array['items'] = \Solunes\Inventory\App\Inventory::get();
      	return view('inventory::list.inventorys', $array);
	}

	public function findInventory($id, $tab = 'description') {
		if($item = \Solunes\Inventory\App\Inventory::find($id)){
			$array = ['item'=>$item, 'tab'=>$tab];
      		return view('inventory::item.inventory', $array);
		} else {
			return redirect($this->prev)->with('message_error', 'Item no encontrado');
		}
	}

	public function findInventoryTask($id) {
		if($item = \Solunes\Inventory\App\InventoryTask::find($id)){
			$array = ['item'=>$item];
      		return view('inventory::item.inventory-task', $array);
		} else {
			return redirect($this->prev)->with('message_error', 'Item no encontrado');
		}
	}

	public function findProjecIssue($id) {
		if($item = \Solunes\Inventory\App\InventoryIssue::find($id)){
			$array = ['item'=>$item];
      		return view('inventory::item.inventory-issue', $array);
		} else {
			return redirect($this->prev)->with('message_error', 'Item no encontrado');
		}
	}

	public function allWikis($inventory_type_id = NULL, $wiki_type_id = NULL) {
		$array['inventory_type_id'] = $inventory_type_id;
		$array['wiki_type_id'] = $wiki_type_id;
		if($inventory_type_id&&$wiki_type_id){
			$array['items'] = \Solunes\Inventory\App\Wiki::where('inventory_type_id',$inventory_type_id)->where('wiki_type_id',$wiki_type_id)->get();
		} else if($inventory_type_id){
			$array['items'] = \Solunes\Inventory\App\WikiType::get();
		} else {
			$array['items'] = \Solunes\Inventory\App\InventoryType::get();
		}
      	return view('inventory::list.wikis', $array);
	}

	public function findWiki($id) {
		if($item = \Solunes\Inventory\App\Wiki::find($id)){
			$array = ['item'=>$item];
      		return view('inventory::item.wiki', $array);
		} else {
			return redirect($this->prev)->with('message_error', 'Item no encontrado');
		}
	}

}