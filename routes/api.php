<?php

use App\Util\helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Con middleware
Route::group([
  'namespace'  => 'API\v1',
  'prefix'     => '/v1',
  'middleware' => 'auth:api',
], function () {

  Route::apiResource("users", "Users");
});

// Sin middleware
Route::group([
  'namespace'  => 'API\v1',
  'prefix'     => '/v1',
], function () {

  Route::apiResource("users", "Users");
});
