<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::group(['prefix' => 'v1', 'as' => 'admin.', 'namespace' => 'Api\V1\Admin'], function () {
    Route::apiResource('permissions', 'PermissionsApiController');

    Route::apiResource('roles', 'RolesApiController');

    Route::apiResource('users', 'UsersApiController');

    Route::apiResource('products', 'ProductsApiController');
});
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/groups', [AuthController::class, 'groups']);
Route::middleware([EnsureFrontendRequestsAreStateful::class])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/group', [AuthController::class, 'groups']);
        Route::delete('/deletegroup/{id}', [AuthController::class, 'deleteGroup']);
        Route::put('/updategroup/{id}', [AuthController::class, 'updateGroup']);
        Route::get('/getgroups/{id?}', [AuthController::class, 'getgroups']);
    
                Route::delete('/deletesinglegroup/{user_id}/{id?}', [AuthController::class, 'deleteSingleGroup']);
                Route::get('/getsinglegroup/{user_id}/{id?}', [AuthController::class, 'getSingleGroup']);
                Route::put('/updatesinglegroup/{user_id}/{id?}', [AuthController::class, 'updateSingleGroup']);
    
        Route::post('/contact', [AuthController::class, 'contacts']);
        Route::get('/contacts/{id?}', [AuthController::class, 'getcontacts']);
        Route::delete('/deletecontact/{id}', [AuthController::class, 'deleteContact']);
        Route::put('/updatecontact/{id}', [AuthController::class, 'updateContact']);
    
                Route::delete('/deletesinglecontact/{user_id}/{id?}', [AuthController::class, 'deleteSingleContact']);
                Route::get('/getsinglecontact/{user_id}/{id?}', [AuthController::class, 'getSingleContact']);
                Route::put('/updatesinglecontact/{user_id}/{id?}', [AuthController::class, 'updateSingleContact']);
            
            
                Route::post('upload-attachment', [AuthController::class, 'upload']);
                Route::post('upload-multi-attachment', [AuthController::class, 'multi_attachment_upload']);
             //   Route::get('/attachment', [AuthController::class, 'attachment']);
             Route::get('/attachment/{surveyId}', [AuthController::class, 'attachment']);
    
                Route::post('participants', [AuthController::class, 'participants']);
                Route::delete('/deleteparticipant/{id}', [AuthController::class, 'deleteParticipant']);
               // Route::get('getparticipants',[AuthController::class, 'getparticipants']);
                Route::get('getparticipants/{surveyId}',[AuthController::class, 'getparticipants']);
                Route::delete('/deletesingleparticipant/{survey_id}/{id?}', [AuthController::class, 'deleteSingleParticipant']);
    
                Route::post('create-survey', [AuthController::class, 'create_survey']);
                Route::get('/getsinglesurvey/{user_id}/{id?}', [AuthController::class, 'getSingleSurvey']);
       
       
       
                Route::get('/getusers', [AuthController::class, 'getusers']);
       
            });
    }); 