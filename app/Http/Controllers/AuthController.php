<?php
namespace App\Http\Controllers;
date_default_timezone_set('Africa/Lagos');
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\user_match_choice;
use App\Models\Notifications;
use App\Models\Channel;
use App\Models\Community;
use App\Models\CommunityPost;
use App\Models\ChannelPost;
use App\Models\User_Post;
use App\Models\Messages;
use App\Models\Comment;
use App\Models\Story;
use App\Models\PostLike;
use App\Models\Comment_Reply;
use App\Models\Shared_Post;
use App\Models\Blocked_List;
use App\Models\Bookmark;
use App\Models\Follow_Request;
use App\Models\CommunityFollower;
use App\Notifications\ResetPassword;
use Pusher\Pusher;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WelcomeNotification;
use DateTime;
use App\Services\UserServices;
use App\Services\ConverSationServices;
use App\Services\UserActivityServices;
use App\Services\CloudinaryServices;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;


header('Access-Control-Allow-Origin','*');
header('Access-Control-Allow-Methods','GET,POST,PUT,PATCH,DELETE,OPTIONS');
header('Access-Control-Allow-Headers','Content-Type, Authorization');
class AuthController extends Controller
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
    public function Signup(Request $request){
        $data=$request->validate([
            'email' => 'required|email|unique:users',
            'password' => [
              'required',
              Password::min(6)
            ]

        ]);
      $user=$this->userService->register($data);


      if(!$user){
        return response([
          'error' => 'Email already choosen by a user'
        ]);
      }else{
        $user=$data['email'];
        $reg_date=date('Y');
        $random=rand(10,1000000);
        $token=$reg_date.$random;
        $this->sendMail($user);
        return response([
          'user' => $user,
          'token' => $token
        ]);
        
        
      }
    }
    public function sendMail($email){
      $recipient=User::where('email',$email)->first();
      $greeting=[
        'welcome_text'=>"Welcome to the Hexarex App, $email Start meeting new people and contribute to discussions
         don't forget we created Hexarex to get back to real life ensure you make impactful and useful comments",
         'body' => "Get started by completing your profile and click on the match session to meet new people around you",
         'call_to_action'=>'Ensure you read our terms correctly so you do not go against our community standards..',
         'url' => url('https://hexarex.com/terms'),
         'conclusion' => "Thank you for Registering",
         'id'  => 1
      ];
      Notification::send($recipient, new WelcomeNotification($greeting));
    }
    public function Login(Request $request){
      $credentials= $request->validate([
        'email' => 'required|email|string|exists:users,email',
        'password' => [
          'required',
        ],
        'remember' => 'boolean',
       
      
      ]);
     $remember= $credentials['remember'] ?? false;
     unset($credentials['remember']);
     if(!Auth::attempt($credentials,$remember)){
      return response([
        'error' => 'The Provided Credentials are Not Correct'
      ], 422);
     }else{
      $user=Auth::user()->email;
     $reg_date=date('Y');
     $random=rand(10,1000000);
     $token=$reg_date.$random;
     return response([
      'user' => $user,
      'token' => $token
     ]);
     }
     
    }
    public function checkIfUserHasPaid(Request $request){
     $user_mail = $request->input('data');
     $check_user_payment_status=$this->userService->checkIfUserHasPaid($user_mail);
     return $check_user_payment_status;
     }
    public function checkIfUserHasCompleteProfile(Request $request) {
      $email=$request->input('email');
     $check_complete_profile= $this->userService->checkCompleteProfile($email);
     return $check_complete_profile;
    }
    public function updateUserPicture(Request $request){
     $request->validate([
        'profile_pic' => 'required|image|mimes:jpeg,jpg,png,JPG,JPEG,PNG,gif|max:500000'

      ]);
      $owner=$request->input('owner'); 
      $file=$request->file('profile_pic');
     // $image_path=$request->file('profile_pic')->store('users_pic','public');
      $this->cloudinaryService->updateProfilePicture($file,$owner);
      return response([
        'update' => "Profile Picture Updated Sucessfully"
      ]);
    }
    public function updateCoverPhoto(Request $request){
      $request->validate([
         'coverPhoto' => 'required|image|mimes:jpeg,jpg,png,JPG,JPEG,PNG,gif|max:2048'
 
       ]); 
       $filePath=$request->file('coverPhoto');
       //$image_path=$request->file('coverPhoto')->store('cover_photo','public');
       
       $upload=  Cloudinary::upload($filePath->getRealPath(), [
        'folder' => 'cover_photo', // Optional: specify a folder
    ]);
    $image_path=$upload->getPublicId();
       $owner=$request->input('owner');
       $check_if_user_has_picture=User::where('email',$owner)->first();
       if($check_if_user_has_picture['coverPhoto'] != null){
        $cover_photo_array=[$check_if_user_has_picture['coverPhoto']];
        $this->delete($cover_photo_array);
        // $old_cover_photo="storage/".$check_if_user_has_picture['coverPhoto'];
        // File::delete(public_path($old_cover_photo));
       }
       User::where('email',$owner)->update([
         'coverPhoto'  =>$image_path,
   
       ]);
       return response([
         'update' => "Cover Photo Updated Sucessfully"
       ]);
     }
     public function updateCoverText(Request $request){
      $email=$request->input('email');
      $cover_text=$request->input('cover_text');
      $update_cover_text=$this->userService->updateCoverText($email,$cover_text);
      return $update_cover_text;
     }
    public function updateUserProfile(Request $request){
    $first_name=$request->input('data.firstname');
    $last_name=$request->input('data.lastname');
    $gender=$request->input('data.gender');
    $state=$request->input('data.state');
    $birthday=$request->input('data.birthday');
    $phone=$request->input('data.phone');
    $email=$request->input('data.email');
    $school=$request->input('data.school');
    $interest=$request->input('data.interest');
    $religion=$request->input('data.religion');
    $current_year=(int)date('Y');
    $extract_year_of_birth_from_birthday=new DateTime($birthday);
    $user_year_of_birth=(int)$extract_year_of_birth_from_birthday->format('Y');
    $check_valid_date_of_birth=$current_year-$user_year_of_birth;
    if($check_valid_date_of_birth < 16){
        return response([
          "success" => "You are not eligible to be on this app due to age restriction",
        ]);
    }else{
      $data=[
        'first_name'  =>$first_name,
        'last_name'   =>$last_name,
        'orientation' =>$gender,
        'location'    =>$state,
        'birthday'    =>$birthday,
        'phone_number'=>$phone,
        'school'=>      $school,
        'interest'=>    $interest,
        'religion'=>    $religion,
        
      ];
      $this->userService->updateUserProfile($email,$data);
        return response([
          "success" => "Updated User Profile Successfully",
        ]);
    }
  
    }

    
    public function findUserMatch(Request $request){
      $email=$request->input('data');
      $find_user_match= $this->userService->findUserMatch($email);
      return $find_user_match;
    }
   
    public function setUserMatch(Request $request){
      $user=$request->input('user');
      $choice=$request->input('choice');
      $set_user_match=$this->userService->setUserMatch($user,$choice);
      return $set_user_match;
    }
    public function unMatchUser(Request $request){
      $user=$request->input('user');
      $find_user_details=User::where('email',$user)->first();
      $sender=$find_user_details['first_name'];
    
      $choice=$request->input('choice');
      user_match_choice::where(["user"=>$user, "choice"=>$choice])->delete();
      $this->userService->notifyUser($user,$choice,"$sender unfollowed you..","match");
      return response([
        "success" => "You have unfollowed\t".$choice.""
      ]);
    }
    public function createUserPost(Request $request){
      $email=$request->input('email');
      $user_caption=$request->input('user_caption');
      $tagged_users=$request->input('tagged_users');
     $create_user_post= $this->userActivityService->createUserPost($email,$user_caption,$tagged_users);
     return $create_user_post;
    }
    
    public function fetchUserPost(Request $request){
      $email=$request->input('email');
     $fetch_user_post= $this->userActivityService->fetchUserPost($email);
     return $fetch_user_post;
    }
    
    public function fetchNewPost(Request $request){
      $email=$request->input('email');
      $fetch_new_post=$this->userActivityService->fetchNewPost($email);
      return $fetch_new_post;
    }
    public function uploadStory(Request $request){
      $request->validate([
        'story_post' => 'mimes:jpeg,jpg,png,mp4,JPG,JPEG,PNG,MP4,gif|max:500000'

      ]); 
      $email=$request->input('email');
      $file=$request->file('story_post');
      ini_set('max_execution_time', 500);
      $this->cloudinaryService->uploadStory($file,$email);
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
    public function Delete_All_Old_User_Stories(){
      $file_date=date('Y-m-d');
      $current_time_in_24hrs_format=date('Y-m-d H:i:s');
      $time_24hrs_ago = date('Y-m-d H:i:s', strtotime('-24 hours', strtotime($current_time_in_24hrs_format)));
      Story::where('created_at','<',$time_24hrs_ago)->delete();
      $fetch_all_old_story_files=Story::where('date_posted','<',$file_date)->where('user_image','!=','')->get();
      $all_files_array=array();
      foreach($fetch_all_old_story_files as $old_story_files){
       // $old_story="storage/".$old_story_files->user_image;
       //$old_story=$old_story_files->user_image;
      $result= Cloudinary::destroy($old_story_files->user_image);
       if (isset($result['result']) && $result['result'] == 'ok') {
        // Deletion as a normal file was successful
        return response()->json(['message' => 'Image deleted successfully.'], 200);
    } else {
        // If deletion as a normal file fails, attempt to delete as a video
        $videoResult = Cloudinary::destroy($old_story_files->user_image, [
            'resource_type' => 'video'
        ]);
     //  array_push($all_files_array,$old_story_files->user_image);
      }
    }
      return response([
        "reply"=>"Deletion Successfully Completed"
      ]);
      
    }
    public function findUserFriend(Request $request){
      $email=$request->input('email');
      $find_current_user_mutual=user_match_choice::where('user',$email)->get();
      $hold_user_mutual_friends=array();
      foreach($find_current_user_mutual as $user_match){
       $owner_friend=$user_match->choice;
       array_push($hold_user_mutual_friends,$owner_friend);
      }
      
     $reply= Story::whereIn('user_email',$hold_user_mutual_friends)->latest()->get();
     $hold_all_friends_who_posted=array();
     foreach($reply as $user_friends){
      array_push($hold_all_friends_who_posted,$user_friends->user_email);
     }
     $all_friends_list_sorted=array_unique($hold_all_friends_who_posted);
     $clean_sorted_friends_list=array();
     foreach($all_friends_list_sorted as $key => $value){
      array_push($clean_sorted_friends_list,$value);
     }
     $find_user_friends_picture=User::whereIn('email',$clean_sorted_friends_list)->get();
     $user_friends_details_collection=array();
     foreach($find_user_friends_picture as $get_info){
     array_push($user_friends_details_collection,["email"=>$get_info->email,"picture"=>$get_info->profile_picture,"first_name"=>$get_info->first_name,"last_name"=>$get_info->last_name]);
     }
     $number_of_friends_story=count($user_friends_details_collection);
     if($number_of_friends_story > 1){
        $paginate="true";
        
     }else{
        $paginate="false";
     }
     return response([
      "reply"=>$user_friends_details_collection,
      "pagination"=>$paginate,
      "story_count"=>$number_of_friends_story
     ]);
    }
    public function fetchAllPost(Request $request){
      $user=$request->input('email');
      $fetch_user_friends=user_match_choice::where('user',$user)->get();
      $hold_all_user_friends=array();
      foreach($fetch_user_friends as $get_friend){
        array_push($hold_all_user_friends,$get_friend->choice);
      }
      $find_all_user_friend_post=DB::table('user__posts')->join('users','user__posts.email','=','users.email')->whereIn('user__posts.email',$hold_all_user_friends)->select('user__posts.name','user__posts.caption', 'user__posts.postid','user__posts.created_at','user__posts.isReply', 'users.profile_picture','users.email')->latest('user__posts.created_at')->take(5)->get();
      $store_all_post=array();
      foreach($find_all_user_friend_post as $post){
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
        array_push($store_all_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
        "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
      ]);
      }
      return response([
        "reply" => $store_all_post
      ]);
    }
    public function fetchNewChannelPost(Request $request){
      $user=$request->input('email');
      $fetch_user_friends=user_match_choice::where('user',$user)->get();
      $hold_all_user_friends=array();
      $store_all_channel_post=array();
      foreach($fetch_user_friends as $get_friend){
        array_push($hold_all_user_friends,$get_friend->choice);
      }
      $get_channel_post=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->whereIn('channel_posts.email',$hold_all_user_friends)->select('channel_posts.name','channel_posts.caption','channel_posts.postid', 'channel_posts.isReply', 'channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','channel_posts.created_at','users.profile_picture','channel_posts.email','channel_posts.id','users.first_name','users.last_name')->inRandomOrder()->orderBy('channel_posts.created_at','ASC','DESC')->take(30)->get();
      foreach($get_channel_post as $new_post){
        $post_id=$new_post->postid;
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
          array_push($store_all_channel_post,["name"=>$new_post->name, "first_name"=>$new_post->first_name, "last_name"=>$new_post->last_name,  "caption"=>$new_post->caption,"date"=>$new_post->created_at, "postid"=>$new_post->postid, "isReply"=>$new_post->isReply,   "avatar"=>$new_post->profile_picture, 
          "email"=>base64_encode($new_post->email),"img_1"=>$new_post->post_img1,"img_2"=>$new_post->post_img2,"img_3"=>$new_post->post_img3,"img_4"=>$new_post->post_img4,"video"=>$new_post->video,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection),"shares"=>count($post_shares_collection)
        ]);
        return response([
          "channel_reply"=> $store_all_channel_post
        ]);
    }
  }
    public function fetchRandomPost(Request $request){
      $user=$request->input('email');
      $fetch_user_friends=user_match_choice::where('user',$user)->get();
      $hold_all_user_friends=array();
      foreach($fetch_user_friends as $get_friend){
        array_push($hold_all_user_friends,$get_friend->choice);
      }
      $get_post=DB::table('user__posts')->join('users','user__posts.email','=','users.email')->whereIn('user__posts.email',$hold_all_user_friends)->select('user__posts.name','user__posts.caption', 'user__posts.postid', 'user__posts.isReply', 'user__posts.created_at','users.profile_picture','user__posts.email')->inRandomOrder()->orderBy('user__posts.created_at','ASC','DESC')->take(10)->get();
      $store_all_post=array();
      foreach($get_post as $new_post){
      $post_id=$new_post->postid;
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
        array_push($store_all_post,["name"=>$new_post->name,  "caption"=>$new_post->caption,"date"=>$new_post->created_at, "postid"=>$new_post->postid, "isReply"=>$new_post->isReply,   "avatar"=>$new_post->profile_picture, 
        "email"=>base64_encode($new_post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
      ]);
      }
      return response([
        "reply" => $store_all_post,
      ]);
    }
    public function fetchNewSharedPost(Request $request){
      $user=$request->input('email');
      $fetch_user_friends=user_match_choice::where('user',$user)->get();
      $hold_all_user_friends=array();
      foreach($fetch_user_friends as $get_friend){
        array_push($hold_all_user_friends,$get_friend->choice);
      }
      $get_post=DB::table('shared__posts')->join('users','shared__posts.email_of_user_who_shared','=','users.email')
      ->whereIn('shared__posts.email_of_user_who_shared',$hold_all_user_friends)->select('shared__posts.name','shared__posts.caption','shared__posts.created_at','shared__posts.post_img1','shared__posts.post_img2','shared__posts.post_img3','shared__posts.post_img4','shared__posts.video','shared__posts.quote','shared__posts.postid','shared__posts.prev_id','shared__posts.isReply', 'shared__posts.name_of_user_who_shared','shared__posts.email','shared__posts.email_of_user_who_shared','users.profile_picture')->inRandomOrder()->orderBy('shared__posts.created_at','ASC','DESC')->take(10)->get();
      $store_all_post=array();
      foreach($get_post as $new_post){
      $post_id=$new_post->postid;
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
      $get_prev_owner_picture=DB::table('users')->select('profile_picture')->where('email',$new_post->email_of_user_who_shared)->first();
        array_push($store_all_post,["name"=>$new_post->name,  "caption"=>$new_post->caption,"created_at"=>$new_post->created_at, "postid"=>$new_post->postid, "isReply"=>$new_post->isReply,   "profile_picture"=>$new_post->profile_picture, 
        "email"=>base64_encode($new_post->email), "email_of_user_who_shared"=>base64_encode($new_post->email_of_user_who_shared),"name_of_user_who_shared"=>$new_post->name_of_user_who_shared,"prev_id"=>$new_post->prev_id,"post_img1"=>$new_post->post_img1, "post_img2"=>$new_post->post_img2, "post_img3"=>$new_post->post_img3, "post_img4"=>$new_post->post_img4, "quote"=>$new_post->quote, "video"=>$new_post->video, "avatar_of_original_poster"=>$get_prev_owner_picture->profile_picture,   "likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
      ]);
      }
      return response([
        "reply" => $store_all_post,
      ]);
    }
     

      
   
    public function createChannel(Request $request){
      
      if(is_numeric($request->input('channel_name')) || is_numeric($request->input('channel_bio'))){
        
        $numeric_error="Please input a name within A-Z";
        return response([
          "number_error" => $numeric_error
        ]);
      }else{
        $email=$request->input('channel_owner');
        $channel_name=$request->validate([
          'channel_owner' => 'unique:channels',
          'channel_name' => 'unique:channels'
        ]);
        $channel_bio=$request->input('channel_bio');
        $channel_category=$request->input('channel_category');
        Channel::create([
          'channel_name'      =>$channel_name['channel_name'],
          'channel_owner'     =>$email,
          'channel_bio'       =>$channel_bio,
          'channel_category'  =>$channel_category
        ]);
        return response([
          "reply" => "Successfull",
        ]);
      }
      
    }
    public function createCommunity(Request $request){
      if(is_numeric($request->input('community_name')) || is_numeric($request->input('community_bio'))){
        $numeric_error="Please input a name within A-Z";
        return response([
          "number_error" => $numeric_error
        ]);
      }else{
        $email=$request->input('community_owner');
        $community_name=$request->validate([
          'community_name' => 'unique:communities'
        ]);
        $community_bio=$request->input('community_bio');
        $community_category=$request->input('community_category');
        Community::create([
          'community_name'      =>$community_name['community_name'],
          'community_owner'     =>$email,
          'community_bio'       =>$community_bio,
          'community_category'  =>$community_category
        ]);
        return response([
          "reply" => "Successfull",
        ]);
      }
      
    }
    public function checkIfUserHasChannel(Request $request){
      $email=$request->input('email');
      $check_if_user_has_channel=Channel::where('channel_owner',$email)->first();
      $find_number_of_subcribers=DB::table('user_match_choices')->select(DB::raw('COUNT(choice) as subscribers'))->where('choice',$email)->first();
      if($check_if_user_has_channel){
        return response([
          'reply'=> "true",
          'channel_data' =>$check_if_user_has_channel,
          'subscribers' => $find_number_of_subcribers->subscribers
        ]);
      }else{
        return response([
          'reply'=> "false"
        ]);
      }
      
    }
    public function createUserChannelPost(Request $request){
      $email=$request->input('email');
      $name=$request->input('name');
      $avatar=$request->input('avatar');
      $caption=$request->input('caption');
      $image_1=$request->file('image1');
      $image_2=$request->file('image2');
      $image_3=$request->file('image3');
      $image_4=$request->file('image4');
      $post_id=rand(10,100000000) . date('d');
      if(empty($request->file('image1')) && empty($request->file('image2')) && empty($request->file('image3')) && empty($request->file('image4'))){
        $create_post=ChannelPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "postid"  => $post_id
  
        ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && empty($request->file('image2')) && empty($request->file('image3')) && empty($request->file('image4'))){
       // $image_path=$request->file('image1')->store('users_posts','public');
       $filePath=$request->file('image1');
        $upload=  Cloudinary::upload($filePath->getRealPath(), [
          'folder' => 'users_posts', // Optional: specify a folder
      ]);
      $pictureId=$upload->getPublicId();
        $create_post=ChannelPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $pictureId,
          "postid"  => $post_id
         
  
        ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && !empty($request->file('image2')) && empty($request->file('image3')) && empty($request->file('image4'))){
       // $image_path=$request->file('image1')->store('users_posts','public');
       // $image_path2=$request->file('image2')->store('users_posts','public');
       $publicIds = [
        'post_img1' => null,
        'post_img2' => null, 
        ];

    // List of file input names
    $fileInputs = ['image1', 'image2'];

    // Iterate through each file input and upload if present
    foreach ($fileInputs as $index => $fileInput) {
        if ($request->hasFile($fileInput)) {
            $filePath = $request->file($fileInput);
            $upload = Cloudinary::upload($filePath->getRealPath(), [
                'folder' => 'users_posts',
            ]);
            // Store the public ID in the corresponding key in the publicIds array
            $publicIds['post_img' . ($index + 1)] = $upload->getPublicId();
        }
    }
      $create_post=ChannelPost::create([
        "email" => $email,
        "name"  => $name,
        "avatar" => $avatar,
        "caption" => $caption,
        "post_img1" => $publicIds['post_img1'],
        "post_img2" => $publicIds['post_img2'],
        "postid"  => $post_id


      ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && !empty($request->file('image2')) && !empty($request->file('image3')) && empty($request->file('image4'))){
        /*$image_path=$request->file('image1')->store('users_posts','public');
        $image_path2=$request->file('image2')->store('users_posts','public');
        $image_path3=$request->file('image3')->store('users_posts','public');**/
        $publicIds = [
          'post_img1' => null,
          'post_img2' => null,
          'post_img3' => null,
          
      ];

      // List of file input names
      $fileInputs = ['image1', 'image2', 'image3'];

      // Iterate through each file input and upload if present
      foreach ($fileInputs as $index => $fileInput) {
          if ($request->hasFile($fileInput)) {
              $filePath = $request->file($fileInput);
              $upload = Cloudinary::upload($filePath->getRealPath(), [
                  'folder' => 'users_posts',
              ]);
              // Store the public ID in the corresponding key in the publicIds array
              $publicIds['post_img' . ($index + 1)] = $upload->getPublicId();
          }
      }
        $create_post=ChannelPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $publicIds['post_img1'],
          "post_img2" => $publicIds['post_img2'],
          "post_img3" => $publicIds['post_img3'],
          "postid"  => $post_id

  
        ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && !empty($request->file('image2')) && !empty($request->file('image3')) && !empty($request->file('image4'))){
      /*  $image_path=$request->file('image1')->store('users_posts','public');
        $image_path2=$request->file('image2')->store('users_posts','public');
        $image_path3=$request->file('image3')->store('users_posts','public');
        $image_path4=$request->file('image4')->store('users_posts','public'); **/
        $publicIds = [
          'post_img1' => null,
          'post_img2' => null,
          'post_img3' => null,
          'post_img4' => null,
      ];

      // List of file input names
      $fileInputs = ['image1', 'image2', 'image3', 'image4'];

      // Iterate through each file input and upload if present
      foreach ($fileInputs as $index => $fileInput) {
          if ($request->hasFile($fileInput)) {
              $filePath = $request->file($fileInput);
              $upload = Cloudinary::upload($filePath->getRealPath(), [
                  'folder' => 'users_posts',
              ]);
              // Store the public ID in the corresponding key in the publicIds array
              $publicIds['post_img' . ($index + 1)] = $upload->getPublicId();
          }
      }

      // Other variables
      $email = $request->input('email');
      $name = $request->input('name');
      $avatar = $request->input('avatar');
      $caption = $request->input('caption');
      $post_id = $request->input('postid');

      // Create the post in the database
      $create_post = ChannelPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $publicIds['post_img1'],
          "post_img2" => $publicIds['post_img2'],
          "post_img3" => $publicIds['post_img3'],
          "post_img4" => $publicIds['post_img4'],
          "postid"  => $post_id
      ]);

        return response([
          "reply"=>$create_post
        ]);
      }
      
    }
    public function createCommunityPost(Request $request){
      $email=$request->input('email');
      $name=$request->input('name');
      $avatar=$request->input('avatar');
      $caption=$request->input('caption');
      $image_1=$request->file('image1');
      $image_2=$request->file('image2');
      $image_3=$request->file('image3');
      $image_4=$request->file('image4');
      $post_id=rand(10,100000000) . date('d');
      if(empty($request->file('image1')) && empty($request->file('image2')) && empty($request->file('image3')) && empty($request->file('image4'))){
        $create_post=CommunityPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "postid"  => $post_id
  
        ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && empty($request->file('image2')) && empty($request->file('image3')) && empty($request->file('image4'))){
       // $image_path=$request->file('image1')->store('users_posts','public');
       $filePath=$request->file('image1');
        $upload=  Cloudinary::upload($filePath->getRealPath(), [
          'folder' => 'users_posts', // Optional: specify a folder
      ]);
      $pictureId=$upload->getPublicId();
        $create_post=CommunityPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $pictureId,
          "postid"  => $post_id
         
  
        ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && !empty($request->file('image2')) && empty($request->file('image3')) && empty($request->file('image4'))){
       // $image_path=$request->file('image1')->store('users_posts','public');
       // $image_path2=$request->file('image2')->store('users_posts','public');
       $publicIds = [
        'post_img1' => null,
        'post_img2' => null, 
        ];

    // List of file input names
    $fileInputs = ['image1', 'image2'];

    // Iterate through each file input and upload if present
    foreach ($fileInputs as $index => $fileInput) {
        if ($request->hasFile($fileInput)) {
            $filePath = $request->file($fileInput);
            $upload = Cloudinary::upload($filePath->getRealPath(), [
                'folder' => 'users_posts',
            ]);
            // Store the public ID in the corresponding key in the publicIds array
            $publicIds['post_img' . ($index + 1)] = $upload->getPublicId();
        }
    }
      $create_post=CommunityPost::create([
        "email" => $email,
        "name"  => $name,
        "avatar" => $avatar,
        "caption" => $caption,
        "post_img1" => $publicIds['post_img1'],
        "post_img2" => $publicIds['post_img2'],
        "postid"  => $post_id


      ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && !empty($request->file('image2')) && !empty($request->file('image3')) && empty($request->file('image4'))){
        /*$image_path=$request->file('image1')->store('users_posts','public');
        $image_path2=$request->file('image2')->store('users_posts','public');
        $image_path3=$request->file('image3')->store('users_posts','public');**/
        $publicIds = [
          'post_img1' => null,
          'post_img2' => null,
          'post_img3' => null,
          
      ];

      // List of file input names
      $fileInputs = ['image1', 'image2', 'image3'];

      // Iterate through each file input and upload if present
      foreach ($fileInputs as $index => $fileInput) {
          if ($request->hasFile($fileInput)) {
              $filePath = $request->file($fileInput);
              $upload = Cloudinary::upload($filePath->getRealPath(), [
                  'folder' => 'users_posts',
              ]);
              // Store the public ID in the corresponding key in the publicIds array
              $publicIds['post_img' . ($index + 1)] = $upload->getPublicId();
          }
      }
        $create_post=CommunityPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $publicIds['post_img1'],
          "post_img2" => $publicIds['post_img2'],
          "post_img3" => $publicIds['post_img3'],
          "postid"  => $post_id

  
        ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && !empty($request->file('image2')) && !empty($request->file('image3')) && !empty($request->file('image4'))){
      /*  $image_path=$request->file('image1')->store('users_posts','public');
        $image_path2=$request->file('image2')->store('users_posts','public');
        $image_path3=$request->file('image3')->store('users_posts','public');
        $image_path4=$request->file('image4')->store('users_posts','public'); **/
        $publicIds = [
          'post_img1' => null,
          'post_img2' => null,
          'post_img3' => null,
          'post_img4' => null,
      ];

      // List of file input names
      $fileInputs = ['image1', 'image2', 'image3', 'image4'];

      // Iterate through each file input and upload if present
      foreach ($fileInputs as $index => $fileInput) {
          if ($request->hasFile($fileInput)) {
              $filePath = $request->file($fileInput);
              $upload = Cloudinary::upload($filePath->getRealPath(), [
                  'folder' => 'users_posts',
              ]);
              // Store the public ID in the corresponding key in the publicIds array
              $publicIds['post_img' . ($index + 1)] = $upload->getPublicId();
          }
      }

      // Other variables
      $email = $request->input('email');
      $name = $request->input('name');
      $avatar = $request->input('avatar');
      $caption = $request->input('caption');
      $post_id = $request->input('postid');

      // Create the post in the database
      $create_post = CommunityPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $publicIds['post_img1'],
          "post_img2" => $publicIds['post_img2'],
          "post_img3" => $publicIds['post_img3'],
          "post_img4" => $publicIds['post_img4'],
          "postid"  => $post_id
      ]);

        return response([
          "reply"=>$create_post
        ]);
      }
      
    }
   
    public function findChannelPost(Request $request){
      $email=$request->input('email');
      $find_channel_post=$this->userActivityService->findChannelPost($email);
      return $find_channel_post;
    }
    public function deleteUserPost(Request $request){
      $postid=$request->input('postid');
      $user_post= DB::table('user__posts')->where("postid",$postid)->delete();
      if($user_post){
        return response([
          "reply"=>$user_post
        ]);
      }else{
        $post_id=$request->input('postid');
        $channel_post= DB::table('channel_posts')->where("postid",$post_id)->first();
        if($channel_post){
          if($channel_post->video !=''){
            $public_id=$channel_post->video;
            $result = Cloudinary::destroy($public_id, [
              'resource_type' => 'video'
          ]);
          }else{
            $publicId=[$channel_post->post_img1, $channel_post->post_img2, $channel_post->post_img3, $channel_post->post_img4,$channel_post->video];
            $publicIds = array_filter($publicId);
            $this->delete($publicIds);
          }
        $deleteSuccess= DB::table('channel_posts')->where("postid",$post_id)->delete();
        return response([
          "reply"=>$deleteSuccess
        ]);
        }else{
          $postid=$request->input('postid');
          $user_post= DB::table('shared__posts')->where("postid",$postid)->delete();
          return response([
            "reply"=>$user_post
          ]);
        }
      }
    }
    public function deleteCommunityPost(Request $request){
      $post_id=$request->input('postid');
      $community_post= DB::table('community_posts')->where("postid",$post_id)->first();
      if($community_post){
        if($community_post->video !=''){
          $public_id=$community_post->video;
          $result = Cloudinary::destroy($public_id, [
            'resource_type' => 'video'
        ]);
        }else{
          $publicId=[$community_post->post_img1, $community_post->post_img2, $community_post->post_img3, $community_post->post_img4,$community_post->video];
          $publicIds = array_filter($publicId);
          $this->delete($publicIds);
        }
      $deleteSuccess= DB::table('community_posts')->where("postid",$post_id)->delete();
      return response([
        "reply"=>$deleteSuccess
      ]);
    }
  }
  public function removeMember(Request $request){
    $evicted_member=$request->input('evicted_member');
    $community_name=$request->input('community_name');
    $remove_member=DB::table('community_followers')->where(["community_member"=>$evicted_member,"community_name"=>$community_name])->delete();
    return response([
      "reply"=>"success"
    ]);
  }
  public function joinCommunity(Request $request){
    $community_member=$request->input('current_user');
    $community_name=$request->input('community_name');
    $new_community_member=CommunityFollower::create([
      "community_member"=>$community_member,
      "community_name" => $community_name
    ]);
    return response([
      "reply"=>"Success"
    ]);
  }
    public function fetchRandomChannelPost(Request $request){
      $user=$request->input('email');
      $date=$request->input('recent_date');
      $find_older_channel_post=$this->userActivityService->fetchOlderChannelPost($user,$date);
      return $find_older_channel_post;
    }
    public function fetchAllChannelsVideo(Request $request){
      $current_user=$request->input('email');
     $find_channel_videos=$this->userActivityService->findChannelVideos($current_user);
     return $find_channel_videos;
    }
    public function fetchAllChannelsPost(Request $request){
      $current_user=$request->input('email');
     $fetch_all_channel_posts=$this->userActivityService->fetchAllChannelsPost($current_user);
     return $fetch_all_channel_posts;
    }
    public function fetchCommunitiesPost(Request $request){
      $current_user=$request->input('email');
      $fetch_all_community_posts=$this->userActivityService->fetchAllCommunityPost($current_user);
      return $fetch_all_community_posts;
    }
    public function fetchCommunityPosts(Request $request){
      $key_type=$request->input('key');
      $community_name=$request->input('community_name');
      $fetch_all_community_posts=$this->userActivityService->fetchCommunityPosts($key_type,$community_name);
      return $fetch_all_community_posts;
    }
    public function fetchRandomTopCommunityPost(Request $request){
      $last_top_post_date=$request->input('last_top_post_date');
      $fetch_all_community_posts=$this->userActivityService->fetchRandomTopCommunityPost($last_top_post_date);
      return $fetch_all_community_posts;
    }
    public function fetchLatestCommunityPosts(Request $request){
      $community_name=$request->input('community_name');
      $fetch_latest_community_post=$this->userActivityService->fetchLatestCommunityPosts($community_name);
      return $fetch_latest_community_post;
    }
    public function fetchRandomLatestCommunityPosts(Request $request){
      $community_name=$request->input('last_top_post_date');
      $fetch_latest_community_post=$this->userActivityService->fetchRandomLatestCommunityPosts($community_name);
      return $fetch_latest_community_post;
    }
    public function findCommunityDetails(Request $request){
      $community_id=$request->input('community_name');
      $find_community_details=$this->userActivityService->findCommunityDetails($community_id);
      return $find_community_details;
    }
    public function findCommunitiesOwnedByUser(Request $request){
      $user_mail=$request->input('user_mail');
      $find_all_communities_owned_by_user=$this->userService->findUserCommunity($user_mail);
      return $find_all_communities_owned_by_user;
    }
    public function checkIfUserLiked(Request $request){
      $current_user=$request->input('email');
      $post_id=$request->input('post_id');
      $check_if_user_has_liked_post_already=PostLike::where(['user_who_liked'=>$current_user, 'post_id'=>$post_id])->first();
      if($check_if_user_has_liked_post_already){
        return response([
          "reply" => "false"
        ]);
      }else{
        PostLike::create([
          "user_who_liked" => $current_user,
          "post_id" => $post_id
        ]);
       // $this->userService->notifyUser($user,$choice,"$sender is interested in you..");
        return response([
          "reply" => "true"
        ]);
      }
    }
    public function findPostLikeCounts(Request $request){
      $post_id=$request->input('post_id');
      $post_like_collection=array();
      $no_of_likes=PostLike::where(['post_id'=>$post_id])->get();
      foreach($no_of_likes as $post_like){
        array_push($post_like_collection,$post_like);
      }
      $number_of_post_likes=count($post_like_collection);
      return response([
        "reply" => $number_of_post_likes
      ]);
     
    }
    public function findPostCommentCounts(Request $request){
      $post_id=$request->input('post_id');
      $post_like_collection=array();
      $no_of_likes=Comment::where(['post_id'=>$post_id])->get();
      foreach($no_of_likes as $post_like){
        array_push($post_like_collection,$post_like);
      }
      $number_of_post_likes=count($post_like_collection);
      return response([
        "reply" => $number_of_post_likes
      ]);
     
    }
    public function findAllPostUserLiked(Request $request){
      $post_id=$request->input('post_id');
      $current_user=$request->input('email');
      $check_if_user_liked_post=DB::table('post_likes')->where(['post_id'=>$post_id, 'user_who_liked'=>$current_user])->first();
      if($check_if_user_liked_post){
        return response([
          "reply" => "true"
        ]);
      }else{
        return response([
          "reply" => "false"
        ]);
      }
    }
    public function DeleteLike(Request $request){
      $post_id=$request->input('post_id');
      $current_user=$request->input('email');
      DB::table('post_likes')->where(['post_id'=>$post_id, 'user_who_liked'=>$current_user])->delete();
      return response([
        "reply" => "successful"
      ]);
   
     
    }
    public function findStatus(Request $request){
      $post_id=$request->input('post_id');
      $check_post_table=DB::table('user__posts')->join('users','user__posts.email','=','users.email')->select('user__posts.name','user__posts.caption','users.profile_picture','user__posts.email','user__posts.created_at','user__posts.postid','user__posts.isReply')->where('user__posts.postid',$post_id)->first();
      if($check_post_table){
        $show_all_post_info=array("name"=>$check_post_table->name,
        "caption"=>$check_post_table->caption, "avatar"=>$check_post_table->profile_picture,"user_email"=>base64_encode($check_post_table->email), "post_date"=>$check_post_table->created_at, "prev_id"=>$check_post_table->postid, "isReply"=>$check_post_table->isReply,   "img_1"=>null,"img_2"=>null,"img_3"=>null,"img_4"=>null,"video"=>null);
        $check_no_of_users_who_comment=DB::select("SELECT count(comment) AS total_comments FROM comments WHERE post_id='$post_id'");
        $check_no_of_users_who_shared=DB::select("SELECT count(quote) AS total_shares FROM shared__posts WHERE prev_id='$post_id'");  
        $check_no_of_users_who_liked=DB::select("SELECT count(user_who_liked) AS total_like FROM post_likes WHERE post_id='$post_id'");    
        $check_no_of_users_who_bookmarked=DB::select("SELECT count(user_who_bookmark) AS total_bookmarks FROM bookmarks WHERE bookmarked_post_id='$post_id'");
        return response([
          "reply" => $show_all_post_info,
          "no_of_comment"=>$check_no_of_users_who_comment,
          "no_of_users_who_shared"=>$check_no_of_users_who_shared,
          "no_of_users_who_liked"=>$check_no_of_users_who_liked,
          "no_of_users_who_bookmarked"=>$check_no_of_users_who_bookmarked
        
        ]);
      }else{
        $check_channels_table=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->select('channel_posts.name','channel_posts.caption','users.profile_picture','channel_posts.email','channel_posts.created_at','channel_posts.postid', 'channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','channel_posts.isReply')->where('channel_posts.postid',$post_id)->first();
        if($check_channels_table){
          $show_all_post_info=array("name"=>$check_channels_table->name,
          "caption"=>$check_channels_table->caption, "avatar"=>$check_channels_table->profile_picture, "user_email"=>base64_encode($check_channels_table->email), "post_date"=>$check_channels_table->created_at, "prev_id"=>$check_channels_table->postid,
          "img_1"=>$check_channels_table->post_img1, "img_2"=>$check_channels_table->post_img2, "img_3"=>$check_channels_table->post_img3, "img_4"=>$check_channels_table->post_img4,"video"=>$check_channels_table->video, "isReply"=>$check_channels_table->isReply
        );
        $check_no_of_users_who_comment=DB::select("SELECT count(comment) AS total_comments FROM comments WHERE post_id='$post_id'");
        $check_no_of_users_who_shared=DB::select("SELECT count(quote) AS total_shares FROM shared__posts WHERE prev_id='$post_id'");  
        $check_no_of_users_who_liked=DB::select("SELECT count(user_who_liked) AS total_like FROM post_likes WHERE post_id='$post_id'");    
        $check_no_of_users_who_bookmarked=DB::select("SELECT count(user_who_bookmark) AS total_bookmarks FROM bookmarks WHERE bookmarked_post_id='$post_id'");
          return response([
            "reply" => $show_all_post_info,
          "no_of_comment"=>$check_no_of_users_who_comment,
          "no_of_users_who_shared"=>$check_no_of_users_who_shared,
          "no_of_users_who_liked"=>$check_no_of_users_who_liked,
          "no_of_users_who_bookmarked"=>$check_no_of_users_who_bookmarked
          ]);
        }else{
          $check_shared_posts_table=DB::table('shared__posts')->join('users','shared__posts.email','=','users.email')->select('shared__posts.name','shared__posts.caption','users.profile_picture','shared__posts.email','shared__posts.created_at','shared__posts.post_img1','shared__posts.post_img2','shared__posts.post_img3','shared__posts.post_img4','shared__posts.video','shared__posts.name_of_user_who_shared','shared__posts.email_of_user_who_shared','shared__posts.quote','shared__posts.postid','shared__posts.prev_id','shared__posts.isReply')->where('shared__posts.postid',$post_id)->first();
          if($check_shared_posts_table){
            $get_sharer_picture=DB::table('users')->select('profile_picture')->where('email',$check_shared_posts_table->email_of_user_who_shared)->first();
            $show_all_post_info=array("name"=>$check_shared_posts_table->name,
            "caption"=>$check_shared_posts_table->caption, "avatar"=>$check_shared_posts_table->profile_picture, "user_email"=>base64_encode($check_shared_posts_table->email), "post_date"=>$check_shared_posts_table->created_at,
            "img_1"=>$check_shared_posts_table->post_img1, "img_2"=>$check_shared_posts_table->post_img2, "img_3"=>$check_shared_posts_table->post_img3, "img_4"=>$check_shared_posts_table->post_img4,"video"=>$check_shared_posts_table->video,"sharer_name"=>$check_shared_posts_table->name_of_user_who_shared,"sharer_email"=>base64_encode($check_shared_posts_table->email_of_user_who_shared),
            "sharer_quote"=>$check_shared_posts_table->quote,"sharer_avatar"=>$get_sharer_picture->profile_picture,"postid"=>$check_shared_posts_table->postid,"prev_id"=>$check_shared_posts_table->prev_id, "isReply"=>$check_shared_posts_table->isReply
          );
          $check_no_of_users_who_comment=DB::select("SELECT count(comment) AS total_comments FROM comments WHERE post_id='$post_id'");
        $check_no_of_users_who_shared=DB::select("SELECT count(quote) AS total_shares FROM shared__posts WHERE prev_id='$post_id'");  
        $check_no_of_users_who_liked=DB::select("SELECT count(user_who_liked) AS total_like FROM post_likes WHERE post_id='$post_id'");    
        $check_no_of_users_who_bookmarked=DB::select("SELECT count(user_who_bookmark) AS total_bookmarks FROM bookmarks WHERE bookmarked_post_id='$post_id'");
            return response([
              "reply" => $show_all_post_info,
              "no_of_comment"=>$check_no_of_users_who_comment,
              "no_of_users_who_shared"=>$check_no_of_users_who_shared,
              "no_of_users_who_liked"=>$check_no_of_users_who_liked,
              "no_of_users_who_bookmarked"=>$check_no_of_users_who_bookmarked
            ]);
        }else{
          return response([
            "reply"=> "error"
          ]);
        }
      
      }
    }
     
    }
    public function findSharedStatus(Request $request){
      $post_id=$request->input('post_id');
      $check_shared_posts_table=DB::table('shared__posts')->join('users','shared__posts.email_of_user_who_shared','=','users.email')->select('shared__posts.name','shared__posts.caption','users.profile_picture','shared__posts.email','shared__posts.created_at','shared__posts.post_img1','shared__posts.post_img2','shared__posts.post_img3','shared__posts.post_img4','shared__posts.video','shared__posts.name_of_user_who_shared','shared__posts.email_of_user_who_shared','shared__posts.quote','shared__posts.postid','shared__posts.prev_id','shared__posts.isReply')->where('shared__posts.prev_id',$post_id)->get();
      $all_quote_posts=array();
      foreach($check_shared_posts_table as $shared_posts){
        $post_id=$shared_posts->postid;
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
        $email_of_original_poster=$shared_posts->email;
        $find_avatar_of_original_poster=DB::table('users')->where('email',$email_of_original_poster)->select('profile_picture')->first();
      array_push($all_quote_posts,["name"=>$shared_posts->name, "caption"=>$shared_posts->caption, "profile_picture"=>$shared_posts->profile_picture, "email"=>base64_encode($shared_posts->email), "created_at"=>$shared_posts->created_at, "post_img1"=>$shared_posts->post_img1, "post_img2"=>$shared_posts->post_img2, "post_img3"=>$shared_posts->post_img3, "post_img4"=>$shared_posts->post_img4, "video"=>$shared_posts->video, "name_of_user_who_shared"=>$shared_posts->name_of_user_who_shared, "email_of_user_who_shared"=>base64_encode($shared_posts->email_of_user_who_shared), "quote"=>$shared_posts->quote,"postid"=>$shared_posts->postid, "prev_id"=>$shared_posts->prev_id,"isReply"=>$shared_posts->isReply, "avatar_of_original_poster"=>$find_avatar_of_original_poster->profile_picture,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)]);

      }
      return response([
        "reply"=>$all_quote_posts,
      ]);
      
      }

    public function postComment(Request $request){
      $post_id=$request->input('post_id');
      $comment=$request->input('comment');
      $user_who_comment=$request->input('user');
      $post_owner=$request->input('post_owner');
      $user_who_comment_name=$request->input('user_who_comment_name');
      $tagged_users=$request->input('tagged_users');
      $comment_id=rand(0,10000000);
      $current_date=date('Y-m-d H:i:s');
      $data=[
        "comment"=>$comment,
        "user_who_comment"=>$user_who_comment,
        "post_id"=>$post_id,
        "comment_id"=>$comment_id,
        "current_date"=>$current_date,
        "tagged_users"=>$tagged_users,
        "post_owner"=>$post_owner,
        "user_who_comment_name"=>$user_who_comment_name
      ];
    $this->ConverSation->createUserComment($data);
    $find_user_who_comment_info=User::where('email',$user_who_comment)->first();
    $all_info=array(
      "comment"=>$comment,
      "post_id"=>$post_id,
      "user_who_comment"=>$user_who_comment,
      "avatar"=>$find_user_who_comment_info['profile_picture'],
      "first_name"=>$find_user_who_comment_info['first_name'],
      "last_name"=>$find_user_who_comment_info['last_name'],
      "comment_date"=>$current_date
    );
    return response([
      "reply"=>$all_info,
    ]);
    }
    public function findAllComments(Request $request){
      $post_id=$request->input("post_id");
      $find_all_comment=DB::table('comments')->join('users','comments.user_who_comment','=','users.email')->where('comments.post_id',$post_id)->select('users.first_name','users.profile_picture', 'comments.created_at','comments.comment','comments.user_who_comment','comments.post_id','comments.comment_id')->orderBy('comments.created_at','DESC')->get();
      $hold_each_comment_reply_count=array();
      foreach($find_all_comment as $comments){
        $each_comment_unique_id=$comments->comment_id;
        $find_comment_reply_count=DB::select("SELECT count(comment_reply) AS total_reply FROM comment__replies WHERE initial_comment_id='$each_comment_unique_id'");
        foreach($find_comment_reply_count as $get_reply_count){
          array_push($hold_each_comment_reply_count,["first_name"=>$comments->first_name, "profile_picture"=>$comments->profile_picture,"created_at"=>$comments->created_at,"comment"=>$comments->comment,"user_who_comment"=>base64_encode($comments->user_who_comment), "post_id"=>$comments->post_id, "comment_id"=>$comments->comment_id,"reply_count"=>$get_reply_count->total_reply]);
        }
      }
      return response([
        "reply"=>$hold_each_comment_reply_count,
      ]);
    }
    
    public function DeleteComment(Request $request){
      $post_id=$request->input('date');
      $current_user=$request->input('email');
      DB::table('comments')->where(['created_at'=>$post_id, 'user_who_comment'=>$current_user])->delete();
      return response([
        "reply" => "successful"
      ]);
   
     
    }
    public function DeleteCommentReply(Request $request){
      $post_id=$request->input('date');
      $current_user=$request->input('email');
      DB::table('comment__replies')->where(['created_at'=>$post_id, 'user_who_replied'=>$current_user])->delete();
      return response([
        "reply" => "successful"
      ]);
   
     
    }
    public function findNotifications(Request $request){
      $current_user=$request->input('email');
      $find_all_user_notifications=DB::table('notifications')->join('users','notifications.from','=','users.email')->where('notifications.owner',$current_user)->
      select('notifications.created_at','notifications.owner','notifications.from','notifications.info','notifications.source','notifications.owner_has_read','users.profile_picture')->orderBy('notifications.id','DESC')->take(20)->get();
      $store_all_notifications=array();
      foreach($find_all_user_notifications as $notifications){
        array_push($store_all_notifications, ["created_at"=>$notifications->created_at,"owner"=>$notifications->owner,"from"=>base64_encode($notifications->from),"info"=>$notifications->info,"source"=>$notifications->source,"owner_has_read"=>$notifications->owner_has_read,
        "profile_picture"=>$notifications->profile_picture]);
      }
      Notifications::where('owner',$current_user)->update([
        'owner_has_read'  =>"true",
  
      ]);
      return response([
        "reply" => $store_all_notifications
      ]);
    }
    public function findNotifyCount(Request $request){
     $owner=$request->input('owner');
     $find_new_notifications_count=Notifications::where(['owner'=>$owner, 'owner_has_read'=>'false'])->get();
     $notify_count_array=array();
     foreach($find_new_notifications_count as $get_count){
      array_push($notify_count_array,$get_count->from);
     }
     return response([
      "reply"=> count($notify_count_array)
     ]);
    }
    public function convo(Request $request){
      $sender=$request->input('sender');
      $reciever=$request->input('reciever');
      $unique_id=$request->input('unique_id');
      
      $check_if_both_users_have_a_conversation=Messages::where(["sender"=>$sender, "reciever"=>$reciever])->first();
      $check_if_any_users_have_a_conversation=Messages::where(["sender"=>$reciever, "reciever"=>$sender])->first();
      if($check_if_both_users_have_a_conversation == null && $check_if_any_users_have_a_conversation == null){
        $start_conversation=Messages::create([
          "sender"=>$sender,
          "reciever"=>$reciever,
          "unique_id"=>$unique_id,
          "conversation"=>'',
          "file"=>''
          
        ]);
        return response([
          "reply"=>$unique_id
        ]);
      }else{
        if($check_if_both_users_have_a_conversation == null){
          return response([
            "reply"=>$check_if_any_users_have_a_conversation['unique_id']
          ]);
        }else{
          return response([
            "reply"=>$check_if_both_users_have_a_conversation['unique_id']
          ]);
        }
      }
      
    }
    public function sendMessage(Request $request){
      if($request->file('doc') !=null){
        $request->validate([
          'doc' => 'mimes:jpeg,jpg,png,JPG,JPEG,PNG,gif,PDF,pdf,mp3,wav,ogg,OGG,MP3,WAV,mp4,MP4|max:500000'
  
        ]); 
        $filePath=$request->file('doc');
        $file_ext=$filePath->getClientOriginalExtension();
        $video_files_array=array('mp4','MP4','webm','WEBM','mkv','MKV','3gp');
        $img_files_array=array('jpg','jpeg','png','gif','JPG','JPEG','PNG','GIF','jfif','JFIF');
        $audio_files_array=array('mp3','ogg','MP3','wav','WAV');
        $doc_files_array=array('pdf','PDF');
      //  $file=$request->file('doc')->store('chat_doc','public');
      $upload = Cloudinary::uploadFile($filePath->getRealPath(), [
        'folder' => 'chat_doc',
    ]);
    if(in_array($file_ext, $video_files_array)){
      $unique_id=$request->input('unique_id');
        $conversation=$request->input('conversation');
        $sender=$request->input('sender');
        $reciever=$request->input('reciever');
        $isRead="false";
        $network_msg=[
          "unique_id"=>$unique_id,
          "conversation"=>$conversation,
          "file"=>$upload->getPublicId(),
          "file_status"=>'video',
          "sender"=>$sender,
          "reciever"=>$reciever,
          "isRead"=>$isRead
        ];
        $send_message=Messages::create([
          "unique_id"=>$unique_id,
          "conversation"=>encrypt($conversation),
          "file"=>encrypt($upload->getPublicId()),
          "file_status"=>'video',
          "sender"=>$sender,
          "reciever"=>$reciever,
          "isRead"=>$isRead
        ]);
        $pusher = new Pusher(
          env('PUSHER_APP_KEY'),
          env('PUSHER_APP_SECRET'),
          env('PUSHER_APP_ID'),
          [
              'cluster' => env('PUSHER_APP_CLUSTER'),
              'useTLS' => true
          ]
      );

      $data = [$network_msg];

      $pusher->trigger('chat', 'send_message', $data);
        return response([
          "reply"=>$network_msg
        ]);
    }elseif (in_array($file_ext,$img_files_array)) {
      $unique_id=$request->input('unique_id');
      $conversation=$request->input('conversation');
      $sender=$request->input('sender');
      $reciever=$request->input('reciever');
      $isRead="false";
      $network_msg=[
        "unique_id"=>$unique_id,
        "conversation"=>$conversation,
        "file"=>$upload->getPublicId(),
        "file_status"=>'image',
        "sender"=>$sender,
        "reciever"=>$reciever,
        "isRead"=>$isRead
      ];
      $send_message=Messages::create([
        "unique_id"=>$unique_id,
        "conversation"=>encrypt($conversation),
        "file"=>encrypt($upload->getPublicId()),
        "file_status"=>'image',
        "sender"=>$sender,
        "reciever"=>$reciever,
        "isRead"=>$isRead
      ]);
      $pusher = new Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true
        ]
    );

    $data = [$network_msg];

    $pusher->trigger('chat', 'send_message', $data);
      return response([
        "reply"=>$network_msg
      ]);
    }elseif (in_array($file_ext,$doc_files_array)) {
      $unique_id=$request->input('unique_id');
      $conversation=$request->input('conversation');
      $sender=$request->input('sender');
      $reciever=$request->input('reciever');
      $isRead="false";
      $network_msg=[
        "unique_id"=>$unique_id,
        "conversation"=>$conversation,
        "file"=>$upload->getPublicId(),
        "file_status"=>'document',
        "sender"=>$sender,
        "reciever"=>$reciever,
        "isRead"=>$isRead
      ];
      $send_message=Messages::create([
        "unique_id"=>$unique_id,
        "conversation"=>encrypt($conversation),
        "file"=>encrypt($upload->getPublicId()),
        "file_status"=>'document',
        "sender"=>$sender,
        "reciever"=>$reciever,
        "isRead"=>$isRead
      ]);
      $pusher = new Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true
        ]
    );

    $data = [$network_msg];

    $pusher->trigger('chat', 'send_message', $data);
      return response([
        "reply"=>$network_msg
      ]);
    }elseif (in_array($file_ext,$audio_files_array)) {
      $unique_id=$request->input('unique_id');
      $conversation=$request->input('conversation');
      $sender=$request->input('sender');
      $reciever=$request->input('reciever');
      $isRead="false";
      $network_msg=[
        "unique_id"=>$unique_id,
        "conversation"=>$conversation,
        "file"=>$upload->getPublicId(),
        "file_status"=>'audio',
        "sender"=>$sender,
        "reciever"=>$reciever,
        "isRead"=>$isRead
      ];
      $send_message=Messages::create([
        "unique_id"=>$unique_id,
        "conversation"=>encrypt($conversation),
        "file"=>encrypt($upload->getPublicId()),
        "file_status"=>'audio',
        "sender"=>$sender,
        "reciever"=>$reciever,
        "isRead"=>$isRead
      ]);
      $pusher = new Pusher(
        env('PUSHER_APP_KEY'),
        env('PUSHER_APP_SECRET'),
        env('PUSHER_APP_ID'),
        [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true
        ]
    );

    $data = [$network_msg];

    $pusher->trigger('chat', 'send_message', $data);
      return response([
        "reply"=>$network_msg
      ]);
    }
        
      }else{
        $unique_id=$request->input('unique_id');
        $conversation=$request->input('conversation');
        $sender=$request->input('sender');
        $reciever=$request->input('reciever');
        $isRead="false";
        $network_msg=[
          "unique_id"=>$unique_id,
          "conversation"=>$conversation,
          "file"=>"",
          "file_status"=>'',
          "sender"=>$sender,
          "reciever"=>$reciever,
          "isRead"=>$isRead
        ];
        $send_message=Messages::create([
          "unique_id"=>$unique_id,
          "conversation"=>encrypt($conversation),
          "file"=>'',
          "file_status"=>'',
          "sender"=>$sender,
          "reciever"=>$reciever,
          "isRead"=>"false"
        ]);
        $pusher = new Pusher(
          env('PUSHER_APP_KEY'),
          env('PUSHER_APP_SECRET'),
          env('PUSHER_APP_ID'),
          [
              'cluster' => env('PUSHER_APP_CLUSTER'),
              'useTLS' => true
          ]
      );

      $data = [$network_msg];

      $pusher->trigger('chat', 'send_message', $data);
        return response([
          "reply"=>$network_msg
        ]);
      }
    
      
    }
    public function findConvo(Request $request){
      $unique_id=$request->input('unique_id');
      $message_info=Messages::where('unique_id',$unique_id)->first();
      $find_all_message=Messages::where(['unique_id'=>$unique_id])->get();
      $all_messages=array();
      $sender=$message_info['sender'];
      $reciever=$message_info['reciever'];
      foreach($find_all_message as $users_conversation){
        if(empty($users_conversation->file)&&empty($users_conversation->conversation)){
          array_push($all_messages,["created_at"=>$users_conversation->created_at,"sender"=>$users_conversation->sender,
        "reciever"=>$users_conversation->reciever,"unique_id"=>$users_conversation->unique_id,"conversation"=>$users_conversation->conversation,"file"=>"","file_status"=>$users_conversation->file_status,"isRead"=>$users_conversation->isRead]);
        }elseif(empty($users_conversation->file)&& $users_conversation->conversation !=''){
          array_push($all_messages,["created_at"=>$users_conversation->created_at,"sender"=>$users_conversation->sender,
        "reciever"=>$users_conversation->reciever,"unique_id"=>$users_conversation->unique_id,"conversation"=>decrypt($users_conversation->conversation),"file"=>"","file_status"=>$users_conversation->file_status,"isRead"=>$users_conversation->isRead]);
        }elseif($users_conversation->file !='' && $users_conversation->conversation ==''){
          array_push($all_messages,["created_at"=>$users_conversation->created_at,"sender"=>$users_conversation->sender,
        "reciever"=>$users_conversation->reciever,"unique_id"=>$users_conversation->unique_id,"conversation"=>decrypt($users_conversation->conversation),"file"=>"","file_status"=>$users_conversation->file_status,"isRead"=>$users_conversation->isRead]);
        }
        else{
        array_push($all_messages,["created_at"=>$users_conversation->created_at,"sender"=>$users_conversation->sender,
        "reciever"=>$users_conversation->reciever,"unique_id"=>$users_conversation->unique_id,"conversation"=>decrypt($users_conversation->conversation),"file"=>decrypt($users_conversation->file),"file_status"=>$users_conversation->file_status,"isRead"=>$users_conversation->isRead]);
        }
        
      }
   
      return response([
        "reply"=>$all_messages,
        "sender"=>$sender,
        "reciever"=>$reciever,

      ]);
    }
    public function findAllMessages(Request $request){
      $current_user=$request->input('email');
      $hold_all_message_gotten=array();
      $find_all_user_message=DB::table('messages')->join('users','users.email','=','messages.sender')->where('messages.reciever',$current_user)->where('messages.conversation','!=','')->
      select('messages.created_at','messages.sender','messages.unique_id','messages.isRead', 'messages.conversation','messages.file','users.profile_picture','users.first_name','users.last_name')->orderBy('messages.id','desc')->get()->unique('sender');
      foreach($find_all_user_message as $message){
        array_push($hold_all_message_gotten,["created_at"=>$message->created_at,"sender"=>$message->sender,"unique_id"=>$message->unique_id,"isRead"=>$message->isRead,"conversation"=>decrypt($message->conversation),"file"=>$message->file,"profile_picture"=>$message->profile_picture,
        "first_name"=>$message->first_name,"last_name"=>$message->last_name]);
      }
      return response([
        "reply"=>$hold_all_message_gotten
      ]);
    }
    public function findRecieverInfo(Request $request){
      $msg_reciever=$request->input('user');
      $find_reciever_info=DB::table('users')->where('email',$msg_reciever)->select('first_name','last_name','profile_picture','updated_at')->first();
      return response([
      "reply"=>$find_reciever_info
      ]);
    }
    public function updateMsgStatus(Request $request){
      $day=$request->input('day');
      $unique_id=$request->input('unique_id');
      $sender=$request->input('sender');
      $update_msg_status=Messages::where('sender',$sender)->where('unique_id',$unique_id)->update([
        "isRead"=>"true"
      ]);
    }
    public function findMsgCount(Request $request){
      $owner=$request->input('owner');
     $find_new_notifications_count=Messages::where(['reciever'=>$owner, 'isRead'=>'false'])->get();
     $notify_count_array=array();
     foreach($find_new_notifications_count as $get_count){
      array_push($notify_count_array,$get_count->conversation);
     }
     return response([
      "reply"=> count($notify_count_array)
     ]);
    }
    public function createCommunityPostVideo(Request $request){
      $email=$request->input('email');
      $name=$request->input('name');
      $caption=$request->input('caption');
      $avatar=$request->input('avatar');
      $post_id=rand(10,1000000) . date('d');
      $request->validate([
        'video' => 'required|file|mimes:mp4,mov,3gp,MP4,MOV,gif|max:512000'

      ]); 
    //  $file=$request->file('video')->store('users_posts','public');
    ini_set('max_execution_time', 500);
    $filePath=$request->file('video');
    $upload = Cloudinary::uploadFile($filePath->getRealPath(), [
      'folder' => 'users_posts',
  ]);
      $create_post=CommunityPost::create([
        "email" => $email,
        "name"  => $name,
        "avatar" => $avatar,
        "caption" => $caption,
        "postid"  => $post_id,
        "video"   => $upload->getPublicId()

      ]);

    }
    public function createUserChannelVideo(Request $request){
      $email=$request->input('email');
      $name=$request->input('name');
      $caption=$request->input('caption');
      $avatar=$request->input('avatar');
      $post_id=rand(10,1000000) . date('d');
      $request->validate([
        'video' => 'required|file|mimes:mp4,mov,3gp,MP4,MOV,gif|max:512000'

      ]); 
    //  $file=$request->file('video')->store('users_posts','public');
    ini_set('max_execution_time', 500);
    $filePath=$request->file('video');
    $upload = Cloudinary::uploadFile($filePath->getRealPath(), [
      'folder' => 'users_posts',
  ]);
      $create_post=ChannelPost::create([
        "email" => $email,
        "name"  => $name,
        "avatar" => $avatar,
        "caption" => $caption,
        "postid"  => $post_id,
        "video"   => $upload->getPublicId()

      ]);

    }
    public function replyComment(Request $request){
      $comment_reply=$request->input('comment_reply');
      $comment_file="";
      $initial_comment_id=$request->input('initial_comment_id');
      $reply_id=rand(5, 50000000);
      $user_being_replied=$request->input('user_being_replied');
      $user_who_replied=$request->input('user_who_replied');
     $reply= Comment_Reply::create([
        "comment_reply"=>$comment_reply,
        "comment_file"=>$comment_file,
        "initial_comment_id"=>$initial_comment_id,
        "reply_id"=>$reply_id,
        "user_being_replied"=>$user_being_replied,
        "user_who_replied"=>$user_who_replied
      ]);
      if($user_being_replied != $user_who_replied){
        $find_initial_comment=Comment::where('comment_id',$initial_comment_id)->first();
        $post_id=$find_initial_comment['post_id'];
        $find_user_who_replied_name=DB::table('users')->where('email',$user_who_replied)->select('first_name','last_name')->first();
        $first_name_of_user_who_replied=$find_user_who_replied_name->first_name;
        $last_name_of_user_who_replied=$find_user_who_replied_name->last_name;
        $info="$first_name_of_user_who_replied"."\t$last_name_of_user_who_replied replied: $comment_reply";
        $this->userService->notifyUser($user_who_replied,$user_being_replied,$info,$post_id);
      }
      
      return response([
        "reply"=>$reply
      ]);
    
    }
    public function findCommentReplies(Request $request){
      $comment_id=$request->input('comment_id');
      $find_all_replies_related_to_comment=DB::table('comment__replies')->join('users','comment__replies.user_who_replied','=','users.email')->where('comment__replies.initial_comment_id',$comment_id)->select('comment__replies.created_at','comment__replies.comment_reply','comment__replies.comment_file','comment__replies.initial_comment_id',
      'comment__replies.reply_id','comment__replies.user_being_replied','comment__replies.user_who_replied','users.profile_picture','users.first_name')->get();
      $hold_all_replies=array();
      foreach($find_all_replies_related_to_comment as $comment_replies){
      $each_comment_unique_id=$comment_replies->reply_id;
       $find_individual_reply_count=DB::select("SELECT count(comment_reply) AS total_reply FROM comment__replies WHERE reply_id='$each_comment_unique_id'");
        foreach($find_individual_reply_count as $other_reply_count){
          if($other_reply_count->total_reply == 1){
            $user_has_reply=0;
          }else{
            $user_has_reply=$other_reply_count->total_reply;
          }
          array_push($hold_all_replies,["created_at"=>$comment_replies->created_at,"comment_reply"=>$comment_replies->comment_reply,"comment_file"=>$comment_replies->comment_file,"initial_comment_id"=>$comment_replies->initial_comment_id,
          "reply_id"=>$comment_replies->reply_id, "user_being_replied"=>$comment_replies->user_being_replied,"user_who_replied"=>base64_encode($comment_replies->user_who_replied),"profile_picture"=>$comment_replies->profile_picture,"first_name"=>$comment_replies->first_name,
          "other_reply_count"=>$user_has_reply
        ]);
       }
       
      }
      return response([
        "reply"=>$hold_all_replies,
        "comment_id"=>$comment_id,
      ]);
    }
    public function findAllChannelPhotos(Request $request){
      $email=$request->input('email');
      $find_all_photos=DB::table("channel_posts")->where("email",$email)->where("post_img1", '!=', '')->select('post_img1','postid','post_img2','post_img3','post_img4')->latest()->take(10)->get();
      $all_channel_photos=array();
      foreach($find_all_photos as $photos){
        array_push($all_channel_photos, $photos);
      }
      return response([
        "reply"=>$all_channel_photos
      ]);
    }
    public function findAllChannelVideos(Request $request){
      $email=$request->input('email');
      $find_all_photos=DB::table("channel_posts")->where("email",$email)->where("video", '!=', '')->select('video','postid')->latest()->take(10)->get();
      $all_channel_photos=array();
      foreach($find_all_photos as $photos){
        array_push($all_channel_photos, $photos);
      }
      return response([
        "reply"=>$all_channel_photos
      ]);
    }
    public function sharePost(Request $request){
      $name_of_previous_post_owner=$request->input('name');
      $email_of_previous_post_owner=$request->input('email');
      $new_post_owner_email=$request->input('email_of_user_who_shared');
      $prev_id=$request->input('prev_id');
      $caption=$request->input('caption');
      $post_img1=$request->input('post_img1');
      $post_img2=$request->input('post_img2');
      $post_img3=$request->input('post_img3');
      $post_img4=$request->input('post_img4');
      $video=$request->input('video');
      $postid=$request->input('postid');
      $quote=$request->input('quote');
      $first_name_of_new_post_owner=$request->input('firstname');
      $last_name_of_new_post_owner=$request->input('lastname');
      $check_if_user_has_shared_post_before=Shared_Post::where(['prev_id'=>$prev_id,'email_of_user_who_shared'=>$new_post_owner_email])->first();
      if($check_if_user_has_shared_post_before){
      return response([
        "reply"=>"false"
      ]);
      }else{
      $name_of_new_post_owner=$first_name_of_new_post_owner."\t".$last_name_of_new_post_owner;
      $tagged_users=$request->input('tagged_users');
      if($post_img1=='undefined' || $post_img2=='undefined' || $post_img3=='undefined' || $post_img4=='undefined' || $video=='undefined'){
        $data=[
          "name"=>$name_of_previous_post_owner,
          "avatar"=>'',
          "caption"=>$caption,
          "email"=>$email_of_previous_post_owner,
          "post_img1"=>"",
          "post_img2"=>"",
          "post_img3"=>"",
          "post_img4"=>"",
          "video"=>"",
          "quote"=>$quote,
          "postid"=>$postid,
          "prev_id"=>$prev_id,
          "name_of_user_who_shared"=>$name_of_new_post_owner,
          "email_of_user_who_shared"=>$new_post_owner_email
        ];
        $new_shared_post=$this->ConverSation->sharePost($data);
        if($new_post_owner_email != $email_of_previous_post_owner)
        $this->userService->notifyUser($new_post_owner_email,$email_of_previous_post_owner,"$name_of_new_post_owner Said $quote",$postid);
        if($tagged_users!=''){
          foreach($tagged_users as $user){
            $this->userService->notifyUser($new_post_owner_email,$user,"$name_of_new_post_owner tagged you in a response $quote",$postid);
          }
        }else{
          return;
        }
        return response([
          "reply"=>"true"
        ]);
      }else{
      if($video=='null')
        $video=null;
      if($post_img1=='null')
      $post_img1=null;
      if($post_img2=='null')
      $post_img2=null;
      if($post_img3=='null')
      $post_img3=null;
      if($post_img4=='null')
      $post_img4=null;
      $data=[
        "name"=>$name_of_previous_post_owner,
        "avatar"=>'',
        "caption"=>$caption,
        "email"=>$email_of_previous_post_owner,
        "post_img1"=>$post_img1,
        "post_img2"=>$post_img2,
        "post_img3"=>$post_img3,
        "post_img4"=>$post_img4,
        "video"=>$video,
        "quote"=>$quote,
        "postid"=>$postid,
        "prev_id"=>$prev_id,
        "name_of_user_who_shared"=>$name_of_new_post_owner,
        "email_of_user_who_shared"=>$new_post_owner_email

      ];
      $new_shared_post=$this->ConverSation->sharePost($data);
      if($new_post_owner_email != $email_of_previous_post_owner)
        $this->userService->notifyUser($new_post_owner_email,$email_of_previous_post_owner,"$name_of_new_post_owner Said: $quote",$postid);
        if($tagged_users!=''){
          foreach($tagged_users as $user){
            $this->userService->notifyUser($new_post_owner_email,$user,"$name_of_new_post_owner tagged you in a response $quote",$postid);
          }
        }else{
          return;
        }
        return response([
          "reply"=>"true",
          "tagged_users"=>$tagged_users
        ]);
      }
      
     
      }
      
    }

    public function fetchAllSharedPost(Request $request){
      $current_user=$request->input('email');
      $find_all_shared_post=$this->userActivityService->fetchAllSharedPostsForCurrentUser($current_user);
      return $find_all_shared_post;
    }
    
    public function findUserPost(Request $request){
      $current_user=$request->input('email');
      $find_user_thoughts=User_Post::join('users','users.email','=','user__posts.email')->where('user__posts.email',$current_user)->select('user__posts.created_at',
      'user__posts.name','user__posts.caption','user__posts.email','user__posts.postid','user__posts.isReply', 'users.profile_picture')->latest()->take(10)->get();
      $store_all_post=array();
      foreach($find_user_thoughts as $post){
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
        array_push($store_all_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
        "email"=>base64_encode($post->email),"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
      ]);
      }
      return response([
        "reply"=> $store_all_post
      ]);
    }
    public function blockUser(Request $request){
      $user_who_block=$request->input('user_who_block');
      $user_getting_blocked=$request->input('user_getting_blocked');
      $check_if_user_who_blocks_know_this_user=user_match_choice::where(["user"=>$user_who_block, "choice"=>$user_getting_blocked])->delete();
      $check_if_user_who_is_getting_blocked_know_this_user=user_match_choice::where(["choice"=>$user_who_block, "user"=>$user_getting_blocked]);
      if($check_if_user_who_is_getting_blocked_know_this_user){
        $delete_from_user_who_is_getting_blocked_choice=user_match_choice::where(["user"=>$user_getting_blocked, "choice"=>$user_who_block])->delete();
      }else{
        return ;
      }
      Blocked_List::create([
        "user_who_block"=>$user_who_block,
        "user_getting_blocked"=>$user_getting_blocked
      ]);
      Blocked_List::create([
        "user_who_block"=>$user_getting_blocked,
        "user_getting_blocked"=>$user_who_block
      ]);
  

      return response([
        "reply"=>$check_if_user_who_blocks_know_this_user
      ]);
    }
    public function check_if_current_user_has_being_blocked(Request $request){
      $current_user=$request->input('current_user');
      $user_who_blocked=$request->input('user_who_blocked');
      $check_if_current_user_has_being_blocked=Blocked_List::where(['user_who_block'=>$user_who_blocked,'user_getting_blocked'=>$current_user])->first();
      if($check_if_current_user_has_being_blocked){
        return response([
          "reply"=>"true"
        ]);
      }else{
        return response([
          "reply"=>"false"
        ]);
      }
    }
    public function check_if_current_user_has_being_followed(Request $request){
      $current_user=$request->input('current_user');
      $user_to_follow=$request->input('user_to_follow');
      $check_if_current_user_has_being_followed=user_match_choice::where(['user'=>$current_user,'choice'=>$user_to_follow,'isPending'=>'false'])->first();
      if($check_if_current_user_has_being_followed){
        return response([
          "reply"=>"true",
          "isPending"=>""
        ]);
      }else{
        $check_if_current_user_has_not_being_followed=user_match_choice::where(['user'=>$current_user,'choice'=>$user_to_follow,'isPending'=>'true'])->first();
        if($check_if_current_user_has_not_being_followed){
          return response([
            "reply"=>"false",
            "isPending"=>"true"
          ]);
        }else{
          return response([
            "reply"=>"false",
            "isPending"=>""
          ]);
        }
       
      }
    }
    public function check_if_private_account_is_followed(Request $request){
      $current_user=$request->input('current_user');
      $user_to_follow=$request->input('user_to_follow');
      $check_if_current_user_has_being_followed=Follow_Request::where(['user_who_is_followed'=>$user_to_follow,'user_who_wants_to_follow'=>$current_user])->first();
      if($check_if_current_user_has_being_followed){
        return response([
          "reply"=>"true"
        ]);
      }else{
        return response([
          "reply"=>""
        ]);
      }
    }
    public function bookmarkPost(Request $request){
      $user=$request->input('email');
      $post_id=$request->input('post_id');
      $bookmark_user_post=$this->userActivityService->bookmarkUserPost($user,$post_id);
      return $bookmark_user_post;
    }
    public function findAllBookMarkedPosts(Request $request){
      $current_user=$request->input('email');
      $find_all_bookmarked_post=$this->userActivityService->findAllBookmarkedPost($current_user);
      return $find_all_bookmarked_post;
    }
    public function Search(Request $request){
      $search_info=$request->input('search_info');
      if(empty($search_info))
      return;
      else{
        $find_users=DB::select("SELECT first_name,last_name,email,profile_picture FROM users WHERE first_name REGEXP '(?=.*($search_info))' ORDER BY id asc");
        $keep_results=array();
        foreach($find_users as $get_user_search){
         array_push($keep_results,["first_name"=>$get_user_search->first_name,"last_name"=>$get_user_search->last_name,"profile_picture"=>$get_user_search->profile_picture,"email"=>base64_encode($get_user_search->email)]);
        }
        return response([
          "reply"=>$keep_results
        ]);
      }
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
    public function suggestUsers(Request $request){
      $email=$request->input('email');
      $suggest_people_to_user=$this->userService->suggestUsers($email);
      return $suggest_people_to_user;
    }
    public function findComment(Request $request){
      $comment_id=$request->input('comment_id');
     return $this->ConverSation->findComment($comment_id);
    }
    public function delete(array $publicId)
    {
        foreach($publicId as $id){
         Cloudinary::destroy($id);
        }
        
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
  public function findUserFollowers(Request $request){
    $email=$request->input('email');
    $find_user_followers=user_match_choice::join('users','user_match_choices.user','=','users.email')->where("user_match_choices.choice",$email)->take(10)->orderBy("user_match_choices.id","DESC")->get();
    $all_user_followers=array();
    foreach($find_user_followers as $followers){
      array_push($all_user_followers,["first_name"=>$followers->first_name,"last_name"=>$followers->last_name,"profile_picture"=>$followers->profile_picture,"cover_text"=>$followers->coverText,"follower"=>base64_encode($followers->user)]);
    }
    return response([
      "reply"=>$all_user_followers
    ]);
  }
  public function updateUserLastActivity(Request $request){
    $email=$request->input('email');
    $current_date=date('Y-m-d H:i:s');
    $update_last_activity=User::where('email',$email)->update([
      'updated_at'  =>$current_date,

    ]);
    return response([
      "reply"=>$update_last_activity
    ]);
  }
  public function sendResetLink(Request $request){
    $email=$request->input('email');
    $recipient=User::where('email',$email)->first();
    $current_date=date('Y-m-d');
    $reset_link=[
      'welcome_text'=>"We would help you get back on hexarex.com, $email",
       'body' => "Click on the link at the bottom of this email to reset your password",
       'call_to_action'=>'This link expires today, do not share this mail with anyone..',
       'url' => url("https://hexarex.com/reset/".base64_encode($current_date)."/".base64_encode($email).""),
       'conclusion' => "From hexarex.com",
       'id'  => 1
    ];
    Notification::send($recipient, new ResetPassword($reset_link));
    return response([
      "reply"=>"Reset Email sent successfuly"
    ]);
  }
  public function sendUpdateProfileLink(Request $request){
    $email=$request->input('email');
    $recipient=User::where('email',$email)->first();
    $current_date=date('Y-m-d');
    $reset_link=[
      'welcome_text'=>"You can update your profile via this mail, $email",
       'body' => "Click on the link at the bottom to update your profile",
       'call_to_action'=>'This link expires today, do not share this mail with anyone..',
       'url' => url("https://hexarex.com/updateprofile/".base64_encode($current_date)."/".base64_encode($email).""),
       'conclusion' => "From hexarex.com",
       'id'  => 1
    ];
    Notification::send($recipient, new ResetPassword($reset_link));
    return response([
      "reply"=>"We sent you an email so you can update your profile."
    ]);
  }
  public function updateDetails(Request $request){
    $data = $request->validate([
      'email' => 'required|email',
      'password' => [
          'required',
          Password::min(6)  // Ensures the password is at least 6 characters
      ]
  ]);

  // Retrieve the email and the password from the request
  $email = $request->input('email');
  $hashedPassword = bcrypt($request->input('password'));

  // Proceed with further logic (e.g., updating the user's password in the database)
  // You can find the user by email and update their password, for example:
  $user = User::where('email', $email)->first();
  
  if ($user) {
      $user->password = $hashedPassword;
      $user->save();

      return response()->json(['reply' => 'Password updated successfully']);
  } else {
      return response()->json(['reply' => 'User not found']);
  }
   
  }
}
