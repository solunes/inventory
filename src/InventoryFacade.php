<?php
namespace Solunes\Inventory;

use Illuminate\Support\Facades\Facade;

class InventoryFacade extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'inventory';
	}
}