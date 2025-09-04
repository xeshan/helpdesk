<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});


Route::get('/{any}', function () {
    return view('app'); // or your main Vue blade file
})->where('any', '.*');
