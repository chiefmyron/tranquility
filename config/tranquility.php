<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Minimum password length
	|--------------------------------------------------------------------------
	| Specify the minimum number of characters required for a password
	|
	*/
	'minimum_password_length' => 8,
    
    /*
	|--------------------------------------------------------------------------
	| Address geolocation
	|--------------------------------------------------------------------------
	| Specify details of service used to generate geolocation details for
    | physical addresses
	|
	*/
    'geolocation_enabled' => env('TRANQUILITY_GEOLOCATION_ENABLED', true),
    'geolocation_service_uri' => 'http://maps.googleapis.com/maps/api/geocode/' 

];
