<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\user_match_choice;
use App\Models\Notifications;
use App\Models\User_Post;
use App\Models\Story;
use Illuminate\Support\Facades\File; 
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public function notify($from,$owner,$info){
      Notifications::create([
        "from"=>$from,
        "owner"=>$owner,
        "info"=>$info,
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
      $this->notify($user,$choice,"$sender is interested in you..");
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
      User_Post::create([
        "name"=>$name,
        "avatar"=>$avatar,
        "caption"=>$user_caption,
        "email"=>$email
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
        "reply"=>$hold_all_replies
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
      
     $reply= Story::whereIn('user_email',$hold_user_mutual_friends)->get();
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
     array_push($user_friends_details_collection,["email"=>$get_info->email,"picture"=>$get_info->profile_picture]);
     }
     $new_collection=array();
     return response([
      "reply"=>$user_friends_details_collection,
     ]);
    }
    public function fetchAllPost(Request $request){
      $user=$request->input('email');
      $fetch_user_friends=user_match_choice::where('user',$user)->get();
      $hold_all_user_friends=array();
      foreach($fetch_user_friends as $get_friend){
        array_push($hold_all_user_friends,$get_friend->choice);
      }
      $find_all_user_friend_post=DB::table('user__posts')->join('users','user__posts.email','=','users.email')->whereIn('user__posts.email',$hold_all_user_friends)->select('user__posts.name','user__posts.caption','user__posts.created_at','users.profile_picture')->orderBy('user__posts.id','DESC')->take(5)->get();
      $store_all_post=array();
      foreach($find_all_user_friend_post as $post){
        array_push($store_all_post, $post);
      }
      return response([
        "reply" => $store_all_post
      ]);
    }
}
