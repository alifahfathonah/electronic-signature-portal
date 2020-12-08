<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::prefix('api')->group(function () {
    Route::prefix('company')->group(function () {
        Route::post('/', [CompanyController::class, 'store'])->middleware(['auth']);
        Route::get('check-slug-availability', [CompanyController::class, 'checkUrlSlug']);

        Route::prefix('{company_id}')->group(function () {
            Route::put('/', [CompanyController::class, 'update'])->middleware(['company.admin']);
        });
    });

    Route::get('get-files/{file-id}', 'FilesController@getFiles');
    Route::post('signatures/get-idcard-token', 'SignatureController@getIdcardToken');
    Route::post('signatures/get-signature-digest', 'SignatureController@getSignatureDigest');
    Route::post('signatures/finish-signature', 'SignatureController@finishSignature');

    Route::prefix('authenticate')->group(function () {
        Route::get('who-am-i', [AuthController::class, 'whoAmI']);

        Route::post('smart-id/start', [AuthController::class, 'startSmartidLogin']);
        Route::post('smart-id/finish', [AuthController::class, 'finishSmartidLogin']);

        Route::post('mobile-id/start', [AuthController::class, 'startMobileidLogin']);
        Route::post('mobile-id/finish', [AuthController::class, 'finishMobileidLogin']);

        Route::post('id-card', [AuthController::class, 'idcardLogin']);
    });
});

// This part should always be last in web.php.
Route::fallback(function ($route = '') {
    // Serve index.html.
    return File::get(public_path('/index.html'));
});
