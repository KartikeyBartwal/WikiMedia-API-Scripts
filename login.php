<?php

// MediaWiki API endpoint
$apiUrl = "https://test.wikipedia.org/w/api.php";

// Your credentials
$username = "Kartikey Singh Bartwal@dogieeee_5688";
$password = '6iq4455j5iu7tfnh7rndaec0a8e4reie';

// Initialize a cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'action' => 'login',
    'lgname' => $username,
    'lgpassword' => $password,
    'format' => 'json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    print "cURL Error: " . curl_error($ch) . "\n";
    exit;
}

// Decode the JSON response
$data = json_decode($response, true);

// Print the response for debugging
print "Initial login response:\n";
print_r($data);

// Check if we need to send a confirmation token
if (isset($data['login']['result']) && $data['login']['result'] == 'NeedToken') {
    $token = $data['login']['token'];
    
    // Set new POST fields
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'action' => 'login',
        'lgname' => $username,
        'lgpassword' => $password,
        'lgtoken' => $token,
        'format' => 'json'
    ]);
    
    // Execute the request again
    $response = curl_exec($ch);
    $data = json_decode($response, true);
    
    // Print the response for debugging
    print "Token confirmation response:\n";
    print_r($data);
}

// Check the final login result
if (isset($data['login']['result']) && $data['login']['result'] == 'Success') {
    print "Login successful!\n";
} else {
    print "Login failed. Error message: " . $data['login']['reason'] . "\n";
}

// Close cURL session
curl_close($ch);