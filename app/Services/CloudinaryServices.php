<?php

namespace App\Services;
use App\Models\User;
use App\Models\Story;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryServices
{
    protected $cloudinary;

    public function __construct()
    {
      
    }
    public function uploadVideo($filePath)
    {
        return Cloudinary::uploadFile($filePath->getRealPath())->getSecurePath();
    }
    public function delete($publicId)
    {
        return Cloudinary::destroy($publicId);
    }

    public function get($publicId)
    {
        return  Cloudinary::getUrl($publicId);
    }
    public function updateProfilePicture($filePath,$owner){
        $upload=  Cloudinary::upload($filePath->getRealPath(), [
            'folder' => 'users_pic', // Optional: specify a folder
        ]);
        $pictureId=$upload->getPublicId();
        $check_if_user_has_picture=User::where('email',$owner)->first();
        if($check_if_user_has_picture['profile_picture'] != null){
          $publicId=$check_if_user_has_picture['profile_picture'];
          $deleteResult = $this->delete($publicId);
        }
       /* if($check_if_user_has_picture['profile_picture'] != null){
          $old_profile_picture="storage/".$check_if_user_has_picture['profile_picture'];
          File::delete(public_path($old_profile_picture));
        } **/
        User::where('email',$owner)->update([
          'profile_picture'  =>$pictureId,
    
        ]);
    }
    public function uploadStory($filePath,$email){
    $video_array=array('mp4','MP4','WEBM','webm','mkv','MKV','3gp');
    $extension = $filePath->getClientOriginalExtension();
    if(in_array($extension,$video_array)){
        $upload=Cloudinary::uploadFile($filePath->getRealPath(), [
            'folder' => 'users_story', // Optional: specify a folder
        ]);
        $image_path=$upload->getPublicId();
          $story_day=date('Y-m-d');
          Story::create([
            "user_email"=>$email,
            "user_image"=>$image_path,
            "user_video"=>"true",
            "date_posted"=>$story_day
          ]);
    }else{
        $upload=Cloudinary::uploadFile($filePath->getRealPath(), [
            'folder' => 'users_story', // Optional: specify a folder
        ]);
        $image_path=$upload->getPublicId();
          $story_day=date('Y-m-d');
          Story::create([
            "user_email"=>$email,
            "user_image"=>$image_path,
            "user_video"=>"",
            "date_posted"=>$story_day
          ]);
    }
      return response([
        "reply"=>"Upload Successful"
      ]);
    }
}