<?php
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