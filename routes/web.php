<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\Home;
use App\Livewire\Pages\Catalog;
use App\Livewire\Pages\Cart;
use App\Livewire\Pages\Checkout;
use App\Livewire\Pages\ThankYou;
use App\Livewire\Pages\TermsAndConditions;


Route::get('/', Home::class)->name('home');
Route::get('/catalog', Catalog::class)->name('catalog');
Route::get('/catalog/category/{category}', Catalog::class)->name('catalog.category');
Route::get('/catalog/brand/{brand}', Catalog::class)->name('catalog.brand');
Route::get('/cart', Cart::class)->name('cart');
Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/thank-you', ThankYou::class)->name('thank-you');
Route::get('/terms-and-conditions', TermsAndConditions::class)->name('terms-and-conditions.index');
