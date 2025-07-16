<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';


Route::get('/import-obat', function () {
    Excel::import(new ObatImport, storage_path('app/public/data-obat.xlsx'));
    return 'Import selesai!';
});


