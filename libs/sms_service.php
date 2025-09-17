<?php

class SMSService
{
    private $apiKey;
    private $partnerID;
    private $shortcode;
    private $apiUrl = 'https://sms.textsms.co.ke/api/services/sendsms/';

    public function __construct($apiKey, $partnerID, $shortcode)
    {
        $this->apiKey = $apiKey;
        $this->partnerID = $partnerID;
        $this->shortcode = $shortcode;
    }

    public function sendSMS($mobile, $message)
    {
        $data = [
            'apikey' => $this->apiKey,
            'partnerID' => $this->partnerID,
            'mobile' => $mobile,
            'message' => $message,
            'shortcode' => $this->shortcode,
            'pass_type' => 'plain'
        ];

        error_log("Attempting to send SMS to $mobile");
        error_log("Request data: " . json_encode($data));

        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("cURL error occurred: $curlError");
            return [
                'httpCode' => 0,
                'error' => $curlError,
                'response' => null
            ];
        }

        error_log("API Response: $response");
        error_log("HTTP Code: $httpCode");

        return [
            'httpCode' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }
}