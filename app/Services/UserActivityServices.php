<?php
namespace App\Services;
use App\Models\Channel;
use App\Models\ChannelPost;
use App\Models\User_Post;
use App\Models\User;
use App\Models\Messages;
use App\Models\Comment;
use App\Models\Story;
use App\Models\PostLike;
use App\Models\Comment_Reply;
use App\Models\Shared_Post;
use App\Models\Bookmark;
use App\Models\Community;
use App\Models\Follow_Request;
use App\Services\UserServices;
use App\Models\user_match_choice;
use Illuminate\Support\Facades\DB;
class UserActivityServices{
    protected $userService;
    public function __construct(UserServices $userService)
    {
      $this->userService=$userService;
    }
    public function createUserPost($email,$user_caption,$tagged_users){
        $get_poster_info=User::where('email',$email)->first();
        $avatar=$get_poster_info['profile_picture'];
        $name=$get_poster_info['first_name']."\t".$get_poster_info['last_name'];
        $post_id=rand(10,1000000000) . date('d');
        if($tagged_users === null){
          User_Post::create([
            "name"=>$name,
            "avatar"=>$avatar,
            "caption"=>$user_caption,
            "email"=>$email,
            "postid"=>$post_id
          ]);
        }else{
          foreach($tagged_users as $users){
            $this->userService->notifyUser($email,$users,"$name tagged you in this: $user_caption",$post_id);
          }
          User_Post::create([
            "name"=>$name,
            "avatar"=>$avatar,
            "caption"=>$user_caption,
            "email"=>$email,
            "postid"=>$post_id
          ]);
        }
        
        return response([
          "reply"=>"Post added successfully",
          "tagged_users"=>$tagged_users
        ]);
    }
    public function fetchUserPost($email){
      $find_current_user_mutual=user_match_choice::where('user',$email)->get();
      $hold_user_mutual_friends=array();
      foreach($find_current_user_mutual as $user_match){
       $owner_friend=$user_match->choice;
       array_push($hold_user_mutual_friends,$owner_friend);
      }
      $find_all_post=User_Post::where('email',$email)->
                      orwhereIn('email',$hold_user_mutual_friends)->latest('created_at')
                      ->first();

      return response([
        "reply" => $find_all_post
      ]);
    }
    public function fetchAllSharedPostsForCurrentUser($current_user){
      $find_user_choice= user_match_choice::where('user',$current_user)->get();
     $keep_all_persons_who_user_follow=array();
     foreach($find_user_choice as $person_who_user_follows){
      array_push($keep_all_persons_who_user_follow, $person_who_user_follows->choice);
     }
     $store_all_post=array();
     $find_all_shared_posts_from_who_user_follows=DB::table('shared__posts')->join('users','shared__posts.email_of_user_who_shared','=','users.email')
     ->whereIn('shared__posts.email_of_user_who_shared',$keep_all_persons_who_user_follow)->select('shared__posts.name','shared__posts.caption','shared__posts.created_at','shared__posts.post_img1','shared__posts.post_img2','shared__posts.post_img3','shared__posts.post_img4','shared__posts.video','shared__posts.quote','shared__posts.postid','shared__posts.prev_id','shared__posts.isReply', 'shared__posts.name_of_user_who_shared','shared__posts.email','shared__posts.email_of_user_who_shared','users.profile_picture')->inRandomOrder()->take(10)->latest()->get();
     foreach($find_all_shared_posts_from_who_user_follows as $post){
      $post_id=$post->postid;
      $email_of_original_poster=$post->email;
    $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
    $post_like_collection=array();
    foreach($no_of_likes as $post_like){
      array_push($post_like_collection,$post_like);
    }
    $post_comments_collection=array();
    $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
    foreach($no_of_comments as $post_comment){
      array_push($post_comments_collection,$post_comment);
    }
    $post_shares_collection=array();
    $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
    foreach($no_of_shares as $shares_count){
      array_push($post_shares_collection,$shares_count);
    }
    $find_avatar_of_original_poster=DB::table('users')->where('email',$email_of_original_poster)->select('profile_picture')->first();
    array_push($store_all_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"name_of_user_who_shared"=>$post->name_of_user_who_shared,"email_of_user_who_shared"=>base64_encode($post->email_of_user_who_shared),"quote"=>$post->quote,"prev_id"=>$post->prev_id, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
    "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection), "avatar_of_original_poster"=>$find_avatar_of_original_poster->profile_picture
  ]);
    }
      return response([
        "reply" => $store_all_post
      ]);
    }
    public function fetchNewPost($email){
        $find_all_post=User_Post::where('email',$email)->
                      latest('id')
                      ->first();
      return response([
        "reply" => $find_all_post
      ]);
    }
    public function fetchOlderChannelPost($channel_owner, $prev_date){
      $store_all_post=array();
      $db_post=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->where('channel_posts.created_at','<',$prev_date)->where('channel_posts.email',$channel_owner)->select('channel_posts.name','channel_posts.caption','channel_posts.created_at','channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','users.profile_picture','channel_posts.email', 'channel_posts.isReply', 'channel_posts.id','channel_posts.postid')->orderBy('channel_posts.id','DESC')->limit(1)->get();
    foreach($db_post as $get_post){
     $post_id=$get_post->postid;
      $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
      $post_like_collection=array();
      foreach($no_of_likes as $post_like){
        array_push($post_like_collection,$post_like);
      }
      $post_comments_collection=array();
      $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
      foreach($no_of_comments as $post_comment){
        array_push($post_comments_collection,$post_comment);
      }
      $post_shares_collection=array();
      $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
      foreach($no_of_shares as $shares_count){
        array_push($post_shares_collection,$shares_count);
      }
      array_push($store_all_post,["name"=>$get_post->name, "caption"=>$get_post->caption, "post_img1"=>$get_post->post_img1, "post_img2"=>$get_post->post_img2, "post_img3"=>$get_post->post_img3,"post_img4"=>$get_post->post_img4,"video"=>$get_post->video, "date"=>$get_post->created_at,"avatar"=>$get_post->profile_picture,"email"=>$get_post->email,"id"=>$get_post->id, "postid"=>$get_post->postid, "isReply"=> $get_post->isReply, "likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)]);

      }
      return response([
        "reply" => $store_all_post
      ]);
    }
    public function findChannelPost($email){
      $channel_post=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->where('channel_posts.email',$email)->select('channel_posts.name','channel_posts.caption','channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','channel_posts.created_at','users.profile_picture','channel_posts.email','channel_posts.id', 'channel_posts.postid', 'channel_posts.isReply')->latest('channel_posts.id')->take(10)->get();
      $store_all_post=array();
      foreach($channel_post as $post){
      $post_id=$post->postid;
      $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
      $post_like_collection=array();
      foreach($no_of_likes as $post_like){
        array_push($post_like_collection,$post_like);
      }
      $post_comments_collection=array();
      $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
      foreach($no_of_comments as $post_comment){
        array_push($post_comments_collection,$post_comment);
      }
      $post_shares_collection=array();
      $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
      foreach($no_of_shares as $shares_count){
        array_push($post_shares_collection,$shares_count);
      }
        array_push($store_all_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"id"=>$post->id, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
        "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
      ]);
      }
      return response([
        "reply"=>$store_all_post
      ]);
    }
   public function findChannelVideos($current_user) {
    // Get the users who are matched, excluding the current user
    $find_user_choice = user_match_choice::where('choice', '!=', $current_user)->pluck('choice');

    // Ensure we have users to work with
    if ($find_user_choice->isEmpty()) {
        return response([
            "reply" => []
        ]);
    }

    // Get the current user's location
    $current_user_location = User::where('email', $current_user)->value('location');

    // Fetch all channel posts from users you follow and in the same location
    $find_all_channels_of_who_user_follows_post = DB::table('channel_posts')
        ->join('users', 'channel_posts.email', '=', 'users.email')
        ->join('channels', 'channel_posts.email', '=', 'channels.channel_owner')
        ->where('channel_posts.video', '!=', '')
        ->whereIn('channel_posts.email', $find_user_choice)
        ->where('users.location', $current_user_location) // Filter by location
        ->select(
            'channel_posts.name',
            'channel_posts.caption',
            'channel_posts.created_at',
            'channel_posts.post_img1',
            'channel_posts.post_img2',
            'channel_posts.post_img3',
            'channel_posts.post_img4',
            'channel_posts.video',
            'channel_posts.postid',
            'channel_posts.isReply',
            'users.profile_picture',
            'users.first_name',
            'users.last_name',
            'channels.channel_bio',
            'channel_posts.email',
            'channel_posts.id'
        )
        ->latest()
        ->get();

    // Process the posts and aggregate likes, comments, and shares
    $store_all_post = $find_all_channels_of_who_user_follows_post->map(function ($post) {
        $post_id = $post->postid;

        // Retrieve counts in a single query per post
        $likesCount = PostLike::where('post_id', $post_id)->count();
        $commentsCount = Comment::where('post_id', $post_id)->count();
        $sharesCount = Shared_Post::where('prev_id', $post_id)->count();

        return [
            "name" => $post->name,
            "caption" => $post->caption,
            "created_at" => $post->created_at,
            "post_img1" => $post->post_img1,
            "post_img2" => $post->post_img2,
            "post_img3" => $post->post_img3,
            "post_img4" => $post->post_img4,
            "video" => $post->video,
            "first_name" => $post->first_name,
            "last_name" => $post->last_name,
            "channel_bio" => $post->channel_bio,
            "id" => $post->id,
            "postid" => $post_id,
            "isReply" => $post->isReply,
            "profile_picture" => $post->profile_picture,
            "email" => base64_encode($post->email),
            "likes" => $likesCount,
            "comments" => $commentsCount,
            "shares" => $sharesCount,
        ];
    });

    // Now, let's fetch posts from random users you do not follow, but with high likes
    $random_posts = DB::table('channel_posts')
        ->join('users', 'channel_posts.email', '=', 'users.email')
        ->join('channels', 'channel_posts.email', '=', 'channels.channel_owner')
        ->where('channel_posts.video', '!=', '')
        ->whereNotIn('channel_posts.email', $find_user_choice)  // Exclude users you follow
        ->where('users.location', $current_user_location)  // Filter by location
        ->select(
            'channel_posts.name',
            'channel_posts.caption',
            'channel_posts.created_at',
            'channel_posts.post_img1',
            'channel_posts.post_img2',
            'channel_posts.post_img3',
            'channel_posts.post_img4',
            'channel_posts.video',
            'channel_posts.postid',
            'channel_posts.isReply',
            'users.profile_picture',
            'users.first_name',
            'users.last_name',
            'channels.channel_bio',
            'channel_posts.email',
            'channel_posts.id'
        )
        ->orderByDesc(DB::raw('(SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = channel_posts.postid)'))  // Order by number of likes
        ->limit(5)  // Get the top 5 posts with the most likes
        ->get();

    // Process the random posts (similar to the previous ones)
    $store_random_posts = $random_posts->map(function ($post) {
        $post_id = $post->postid;

        // Retrieve counts in a single query per post
        $likesCount = PostLike::where('post_id', $post_id)->count();
        $commentsCount = Comment::where('post_id', $post_id)->count();
        $sharesCount = Shared_Post::where('prev_id', $post_id)->count();

        return [
            "name" => $post->name,
            "caption" => $post->caption,
            "created_at" => $post->created_at,
            "post_img1" => $post->post_img1,
            "post_img2" => $post->post_img2,
            "post_img3" => $post->post_img3,
            "post_img4" => $post->post_img4,
            "video" => $post->video,
            "first_name" => $post->first_name,
            "last_name" => $post->last_name,
            "channel_bio" => $post->channel_bio,
            "id" => $post->id,
            "postid" => $post_id,
            "isReply" => $post->isReply,
            "profile_picture" => $post->profile_picture,
            "email" => base64_encode($post->email),
            "likes" => $likesCount,
            "comments" => $commentsCount,
            "shares" => $sharesCount,
        ];
    });

    // Combine both arrays of posts
    $all_posts = $store_all_post->merge($store_random_posts);

    return response([
        "reply" => $all_posts
    ]);
}

    public function fetchAllChannelsPost($current_user){
      $find_user_choice= user_match_choice::where('user',$current_user)->get();
     $keep_all_persons_who_user_follow=array();
     foreach($find_user_choice as $person_who_user_follows){
      array_push($keep_all_persons_who_user_follow, $person_who_user_follows->choice);
     }
     $find_all_channels_of_who_user_follows_post=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->join('channels','channel_posts.email','=','channels.channel_owner')
     ->whereIn('channel_posts.email',$keep_all_persons_who_user_follow)->select('channel_posts.name','channel_posts.caption','channel_posts.created_at','channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','channel_posts.postid', 'channel_posts.isReply', 'users.profile_picture','users.first_name','users.last_name','channels.channel_bio',   'channel_posts.email','channel_posts.id')->take(10)->latest()->get();
     $store_all_post=array();
     foreach($find_all_channels_of_who_user_follows_post as $post){
      $post_id=$post->postid;
    $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
    $post_like_collection=array();
    foreach($no_of_likes as $post_like){
      array_push($post_like_collection,$post_like);
    }
    $post_comments_collection=array();
    $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
    foreach($no_of_comments as $post_comment){
      array_push($post_comments_collection,$post_comment);
    }
    $post_shares_collection=array();
    $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
    foreach($no_of_shares as $shares_count){
      array_push($post_shares_collection,$shares_count);
    }
      array_push($store_all_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"first_name"=>$post->first_name,"last_name"=>$post->last_name,"channel_bio"=>$post->channel_bio,"id"=>$post->id, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
      "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
    ]);
    }
      return response([
        "reply" => $store_all_post
      ]);
    }
    public function findCommunityDetails($community_id){
      $find_community_details=Community::where('communities.community_name',$community_id)->leftJoin('community_followers', 'communities.community_name', '=', 'community_followers.community_name')->
      select('communities.created_at','communities.community_name','communities.community_owner','communities.community_bio','communities.community_avatar','communities.community_cover','communities.community_category','communities.community_rules',DB::raw('COUNT(community_followers.community_member) as member_count'))->
      first();
      return response([
        "reply"=>$find_community_details
      ]);
    }
    public function fetchAllCommunityPost($current_user){
      $user_interest="Gaming";
      $find_user_choice= Community::where('community_category',$user_interest)->get();
     $keep_all_communities_with_user_interests=array();
     foreach($find_user_choice as $person_who_user_follows){
      array_push($keep_all_communities_with_user_interests, $person_who_user_follows->choice);
     }
     $find_all_communities_with_user_interests=DB::table('community_posts')->join('users','community_posts.email','=','users.email')->join('communities','community_posts.name','=','communities.community_name')
     ->whereIn('communities.community_category',$keep_all_communities_with_user_interests)->select('community_posts.name','community_posts.caption','community_posts.created_at','community_posts.post_img1','community_posts.post_img2','community_posts.post_img3','community_posts.post_img4','community_posts.video','community_posts.postid', 'community_posts.isReply', 'users.profile_picture','users.first_name','users.last_name', 'community_posts.email','communities.community_name')->take(20)->latest()->get();
     $store_all_post=array();
     foreach($find_all_communities_with_user_interests as $post){
      $post_id=$post->postid;
    $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
    $post_like_collection=array();
    foreach($no_of_likes as $post_like){
      array_push($post_like_collection,$post_like);
    }
    $post_comments_collection=array();
    $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
    foreach($no_of_comments as $post_comment){
      array_push($post_comments_collection,$post_comment);
    }
    $post_shares_collection=array();
    $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
    foreach($no_of_shares as $shares_count){
      array_push($post_shares_collection,$shares_count);
    }
      array_push($store_all_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"first_name"=>$post->first_name,"last_name"=>$post->last_name,"channel_bio"=>$post->channel_bio,"id"=>$post->id, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
      "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
    ]);
    }
      return response([
        "reply" => $store_all_post
      ]);
    }
    public function uploadTextStory($user_text,$user_bg_color,$email){
        $story_day=date('Y-m-d');
      Story::create([
        "user_email"=>$email,
        "user_image"=>"",
        "user_text"=>$user_text,
        "background"=>$user_bg_color,
        "user_video"=>"",
        "date_posted"=>$story_day
      ]);
      return response([
        "reply"=>"Success"
      ]);
    }
    public function findUserStory($user_email){
        $reply=Story::where('user_email',$user_email)->latest('id')->get();
      $hold_all_replies=array();
      foreach($reply as $story_post){
        array_push($hold_all_replies,["file"=>$story_post->user_image,"text"=>$story_post->user_text,"color"=>$story_post->background,"isVideoFile"=>$story_post->user_video]);
      }
      
      return response([
        "reply"=>$hold_all_replies,
        
      ]);
    }
    public function findRelatedPost($key){
        $tag="#".$key;
        $query = "
          (SELECT user__posts.id, user__posts.name, user__posts.caption, user__posts.created_at, user__posts.postid, user__posts.isReply, user__posts.email, NULL AS post_img1, NULL AS post_img2, NULL AS post_img3, NULL AS post_img4, NULL AS video, users.profile_picture 
         FROM user__posts 
         JOIN users ON user__posts.email = users.email 
         WHERE user__posts.caption REGEXP ?)
        
        UNION
        
        (SELECT channel_posts.id, channel_posts.name, channel_posts.caption, channel_posts.created_at, channel_posts.postid, channel_posts.isReply, channel_posts.email, channel_posts.post_img1, channel_posts.post_img2, channel_posts.post_img3, channel_posts.post_img4,channel_posts.video, users.profile_picture 
         FROM channel_posts 
         JOIN users ON channel_posts.email = users.email 
         WHERE channel_posts.caption REGEXP ?)
        
        
        ORDER BY id DESC
        LIMIT 30;
        ";
        $store_all_post=array();
        $find_related_post_in_db = DB::select($query, ["(?=.*($tag))", "(?=.*($tag))", "(?=.*($tag))"]);
        foreach($find_related_post_in_db as $post){
          $post_id=$post->postid;
        $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
        $post_like_collection=array();
        foreach($no_of_likes as $post_like){
          array_push($post_like_collection,$post_like);
        }
        $post_comments_collection=array();
        $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
        foreach($no_of_comments as $post_comment){
          array_push($post_comments_collection,$post_comment);
        }
        $post_shares_collection=array();
        $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
        foreach($no_of_shares as $shares_count){
          array_push($post_shares_collection,$shares_count);
        }
          array_push($store_all_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"id"=>$post->id, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
          "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
        ]);
        }
              return response([
                "reply"=>$store_all_post
              ]);
    }
    public function findUserReply($email){
      $find_all_shared_posts=DB::table('shared__posts')->join('users','shared__posts.email_of_user_who_shared','=','users.email')->where('shared__posts.email_of_user_who_shared',$email)->select('shared__posts.id','shared__posts.created_at','shared__posts.name','shared__posts.caption','shared__posts.email','shared__posts.post_img1','shared__posts.post_img2','shared__posts.post_img3','shared__posts.post_img4','shared__posts.video','shared__posts.quote','shared__posts.postid','shared__posts.prev_id','shared__posts.isReply','shared__posts.email_of_user_who_shared','shared__posts.name_of_user_who_shared','users.profile_picture')->latest()->take(10)->get();
            $store_all_post=array();
            foreach($find_all_shared_posts as $post){
              $post_id=$post->postid;
              $email_of_original_poster=$post->email;
            $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
            $post_like_collection=array();
            foreach($no_of_likes as $post_like){
              array_push($post_like_collection,$post_like);
            }
            $post_comments_collection=array();
            $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
            foreach($no_of_comments as $post_comment){
              array_push($post_comments_collection,$post_comment);
            }
            $post_shares_collection=array();
            $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
            foreach($no_of_shares as $shares_count){
              array_push($post_shares_collection,$shares_count);
            }
            $find_avatar_of_original_poster=DB::table('users')->where('email',$email_of_original_poster)->select('profile_picture')->first();
            array_push($store_all_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"name_of_user_who_shared"=>$post->name_of_user_who_shared,"email_of_user_who_shared"=>$post->email_of_user_who_shared,"quote"=>$post->quote,"prev_id"=>$post->prev_id, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
            "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection),"avatar_of_original_poster"=>$find_avatar_of_original_poster->profile_picture
          ]);
            }
            return response([
              "reply"=>$store_all_post
            ]);
  }
  public function disableUserComment($postid){
    $disable_comment=User_Post::where('postid',$postid)->update(['isReply'=>'false']);
    if($disable_comment){
      return response([
        "reply"=>$disable_comment
      ]);
    }
    else{
      $disable_comment=ChannelPost::where('postid',$postid)->update(['isReply'=>'false']);
      if($disable_comment){
        return response([
          "reply"=>$disable_comment
        ]);
      }else{
        $disable_comment=Shared_Post::where('postid',$postid)->update(['isReply'=>'false']);
        return response([
          "reply"=>$disable_comment
        ]);
      }
    }
  }
  public function findUserFollowersCount($email){
    $find_no_of_user_followers=DB::table('user_match_choices')->where('choice',$email)->get();
    $user_followers=array();
    foreach($find_no_of_user_followers as $followers){
      array_push($user_followers,$followers->choice);
    }
    return response([
      "followers"=>count($user_followers)
    ]);
  }
  public function bookmarkUserPost($user,$post_id){
    $check_if_user_already_bookmarked_post=DB::table('bookmarks')->where(["user_who_bookmark"=>$user,"bookmarked_post_id"=>$post_id])->first();
      if($check_if_user_already_bookmarked_post){
        Bookmark::where(["user_who_bookmark"=>$user, "bookmarked_post_id"=>$post_id])->delete();
        return response([
          "reply"=>"duplicate"
        ]);
      }else{
        Bookmark::create([
          "user_who_bookmark"=>$user,
          "bookmarked_post_id"=>$post_id
        ]);
        return response([
          "reply"=>"success"
        ]);
      }
  }
  public function findAllBookmarkedPost($current_user){
    $check_all_bookmarked_posts_by_user=Bookmark::where(['user_who_bookmark'=>$current_user])->get();
      $hold_all_bookmarked_posts_id=array();
      foreach($check_all_bookmarked_posts_by_user as $bookmark_posts){
        array_push($hold_all_bookmarked_posts_id, $bookmark_posts->bookmarked_post_id);
      }
      $show_all_bookmarked_posts=DB::table('user__posts')->join('users','user__posts.email','=','users.email')->select('user__posts.created_at','user__posts.name','user__posts.caption','user__posts.email','user__posts.postid', 'user__posts.isReply', 'users.profile_picture')->whereIn('user__posts.postid',$hold_all_bookmarked_posts_id)->latest()->get();
      $show_all_bookmarked_channel_posts=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->select('channel_posts.created_at','channel_posts.name','channel_posts.caption','channel_posts.email','channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','channel_posts.postid','channel_posts.isReply', 'users.first_name','users.last_name', 'users.profile_picture')->whereIn('channel_posts.postid',$hold_all_bookmarked_posts_id)->latest()->get();
      $show_all_bookmarked_shared_posts=DB::table('shared__posts')->join('users','shared__posts.email','=','users.email')->select('shared__posts.created_at','shared__posts.name','shared__posts.avatar','shared__posts.caption','shared__posts.email','shared__posts.post_img1','shared__posts.post_img2','shared__posts.post_img3','shared__posts.post_img4','shared__posts.video','shared__posts.quote','shared__posts.postid','shared__posts.prev_id','shared__posts.isReply', 'shared__posts.name_of_user_who_shared','shared__posts.email_of_user_who_shared','users.profile_picture')->whereIn('shared__posts.postid',$hold_all_bookmarked_posts_id)->latest()->get();
      $store_all_bookmarked_posts=array();
      foreach($show_all_bookmarked_posts as $post){
        $post_id=$post->postid;
      $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
      $post_like_collection=array();
      foreach($no_of_likes as $post_like){
        array_push($post_like_collection,$post_like);
      }
      $post_comments_collection=array();
      $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
      foreach($no_of_comments as $post_comment){
        array_push($post_comments_collection,$post_comment);
      }
      $post_shares_collection=array();
      $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
      foreach($no_of_shares as $shares_count){
        array_push($post_shares_collection,$shares_count);
      }
        array_push($store_all_bookmarked_posts,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
        "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
      ]);
      }
      $store_all_bookmarked_channels_post=array();
      foreach($show_all_bookmarked_channel_posts as $post){
       $post_id=$post->postid;
     $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
     $post_like_collection=array();
     foreach($no_of_likes as $post_like){
       array_push($post_like_collection,$post_like);
     }
     $post_comments_collection=array();
     $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
     foreach($no_of_comments as $post_comment){
       array_push($post_comments_collection,$post_comment);
     }
     $post_shares_collection=array();
     $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
     foreach($no_of_shares as $shares_count){
       array_push($post_shares_collection,$shares_count);
     }
       array_push($store_all_bookmarked_channels_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"first_name"=>$post->first_name,"last_name"=>$post->last_name, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
       "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
     ]);
     }
     $store_all_bookmarked_shared_posts=array();
     foreach($show_all_bookmarked_shared_posts as $post){
      $post_id=$post->postid;
    $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
    $post_like_collection=array();
    foreach($no_of_likes as $post_like){
      array_push($post_like_collection,$post_like);
    }
    $post_comments_collection=array();
    $no_of_comments=Comment::where(['post_id'=>$post_id])->get();
    foreach($no_of_comments as $post_comment){
      array_push($post_comments_collection,$post_comment);
    }
    $post_shares_collection=array();
    $no_of_shares=Shared_Post::where(['prev_id'=>$post_id])->get();
    foreach($no_of_shares as $shares_count){
      array_push($post_shares_collection,$shares_count);
    }
    array_push($store_all_bookmarked_shared_posts,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"name_of_user_who_shared"=>$post->name_of_user_who_shared,"email_of_user_who_shared"=>base64_encode($post->email_of_user_who_shared),"quote"=>$post->quote,"prev_id"=>$post->prev_id, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
    "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
  ]);
    }
      return response([
        "user_post_reply"=>$store_all_bookmarked_posts,
        "channel_post_reply"=>$store_all_bookmarked_channels_post,
        "shared_post_reply"=>$store_all_bookmarked_shared_posts

      ]);
  }
  public function fetchCommunityPosts($key,$community_name){
    $find_community_posts = DB::table('community_posts')
    ->join('users', 'community_posts.email', '=', 'users.email')
    ->leftJoin('post_likes', 'community_posts.postid', '=', 'post_likes.post_id')
    ->leftJoin('comments', 'community_posts.postid', '=', 'comments.post_id')
    ->leftJoin('shared__posts', 'community_posts.postid', '=', 'shared__posts.prev_id')
    ->where('community_posts.name', $community_name)
    ->select(
        'community_posts.name',
        'community_posts.caption',
        'community_posts.created_at',
        'community_posts.post_img1',
        'community_posts.post_img2',
        'community_posts.post_img3',
        'community_posts.post_img4',
        'community_posts.video',
        'community_posts.postid',
        'community_posts.isReply',
        'users.profile_picture',
        'users.first_name',
        'users.last_name',
        'community_posts.email',
        DB::raw('COUNT(DISTINCT post_likes.id) as like_count'),
        DB::raw('COUNT(DISTINCT comments.id) as comment_count'),
        DB::raw('COUNT(DISTINCT shared__posts.id) as share_count')
    )
    ->groupBy(
        'community_posts.name',
        'community_posts.caption',
        'community_posts.created_at',
        'community_posts.post_img1',
        'community_posts.post_img2',
        'community_posts.post_img3',
        'community_posts.post_img4',
        'community_posts.video',
        'community_posts.postid',
        'community_posts.isReply',
        'users.profile_picture',
        'users.first_name',
        'users.last_name',
        'community_posts.email'
    )
    ->orderBy('like_count', 'desc')  // Sort by the number of likes
    ->take(20)
    ->get();

$store_all_post = [];

foreach ($find_community_posts as $post) {
    array_push($store_all_post, [
        "name" => $post->name,
        "caption" => $post->caption,
        "created_at" => $post->created_at,
        "post_img1" => $post->post_img1,
        "post_img2" => $post->post_img2,
        "post_img3" => $post->post_img3,
        "post_img4" => $post->post_img4,
        "video" => $post->video,
        "first_name" => $post->first_name,
        "last_name" => $post->last_name,
        "postid" => $post->postid,
        "isReply" => $post->isReply,
        "profile_picture" => $post->profile_picture,
        "email" => base64_encode($post->email),
        "likes" => $post->like_count,
        "comments" => $post->comment_count,
        "shares" => $post->share_count,
    ]);
}

return response([
    "reply" => $store_all_post
]);
}
public function fetchRandomTopCommunityPost($last_top_post_date){
 $find_community_posts = DB::table('community_posts')
    ->join('users', 'community_posts.email', '=', 'users.email')
    ->leftJoin('post_likes', 'community_posts.postid', '=', 'post_likes.post_id')
    ->leftJoin('comments', 'community_posts.postid', '=', 'comments.post_id')
    ->leftJoin('shared__posts', 'community_posts.postid', '=', 'shared__posts.prev_id')
    ->where('community_posts.created_at', '<', $last_top_post_date)
    ->select(
        'community_posts.name',
        'community_posts.caption',
        'community_posts.created_at',
        'community_posts.post_img1',
        'community_posts.post_img2',
        'community_posts.post_img3',
        'community_posts.post_img4',
        'community_posts.video',
        'community_posts.postid',
        'community_posts.isReply',
        'users.profile_picture',
        'users.first_name',
        'users.last_name',
        'community_posts.email',
        DB::raw('COUNT(DISTINCT post_likes.id) as like_count'),
        DB::raw('COUNT(DISTINCT comments.id) as comment_count'),
        DB::raw('COUNT(DISTINCT shared__posts.id) as share_count')
    )
    ->groupBy(
        'community_posts.name',
        'community_posts.caption',
        'community_posts.created_at',
        'community_posts.post_img1',
        'community_posts.post_img2',
        'community_posts.post_img3',
        'community_posts.post_img4',
        'community_posts.video',
        'community_posts.postid',
        'community_posts.isReply',
        'users.profile_picture',
        'users.first_name',
        'users.last_name',
        'community_posts.email'
    )
    ->orderBy('like_count', 'desc')  // Sort by the number of likes first
    ->orderBy('created_at', 'desc')  // Sort by date (older posts first) as secondary condition
    ->take(20)
    ->get();

$store_all_post = [];

foreach ($find_community_posts as $post) {
    array_push($store_all_post, [
        "name" => $post->name,
        "caption" => $post->caption,
        "created_at" => $post->created_at,
        "post_img1" => $post->post_img1,
        "post_img2" => $post->post_img2,
        "post_img3" => $post->post_img3,
        "post_img4" => $post->post_img4,
        "video" => $post->video,
        "first_name" => $post->first_name,
        "last_name" => $post->last_name,
        "postid" => $post->postid,
        "isReply" => $post->isReply,
        "profile_picture" => $post->profile_picture,
        "email" => base64_encode($post->email),
        "likes" => $post->like_count,
        "comments" => $post->comment_count,
        "shares" => $post->share_count,
    ]);
}

return response([
    "reply" => $store_all_post,
]);
}
public function fetchLatestCommunityPosts($community_name){
  $find_community_posts = DB::table('community_posts')
  ->join('users', 'community_posts.email', '=', 'users.email')
  ->leftJoin('post_likes', 'community_posts.postid', '=', 'post_likes.post_id')
  ->leftJoin('comments', 'community_posts.postid', '=', 'comments.post_id')
  ->leftJoin('shared__posts', 'community_posts.postid', '=', 'shared__posts.prev_id')
  ->where('community_posts.name', $community_name)
  ->select(
      'community_posts.name',
      'community_posts.caption',
      'community_posts.created_at',
      'community_posts.post_img1',
      'community_posts.post_img2',
      'community_posts.post_img3',
      'community_posts.post_img4',
      'community_posts.video',
      'community_posts.postid',
      'community_posts.isReply',
      'users.profile_picture',
      'users.first_name',
      'users.last_name',
      'community_posts.email',
      DB::raw('COUNT(DISTINCT post_likes.id) as like_count'),
      DB::raw('COUNT(DISTINCT comments.id) as comment_count'),
      DB::raw('COUNT(DISTINCT shared__posts.id) as share_count')
  )
  ->groupBy(
      'community_posts.name',
      'community_posts.caption',
      'community_posts.created_at',
      'community_posts.post_img1',
      'community_posts.post_img2',
      'community_posts.post_img3',
      'community_posts.post_img4',
      'community_posts.video',
      'community_posts.postid',
      'community_posts.isReply',
      'users.profile_picture',
      'users.first_name',
      'users.last_name',
      'community_posts.email'
  )
  ->orderBy('community_posts.created_at', 'desc')  // Sort by the number of likes
  ->take(20)
  ->get();

$store_all_post = [];

foreach ($find_community_posts as $post) {
  array_push($store_all_post, [
      "name" => $post->name,
      "caption" => $post->caption,
      "created_at" => $post->created_at,
      "post_img1" => $post->post_img1,
      "post_img2" => $post->post_img2,
      "post_img3" => $post->post_img3,
      "post_img4" => $post->post_img4,
      "video" => $post->video,
      "first_name" => $post->first_name,
      "last_name" => $post->last_name,
      "postid" => $post->postid,
      "isReply" => $post->isReply,
      "profile_picture" => $post->profile_picture,
      "email" => base64_encode($post->email),
      "likes" => $post->like_count,
      "comments" => $post->comment_count,
      "shares" => $post->share_count,
  ]);
}

return response([
  "reply" => $store_all_post
]);
}
public function fetchRandomLatestCommunityPosts($last_top_post_date){
  $find_community_posts = DB::table('community_posts')
  ->join('users', 'community_posts.email', '=', 'users.email')
  ->leftJoin('post_likes', 'community_posts.postid', '=', 'post_likes.post_id')
  ->leftJoin('comments', 'community_posts.postid', '=', 'comments.post_id')
  ->leftJoin('shared__posts', 'community_posts.postid', '=', 'shared__posts.prev_id')
  ->where('community_posts.created_at', '<', $last_top_post_date)
  ->select(
      'community_posts.name',
      'community_posts.caption',
      'community_posts.created_at',
      'community_posts.post_img1',
      'community_posts.post_img2',
      'community_posts.post_img3',
      'community_posts.post_img4',
      'community_posts.video',
      'community_posts.postid',
      'community_posts.isReply',
      'users.profile_picture',
      'users.first_name',
      'users.last_name',
      'community_posts.email',
      DB::raw('COUNT(DISTINCT post_likes.id) as like_count'),
      DB::raw('COUNT(DISTINCT comments.id) as comment_count'),
      DB::raw('COUNT(DISTINCT shared__posts.id) as share_count')
  )
  ->groupBy(
      'community_posts.name',
      'community_posts.caption',
      'community_posts.created_at',
      'community_posts.post_img1',
      'community_posts.post_img2',
      'community_posts.post_img3',
      'community_posts.post_img4',
      'community_posts.video',
      'community_posts.postid',
      'community_posts.isReply',
      'users.profile_picture',
      'users.first_name',
      'users.last_name',
      'community_posts.email'
  )
  ->orderBy('like_count', 'desc')  // Sort by the number of likes first
  ->orderBy('created_at', 'desc')  // Sort by date (older posts first) as secondary condition
  ->take(20)
  ->get();

$store_all_post = [];

foreach ($find_community_posts as $post) {
  array_push($store_all_post, [
      "name" => $post->name,
      "caption" => $post->caption,
      "created_at" => $post->created_at,
      "post_img1" => $post->post_img1,
      "post_img2" => $post->post_img2,
      "post_img3" => $post->post_img3,
      "post_img4" => $post->post_img4,
      "video" => $post->video,
      "first_name" => $post->first_name,
      "last_name" => $post->last_name,
      "postid" => $post->postid,
      "isReply" => $post->isReply,
      "profile_picture" => $post->profile_picture,
      "email" => base64_encode($post->email),
      "likes" => $post->like_count,
      "comments" => $post->comment_count,
      "shares" => $post->share_count,
  ]);
}

return response([
  "reply" => $store_all_post,
]);
}
  public function customiseUserProfile($customize_option,$email){
    switch ($customize_option) {
      case 'isPrivateOn':
        $update_user_preference=User::where(['email'=> $email])->update(['isPrivate'=>'true']);
        break;
      case 'isPrivateOff':
        $update_user_preference=User::where(['email'=> $email])->update(['isPrivate'=>'false']);
          break;
      case 'isSoundOn':
        $update_user_preference=User::where(['email'=> $email])->update(['isSound'=>'true']);
            break;
      case 'isSoundOff':
        $update_user_preference=User::where(['email'=> $email])->update(['isSound'=>'false']);
              break;
      case 'isDarkModeOn':
        $update_user_preference=User::where(['email'=> $email])->update(['isColor'=>'dark']);
                break;
      case 'isDarkModeOff':
        $update_user_preference=User::where(['email'=> $email])->update(['isColor'=>'light']);
          break;
      default:
      return;
        break;

    }
  }
  public function setFollowRequest($current_user,$user_who_is_to_be_followed){
    $create_follow_request=user_match_choice::create([
      "user"=>$current_user,
      "choice"=>$user_who_is_to_be_followed,
      "isPending"=>"true",
    ]);
  }
}