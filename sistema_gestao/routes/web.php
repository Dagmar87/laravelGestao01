<?php

use App\Http\Controllers\BandeiraController;
use App\Http\Controllers\ColaboradorController;
use App\Http\Controllers\GrupoEconomicoController;
use App\Http\Controllers\UnidadeController;
use Illuminate\Support\Facades\Route;

Route::resource('/grupo_economicos', GrupoEconomicoController::class);
Route::resource('/bandeiras', BandeiraController::class);
Route::resource('/unidades', UnidadeController::class);
Route::resource('/colaboradors', ColaboradorController::class);