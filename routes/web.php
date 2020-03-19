<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/



$router->get('/', 'MainController@intro');
$router->post('/upload-json', 'MainController@uploadJson');
$router->post('/send-regions', 'MainController@sendRegions');
$router->post('/send-organization', 'MainController@sendOrganization');
$router->get('/finished', 'MainController@finished');
