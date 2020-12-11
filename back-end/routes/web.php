<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\PasswordLoginController;
use App\Http\Controllers\SignatureController;
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

    Route::post('/password/login', [PasswordLoginController::class, 'login']);
    Route::post('/password/register', [PasswordLoginController::class, 'register']);

    Route::get('/company/check-slug-availability', [CompanyController::class, 'checkUrlSlug']);

    Route::post('/company', [CompanyController::class, 'store'])->middleware(['auth']);

    Route::put('/company/{company:url_slug}/', [CompanyController::class, 'update'])->middleware(['company.admin']);
    Route::post('/company/{company:url_slug}/container', [FilesController::class, 'createSignatureContainer'])->middleware(['company.member']);

    Route::get('/container/{container_id}', [FilesController::class, 'getContainerInfo'])->middleware(['container.can-read']);
    Route::get('/container/{container_id}/download', [FilesController::class, 'downloadFile'])->middleware(['container.can-read']);

    Route::post('signatures/get-idcard-token', 'SignatureController@getIdcardToken');
    Route::post('signatures/get-signature-digest', 'SignatureController@getSignatureDigest');
    Route::post('signatures/finish-signature', 'SignatureController@finishSignature');
    Route::post('signatures/container/{container}/signer/{signer:public_id}/visual-sign', [SignatureController::class, 'applyVisualSignature']);

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
