<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/produto/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/categorias', [\App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
});
Route::get('/checkout/sucesso/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/status/{order}', [CheckoutController::class, 'checkStatus'])->name('checkout.status');

Route::get('/carrinho', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrinho/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/carrinho/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/carrinho/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/busca', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::post('/newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Route::post('/frete/calcular', [\App\Http\Controllers\ShippingController::class, 'calculate'])->name('shipping.calculate');

// Asaas Webhook – CSRF exempt via VerifyCsrfToken middleware
Route::post('/webhooks/asaas', [\App\Http\Controllers\AsaasWebhookController::class, 'handle'])->name('webhooks.asaas');


Route::get('/dashboard', function () {
    return redirect()->route('customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('orders', App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::get('/configuracoes', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/configuracoes', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('/configuracoes/test-enviamais', [\App\Http\Controllers\Admin\SettingsController::class, 'testEnviaMais'])->name('settings.test-enviamais');

    Route::get('/aparencia', [\App\Http\Controllers\Admin\AppearanceController::class, 'index'])->name('appearance.index');
    Route::put('/aparencia', [\App\Http\Controllers\Admin\AppearanceController::class, 'update'])->name('appearance.update');

    Route::get('/newsletters', [\App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('newsletters.index');
    Route::delete('/newsletters/{newsletter}', [\App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('newsletters.destroy');
});

Route::middleware('auth')->prefix('minha-conta')->name('customer.')->group(function () {
    Route::get('/', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/pedidos', [App\Http\Controllers\Customer\DashboardController::class, 'orders'])->name('orders');
    Route::get('/pedidos/{order}', [App\Http\Controllers\Customer\DashboardController::class, 'showOrder'])->name('orders.show');

    Route::get('/enderecos', [App\Http\Controllers\Customer\AddressController::class, 'index'])->name('addresses');
    Route::post('/enderecos', [App\Http\Controllers\Customer\AddressController::class, 'store'])->name('addresses.store');
    Route::patch('/enderecos/{address}', [App\Http\Controllers\Customer\AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/enderecos/{address}', [App\Http\Controllers\Customer\AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/enderecos/{address}/set-default', [App\Http\Controllers\Customer\AddressController::class, 'setDefault'])->name('addresses.set-default');

    Route::get('/dados', [App\Http\Controllers\Customer\DashboardController::class, 'profile'])->name('profile');
    Route::patch('/dados', [App\Http\Controllers\Customer\DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [App\Http\Controllers\Auth\PasswordController::class, 'update'])->name('password.update');
});

require __DIR__ . '/auth.php';
