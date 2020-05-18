<?php

return [

	// GENERAL
	'after_seed' => true,
	'basic_inventory' => true,
	'reduce_inventory_after_purchase' => true,

	// CUSTOM FORMS
    'item_get_after_vars' => ['purchase','product'], // array de nodos: 'node'
    'item_child_after_vars' => ['product'],
    'item_remove_scripts' => ['purchase'=>['leave-form']],
    'item_add_script' => ['product-bridge-stock'=>['product-variation'], 'stock-addition'=>['product-variation'], 'stock-transfer'=>['product-variation'], 'stock-removal'=>['product-variation']],

];