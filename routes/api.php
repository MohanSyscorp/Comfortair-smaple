<?php

use App\Http\Controllers\Api\PublicApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public REST API Routes for Next.js Website (Version 1)
|--------------------------------------------------------------------------
|
| These endpoints are consumed by the Comfort Aircon Next.js public website
| for catalogue browsing, gallery viewing, contact reachability sync,
| and 7-field customer lead submissions.
|
*/

Route::prefix('v1')->group(function () {
    // 1. Synchronized Business Reachability (Header, Footer, Contact Page)
    Route::get('/contact', [PublicApiController::class, 'getContact']);

    // 2. Published Carrier AC Products Catalogue & Enquiry Dropdown
    Route::get('/products', [PublicApiController::class, 'getProducts']);

    // 3. Published Portfolio Gallery Photos (supports ?category=)
    Route::get('/gallery', [PublicApiController::class, 'getGallery']);

    // 4. Unified 7-Field Customer Enquiry Submission (Throttled)
    Route::post('/enquiry', [PublicApiController::class, 'submitEnquiry'])
        ->middleware('throttle:15,1');
});
