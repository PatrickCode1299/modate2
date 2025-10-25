<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    //
    public function redirectToPay(Request $request){
        $response = Http::withHeaders([
            'x-k' => env('API_KEY')
        ])->post(env('APP_URL') . "/api/pay");

        return response($response->body(), $response->status());
    }
    public function checkIfUserHasCompleteProfile(Request $request){
       $email = $request->input('email');



        if (env('API_KEY') !== env('API_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $controller = app(AuthController::class);
        return $controller->checkIfUserHasCompleteProfile($request);


    }
}
