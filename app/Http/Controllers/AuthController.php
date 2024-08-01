<?php
namespace App\Http\Controllers;
date_default_timezone_set('Africa/Lagos');

use App\Mail\Signup;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\user_match_choice;
use App\Models\Notifications;
use App\Models\Channel;
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
use Pusher\Pusher;
use App\Events\VideoStream;
use Illuminate\Support\Facades\Log;
//use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File; 
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis;
use Psy\Readline\Userland;
use App\Notifications\WelcomeNotification;
use App\Events\MessageSent;
use DateTime;
use App\Services\UserServices;
use App\Services\ConverSationServices;
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

  public function __construct(UserServices $userService, ConverSationServices $ConverSation, CloudinaryServices $cloudinaryService)
  {
      $this->userService = $userService;
      $this->ConverSation=$ConverSation;
      $this->cloudinaryService = $cloudinaryService;
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
     $find_user_payment_status=DB::table('users')->where(['email' => $user_mail])->get();
     foreach($find_user_payment_status as $status){
      $get_if_user_has_paid=$status->isPaid;
      if($get_if_user_has_paid == "false"){
        return response([
          'user_status' => 'false'
        ]);
      }else{
        return response([
          'user_status' => 'true'
        ]);
      }
     }
     
   
    
    }
    public function checkIfUserHasCompleteProfile(Request $request) {
      $email=$request->input('email');
      $query_db=DB::table('users')->where(['email'=> $email])->get();
      foreach($query_db as $user_data){
        $first_name=$user_data->first_name;
        $last_name=$user_data->last_name;
        $location=$user_data->location;
        $phone_number=$user_data->phone_number;

        if(empty($first_name)||empty($last_name)||empty($location)||empty($phone_number)){
          return response([
            "info" => "false"
          ]);
        }else{
          return response([
            'first_name'=> $user_data->first_name,
            'last_name'=> $user_data->last_name,
            'location' => $user_data->location,
            'phone_number' => $user_data->phone_number,
            'profile_picture' => $user_data->profile_picture,
            'coverPhoto' =>$user_data->coverPhoto,
            'coverText' =>$user_data->coverText,
          ]);
        }
       

      }
      
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
      User::where('email',$email)->update([
        'coverText'  =>$cover_text,
  
      ]);
      return response([
        "reply"=>$cover_text
      ]);
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
      $find_user_choice=user_match_choice::where('user','=',$email)->get();
      $store_user_choice=array();
      foreach($find_user_choice as $user_choice){
       array_push($store_user_choice,$user_choice->choice);
      }
      $find_blocked_users=Blocked_List::where(['user_who_block'=>$email])->get()->unique('user_getting_blocked');
      $store_all_blocked_friends=array();
      foreach($find_blocked_users as $blocked_users){
      array_push($store_all_blocked_friends,$blocked_users->user_getting_blocked);
      }
      $find_user_info=User::where('email', '=', $email)->first();
      $user_location=$find_user_info['location'];

      $user_orientation=ucfirst($find_user_info['orientation']);
      switch($user_orientation){
        case 'Male':
          $find_user_match=User::inRandomOrder()->
           where('email', '!=', $email)
          ->whereNotIn('email',$store_user_choice)
          ->whereNotIn('email',$store_all_blocked_friends)
          ->where('location',$user_location)
          ->where('orientation','Female')
          ->latest('id')
          ->first();
          return response([
            "reply" => $find_user_match
          ]);
    
        break;
        case 'Female':
          $find_user_match=User::inRandomOrder()->
           where('email', '!=', $email)
          ->whereNotIn('email',$store_user_choice)
          ->whereNotIn('email',$store_all_blocked_friends)
          ->where('location',$user_location)
          ->where('orientation','Male')
          ->latest('id')
         ->first();
        return response([
          "reply" => $find_user_match
        ]);
        break;
        case 'Gay':
          $find_user_match=User::inRandomOrder()->
           where('email', '!=', $email)
          ->whereNotIn('email',$store_user_choice)
          ->where('location',$user_location)
          ->where('orientation','Gay')
          ->first();
        return response([
          "reply" => $find_user_match
        ]);
        break;
        case 'Bisexual':
          $find_user_match=User::inRandomOrder()->
           where('email', '!=', $email)
          ->whereNotIn('email',$store_user_choice)
          ->where('location',$user_location)
          ->where('orientation','Male')
          ->orwhere('orientation','Female')
          ->first();
        return response([
          "reply" => $find_user_match
        ]);
          break;
          case 'Lesbian':
            $find_user_match=User::inRandomOrder()->
             where('email', '!=', $email)
            ->whereNotIn('email',$store_user_choice)
            ->where('location',$user_location)
            ->where('orientation','Female')
            ->orwhere('orientation','Lesbian')
            ->first();
          return response([
            "reply" => $find_user_match
          ]);
            break;
          case 'Non Binary':
            $find_user_match=User::inRandomOrder()->
             where('email', '!=', $email)
            ->whereNotIn('email',$store_user_choice)
            ->where('location',$user_location)
            ->where('orientation','Non Binary')
            ->first();
          return response([
            "reply" => $find_user_match
          ]);
        break;
        default:
        return response([
          "reply" => "User match not found"
        ]);
      }
      
     
    }
   
    public function setUserMatch(Request $request){
      $user=$request->input('user');
      $find_user_details=User::where('email',$user)->first();
      $sender=$find_user_details['first_name'];
    
      $choice=$request->input('choice');
      user_match_choice::create([
        "user"=>$user,
        "choice"=>$choice
      ]);
      $this->userService->notifyUser($user,$choice,"$sender is following in you..","match");
      return response([
        "success" => "You tried matching with\t".$choice."\twe have notified them"
      ]);
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
      $get_poster_info=User::where('email',$email)->first();
      $avatar=$get_poster_info['profile_picture'];
      $name=$get_poster_info['first_name']."\t".$get_poster_info['last_name'];
      $user_caption=$request->input('user_caption');
      $tagged_users=$request->input('tagged_users');
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
    
    public function fetchUserPost(Request $request){
      $email=$request->input('email');
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
    
    public function fetchNewPost(Request $request){
      $email=$request->input('email');
      $find_all_post=User_Post::where('email',$email)->
                      latest('id')
                      ->first();
      return response([
        "reply" => $find_all_post
      ]);
    }
    public function uploadStory(Request $request){
      $request->validate([
        'story_post' => 'mimes:jpeg,jpg,png,mp4,JPG,JPEG,PNG,MP4,gif|max:500000'

      ]); 
      $email=$request->input('email');
      $file=$request->file('story_post');
      $this->cloudinaryService->uploadStory($file,$email);
    }
    public function uploadTextStory(Request $request){
      $user_text=$request->input('story_post');
      $user_bg_color=$request->input('bgColor');
      $email=$request->input('email');
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
    public function findUserStory(Request $request){
      $user_email=$request->input('email');
      $reply=Story::where('user_email',$user_email)->latest('id')->get();
      $hold_all_replies=array();
      foreach($reply as $story_post){
        array_push($hold_all_replies,["file"=>$story_post->user_image,"text"=>$story_post->user_text,"color"=>$story_post->background,"isVideoFile"=>$story_post->user_video]);
      }
      
      return response([
        "reply"=>$hold_all_replies,
        
      ]);
    }
    public function Delete_All_Old_User_Stories(){
      $current_time_in_24hrs_format=date('Y-m-d');
      $fetch_all_old_story_files=Story::where('date_posted','<',$current_time_in_24hrs_format)->get();
      foreach($fetch_all_old_story_files as $old_story_files){
        $old_story="storage/".$old_story_files->user_image;
        File::delete(public_path($old_story));
      }
      Story::where('date_posted','<',$current_time_in_24hrs_format)->delete();
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
        "email"=>$post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
      ]);
      }
      return response([
        "reply" => $store_all_post
      ]);
    }
    public function fetchRandomPost(Request $request){
      $user=$request->input('email');
      $fetch_user_friends=user_match_choice::where('user',$user)->get();
      $hold_all_user_friends=array();
      foreach($fetch_user_friends as $get_friend){
        array_push($hold_all_user_friends,$get_friend->choice);
      }
      $get_post=DB::table('user__posts')->join('users','user__posts.email','=','users.email')->whereIn('user__posts.email',$hold_all_user_friends)->select('user__posts.name','user__posts.caption', 'user__posts.postid', 'user__posts.isReply', 'user__posts.created_at','users.profile_picture','user__posts.email')->inRandomOrder()->orderBy('user__posts.created_at','ASC','DESC')->take(10)->get();
      $get_channel_post=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->whereIn('channel_posts.email',$hold_all_user_friends)->select('channel_posts.name','channel_posts.caption','channel_posts.postid', 'channel_posts.isReply', 'channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','channel_posts.created_at','users.profile_picture','channel_posts.email','channel_posts.id','users.first_name','users.last_name')->inRandomOrder()->orderBy('channel_posts.created_at','ASC','DESC')->take(10)->get();
      $store_all_post=array();
      $store_all_channel_post=array();
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
        "email"=>$new_post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
      ]);
      }
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
          "email"=>$new_post->email,"img_1"=>$new_post->post_img1,"img_2"=>$new_post->post_img2,"img_3"=>$new_post->post_img3,"img_4"=>$new_post->post_img4,"video"=>$new_post->video,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection),"shares"=>count($post_shares_collection)
        ]);
        }
   
      return response([
        "reply" => $store_all_post,
        "channel_reply"=> $store_all_channel_post
        
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
   
    public function findChannelPost(Request $request){
      $email=$request->input('email');
      $name=$request->input('name');
      $avatar=$request->input('avatar');
      $caption=$request->input('caption');
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
        "email"=>$post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
      ]);
      }
      return response([
        "reply"=>$store_all_post
      ]);
    }
    public function deleteChannelPost(Request $request){
      $email=$request->input('email');
      $post_id=$request->input('post_id');
      $caption=$request->input('caption');
      $channel_post= DB::table('channel_posts')->where("id",$post_id)->first();
      $image_1=$request->input('image1');
      $image_2=$request->input('image2');
      $image_3=$request->input('image3');
      $image_4=$request->input('image4');
      $video=$request->input('video');
      $publicId=[$channel_post->post_img1, $channel_post->post_img2, $channel_post->post_img3, $channel_post->post_img4];
      $publicIds = array_filter($publicId);
      $this->delete($publicIds);
    $deleteSuccess= DB::table('channel_posts')->where("id",$post_id)->delete();
   /*  File::delete(public_path("storage/".$image_1));
     File::delete(public_path("storage/".$image_2));
     File::delete(public_path("storage/".$image_3));
     File::delete(public_path("storage/".$image_4));
     File::delete(public_path("storage/".$video)); **/
      return response([
       "reply"=>$deleteSuccess
      ]);
    }
    public function delete(array $publicId)
    {
        foreach($publicId as $id){
         Cloudinary::destroy($id);
        }
        
    }
    public function deleteUserPost(Request $request){
      $postid=$request->input('postid');
      $user_post= DB::table('user__posts')->where("postid",$postid)->delete();
      if($user_post){
        return response([
          "reply"=>$user_post
        ]);
      }else{
        $user_post= DB::table('shared__posts')->where("postid",$postid)->delete();
        return response([
          "reply"=>$user_post
        ]);
      }
     
    }
    public function fetchRandomChannelPost(Request $request){
      $user=$request->input('email');
      $get_post=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->where('channel_posts.email',$user)->select('channel_posts.name','channel_posts.caption','channel_posts.created_at','channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','users.profile_picture','channel_posts.email','channel_posts.id')->inRandomOrder()->orderBy('channel_posts.id','ASC')->first();
      $store_all_post=array(["name"=>$get_post->name, "caption"=>$get_post->caption, "post_img1"=>$get_post->post_img1, "post_img2"=>$get_post->post_img2, "post_img3"=>$get_post->post_img3,"post_img4"=>$get_post->post_img4,"video"=>$get_post->video, "date"=>$get_post->created_at,"avatar"=>$get_post->profile_picture, 
        "email"=>$get_post->email,"id"=>$get_post->id
        ]);
   
      return response([
        "reply" => $store_all_post
      ]);
    }
    public function fetchAllChannelsPost(Request $request){
      $current_user=$request->input('email');
     $find_user_choice= user_match_choice::where('user',$current_user)->get();
     $keep_all_persons_who_user_follow=array();
     foreach($find_user_choice as $person_who_user_follows){
      array_push($keep_all_persons_who_user_follow, $person_who_user_follows->choice);
     }
     $find_all_channels_of_who_user_follows_post=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->join('channels','channel_posts.email','=','channels.channel_owner')
     ->whereIn('channel_posts.email',$keep_all_persons_who_user_follow)->select('channel_posts.name','channel_posts.caption','channel_posts.created_at','channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','channel_posts.postid', 'channel_posts.isReply', 'users.profile_picture','users.first_name','users.last_name','channels.channel_bio',   'channel_posts.email','channel_posts.id')->inRandomOrder()->take(10)->latest()->get();
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
      "email"=>$post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
    ]);
    }
      return response([
        "reply" => $store_all_post
      ]);

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
        "caption"=>$check_post_table->caption, "avatar"=>$check_post_table->profile_picture,"user_email"=>$check_post_table->email, "post_date"=>$check_post_table->created_at, "prev_id"=>$check_post_table->postid, "isReply"=>$check_post_table->isReply,   "img_1"=>null,"img_2"=>null,"img_3"=>null,"img_4"=>null,"video"=>null);
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
          "caption"=>$check_channels_table->caption, "avatar"=>$check_channels_table->profile_picture, "user_email"=>$check_channels_table->email, "post_date"=>$check_channels_table->created_at, "prev_id"=>$check_channels_table->postid,
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
            "caption"=>$check_shared_posts_table->caption, "avatar"=>$check_shared_posts_table->profile_picture, "user_email"=>$check_shared_posts_table->email, "post_date"=>$check_shared_posts_table->created_at,
            "img_1"=>$check_shared_posts_table->post_img1, "img_2"=>$check_shared_posts_table->post_img2, "img_3"=>$check_shared_posts_table->post_img3, "img_4"=>$check_shared_posts_table->post_img4,"video"=>$check_shared_posts_table->video,"sharer_name"=>$check_shared_posts_table->name_of_user_who_shared,"sharer_email"=>$check_shared_posts_table->email_of_user_who_shared,
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
      $check_shared_posts_table=DB::table('shared__posts')->join('users','shared__posts.email','=','users.email')->select('shared__posts.name','shared__posts.caption','users.profile_picture','shared__posts.email','shared__posts.created_at','shared__posts.post_img1','shared__posts.post_img2','shared__posts.post_img3','shared__posts.post_img4','shared__posts.video','shared__posts.name_of_user_who_shared','shared__posts.email_of_user_who_shared','shared__posts.quote','shared__posts.postid','shared__posts.prev_id')->where('shared__posts.prev_id',$post_id)->get();
      $all_quote_posts=array();
      foreach($check_shared_posts_table as $shared_posts){
      $check_no_of_users_who_comment=DB::select("SELECT count(comment) AS total_comments FROM comments WHERE post_id='".$shared_posts->postid."'");
      $check_no_of_users_who_shared=DB::select("SELECT count(quote) AS total_shares FROM shared__posts WHERE prev_id='".$shared_posts->postid."'");  
      $check_no_of_users_who_liked=DB::select("SELECT count(user_who_liked) AS total_like FROM post_likes WHERE post_id='".$shared_posts->postid."'");    
      $check_no_of_users_who_bookmarked=DB::select("SELECT count(user_who_bookmark) AS total_bookmarks FROM bookmarks WHERE bookmarked_post_id='".$shared_posts->postid."'");
      array_push($all_quote_posts,["name"=>$shared_posts->name, "caption"=>$shared_posts->caption, "profile_picture"=>$shared_posts->profile_picture, "email"=>$shared_posts->email, "created_at"=>$shared_posts->created_at, "post_img1"=>$shared_posts->post_img1, "post_img2"=>$shared_posts->post_img2, "post_img3"=>$shared_posts->post_img3, "post_img4"=>$shared_posts->post_img4, "video"=>$shared_posts->video, "name_of_user_who_shared"=>$shared_posts->name_of_user_who_shared, "email_of_user_who_shared"=>$shared_posts->email_of_user_who_shared, "quote"=>$shared_posts->quote,"postid"=>$shared_posts->postid, "prev_id"=>$shared_posts->prev_id]);

      }
      return response([
        "reply"=>$all_quote_posts,
        "total_comment"=>$check_no_of_users_who_comment,
        "total_likes"=>$check_no_of_users_who_liked,
        "total_shares"=>$check_no_of_users_who_shared,
        "total_bookmarks"=>$check_no_of_users_who_bookmarked
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
      return $this->ConverSation->createUserComment($data);
    }
    public function findAllComments(Request $request){
      $post_id=$request->input("post_id");
      $find_all_comment=DB::table('comments')->join('users','comments.user_who_comment','=','users.email')->where('comments.post_id',$post_id)->select('users.first_name','users.profile_picture', 'comments.created_at','comments.comment','comments.user_who_comment','comments.post_id','comments.comment_id')->orderBy('comments.created_at','DESC')->get();
      $hold_each_comment_reply_count=array();
      foreach($find_all_comment as $comments){
        $each_comment_unique_id=$comments->comment_id;
        $find_comment_reply_count=DB::select("SELECT count(comment_reply) AS total_reply FROM comment__replies WHERE initial_comment_id='$each_comment_unique_id'");
        foreach($find_comment_reply_count as $get_reply_count){
          array_push($hold_each_comment_reply_count,["first_name"=>$comments->first_name, "profile_picture"=>$comments->profile_picture,"created_at"=>$comments->created_at,"comment"=>$comments->comment,"user_who_comment"=>$comments->user_who_comment, "post_id"=>$comments->post_id, "comment_id"=>$comments->comment_id,"reply_count"=>$get_reply_count->total_reply]);
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
      Notifications::where('owner',$current_user)->update([
        'owner_has_read'  =>"true",
  
      ]);
      return response([
        "reply" => $find_all_user_notifications
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
        $send_message=Messages::create([
          "unique_id"=>$unique_id,
          "conversation"=>$conversation,
          "file"=>$upload->getPublicId(),
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

      $data = [$send_message];

      $pusher->trigger('chat', 'send_message', $data);
        return response([
          "reply"=> $send_message
        ]);
    }elseif (in_array($file_ext,$img_files_array)) {
      $unique_id=$request->input('unique_id');
      $conversation=$request->input('conversation');
      $sender=$request->input('sender');
      $reciever=$request->input('reciever');
      $isRead="false";
      $send_message=Messages::create([
        "unique_id"=>$unique_id,
        "conversation"=>$conversation,
        "file"=>$upload->getPublicId(),
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

    $data = [$send_message];

    $pusher->trigger('chat', 'send_message', $data);
      return response([
        "reply"=> $send_message
      ]);
    }elseif (in_array($file_ext,$doc_files_array)) {
      $unique_id=$request->input('unique_id');
      $conversation=$request->input('conversation');
      $sender=$request->input('sender');
      $reciever=$request->input('reciever');
      $isRead="false";
      $send_message=Messages::create([
        "unique_id"=>$unique_id,
        "conversation"=>$conversation,
        "file"=>$upload->getPublicId(),
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

    $data = [$send_message];

    $pusher->trigger('chat', 'send_message', $data);
      return response([
        "reply"=> $send_message
      ]);
    }elseif (in_array($file_ext,$audio_files_array)) {
      $unique_id=$request->input('unique_id');
      $conversation=$request->input('conversation');
      $sender=$request->input('sender');
      $reciever=$request->input('reciever');
      $isRead="false";
      $send_message=Messages::create([
        "unique_id"=>$unique_id,
        "conversation"=>$conversation,
        "file"=>$upload->getPublicId(),
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

    $data = [$send_message];

    $pusher->trigger('chat', 'send_message', $data);
      return response([
        "reply"=> $send_message
      ]);
    }
        
      }else{
        $unique_id=$request->input('unique_id');
        $conversation=$request->input('conversation');
        $sender=$request->input('sender');
        $reciever=$request->input('reciever');
        $isRead="false";
        $send_message=Messages::create([
          "unique_id"=>$unique_id,
          "conversation"=>$conversation,
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

      $data = [$send_message];

      $pusher->trigger('chat', 'send_message', $data);
        return response([
          "reply"=> $send_message
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
        array_push($all_messages,$users_conversation);
      }
   
      return response([
        "reply"=>$all_messages,
        "sender"=>$sender,
        "reciever"=>$reciever,

      ]);
    }
    public function findAllMessages(Request $request){
      $current_user=$request->input('email');
      $find_all_user_message=DB::table('messages')->join('users','users.email','=','messages.sender')->where('messages.reciever',$current_user)->where('messages.conversation','!=','')->
      select('messages.created_at','messages.sender','messages.unique_id','messages.isRead', 'messages.conversation','messages.file','users.profile_picture','users.first_name','users.last_name')->orderBy('messages.id','desc')->get()->unique('sender');
      return response([
        "reply"=>$find_all_user_message
      ]);
    }
    public function findRecieverInfo(Request $request){
      $msg_reciever=$request->input('user');
      $find_reciever_info=DB::table('users')->where('email',$msg_reciever)->select('first_name','last_name','profile_picture')->first();
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
          "reply_id"=>$comment_replies->reply_id, "user_being_replied"=>$comment_replies->user_being_replied,"user_who_replied"=>$comment_replies->user_who_replied,"profile_picture"=>$comment_replies->profile_picture,"first_name"=>$comment_replies->first_name,
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
      $find_all_photos=DB::table("channel_posts")->where("email",$email)->where("post_img1", '!=', '')->select('post_img1','postid','post_img2','post_img3','post_img4')->take(10)->get();
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
      $find_all_photos=DB::table("channel_posts")->where("email",$email)->where("video", '!=', '')->select('video','postid')->take(10)->get();
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
      $check_if_user_has_shared_post_before=Shared_Post::where(['prev_id'=>$prev_id,'email_of_user_who_shared'=>$new_post_owner_email])->first();
      if($check_if_user_has_shared_post_before){
      return response([
        "reply"=>"false"
      ]);
      }else{
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
     $find_user_choice= user_match_choice::where('user',$current_user)->get();
     $keep_all_persons_who_user_follow=array();
     foreach($find_user_choice as $person_who_user_follows){
      array_push($keep_all_persons_who_user_follow, $person_who_user_follows->choice);
     }
     $store_all_post=array();
     $find_all_shared_posts_from_who_user_follows=DB::table('shared__posts')->join('users','shared__posts.email_of_user_who_shared','=','users.email')
     ->whereIn('shared__posts.email_of_user_who_shared',$keep_all_persons_who_user_follow)->select('shared__posts.name','shared__posts.caption','shared__posts.created_at','shared__posts.post_img1','shared__posts.post_img2','shared__posts.post_img3','shared__posts.post_img4','shared__posts.video','shared__posts.quote','shared__posts.postid','shared__posts.prev_id','shared__posts.isReply', 'shared__posts.name_of_user_who_shared','shared__posts.email','shared__posts.email_of_user_who_shared','users.profile_picture')->inRandomOrder()->take(10)->latest('shared__posts.id')->get();
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
    array_push($store_all_post,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"name_of_user_who_shared"=>$post->name_of_user_who_shared,"email_of_user_who_shared"=>$post->email_of_user_who_shared,"quote"=>$post->quote,"prev_id"=>$post->prev_id, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
    "email"=>$post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection), "avatar_of_original_poster"=>$find_avatar_of_original_poster->profile_picture
  ]);
    }
      return response([
        "reply" => $store_all_post
      ]);

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
        "email"=>$post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
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
      $check_if_current_user_has_being_followed=user_match_choice::where(['user'=>$current_user,'choice'=>$user_to_follow])->first();
      if($check_if_current_user_has_being_followed){
        return response([
          "reply"=>"true"
        ]);
      }else{
        return response([
          "reply"=>"false"
        ]);
      }
    }
    public function bookmarkPost(Request $request){
      $user=$request->input('email');
      $post_id=$request->input('post_id');
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
    public function findAllBookMarkedPosts(Request $request){
      $current_user=$request->input('email');
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
        "email"=>$post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
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
       "email"=>$post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
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
    array_push($store_all_bookmarked_shared_posts,["name"=>$post->name,  "caption"=>$post->caption,"created_at"=>$post->created_at,"post_img1"=>$post->post_img1,"post_img2"=>$post->post_img2,"post_img3"=>$post->post_img3, "post_img4"=>$post->post_img4,"video"=>$post->video,"name_of_user_who_shared"=>$post->name_of_user_who_shared,"email_of_user_who_shared"=>$post->email_of_user_who_shared,"quote"=>$post->quote,"prev_id"=>$post->prev_id, "postid"=>$post->postid, "isReply"=>$post->isReply,   "profile_picture"=>$post->profile_picture, 
    "email"=>$post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection)
  ]);
    }
      return response([
        "user_post_reply"=>$store_all_bookmarked_posts,
        "channel_post_reply"=>$store_all_bookmarked_channels_post,
        "shared_post_reply"=>$store_all_bookmarked_shared_posts

      ]);
    }
    public function Search(Request $request){
      $search_info=$request->input('search_info');
      if(empty($search_info))
      return;
      else{
        $find_users=DB::select("SELECT first_name,last_name,email,profile_picture FROM users WHERE first_name REGEXP '(?=.*($search_info))' ORDER BY id asc");
        $keep_results=array();
        foreach($find_users as $get_user_search){
         array_push($keep_results,$get_user_search);
        }
        return response([
          "reply"=>$keep_results
        ]);
      }
    }
    public function findAllUserReply(Request $request){
      $email=$request->input('user_email');
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
      "email"=>$post->email,"likes"=>count($post_like_collection),"comments"=>count($post_comments_collection), "shares"=>count($post_shares_collection),"avatar_of_original_poster"=>$find_avatar_of_original_poster->profile_picture
    ]);
      }
      return response([
        "reply"=>$store_all_post
      ]);

    }
    public function disableUserComment(Request $request){
      $postid=$request->input('postid');
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
    public function find_user_followers(Request $request){
      $email=$request->input('email');
      $find_no_of_user_followers=DB::table('user_match_choices')->where('choice',$email)->get();
      $user_followers=array();
      foreach($find_no_of_user_followers as $followers){
        array_push($user_followers,$followers->choice);
      }
      return response([
        "followers"=>count($user_followers)
      ]);
    }
    public function suggestUsers(Request $request){
      $email=$request->input('email');
      $get_user_interest=DB::table('users')->select('location','school','orientation','birthday','religion','interest')->where('email',$email)->first();
      $user_interest=$get_user_interest->interest;
      $user_orientation=$get_user_interest->orientation;
      $user_religion=$get_user_interest->religion;
      $user_school=$get_user_interest->school;
      $store_all_members_who_user_is_following=array();
      $check_if_users_have_been_followed_before=user_match_choice::where('user',$email)->get();
      foreach($check_if_users_have_been_followed_before as $following){
        array_push($store_all_members_who_user_is_following,$following->choice);
      }
      $store_all_members_with_similar_interest=array();
      $store_all_members_with_similar_religion=array();
      $find_users_with_similar_interest=DB::table('users')->select('first_name','last_name','profile_picture','email')->where(['interest'=>$user_interest])->whereNotIn('email',$store_all_members_who_user_is_following)->where('email', '!=', $email)->where('interest', '!=', '')->latest()->take(12)->get();
      foreach($find_users_with_similar_interest as $user_with_similar_interest){
        array_push($store_all_members_with_similar_interest,$user_with_similar_interest->email);
      }
      $find_users_with_similar_religion=DB::table('users')->select('first_name','last_name','profile_picture','email')->where(['religion'=>$user_religion])->whereNotIn('email',$store_all_members_who_user_is_following)->whereNotIn('email',$store_all_members_with_similar_interest)->where('email', '!=', $email)->where('religion', '!=', '')->latest()->take(12)->get();
      foreach($find_users_with_similar_religion as $user_with_similar_religion){
        array_push($store_all_members_with_similar_religion,$user_with_similar_religion->email);
      }
      $find_users_with_similar_college=DB::table('users')->select('first_name','last_name','profile_picture','email')->where(['school'=>$user_school])->whereNotIn('email',$store_all_members_who_user_is_following)->whereNotIn('email',$store_all_members_with_similar_religion)->where('email', '!=', $email)->where('school', '!=', '')->latest()->take(12)->get();
      
      return response([
        "user_with_similar_interest"=>$find_users_with_similar_interest,
        "user_with_similar_religion"=>$find_users_with_similar_religion,
        "user_with_similar_school"=>$find_users_with_similar_college,
        "user_interest"=>$user_interest,
        "user_religion"=>$user_religion,
        "user_orientation"=>$user_orientation
      ]);
      
    }
    public function generateLink(Request $request){
        $uniqueId = Str::random(8);
        return response()->json(['link' => url('/live/' . $uniqueId)]);
    }
    public function broadcastVideo(Request $request)
    {
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ]
        );

        $data = $request->input('streamData');
        $streamId = $request->input('streamId');
        $isLastChunk = $request->input('chunk', false);

        try {
            $pusher->trigger('live-stream.' . $streamId, 'video-chunk', [
                'streamData' => $data,
                'isLastChunk' => $isLastChunk,
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error broadcasting video: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function findComment(Request $request){
      $comment_id=$request->input('comment_id');
     return $this->ConverSation->findComment($comment_id);
    }
}
