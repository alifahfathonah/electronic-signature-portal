<?php

use App\Http\Controllers\CompanyController;
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
        Route::get('check-slug-availability', [CompanyController::class, 'checkUrlSlug']);
    });

    Route::get('get-files/{file-id}', 'FilesController@getFiles');
    Route::post('signatures/get-idcard-token', 'SignatureController@getIdcardToken');
    Route::post('signatures/get-signature-digest', 'SignatureController@getSignatureDigest');
    Route::post('signatures/finish-signature', 'SignatureController@finishSignature');

    Route::prefix('authenticate')->group(function () {

        Route::post('smart-id/start', 'EmbeddedIdentityController@startSmartidLogin');
        Route::post('smart-id/finish', 'EmbeddedIdentityController@finishSmartidLogin');

        Route::post('mobile-id/start', 'EmbeddedIdentityController@startMobileidLogin');
        Route::post('mobile-id/finish', 'EmbeddedIdentityController@finishMobileidLogin');

        Route::post('id-card', 'EmbeddedIdentityController@idcardLogin');
    });
});

// This part should always be last in web.php.
Route::fallback(function ($route = '') {
    // Serve index.html.
    return File::get(public_path('/index.html'));
});
