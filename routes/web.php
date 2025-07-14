<?php

use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ObatImport;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import-obat', function () {
    Excel::import(new ObatImport, storage_path('app/public/data-obat.xlsx'));
    return 'Import selesai!';
});
