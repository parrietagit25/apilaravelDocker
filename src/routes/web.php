<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CursosController;
use App\Http\Controllers\TodoscursosController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|


Route::get('/', function () {
    return view('welcome');
});
*/

//Route::get('/', [ClientesController::class, 'index']);

// rutas que incluyen todos los metodos http
Route::resource('/', ClientesController::class);
Route::resource('/registro', ClientesController::class);
Route::resource('/cursos', CursosController::class);
Route::resource('/todos', TodoscursosController::class);