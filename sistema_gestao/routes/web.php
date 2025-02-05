<?php

use App\Http\Controllers\GrupoEconomicoController;
use Illuminate\Support\Facades\Route;

Route::resource('/grupo_economicos', GrupoEconomicoController::class);