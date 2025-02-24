<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Paystack;

class PaymentController extends Controller
{

        /**
         * Redirect the User to Paystack Payment Page
         * @return Url
         */
     
        public function redirectToGateway(Request $request)
        {
     
           try{
                $reference=Paystack::genTranxRef();
                $email=$request->input('email');
                $data= array(
                "amount"    => 500 * 100,
                "reference" => $reference,
                "email"     => $email
                ); 
                $url=Paystack::getAuthorizationUrl($data);
                return response(['url'=>$url]);
            }catch(\Exception $e) {
                return Redirect::back()->withMessage(['msg'=>'The paystack token has expired. Please refresh the page and try again.', 'type'=>'error']);
            }      
        }
    
        /**
         * Obtain Paystack payment information
         * @return void
         */
        public function handleGatewayCallback()
        {
           // $paymentDetails = Paystack::getPaymentData();
    
            //dd($paymentDetails);
            // Now you have the payment details,
            // you can store the authorization_code in your db to allow for recurrent subscriptions
            // you can then redirect or do whatever you want
   
}
}
?>
