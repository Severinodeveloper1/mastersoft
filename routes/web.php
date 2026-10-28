<?php

use App\Http\Controllers\Store\AboutController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CatalogController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\ClaimController;
use App\Http\Controllers\Store\ContactController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\LegalPageController;
use App\Http\Controllers\Store\ProductController;
use App\Models\LegalPage;
use Illuminate\Support\Facades\Route;

/*
| Storefront routes. Every page controller lives in App\Http\Controllers\Store;
| feature work fills those controllers without changing this file.
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/tienda', CatalogController::class)->name('store.index');
Route::get('/producto/{product:slug}', ProductController::class)->name('products.show');
Route::get('/nosotros', AboutController::class)->name('about');
Route::get('/contactanos', ContactController::class)->name('contact');

Route::get('/carrito', [CartController::class, 'show'])->name('cart.show');
Route::post('/carrito', [CartController::class, 'add'])->name('cart.add');
Route::get('/pedido', [CheckoutController::class, 'create'])->name('checkout.create');
Route::get('/pedido/{order:code}/confirmacion', [CheckoutController::class, 'confirmation'])
    ->middleware('signed')
    ->name('checkout.confirmation');

Route::get('/libro-de-reclamaciones', ClaimController::class)->name('claims.create');

Route::get('/politicas-de-privacidad', LegalPageController::class)
    ->defaults('slug', LegalPage::PRIVACY)
    ->name('legal.privacy');
Route::get('/terminos-y-condiciones', LegalPageController::class)
    ->defaults('slug', LegalPage::TERMS)
    ->name('legal.terms');

// Legacy URLs from the previous site.
Route::permanentRedirect('/store', '/tienda');
Route::permanentRedirect('/about', '/nosotros');
Route::permanentRedirect('/contact', '/contactanos');
Route::permanentRedirect('/cart', '/carrito');
Route::permanentRedirect('/libro-reclamaciones', '/libro-de-reclamaciones');
Route::get('/product/{slug}', fn (string $slug) => redirect()->route('products.show', $slug, 301));
