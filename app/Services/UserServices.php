<?php
namespace App\Services;

use App\Models\User;
use App\Models\Community;
use App\Models\user_match_choice;
use App\Models\Notifications;
use App\Models\Blocked_List;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserServices
{
public function register(array $data)
{
$user = User::create([
'email' => $data['email'],
'password' =>  bcrypt($data['password']),
'isPaid' => "false", // Assuming 'isPaid' defaults to false
]);

return $user;
}
public function checkIfUserHasPaid($user_mail){
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
public function updateUserProfile($user_email,$data)
{
    User::where('email',$user_email)->update([
        'first_name'  =>$data['first_name'],
        'last_name'   =>$data['last_name'],
        'orientation' =>$data['orientation'],
        'location'    =>$data['location'],
        'birthday'    =>$data['birthday'],
        'phone_number'=>$data['phone_number'],
        'school'=>      $data['school'],
        'interest'=>    $data['interest'],
        'religion'=>    $data['religion'],
        'isPrivate'=>    "false",
        'isSound'=>      "true",
        'isColor'=>      "light",
      ]);
}
public function updateCoverText($user_email,$cover_text){
  User::where('email',$user_email)->update([
    'coverText'  =>$cover_text,

  ]);
  return response([
    "reply"=>$cover_text
  ]);
}
public function findUserCommunity($user_mail){
  $find_all_community_owned_by_user = Community::where('community_owner', $user_mail)
    ->leftJoin('community_followers', 'communities.community_name', '=', 'community_followers.community_name')
    ->select(
        'communities.community_name', 
        'communities.community_bio', 
        'communities.community_avatar', 
        'communities.community_cover', 
        'communities.community_category', 
        'communities.community_rules',
        DB::raw('COUNT(community_followers.community_member) as followers_count')
    )
    ->groupBy(
        'communities.community_name', 
        'communities.community_bio', 
        'communities.community_avatar', 
        'communities.community_cover', 
        'communities.community_category', 
        'communities.community_rules'
    )
    ->get();
  return response([
    "reply"=>$find_all_community_owned_by_user
  ]);
}
public function findUserMatch($email){
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
public function suggestUsers($email){
  $get_user_interest=DB::table('users')->select('location','school','orientation','birthday','religion','interest')->where('email',$email)->first();
      $user_interest=$get_user_interest->interest;
      $user_orientation=$get_user_interest->orientation;
      $user_religion=$get_user_interest->religion;
      $user_school=$get_user_interest->school;
      $user_with_similar_interest_array=array();
      $user_with_similar_religion_array=array();
      $user_with_similar_college_array=array();
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
        array_push($user_with_similar_interest_array,["first_name"=>$user_with_similar_interest->first_name,"last_name"=>$user_with_similar_interest->last_name,"profile_picture"=>$user_with_similar_interest->profile_picture,"email"=>base64_encode($user_with_similar_interest->email)]);
      }
      $find_users_with_similar_religion=DB::table('users')->select('first_name','last_name','profile_picture','email')->where(['religion'=>$user_religion])->whereNotIn('email',$store_all_members_who_user_is_following)->whereNotIn('email',$store_all_members_with_similar_interest)->where('email', '!=', $email)->where('religion', '!=', '')->latest()->take(12)->get();
      foreach($find_users_with_similar_religion as $user_with_similar_religion){
        array_push($store_all_members_with_similar_religion,$user_with_similar_religion->email);
        array_push($user_with_similar_religion_array,["first_name"=>$user_with_similar_religion->first_name,"last_name"=>$user_with_similar_religion->last_name,"profile_picture"=>$user_with_similar_religion->profile_picture,"email"=>base64_encode($user_with_similar_religion->email)]);
      }
      $find_users_with_similar_college=DB::table('users')->select('first_name','last_name','profile_picture','email')->where(['school'=>$user_school])->whereNotIn('email',$store_all_members_who_user_is_following)->whereNotIn('email',$store_all_members_with_similar_religion)->whereNotIn('email',$store_all_members_with_similar_interest)->where('email', '!=', $email)->where('school', '!=', '')->latest()->take(12)->get();
      foreach($find_users_with_similar_college as $user_with_similar_college){
        array_push($user_with_similar_college_array,["first_name"=>$user_with_similar_college->first_name,"last_name"=>$user_with_similar_college->last_name,"profile_picture"=>$user_with_similar_college->profile_picture,"email"=>base64_encode($user_with_similar_college->email)]);
      }
      return response([
        "user_with_similar_interest"=>$user_with_similar_interest_array,
        "user_with_similar_religion"=>$user_with_similar_religion_array,
        "user_with_similar_school"=>$user_with_similar_college_array,
        "user_interest"=>$user_interest,
        "user_religion"=>$user_religion,
        "user_orientation"=>$user_orientation
      ]);
      
}
public function setUserMatch($user,$choice){
  $find_user_details=User::where('email',$user)->select('first_name')->first();
  $find_choice_details=User::where('email',$choice)->select('first_name','last_name')->first();
  
      $sender=$find_user_details['first_name'];
      user_match_choice::create([
        "user"=>$user,
        "choice"=>$choice,
        "isPending"=>"false",
      ]);
      $this->notifyUser($user,$choice,"$sender is following in you..","match");
      return response([
        "success" => "You followed \t".$find_choice_details['first_name'].$find_choice_details['last_name']."\twe have notified them"
      ]);
}
public function checkCompleteProfile($user_email){
  $query_db=DB::table('users')->where(['email'=> $user_email])->get();
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
        'birthday'=>$user_data->birthday,
        'isPrivate'=>$user_data->isPrivate,
        'isColor'=>$user_data->isColor,
        'isSound'=>$user_data->isSound
      ]);
    }
   

  }
}
public function notifyUser($from,$owner,$info,$source)
{
    Notifications::create([
      "from"=>$from,
      "owner"=>$owner,
      "info"=>$info,
      "source"=>$source,
      "owner_has_read"=>"false"
    ]);
  }
}