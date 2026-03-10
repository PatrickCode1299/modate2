<?php

namespace App\Http\Controllers;
use App\Services\UserActivityServices;
use App\Services\UserServices;
use App\Services\ConverSationServices;
use App\Services\CloudinaryServices;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
  protected $ConverSation;
  protected $cloudinaryService;
  protected $userActivityService;

  public function __construct(UserServices $userService, ConverSationServices $ConverSation, CloudinaryServices $cloudinaryService, UserActivityServices $userActivityService)
  {
      $this->userService = $userService;
      $this->ConverSation=$ConverSation;
      $this->cloudinaryService = $cloudinaryService;
      $this->userActivityService= $userActivityService;
  }
  public function bookmarkPost(Request $request){
      $user=$request->input('email');
      $post_id=$request->input('post_id');
      $bookmark_user_post=$this->userActivityService->bookmarkUserPost($user,$post_id);
      return $bookmark_user_post;
    }


  public function createUserPost(Request $request){
      $email=$request->input('email');
      $user_caption=$request->input('user_caption');
      $tagged_users=$request->input('tagged_users');
     $create_user_post= $this->userActivityService->createUserPost($email,$user_caption,$tagged_users);
     return $create_user_post;
    }

    public function findAllBookMarkedPosts(Request $request){
      $current_user=$request->input('email');
      $find_all_bookmarked_post=$this->userActivityService->findAllBookmarkedPost($current_user);
      return $find_all_bookmarked_post;
    }

    public function findAllUserReply(Request $request){
      $email=$request->input('user_email');
      $find_user_reply=$this->userActivityService->findUserReply($email);
      return $find_user_reply;
    }

    public function disableUserComment(Request $request){
      $postid=$request->input('postid');
      $disable_users_from_commenting=$this->userActivityService->disableUserComment($postid);
      return $disable_users_from_commenting;

    }

    public function find_user_followers(Request $request){
      $email=$request->input('email');
      $find_user_followers_count=$this->userActivityService->findUserFollowersCount($email);
      return $find_user_followers_count;
    }

    public function customiseProfile(Request $request){
      $option=$request->input('option');
      $email=$request->input('email');
      $customise_user_profile=$this->userActivityService->customiseUserProfile($option,$email);
      return $customise_user_profile;
    }

    public function setPendingMatch(Request $request){
      $current_user=$request->input('current_user');
      $user_who_is_to_be_followed=$request->input('choice');
      $send_follow_request=$this->userActivityService->setFollowRequest($current_user,$user_who_is_to_be_followed);
    }

    public function findRelatedPost(Request $request){
      $key=$request->input('data');
      $find_hashtag_post=$this->userActivityService->findRelatedPost($key);
      return $find_hashtag_post;
    }

    public function fetchUserPost(Request $request){
      $email=$request->input('email');
     $fetch_user_post= $this->userActivityService->fetchUserPost($email);
     return $fetch_user_post;
    }

    public function uploadTextStory(Request $request){
      $user_text=$request->input('story_post');
      $user_bg_color=$request->input('bgColor');
      $email=$request->input('email');
     $upload_text_story=$this->userActivityService->uploadTextStory($user_text,$user_bg_color,$email);
     return $upload_text_story;
    }

    public function findUserStory(Request $request){
      $user_email=$request->input('email');
      $find_user_story=$this->userActivityService->findUserStory($user_email);
      return $find_user_story;
    }

    public function findChannelPost(Request $request){
      $email=$request->input('email');
      $find_channel_post=$this->userActivityService->findChannelPost($email);
      return $find_channel_post;
    }

    public function fetchRandomChannelPost(Request $request){
      $user=$request->input('email');
      $date=$request->input('recent_date');
      $find_older_channel_post=$this->userActivityService->fetchOlderChannelPost($user,$date);
      return $find_older_channel_post;
    }

    public function fetchAllChannelsVideo(Request $request){
      $current_user=$request->input('email');
      $ip=$request->input('ip');
     $find_channel_videos=$this->userActivityService->findChannelVideos($current_user,$ip);
     return $find_channel_videos;
    }

    public function fetchAllChannelsPost(Request $request){
      $current_user=$request->input('email');
     $fetch_all_channel_posts=$this->userActivityService->fetchAllChannelsPost($current_user);
     return $fetch_all_channel_posts;
    }

    public function fetchAllSharedPost(Request $request){
      $current_user=$request->input('email');
      $find_all_shared_post=$this->userActivityService->fetchAllSharedPostsForCurrentUser($current_user);
      return $find_all_shared_post;
    }

    public function fetchNewPost(Request $request){
      $email=$request->input('email');
      $fetch_new_post=$this->userActivityService->fetchNewPost($email);
      return $fetch_new_post;
    }
}
