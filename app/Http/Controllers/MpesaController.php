<?php

namespace App\Http\Controllers;

use App\Models\MpesaTransaction;
use App\Models\StkPush;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

    public function stkPush(Request $request): RedirectResponse
    {
        $url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

        $amount = $request->input('amount');
        $phone = $request->input('phoneNumber');
        $formattedPhone = substr($phone, 1);
        $code = "254";
        $phoneNumber = $code . $formattedPhone;
        $timeStamp = Carbon::rawParse('now')->format('YmdHms');

        $curl_post_data = [
            'BusinessShortCode' => 174379,
            'Password' => $this->lipaNaMpesaPassword(),
            'Timestamp' => $timeStamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phoneNumber,
            'PartyB' => 174379,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => "https://modernwheels.co.ke/dollar/stk/",              //api/stk/push/callback/url
            'AccountReference' => 'Ndunya Payment',
            'TransactionDesc' => 'lollipop'
        ];
        $dataString = json_encode($curl_post_data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:application/json", "Authorization: Bearer " . $this->newAccessToken()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $dataString);

        $curl_response = curl_exec($curl);
//        echo $curl_response;

        $jsonResponse = json_decode($curl_response);

        $newStkPush = new StkPush();
        $newStkPush['merchantRequestID'] = $jsonResponse->MerchantRequestID;
        $newStkPush['checkoutRequestID'] = $jsonResponse->CheckoutRequestID;
        $newStkPush['responseCode'] = $jsonResponse->ResponseCode;
        $newStkPush['responseDescription'] = $jsonResponse->ResponseDescription;
        $newStkPush['customerMessage'] = $jsonResponse->CustomerMessage;
        $newStkPush['phoneNumber'] = $phoneNumber;
        $newStkPush['amount'] = $amount;
        $newStkPush['transactionDate'] = $timeStamp;

        $newStkPush->save();

        return redirect()->route('confirm-page');
    }

    public function stkResponse()
    {
        $data = file_get_contents('php://input');
        $decoded_data = json_decode($data, true);
        $res = $decoded_data['Body']['stkCallback'];

        if ($res['ResultCode'] == 0) {

            $amount = $res['CallbackMetadata']['Item'][0]['Value'];
            $phoneNumber = $res['CallbackMetadata']['Item'][4]['Value'];
            $transactionDate = $res['CallbackMetadata']['Item'][3]['Value'];
            $mpesaReceiptNumber = $res['CallbackMetadata']['Item'][1]['Value'];
            $merchantRequestID = $res["MerchantRequestID"];
            $checkoutRequestID = $res["CheckoutRequestID"];
            $resultCode = $res["ResultCode"];

            $handle = fopen('stk_response.txt', 'w');
            fwrite($handle, "$data\n");
            fwrite($handle, "Amount : $amount\n");
            fwrite($handle, "MpesaReceiptNumber : $mpesaReceiptNumber\n");
            fwrite($handle, "TransactionDate : $transactionDate\n");
            fwrite($handle, "PhoneNumber : $phoneNumber\n");
            fwrite($handle, "MerchantRequestID : $merchantRequestID\n");
            fwrite($handle, "CheckoutRequestID : $checkoutRequestID\n");
            fwrite($handle, "ResultCode : $resultCode\n");
            fclose($handle);

            $con = mysqli_connect("localhost", "modernwh_dmungai", "mendoza10095", "modernwh_cars_database");
            if (!$con) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $sql = "INSERT INTO `stk_transactions`(`merchantRequestID`, `checkoutRequestID`, `amount`, `mpesaReceiptNumber`, `transactionDate`, `phoneNumber`) VALUES (
                                            '$merchantRequestID','$checkoutRequestID','$amount','$mpesaReceiptNumber','$transactionDate','$phoneNumber')";

            if (!mysqli_query($con, $sql)) {
                echo mysqli_error($con);
            }
            mysqli_close($con);
        } else {
            $handle = fopen('err_stk_response.txt', 'a');
            fwrite($handle, "$data\n");
            fclose($handle);
        }
    }

    public function c2bResponse()
    {
        $expectedAmount = 1000;
        $callBackJSONData = file_get_contents("php://input");

        $logFile = "STKPushResponse.log";
        $log = fopen($logFile, "a");
        fwrite($log, $callBackJSONData);
        fwrite($log,"stk end");
        fclose($log);

        $callBackData = json_decode($callBackJSONData);
        if ($callBackData->amount = $expectedAmount) {
            $response = array(
                "ResultCode" => 0,
                "ResultDesc" => "Accepted"
            );

            $trx = new MpesaTransaction();
            $trx['FirstName'] = $callBackData->FirstName;
            $trx['MiddleName'] = $callBackData->MiddleName;
            $trx['LastName'] = $callBackData->LastName;
            $trx['TransactionType'] = $callBackData->TransactionType;
            $trx['TransID'] = $callBackData->TransID;
            $trx['TransTime'] = $callBackData->TransTime;
            $trx['TransAmount'] = $callBackData->TransAmount;
            $trx['BusinessShortCode'] = $callBackData->BusinessShortCode;
            $trx['BillRefNumber'] = $callBackData->BillRefNumber;
            $trx['InvoiceNumber'] = $callBackData->InvoiceNumber;
            $trx['OrgAccountBalance'] = $callBackData->OrgAccountBalance;
            $trx['ThirdPartyTransID'] = $callBackData->ThirdPartyTransID;
            $trx['MSISDN'] = $callBackData->MSISDN;
//        $trx['response'] = json_encode($callBackData);
//        $trx['status'] = 'pending';
//        $trx['response'] = json_encode($callBackData);
//        $transaction['response'] = "Crazy Ass";
            $trx->save();
        } else {
//            Reject the transaction
            $response = array(
                "ResultCode" => 25,
                "ResultDesc" => "Rejected"
            );
        }
        echo $response;
    }

    public function c2bConfirm(Request $request)
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

    public function mpesaRegisterUrls()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '. $this->generateAccessToken()));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            'ShortCode' => 600988,
            'ResponseType' => 0,
            'ConfirmationURL' => 'https://www.modernwheels.co.ke/dollar/confirmation/',
            'ValidationURL' => 'https://www.modernwheels.co.ke/dollar/validation/'
        )));
        $curl_response = curl_exec($curl);
        echo $curl_response;
    }
}
