<?php
namespace App\Services;

use App\Models\Comment_Reply;
use App\Models\Comment;
use App\Models\User;
use App\Models\Shared_Post;
use App\Services\UserServices;
use Illuminate\Support\Facades\Hash;

class ConverSationServices{
  protected $userServices;
  public function __construct(UserServices $userServices)
  {
    $this->userServices=$userServices;
  }
public function createUserComment($data){
  $user_who_comment=$data['user_who_comment'];
  $comment=$data['comment'];
  $post_id=$data['post_id'];
  $current_date=$data['current_date'];
  $tagged_users=$data['tagged_users'];
  $post_owner=$data['post_owner'];
  $user_who_comment_name=$data['user_who_comment_name'];
  if($tagged_users === null){
    Comment::create([
      "comment"=>$data['comment'],
      "user_who_comment"=>$data['user_who_comment'],
      "post_id"=>$data['post_id'],
      "comment_id"=>$data['comment_id']
  ]);
  }else{
    Comment::create([
      "comment"=>$data['comment'],
      "user_who_comment"=>$data['user_who_comment'],
      "post_id"=>$data['post_id'],
      "comment_id"=>$data['comment_id']
  ]);
    foreach($tagged_users as $users){
      $this->userServices->notifyUser($user_who_comment,$users,"$user_who_comment_name tagged you in this: $comment",$post_id);
    }
  if($post_owner === $user_who_comment){
    return;
  }else{
    $this->userServices->notifyUser($user_who_comment,$post_owner,"$user_who_comment_name made a comment on your post: $comment",$post_id);
  }
}
}
public function sharePost($data){
    Shared_Post::create([
        "name"=>$data['name'],
        "avatar"=>'',
        "caption"=>$data['caption'],
        "email"=>$data['email'],
        "post_img1"=>$data['post_img1'],
        "post_img2"=>$data['post_img2'],
        "post_img3"=>$data['post_img3'],
        "post_img4"=>$data['post_img4'],
        "video"=>$data['video'],
        "quote"=>$data['quote'],
        "postid"=>$data['postid'],
        "prev_id"=>$data['prev_id'],
        "name_of_user_who_shared"=>$data['name_of_user_who_shared'],
        "email_of_user_who_shared"=>$data['email_of_user_who_shared']

      ]);
}
public function findComment($data){
$find_comment_in_comment_table=Comment::join('users','comments.user_who_comment','=','users.email')->select('comments.user_who_comment','comments.comment','comments.post_id','comments.created_at','users.profile_picture','users.first_name','users.last_name')->where('comments.comment_id',$data)->first();
if($find_comment_in_comment_table){
$store_all_replies=array();
$find_reply=Comment_Reply::join('users','comment__replies.user_who_replied','=','users.email')->select('comment__replies.user_who_replied','comment__replies.user_being_replied','comment__replies.comment_reply','comment__replies.created_at','users.profile_picture','users.first_name','users.last_name')->where('comment__replies.initial_comment_id',$data)->get();
foreach($find_reply as $comment_replies){
array_push($store_all_replies,$comment_replies);
}
return response([
"reply"=>$find_comment_in_comment_table,
"comment_replies"=>$store_all_replies
]);
}else{
    $find_comment_in_comment_table=Comment_Reply::join('users',' comment__replies.user_who_replied','=','users.email')->where('comment__replies.reply_id',$data)->first();
    $store_all_replies=array();
    $find_reply=Comment_Reply::join('users','comment__replies.user_who_replied','=','users.email')->where('comment__replies.initial_comment_id',$data)->get();
    foreach($find_reply as $comment_replies){
    array_push($store_all_replies,$comment_replies);
    }
    return response([
        "reply"=>$find_comment_in_comment_table,
        "comment_replies"=>$store_all_replies
        ]);
}
}
}