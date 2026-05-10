<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdvertisementService;
class AdvertisementController extends Controller
{
   public $advertisementService;
   public function __construct(AdvertisementService $advertisementService)
   {
    $this->advertisementService = $advertisementService;
   }
   public function createAdvertisement(Request $request){
    $email=$request->input('email');
    $max_target_age=$request->input('maximum_target_age');
    $min_target_age=$request->input('minimum_target_age');
    $postid=$request->input('postid');
    $ad_duration=(int)$request->input('ad_duration');
    $target_location=$request->input('target_location');
    $initial_amount= $request->input('amount_paid');
    $amount_paid=$initial_amount * 0.01;
    $daysToAdd = $ad_duration;
    $createAdvert = $this->advertisementService->createAdvertisement($email,$max_target_age,$min_target_age,$postid,$ad_duration,$target_location,$initial_amount);
    return $createAdvert;

}
}
