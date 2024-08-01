<?php
namespace App\Services;

use App\Models\User;
use App\Models\Notifications;
use Illuminate\Support\Facades\Hash;

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
  
      ]);
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