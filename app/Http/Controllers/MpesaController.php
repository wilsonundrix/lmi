<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MpesaController extends Controller
{
    public function lipaNaMpesaPassword(): string
    {
        $timestamp = Carbon::rawParse('now')->format('YmdHms');
        $passKey = "";
        $shortCode = 174379;

        $mpesaPassword = base64_encode($shortCode.$passKey.$timestamp);

        return $mpesaPassword;
    }

    public function newAccessToken()
    {
        $consumerKey = "";
        $consumerSecret = "";

        $credentials = base64_encode($consumerKey . ":" .$consumerSecret);
        $url = "";

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_HTTPHEADER,array("Authorisation: Basic " . $credentials,"Content-Type:application/json"));
        curl_setopt($curl,CURLOPT_HEADER,false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

        $curl_response = curl_exec($curl);
        $json_response = json_decode($curl_response);
        curl_close($curl);

        return $json_response->access_token;
    }

    public function stkPush()
    {
        $url = "";
        $curl_post_data = [
            'BusinessShortCode'=>174379,
            'Password'=>$this->lipaNaMpesaPassword(),
            'Timestamp'=>Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType'=>'CustomerPayBillOnline',
            'Amount'=>'5',
            'PartyA'=>'254716729060',
            'PartyB'=>174379,
            'PhoneNumber'=>'254716729060',
            'CallBackURL'=>'/stk/push/callback/url',
            'AccountReference'=>'Ndunya Payment System',
            'TransactionDescription'=>'Payment for lollipop'
        ];
        $dataString = json_encode($curl_post_data);

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_HTTPHEADER,array("Content-Type:application/json","Authorisation:Bearer " . $this->newAccessToken()));
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_POST,true);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$dataString);

        $curl_response = curl_exec($curl);

//        return $curl_response;
        return view('confirm')->with([
            'curl_response'=>$curl_response
        ]);
    }
}
