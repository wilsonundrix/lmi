<?php

namespace App\Http\Controllers;

use App\Models\MpesaTransaction;
use App\Models\StkPush;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;

class MpesaController extends Controller
{
    public function lipaNaMpesaPassword(): string
    {
        $timestamp = Carbon::rawParse('now')->format('YmdHms');
        $passKey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $shortCode = 174379;

        return base64_encode($shortCode . $passKey . $timestamp);
    }

    public function newAccessToken()
    {
        $consumerKey = "2gT4rlPQwKaVUknAqLmh8BwoIBzpFd6d";
        $consumerSecret = "o5puyXpgEIWK0UDv";

        $credentials = base64_encode($consumerKey . ":" . $consumerSecret);
        $url = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Basic " . $credentials, "Content-Type:application/json"));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
        $json_response = json_decode($curl_response);
        curl_close($curl);

        return $json_response->access_token;
    }



    public function stkPush(Request $request)
    {
        $url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

        $amount = $request->amount;
        $phone = $request->phoneNumber;
        $formattedPhone = substr($phone, 1);
        $code = "254";
        $phoneNumber = $code . $formattedPhone;

        $curl_post_data = [
            'BusinessShortCode' => 174379,
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phoneNumber,
            'PartyB' => 174379,
            'PhoneNumber' => $phoneNumber,
//            'CallBackURL' => "https://modernwheels.co.ke/dollar/stk/",              //api/stk/push/callback/url
            'CallBackURL' => route('mpesa-res'),              //api/stk/push/callback/url
            'AccountReference' => 'Ndunya Payment',
            'TransactionDesc' => 'lollipop'
        ];
        $dataString = json_encode($curl_post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: Bearer " . $this->newAccessToken()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataString);

        $curl_response = curl_exec($curl);

        return $curl_response;
        // return redirect()->route('mpesa-res');
    }


    public function mpesaRes()
    {
        echo "haha";
        $callBackJSONData = file_get_contents("php://input");

        $logFile = "STKPush.json";
        $log = fopen($logFile, "a");
        fwrite($log, $callBackJSONData);
        fclose($log);

        $callBackData = json_decode($callBackJSONData);

       $trx = new MpesaTransaction();
       $trx->TransactionType = $callBackData->TransactionType;
       $trx->TransID = $callBackData->TransID;
       $trx->TransTime = $callBackData->TransTime;
       $trx->TransAmount = $callBackData->TransAmount;
       $trx->BusinessShortCode = $callBackData->BusinessShortCode;
       $trx->BillRefNumber = $callBackData->BillRefNumber;
       $trx->InvoiceNumber = $callBackData->InvoiceNumber;
       $trx->OrgAccountBalance = $callBackData->OrgAccountBalance;
       $trx->ThirdPartyTransID = $callBackData->ThirdPartyTransID;
       $trx->MSISDN = $callBackData->MSISDN;
       $trx->FirstName = $callBackData->FirstName;
       $trx->MiddleName = $callBackData->MiddleName;
       $trx->LastName = $callBackData->LastName;
       $trx->response = json_encode($callBackData);
       $trx->status = 'pending';
       $trx->response = json_encode($callBackData);
//        $transaction->response = "Crazy Ass";
       $trx->save();

        echo "Sasa na sasa";
    }

    public function confirm(Request $request)
    {
//        dd($request);
        $transId = $request->get('transactionID');
        $transaction = MpesaTransaction::where('TransID', $transId)->first();
        if ($transaction) {
            $transaction->status = 'paid';
            $transaction->save();
            echo "Saved kwa db";
        } else {
            echo "Nan kwa db !!!!!!!!!!!!";
        }

    }
}
