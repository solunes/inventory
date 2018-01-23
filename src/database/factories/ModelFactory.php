<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Customer::class, function ($faker) {
    return ['name'=>$faker->company];
});

$factory->define(App\CustomerPoint::class, function ($faker) {
	$date =$faker->date($format='Y-m-d', $min = '-90 days', $max = 'now');
    return ['customer_id'=>rand(1,30), 'city_id'=>rand(1,4), 'name'=>$faker->company, 'assigned_staff'=>rand(1,10), 'contract_signed'=>$date, 'contract_duration'=>rand(1,150)];
});
