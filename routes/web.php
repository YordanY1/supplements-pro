<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\Home;
use App\Livewire\Pages\Catalog;

Route::get('/', Home::class)->name('home');
Route::get('/catalog', Catalog::class)->name('catalog');
Route::get('/catalog/category/{category}', Catalog::class)->name('catalog.category');
Route::get('/catalog/brand/{brand}', Catalog::class)->name('catalog.brand');
