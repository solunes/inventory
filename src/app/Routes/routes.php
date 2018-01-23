<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix'=>'process'], function(){
    Route::get('/calculate-shipping/{shipping_id}/{city_id}/{weight}', 'ProcessController@getCalculateShipping');
});

Route::group(['prefix'=>'gitlab'], function(){
    Route::get('/group-inventorys/{group_name}', 'GitlabController@getGroupInventorys');
    Route::get('/inventory/{inventory_name}/{group_name}', 'GitlabController@getInventory');
    Route::get('/inventory-commits/{inventory_name}/{group_name}', 'GitlabController@getInventoryCommits');
});

Route::group(['prefix'=>'hubspot'], function(){
    Route::get('/import-companies/{count?}', 'Integrations\HubspotController@getImportCompanies');
    Route::get('/import-contacts/{count?}', 'Integrations\HubspotController@getImportContacts');
    Route::get('/import-deals/{count?}', 'Integrations\HubspotController@getImportDeals');
    Route::post('/webhook', 'Integrations\HubspotController@postHubspotWebhook');
});