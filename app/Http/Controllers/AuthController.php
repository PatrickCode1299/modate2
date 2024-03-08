<?php
namespace App\Http\Controllers;
date_default_timezone_set('Africa/Lagos');
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\user_match_choice;
use App\Models\Notifications;
use App\Models\Channel;
use App\Models\ChannelPost;
use App\Models\User_Post;
use App\Models\Comment;
use App\Models\Story;
use App\Models\PostLike;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\File; 
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Psy\Readline\Userland;

header('Access-Control-Allow-Origin','*');
header('Access-Control-Allow-Methods','GET,POST,PUT,PATCH,DELETE,OPTIONS');
header('Access-Control-Allow-Headers','Content-Type, Authorization');
class AuthController extends Controller
{
  
    public function Signup(Request $request){
        $data=$request->validate([
            'email' => 'required|email|unique:users',
            'password' => [
              'required',
              Password::min(6)
            ]
        ]);
      $user=User::create([
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
        'isPaid' => "false"
      ]);
      if(!$user){
        return response([
          'error' => 'Email already choosen by a user'
        ]);
      }else{
        $user=$data['email'];
        $reg_date=date('Y');
        $random=rand(10,1000000);
        $token=$reg_date.$random;
        return response([
          'user' => $user,
          'token' => $token
        ]);
      }
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
            'coverText' =>$user_data->coverText
          ]);
        }
       

      }
      
    }
    public function updateUserPicture(Request $request){
     $request->validate([
        'profile_pic' => 'required|image|mimes:jpeg,jpg,png,JPG,JPEG,PNG,gif|max:2048'

      ]); 
      $image_path=$request->file('profile_pic')->store('users_pic','public');
      $owner=$request->input('owner');
      $check_if_user_has_picture=User::where('email',$owner)->first();
      if($check_if_user_has_picture['profile_picture'] != null){
        $old_profile_picture="storage/".$check_if_user_has_picture['profile_picture'];
        File::delete(public_path($old_profile_picture));
      }
      User::where('email',$owner)->update([
        'profile_picture'  =>$image_path,
  
      ]);
      return response([
        'update' => "Profile Picture Updated Sucessfully"
      ]);
    }
    public function updateCoverPhoto(Request $request){
      $request->validate([
         'coverPhoto' => 'required|image|mimes:jpeg,jpg,png,JPG,JPEG,PNG,gif|max:2048'
 
       ]); 
       $image_path=$request->file('coverPhoto')->store('cover_photo','public');
       $owner=$request->input('owner');
       $check_if_user_has_picture=User::where('email',$owner)->first();
       if($check_if_user_has_picture['coverPhoto'] != null){
         $old_cover_photo="storage/".$check_if_user_has_picture['coverPhoto'];
         File::delete(public_path($old_cover_photo));
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
    User::where('email',$email)->update([
      'first_name'  =>$first_name,
      'last_name'   =>$last_name,
      'orientation' =>$gender,
      'location'    =>$state,
      'birthday'    =>$birthday,
      'phone_number'=>$phone

    ]);
    
      return response([
        "success" => "Updated User Profile Successfully"
      ]);
    }

    
    public function findUserMatch(Request $request){
      $email=$request->input('data');
      $find_user_choice=user_match_choice::where('user','=',$email)->get();
      $store_user_choice=array();
      foreach($find_user_choice as $user_choice){
       array_push($store_user_choice,$user_choice->choice);
      }
      $find_user_info=User::where('email', '=', $email)->first();
      $user_location=$find_user_info['location'];

      $user_orientation=ucfirst($find_user_info['orientation']);
      switch($user_orientation){
        case 'Male':
          $find_user_match=User::inRandomOrder()->
           where('email', '!=', $email)
          ->whereNotIn('email',$store_user_choice)
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
    public function notify($from,$owner,$info,$source){
      Notifications::create([
        "from"=>$from,
        "owner"=>$owner,
        "info"=>$info,
        "source"=>$source,
        "owner_has_read"=>"false"
      ]);
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
      $this->notify($user,$choice,"$sender is interested in you..","match");
      return response([
        "success" => "You tried matching with\t".$choice."\twe have notified them"
      ]);
    }
    public function createUserPost(Request $request){
      $email=$request->input('email');
      $get_poster_info=User::where('email',$email)->first();
      $avatar=$get_poster_info['profile_picture'];
      $name=$get_poster_info['first_name']."\t".$get_poster_info['last_name'];
      $user_caption=htmlspecialchars($request->input('user_caption'));
      $post_id=rand(10,100000) . date('d');
      User_Post::create([
        "name"=>$name,
        "avatar"=>$avatar,
        "caption"=>$user_caption,
        "email"=>$email,
        "postid"=>$post_id
      ]);
      return response([
        "reply"=>"Post added successfully"
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
        'story_post' => 'required|image|mimes:jpeg,jpg,png,JPG,JPEG,PNG,gif|max:3048'

      ]); 
      $email=$request->input('email');
      $image_path=$request->file('story_post')->store('users_story','public');
      $story_day=date('H');
      Story::create([
        "user_email"=>$email,
        "user_image"=>$image_path,
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
        array_push($hold_all_replies,$story_post->user_image);
      }
      
      return response([
        "reply"=>$hold_all_replies,
        
      ]);
    }
    public function Delete_All_Old_User_Stories(){
      $current_time_in_24hrs_format=date('H');
      $fetch_all_old_story_files=Story::where('date_posted','>',$current_time_in_24hrs_format)->get();
      foreach($fetch_all_old_story_files as $old_story_files){
        $old_story="storage/".$old_story_files->user_image;
        File::delete(public_path($old_story));
      }
 
        

   
      Story::where('date_posted','>',$current_time_in_24hrs_format)->delete();
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
     if($number_of_friends_story > 3){
        $paginate="true";
        
     }else{
        $paginate="false";
     }
     return response([
      "reply"=>$user_friends_details_collection,
      "pagination"=>$paginate
     ]);
    }
    public function fetchAllPost(Request $request){
      $user=$request->input('email');
      $fetch_user_friends=user_match_choice::where('user',$user)->get();
      $hold_all_user_friends=array();
      foreach($fetch_user_friends as $get_friend){
        array_push($hold_all_user_friends,$get_friend->choice);
      }
      $find_all_user_friend_post=DB::table('user__posts')->join('users','user__posts.email','=','users.email')->whereIn('user__posts.email',$hold_all_user_friends)->select('user__posts.name','user__posts.caption', 'user__posts.postid','user__posts.created_at','users.profile_picture','users.email')->inRandomOrder()->orderBy('user__posts.created_at','DESC')->take(5)->get();
      $store_all_post=array();
      foreach($find_all_user_friend_post as $post){
        array_push($store_all_post, $post);
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
      $get_post=DB::table('user__posts')->join('users','user__posts.email','=','users.email')->whereIn('user__posts.email',$hold_all_user_friends)->select('user__posts.name','user__posts.caption', 'user__posts.postid','user__posts.created_at','users.profile_picture','user__posts.email')->inRandomOrder()->orderBy('user__posts.created_at','ASC','DESC')->first();
      $store_all_post=array(["name"=>$get_post->name, "caption"=>$get_post->caption,"date"=>$get_post->created_at, "postid"=>$get_post->postid,   "avatar"=>$get_post->profile_picture, 
        "email"=>$get_post->email
        ]);
   
      return response([
        "reply" => $store_all_post
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
      $post_id=rand(10,100000) . date('d');
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
        $image_path=$request->file('image1')->store('users_posts','public');
        $create_post=ChannelPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $image_path,
          "postid"  => $post_id
         
  
        ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && !empty($request->file('image2')) && empty($request->file('image3')) && empty($request->file('image4'))){
        $image_path=$request->file('image1')->store('users_posts','public');
        $image_path2=$request->file('image2')->store('users_posts','public');
        $create_post=ChannelPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $image_path,
          "post_img2" => $image_path2,
          "postid"  => $post_id
  
        ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && !empty($request->file('image2')) && !empty($request->file('image3')) && empty($request->file('image4'))){
        $image_path=$request->file('image1')->store('users_posts','public');
        $image_path2=$request->file('image2')->store('users_posts','public');
        $image_path3=$request->file('image3')->store('users_posts','public');
        $create_post=ChannelPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $image_path,
          "post_img2" => $image_path2,
          "post_img3" => $image_path3,
          "postid"  => $post_id

  
        ]);
        return response([
          "reply"=>$create_post
        ]);
      }else if(!empty($request->file('image1')) && !empty($request->file('image2')) && !empty($request->file('image3')) && !empty($request->file('image4'))){
        $image_path=$request->file('image1')->store('users_posts','public');
        $image_path2=$request->file('image2')->store('users_posts','public');
        $image_path3=$request->file('image3')->store('users_posts','public');
        $image_path4=$request->file('image4')->store('users_posts','public');
        $create_post=ChannelPost::create([
          "email" => $email,
          "name"  => $name,
          "avatar" => $avatar,
          "caption" => $caption,
          "post_img1" => $image_path,
          "post_img2" => $image_path2,
          "post_img3" => $image_path3,
          "post_img4" => $image_path4,
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
      $channel_post=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->where('channel_posts.email',$email)->select('channel_posts.name','channel_posts.caption','channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','channel_posts.created_at','users.profile_picture','channel_posts.email','channel_posts.id')->latest('channel_posts.id')->take(10)->get();
      return response([
        "reply"=>$channel_post
      ]);
    }
    public function deleteChannelPost(Request $request){
      $email=$request->input('email');
      $post_id=$request->input('post_id');
      $caption=$request->input('caption');
      $channel_post= DB::table('channel_posts')->where("id",$post_id)->delete();
      $image_1=$request->input('image1');
      $image_2=$request->input('image2');
      $image_3=$request->input('image3');
      $image_4=$request->input('image4');
     File::delete(public_path("storage/".$image_1));
     File::delete(public_path("storage/".$image_2));
     File::delete(public_path("storage/".$image_3));
     File::delete(public_path("storage/".$image_4));
      return response([
       "reply"=>$channel_post
      ]);
    }
    public function fetchRandomChannelPost(Request $request){
      $user=$request->input('email');
      $get_post=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->where('channel_posts.email',$user)->select('channel_posts.name','channel_posts.caption','channel_posts.created_at','channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','users.profile_picture','channel_posts.email','channel_posts.id')->inRandomOrder()->orderBy('channel_posts.id','ASC')->first();
      $store_all_post=array(["name"=>$get_post->name, "caption"=>$get_post->caption, "post_img1"=>$get_post->post_img1, "post_img2"=>$get_post->post_img2, "post_img3"=>$get_post->post_img3,"post_img4"=>$get_post->post_img4, "date"=>$get_post->created_at,"avatar"=>$get_post->profile_picture, 
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
     $find_all_channels_of_who_user_follows=DB::table('channel_posts')->join('users','channel_posts.email','=','users.email')->join('channels','channel_posts.email','=','channels.channel_owner')
     ->whereIn('channel_posts.email',$keep_all_persons_who_user_follow)->select('channel_posts.name','channel_posts.caption','channel_posts.created_at','channel_posts.post_img1','channel_posts.post_img2','channel_posts.post_img3','channel_posts.post_img4','channel_posts.video','channel_posts.postid', 'users.profile_picture','channels.channel_bio',   'channel_posts.email','channel_posts.id')->inRandomOrder()->take(10)->orderBy('channel_posts.id','DESC')->get();
      return response([
        "reply" => $find_all_channels_of_who_user_follows
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
       // $this->notify($user,$choice,"$sender is interested in you..");
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
      $check_post_table=User_Post::where('postid',$post_id)->first();
      if($check_post_table){
        $show_all_post_info=array("name"=>$check_post_table['name'],
        "caption"=>$check_post_table['caption'], "avatar"=>$check_post_table['avatar'],"user_email"=>$check_post_table['email'], "post_date"=>$check_post_table['created_at'],"img_1"=>null,"img_2"=>null,"img_3"=>null,"img_4"=>null
      );
        return response([
          "reply" => $show_all_post_info
        ]);
      }else{
        $check_channels_table=ChannelPost::where('postid',$post_id)->first();
        $show_all_post_info=array("name"=>$check_channels_table['name'],
        "caption"=>$check_channels_table['caption'], "avatar"=>$check_channels_table['avatar'], "user_email"=>$check_channels_table['email'], "post_date"=>$check_channels_table['created_at'],
        "img_1"=>$check_channels_table['post_img1'], "img_2"=>$check_channels_table['post_img2'], "img_3"=>$check_channels_table['post_img3'], "img_4"=>$check_channels_table['post_img4']
      );
        return response([
          "reply" => $show_all_post_info
        ]);
      }
   
     
    }
    public function postComment(Request $request){
      $post_id=$request->input('post_id');
      $comment=$request->input('comment');
      $user_who_comment=$request->input('user');
      $post_owner=$request->input('post_owner');
      $user_who_comment_name=$request->input('user_who_comment_name');
      $current_date=date('Y-m-d H:i:s');
      $post_user_comment=Comment::create([
        "comment"=>$comment,
        "user_who_comment"=>$user_who_comment,
        "post_id"=>$post_id
      ]);
      if($post_owner === $user_who_comment){
        
      }else{
        $this->notify($user_who_comment,$post_owner,"$user_who_comment_name made a comment on your post.",$post_id);
      }
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
        "reply"=>$all_info
      ]);
    }
    public function findAllComments(Request $request){
      $post_id=$request->input("post_id");
      $find_all_comment=DB::table('comments')->join('users','comments.user_who_comment','=','users.email')->where('comments.post_id',$post_id)->select('users.first_name','users.profile_picture', 'comments.created_at','comments.comment','comments.user_who_comment')->orderBy('comments.created_at','DESC')->get();
      return response([
        "reply"=>$find_all_comment
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
    public function findNotifications(Request $request){
      $current_user=$request->input('email');
      $find_all_user_notifications=DB::table('notifications')->join('users','notifications.from','=','users.email')->where('notifications.owner',$current_user)->
      select('notifications.created_at','notifications.owner','notifications.from','notifications.info','notifications.source','notifications.owner_has_read','users.profile_picture')->orderBy('notifications.id','DESC')->get();
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

}
