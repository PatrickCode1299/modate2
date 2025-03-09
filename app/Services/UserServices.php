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
// Fetch user choices and blocked users in one go
$find_user_choice = user_match_choice::where('user', $email)->pluck('choice')->toArray();
$blocked_users = Blocked_List::where('user_who_block', $email)->distinct()->pluck('user_getting_blocked')->toArray();

// Fetch user info
$find_user_info = User::where('email', $email)->first(['location', 'orientation']);

if (!$find_user_info) {
    return response()->json(['reply' => 'User not found'], 404);
}

$user_location = $find_user_info->location;
$user_orientation = ucfirst($find_user_info->orientation);

// Build query for finding matches
$query = User::where('email', '!=', $email)
    ->whereNotIn('email', array_merge($find_user_choice, $blocked_users))
    ->where('location', $user_location);

// Add orientation conditions
switch ($user_orientation) {
    case 'Male':
        $query->where('orientation', 'Female');
        break;
    case 'Female':
        $query->where('orientation', 'Male');
        break;
    case 'Gay':
        $query->where('orientation', 'Gay');
        break;
    case 'Bisexual':
        $query->where(function ($q) {
            $q->where('orientation', 'Male')
              ->orWhere('orientation', 'Female');
        });
        break;
    case 'Lesbian':
        $query->where(function ($q) {
            $q->where('orientation', 'Female')
              ->orWhere('orientation', 'Lesbian');
        });
        break;
    case 'Non Binary':
        $query->where('orientation', 'Non Binary');
        break;
    default:
        return response()->json(['reply' => 'User match not found'], 404);
}

// Fetch matches with all relevant columns
$find_user_matches = $query->select([
        'first_name', 
        'last_name', 
        'email', 
        'location', 
        'orientation', 
        'school', 
        'birthday', 
        'religion', 
        'phone_number', 
        'interest', 
        'profile_picture', 
        'isPaid', 
        'isPrivate', 
        'coverText', 
        'isSound', 
        'coverPhoto', 
        'isColor', 
        'created_at', 
        'updated_at'
    ])
    ->inRandomOrder()
    ->latest('id')
    ->first();
if($find_user_matches){
// Build the response with only keys
    $response = [
        'first_name' => $find_user_matches->first_name,
        'last_name' => $find_user_matches->last_name,
        'email' => base64_encode($find_user_matches->email),
        'location' => $find_user_matches->location,
        'orientation' => $find_user_matches->orientation,
        'school' => $find_user_matches->school,
        'birthday' => $find_user_matches->birthday,
        'religion' => $find_user_matches->religion,
        'phone_number' => $find_user_matches->phone_number,
        'interest' => $find_user_matches->interest,
        'profile_picture' => $find_user_matches->profile_picture,
        'created_at' => $find_user_matches->created_at,
        'updated_at' => $find_user_matches->updated_at,
    ];
    return response(['reply' => $response]);
}else {
    return response(['reply' => 'User match not found']);
}

}
public function suggestUsers($email) { 
    // Get user details
    $get_user_details = DB::table('users')
        ->select('location', 'school', 'orientation', 'birthday', 'religion', 'interest')
        ->where('email', $email)
        ->first();

    if (!$get_user_details) {
        return response([
            "error" => "User not found."
        ]);
    }

    $user_interest = $get_user_details->interest;
    $user_orientation = $get_user_details->orientation;
    $user_religion = $get_user_details->religion;
    $user_school = $get_user_details->school;
    $user_location = $get_user_details->location;
    $user_birthday = $get_user_details->birthday;

    // Calculate the birth year range for suggestions
    $user_birth_year = date('Y', strtotime($user_birthday));
    $min_year = $user_birth_year - 5; // 5 years younger
    $max_year = $user_birth_year + 10; // up to 10 years older

    // Get the users the current user is following
    $store_all_members_who_user_is_following = user_match_choice::where('user', $email)->pluck('choice')->toArray();

    // Get users with similar interest and within the birth year range
    $find_users_with_similar_interest = DB::table('users')
        ->select('first_name', 'last_name', 'profile_picture', 'email', 'birthday')
        ->where('interest', $user_interest)
        ->where('location', $user_location)
        ->whereNotIn('email', $store_all_members_who_user_is_following)
        ->where('email', '!=', $email)
        ->where('interest', '!=', '')
        ->whereYear('birthday', '>=', $min_year)
        ->whereYear('birthday', '<=', $max_year)
        ->latest()
        ->take(12)
        ->get();

    $user_with_similar_interest_array = $find_users_with_similar_interest->map(function ($user) {
        return [
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "profile_picture" => $user->profile_picture,
            "email" => base64_encode($user->email),
        ];
    })->toArray();

    // Get users with similar religion within the birth year range
    $store_all_members_with_similar_interest = $find_users_with_similar_interest->pluck('email')->toArray();

    $find_users_with_similar_religion = DB::table('users')
        ->select('first_name', 'last_name', 'profile_picture', 'email', 'birthday')
        ->where('religion', $user_religion)
        ->where('location', $user_location)
        ->whereNotIn('email', $store_all_members_who_user_is_following)
        ->whereNotIn('email', $store_all_members_with_similar_interest)
        ->where('email', '!=', $email)
        ->where('religion', '!=', '')
        ->whereYear('birthday', '>=', $min_year)
        ->whereYear('birthday', '<=', $max_year)
        ->latest()
        ->take(12)
        ->get();

    $user_with_similar_religion_array = $find_users_with_similar_religion->map(function ($user) {
        return [
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "profile_picture" => $user->profile_picture,
            "email" => base64_encode($user->email),
        ];
    })->toArray();

    // Get users with similar school within the birth year range
    $store_all_members_with_similar_religion = $find_users_with_similar_religion->pluck('email')->toArray();

    $find_users_with_similar_college = DB::table('users')
        ->select('first_name', 'last_name', 'profile_picture', 'email', 'birthday')
        ->where('school', $user_school)
        ->whereNotIn('email', $store_all_members_who_user_is_following)
        ->whereNotIn('email', $store_all_members_with_similar_religion)
        ->whereNotIn('email', $store_all_members_with_similar_interest)
        ->where('email', '!=', $email)
        ->where('school', '!=', '')
        ->whereYear('birthday', '>=', $min_year)
        ->whereYear('birthday', '<=', $max_year)
        ->latest()
        ->take(12)
        ->get();

    $user_with_similar_college_array = $find_users_with_similar_college->map(function ($user) {
        return [
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "profile_picture" => $user->profile_picture,
            "email" => base64_encode($user->email),
        ];
    })->toArray();

    return response([
        "user_with_similar_interest" => $user_with_similar_interest_array,
        "user_with_similar_religion" => $user_with_similar_religion_array,
        "user_with_similar_school" => $user_with_similar_college_array,
        "user_interest" => $user_interest,
        "user_religion" => $user_religion,
        "user_orientation" => $user_orientation,
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
        'last_seen'   => $user_data->updated_at,
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
