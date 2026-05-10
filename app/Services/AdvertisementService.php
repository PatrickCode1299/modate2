<?php
namespace App\Services;
use App\Models\Advertisement;
use DateTime;
class AdvertisementService{

public function createAdvertisement($email,$max_target_age,$min_target_age,$postid,$ad_duration,$target_location,$initial_amount,){

    $amount_paid=$initial_amount * 0.01;
    $daysToAdd = $ad_duration;
    $date = new DateTime();

    $date->modify("+$daysToAdd days");

    $formattedDate = $date->format('Y-m-d H:i:s');
    $checkIfAdHasNotBeenCreated=Advertisement::where('post_id',$postid)->first();
    if($checkIfAdHasNotBeenCreated){
      return response([
        "reply" => "error",
      ]);
    }else{
    $create_advertisement=Advertisement::create([
      "post_id" => $postid,
      "ad_owner" => $email,
      "ad_amount" => $amount_paid,
      "ad_reach"  => $ad_duration,
      "ad_stop_date" => $formattedDate,
      "min_age"  => $min_target_age ?? 0,
      "max_age"  => $max_target_age ?? 0,
      "target_location" => $target_location ?? '',
    ]);
    return response([
      "reply"           => $create_advertisement
    ]);
  }
}
}
