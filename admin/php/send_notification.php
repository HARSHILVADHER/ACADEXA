<?php
function sendSMS($number, $message) {
    $apiKey = 'j8NF6Y9TeBwBGMuilMX8P0v9uKPfD8VWbjh0Sw1JSrHUqCMGCtWthy9yFPtF'; // Replace with your actual Fast2SMS API key

    $postData = array(
        'authorization' => $apiKey,
        'message' => $message,
        'language' => 'english',
        'route' => 'q',
        'numbers' => $number,
    );

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($postData),
        CURLOPT_HTTPHEADER => array(
            "authorization: {$apiKey}",
            "cache-control: no-cache",
            "content-type: application/x-www-form-urlencoded"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    // Optional: log or debug
    if ($err) {
        error_log("SMS Error: $err");
    }
}
?>
