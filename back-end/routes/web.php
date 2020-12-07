<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmbeddedIdentityController;
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
Route::get('/api/get-files/{file-id}', 'SignatureController@getFiles');

Route::prefix('api')->group(function () {
    Route::prefix('company')->group(function () {
        Route::get('check-slug-availability', [CompanyController::class, 'checkUrlSlug']);
    });

    Route::prefix('authenticate')->group(function () {
        Route::post('smart-id/start', [EmbeddedIdentityController::class, 'startSmartidLogin']);
        Route::post('smart-id/finish', [EmbeddedIdentityController::class, 'finishSmartidLogin']);

        Route::post('mobile-id/start', [EmbeddedIdentityController::class, 'startMobileidLogin']);
        Route::post('mobile-id/finish', [EmbeddedIdentityController::class, 'finishMobileidLogin']);

        Route::post('id-card', [EmbeddedIdentityController::class, 'idcardLogin']);
    });
});

// This part should always be last in web.php.
Route::fallback(function ($route = '') {
    // Serve index.html.
    $csrf = csrf_token();
    $html = File::get(public_path('/index.html'));
    $html = str_replace('<body>', "<body><script>window.CSRF_TOKEN = '$csrf'</script>", $html);
    return $html;
});
