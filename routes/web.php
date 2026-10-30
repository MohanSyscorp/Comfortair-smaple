<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to admin login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin Authentication Routes
Route::get('/admin-login', [AuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin-login', [AuthController::class, 'login'])->name('admin.login.submit');

// Protected Admin Panel Routes
Route::prefix('admin')->middleware(['auth', 'active'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::post('/password/change', [ProfileController::class, 'updatePassword'])->name('admin.password.change');

    // Super Admin: User & Permission Management (Section 8.3 & 9.1)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])->name('admin.users.status');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Product Management (Section 8.2 & 9.2)
    Route::middleware('permission:products')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
        Route::patch('/products/{product}/publish', [ProductController::class, 'togglePublish'])->name('admin.products.publish');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    });

    // Gallery Management (Section 8.2 & 9.3)
    Route::middleware('permission:gallery')->group(function () {
        Route::get('/gallery', [GalleryController::class, 'index'])->name('admin.gallery.index');
        Route::get('/gallery/create', [GalleryController::class, 'create'])->name('admin.gallery.create');
        Route::post('/gallery', [GalleryController::class, 'store'])->name('admin.gallery.store');
        Route::get('/gallery/{gallery}/edit', [GalleryController::class, 'edit'])->name('admin.gallery.edit');
        Route::put('/gallery/{gallery}', [GalleryController::class, 'update'])->name('admin.gallery.update');
        Route::patch('/gallery/{gallery}/publish', [GalleryController::class, 'togglePublish'])->name('admin.gallery.publish');
        Route::delete('/gallery/{gallery}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');
    });

    // Contact Management (Section 8.2 & 9.5)
    Route::middleware('permission:contact')->group(function () {
        Route::get('/contact', [ContactController::class, 'index'])->name('admin.contact.index');
        Route::get('/contact/create', [ContactController::class, 'create'])->name('admin.contact.create');
        Route::post('/contact/create', [ContactController::class, 'store'])->name('admin.contact.store');
        Route::get('/contact/edit', [ContactController::class, 'edit'])->name('admin.contact.edit');
        Route::match(['post', 'put'], '/contact', [ContactController::class, 'update'])->name('admin.contact.update');
    });

    // Enquiry Management (Section 8.2 & 9.4)
    Route::middleware('permission:enquiries')->group(function () {
        Route::get('/enquiries', [\App\Http\Controllers\Admin\EnquiryController::class, 'index'])->name('admin.enquiries.index');
        Route::get('/enquiries/{enquiry}', [\App\Http\Controllers\Admin\EnquiryController::class, 'show'])->name('admin.enquiries.show');
        Route::patch('/enquiries/{enquiry}/status', [\App\Http\Controllers\Admin\EnquiryController::class, 'updateStatus'])->name('admin.enquiries.status');
        Route::delete('/enquiries/{enquiry}', [\App\Http\Controllers\Admin\EnquiryController::class, 'destroy'])->name('admin.enquiries.destroy');
    });
});
