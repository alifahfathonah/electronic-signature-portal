<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilesController;
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

    Route::post('/company/', [CompanyController::class, 'store'])->middleware(['auth']);
    Route::get('/company/check-slug-availability', [CompanyController::class, 'checkUrlSlug']);

    Route::middleware(['auth', 'company.admin'])->group(function () {
        Route::put('/company/{company}', [CompanyController::class, 'update']);
    });

    Route::middleware(['auth'])->group(function () {
        Route::post('/container', [FilesController::class, 'createSignatureContainer']);
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
