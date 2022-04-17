<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Actions\Breadcrumb;
use App\Actions\UploadFile;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorityController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DownlinkController;
use App\Http\Controllers\GalleriesController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\MixCalibrationController;
use App\Http\Controllers\MixController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\SensorsController;
use App\Http\Controllers\UplinkController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\NavigationController;
use App\Http\Controllers\LookupController;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::get('/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return response()->json(['message' => 'artisan cleared'], 200);
});

Route::prefix('{storeToken}')->group(function () {
    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('Breadcrumb', Breadcrumb::class);
        Route::post('Upload', UploadFile::class);
        Route::apiResource('Auth', AuthController::class);
        Route::apiResource('Navigation', NavigationController::class);
        Route::apiResource('Authority', AuthorityController::class);
        Route::apiResource('Companies', CompaniesController::class);
        Route::apiResource('Downlink', DownlinkController::class);
        Route::apiResource('Galleries', GalleriesController::class);
        Route::apiResource('Log', LogController::class);
        Route::apiResource('Measurement', MeasurementController::class);
        Route::apiResource('MixCalibration', MixCalibrationController::class);
        Route::apiResource('Mix', MixController::class);
        Route::apiResource('Projects', ProjectsController::class);
        Route::apiResource('Sensors', SensorsController::class);
        Route::apiResource('Uplink', UplinkController::class);
        Route::apiResource('Users', UsersController::class);
        Route::put('Users/UpdatePassword/{id}', [UsersController::class, 'updatePassword']);
        Route::apiResource('Lookup', LookupController::class);
    });
});
