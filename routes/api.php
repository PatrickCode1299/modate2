<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AgoraController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\WebRTCController;

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
Route::post('/request', [AuthController::class, 'setPendingMatch']);
Route::post('/removeChoice', [AuthController::class, 'unMatchUser']);
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
Route::post('/convo',[AuthController::class, 'convo']);
Route::post('/findConvo',[AuthController::class, 'findConvo']);
Route::post('/sendMessage',[AuthController::class, 'sendMessage']);
Route::post('/findAllMessages',[AuthController::class, 'findAllMessages']);
Route::post('/findRecieverInfo',[AuthController::class, 'findRecieverInfo']);
Route::post('/updateMsgStatus',[AuthController::class, 'updateMsgStatus']);
Route::post('/findMsgCount',[AuthController::class, 'findMsgCount']);
Route::post('/createUserChannelVideo',[AuthController::class, 'createUserChannelVideo']);
Route::post('/replyComment',[AuthController::class, 'replyComment']);
Route::post('/findCommentReplies',[AuthController::class, 'findCommentReplies']);
Route::post('/DeleteCommentReply',[AuthController::class, 'DeleteCommentReply']);
Route::post('/findAllChannelPhotos',[AuthController::class, 'findAllChannelPhotos']);
Route::post('/findAllChannelVideos',[AuthController::class, 'findAllChannelVideos']);
Route::post('/sharePost',[AuthController::class, 'sharePost']);
Route::post('/fetchAllSharedPost',[AuthController::class, 'fetchAllSharedPost']);
Route::post('/findUserPost',[AuthController::class, 'findUserPost']);
Route::post('/blockUser',[AuthController::class, 'blockUser']);
Route::post('/isUserBlocked',[AuthController::class, 'check_if_current_user_has_being_blocked']);
Route::post('/isUserFollowed',[AuthController::class, 'check_if_current_user_has_being_followed']);
Route::post('/isPrivateUserFollowed',[AuthController::class, 'check_if_private_account_is_followed']);
Route::post('/bookmarkPost',[AuthController::class, 'bookmarkPost']);
Route::post('/findAllBookMarkedPosts',[AuthController::class, 'findAllBookMarkedPosts']);
Route::post('/Search',[AuthController::class, 'Search']);
Route::post('/deleteUserPost',[AuthController::class, 'deleteUserPost']);
Route::post('/findAllUserReply',[AuthController::class, 'findAllUserReply']);
Route::post('/findSharedStatus',[AuthController::class, 'findSharedStatus']);
Route::post('/disableUserComment',[AuthController::class, 'disableUserComment']);
Route::post('/findFollowersCount',[AuthController::class, 'find_user_followers']);
Route::post('/suggestUsers',        [AuthController::class, 'suggestUsers']);
Route::post('/generate-link', [WebRTCController::class, 'generateLink']);
Route::post('/broadcast-signal', [WebRTCController::class, 'broadcastSignal']);
Route::post('/signalPusher', [WebRTCController::class, 'signalPusher']);
Route::delete('/delete-stream-videos/{streamId}', [WebRTCController::class, 'deleteStreamVideos']);
Route::post('/live-video/chunk', [WebRTCController::class, 'getVideoChunk']);
Route::post('/uploadTextStory', [AuthController::class, 'uploadTextStory']);
Route::post('/findComment', [AuthController::class, 'findComment']);
Route::post('/fetchNewChannelPost', [AuthController::class, 'fetchNewChannelPost']);
Route::post('/fetchNewSharedPost', [AuthController::class, 'fetchNewSharedPost']);
Route::post('/customiseProfile', [AuthController::class, 'customiseProfile']);
Route::post('/related', [AuthController::class, 'findRelatedPost']);
Route::post('/findUserFollowers', [AuthController::class, 'findUserFollowers']);
Route::post('/fetchAllChannelsVideo', [AuthController::class, 'fetchAllChannelsVideo']);
Route::post('/updateLastActivity', [AuthController::class, 'updateUserLastActivity']);
Route::post('/createCommunity', [AuthController::class, 'createCommunity']);
Route::post('/fetchAllCommunitiesPost',[AuthController::class, 'fetchCommunitiesPost']);
Route::post('/findCommunitiesOwnedByUser',[AuthController::class, 'findCommunitiesOwnedByUser']);
Route::post('/findCommunityDetails',[AuthController::class, 'findCommunityDetails']);
Route::post('/fetchCommunityPosts',[AuthController::class, 'fetchCommunityPosts']);
Route::post('/createCommunityPost',[AuthController::class, 'createCommunityPost']);
Route::post('/fetchLatestCommunityPosts',[AuthController::class, 'fetchLatestCommunityPosts']);
Route::post('/fetchRandomTopCommunityPost',[AuthController::class, 'fetchRandomTopCommunityPost']);
Route::post('/fetchRandomLatestCommunityPosts',[AuthController::class, 'fetchRandomLatestCommunityPosts']);
Route::post('/createCommunityPostVideo',[AuthController::class, 'createCommunityPostVideo']);
Route::post('/removeMember',[AuthController::class, 'removeMember']);
Route::post('/joinCommunity',[AuthController::class, 'joinCommunity']);
Route::post('/deleteCommunityPost',[AuthController::class, 'deleteCommunityPost']);
Route::post('/sendResetLink',[AuthController::class, 'sendResetLink']);
Route::post('/updateDetails',[AuthController::class, 'updateDetails']);
Route::post('/sendUpdateProfileLink',[AuthController::class, 'sendUpdateProfileLink']);
























