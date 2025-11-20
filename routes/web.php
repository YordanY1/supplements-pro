<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\Pages\Home;
use App\Livewire\Pages\Catalog;
use App\Livewire\Pages\Cart;
use App\Livewire\Pages\Checkout;
use App\Livewire\Pages\ThankYou;
use App\Livewire\Pages\TermsAndConditions;
use App\Livewire\Pages\Categories;
use App\Livewire\Pages\Brands;

use App\Livewire\Pages\Privacy;
use App\Livewire\Pages\Cookies;
use App\Livewire\Pages\Returns;
use App\Livewire\Pages\Shipping;
use App\Livewire\Pages\Payments;


Route::get('/', Home::class)->name('home');
Route::get('/catalog', Catalog::class)->name('catalog');
Route::get('/catalog/category/{category}', Catalog::class)->name('catalog.category');
Route::get('/catalog/brand/{brand}', Catalog::class)->name('catalog.brand');
Route::get('/cart', Cart::class)->name('cart');
Route::get('/checkout', Checkout::class)->name('checkout');
Route::get('/thank-you', ThankYou::class)->name('thank-you');
Route::get('/terms-and-conditions', TermsAndConditions::class)->name('terms-and-conditions.index');
Route::get('/privacy-policy', Privacy::class)->name('privacy');
Route::get('/cookie-policy', Cookies::class)->name('cookies');
Route::get('/return-policy', Returns::class)->name('returns');
Route::get('/shipping-policy', Shipping::class)->name('shipping');
Route::get('/payment-policy', Payments::class)->name('payments');

Route::get('/categories', Categories::class)->name('categories');
Route::get('/brands', Brands::class)->name('brands');

Route::get('/product/{slug}', \App\Livewire\Pages\Product::class)->name('product.show');

Route::get('/blog', \App\Livewire\Pages\Blog::class)->name('blog');
Route::get('/blog/{slug}', \App\Livewire\Pages\BlogPost::class)->name('blog.post');



Route::get('/stripe/create/{order}', [\App\Http\Controllers\StripeController::class, 'create'])->name('stripe.create');
Route::get('/stripe/success', [\App\Http\Controllers\StripeController::class, 'success'])->name('stripe.success');
Route::get('/stripe/cancel', [\App\Http\Controllers\StripeController::class, 'cancel'])->name('stripe.cancel');
