<?php

// Get the raw JSON POST data
$jsonData = file_get_contents("php://input");

// Decode the JSON data
$requestData = json_decode($jsonData, true);

// Check if the JSON decoding was successful and the "psid" field exists
if ($requestData && isset($requestData['psid'])) {
    $psid = $requestData['psid'];
    
    // Generate a random 6-digit number
    $actionValue = rand(100000, 999999);

    // Prepare the payload for the API request
    $apiData = [
        "psid" => $psid,
        "data" => [
            "version" => "v2",
            "content" => [
                "messages" => [
                    [
                        "type" => "text",
                        "text" => "রেজিস্ট্রেশন  কোডটি  আপনার নাম্বারে পাঠানো হয়েছে  দয়া করে কিছুক্ষণ অপেক্ষা করুন"
                    ]
                ],
                "actions" => [
                    [
                        "action" => "set_custom_field",
                        "action_id" => 60747,
                        "action_value" => $actionValue
                    ],
                    [
                        "action" => "set_custom_field",
                        "action_id" => 61559,
                        "action_value" => $psid,
                    ]
                   
                ],
                "quick_replies" => []
            ]
        ]
    ];

    $accessToken = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0aW1lc3RhbXAiOjE2OTg3NDUzOTgsImlkIjoiMTAwNTI0NDg2MDMzNTM4In0.fXW6DRrFyMgx68VUZAM0938XmhZsZy-wxjp0EPSd29s'; // Replace with your actual access token
    $apiEndpoint = 'https://botcake.io/api/public_api/v1/pages/100524486033538/flows/send_content';

    // Set up the cURL request
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $apiEndpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($apiData),
        CURLOPT_HTTPHEADER => [
            'access-token: ' . $accessToken,
            'Content-Type: application/json'
        ]
    ]);

    // Execute the cURL request
    $response = curl_exec($curl);

    // Check for cURL errors and HTTP response code
    if (curl_errno($curl)) {
        echo "cURL Error: " . curl_error($curl);
    } else {
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($httpCode == 200) {
            echo "API Response: " . $response;
        } else {
            echo "API Request Failed. HTTP Status Code: " . $httpCode;
        }
    }

    // Close the cURL session
    curl_close($curl);
} else {
    echo "Failed to extract PSID from JSON data.";
}
