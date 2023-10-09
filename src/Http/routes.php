<?php

use Ysnow\Scheduling\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('scheduling', Controllers\SchedulingController::class.'@index');