<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/pay', [PaymentController::class, 'redirectToGateway']);
Route::post('/register', [AuthController::class, 'Signup']);
Route::post('/login', [AuthController::class, 'Login']);
Route::post('/checkIfUserHasPaid', [AuthController::class, 'checkIfUserHasPaid']);
Route::post('/profile', [AuthController::class, 'checkIfUserHasCompleteProfile']);
Route::post('/updateProfile', [AuthController::class, 'updateUserProfile']);
Route::post('/find', [AuthController::class, 'findUserMatch']);
Route::post('/choice', [AuthController::class, 'setUserMatch']);
Route::post('/update',[AuthController::class, 'updateUserPicture']);
Route::post('/updateCoverPhoto',[AuthController::class, 'updateCoverPhoto']);
Route::post('/createUserPost',[AuthController::class, 'createUserPost']);
Route::post('/fetchUserPost',[AuthController::class, 'fetchUserPost']);
Route::post('/fetchNewPost',[AuthController::class, 'fetchNewPost']);
Route::post('/uploadStory',[AuthController::class, 'uploadStory']);
Route::post('/findUserStory',[AuthController::class, 'findUserStory']);
Route::post('/deleteOldStory',[AuthController::class, 'Delete_All_Old_User_Stories']);
Route::post('/findUserFriend',[AuthController::class, 'findUserFriend']);
Route::post('/updateCoverText',[AuthController::class, 'updateCoverText']);
Route::post('/fetchAllPost',[AuthController::class, 'fetchAllPost']);
Route::post('/fetchRandomPost',[AuthController::class, 'fetchRandomPost']);
Route::post('/createChannel',[AuthController::class, 'createChannel']);
Route::post('/checkIfUserHasChannel',[AuthController::class, 'checkIfUserHasChannel']);
Route::post('/createUserChannelPost',[AuthController::class, 'createUserChannelPost']);
Route::post('/findChannelPost',[AuthController::class, 'findChannelPost']);
Route::post('/deleteChannelPost',[AuthController::class, 'deleteChannelPost']);
Route::post('/fetchRandomChannelPost',[AuthController::class, 'fetchRandomChannelPost']);
Route::post('/fetchAllChannelsPost',[AuthController::class, 'fetchAllChannelsPost']);
Route::post('/checkIfUserLiked',[AuthController::class, 'checkIfUserLiked']);
Route::post('/findPostLikeCounts',[AuthController::class, 'findPostLikeCounts']);
Route::post('/findPostCommentCounts',[AuthController::class, 'findPostCommentCounts']);
Route::post('/DeleteLike',[AuthController::class, 'DeleteLike']);
Route::post('/findAllPostUserLiked',[AuthController::class, 'findAllPostUserLiked']);
Route::post('/findStatus',[AuthController::class, 'findStatus']);
Route::post('/postComment',[AuthController::class, 'postComment']);
Route::post('/findAllComments',[AuthController::class, 'findAllComments']);
Route::post('/DeleteComment',[AuthController::class, 'DeleteComment']);
Route::post('/findNotifications',[AuthController::class, 'findNotifications']);
Route::post('/findNotifyCount',[AuthController::class, 'findNotifyCount']);







