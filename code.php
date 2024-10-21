<?php

/*
    edit.php

    MediaWiki API Demos
    Demo of `Edit` module: POST request to edit a page
    MIT license
*/

require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
echo "Dotenv loaded successfully.\n"; 

$endPoint = "https://test.wikipedia.org/w/api.php";
echo "Starting process to fetch login token.\n"; 

$login_Token = getLoginToken(); // Step 1
echo "Login token fetched successfully: $login_Token\n\n"; 

loginRequest($login_Token); // Step 2
echo "Starting process to fetch CSRF token.\n\n"; 

$csrf_Token = getCSRFToken(); // Step 3
echo "CSRF token fetched successfully: $csrf_Token\n\n"; 

editRequest($csrf_Token); // Step 4
echo "Process complete.\n\n"; 

// Step 1: GET request to fetch login token
function getLoginToken() {
    global $endPoint;

    $params1 = [
        "action" => "query",
        "meta" => "tokens",
        "type" => "login",
        "format" => "json"
    ];

    $url = $endPoint . "?" . http_build_query($params1);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

    echo "Sending request to fetch login token.\n"; 

    $output = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($output, true);
    
    echo "Login token response: " . json_encode($result) . "\n"; 

    return $result["query"]["tokens"]["logintoken"];
}

// Step 2: POST request to log in
function loginRequest($logintoken) {
    global $endPoint;

    $params2 = [
        "action" => "login",
        "lgname" => $_ENV["bot_username"],
        "lgpassword" => $_ENV["bot_password"], 
        "lgtoken" => $logintoken,
        "format" => "json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params2));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

    echo "Sending clientlogin request.\n\n\n";

    $output = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($output, true);
    
    echo "Full clientlogin response: " . $output . "\n\n";
    
    echo "result: " . json_encode($result) . "\n\n"; 
}

// Step 3: GET request to fetch CSRF token
function getCSRFToken() {
    global $endPoint;

    $params3 = [
        "action" => "query",
        "meta" => "tokens",
        "format" => "json"
    ];

    $url = $endPoint . "?" . http_build_query($params3);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

    echo "Sending request to fetch CSRF token.\n"; 

    $output = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($output, true);
    
    echo "CSRF token response: " . json_encode($result) . "\n"; 

    return $result["query"]["tokens"]["csrftoken"];
}

// Step 4: POST request to edit a page
function editRequest($csrftoken) {
    global $endPoint;

    $wikiassignment_subpage_text = "
    == Kartikey's Section ==

    Greetings everyone! Welcome to my section. In this section, I will share my experiences and insights from the wiki tech sessions.

    === Kartikey's Subsection ===

    In this subsection, I want to express my gratitude to the wiki tech team. I am truly enjoying all of your sessions and learning a lot. Thank you very much for your hospitality!

    === More Information ===

    For more details, please visit my user page: [[User:Kartikey_Singh_Bartwal]].

    === External Resources ===

    * [https://www.mediawiki.org/wiki/API:Edit MediaWiki API Documentation MediaWiki API Documentation]
    * [https://www.mediawiki.org/wiki/Help:Editing Editing]
    ";

    $params4 = [
        "action" => "edit",
        "title" => "User:Kartikey Singh Bartwal/wikiassignment",
        "text" => $wikiassignment_subpage_text,
        "token" => $csrftoken,
        "format" => "json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params4));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

    echo "Sending edit request to update the page.\n"; 

    $output = curl_exec($ch);
    curl_close($ch);

    echo "Edit request response: $output\n"; 
}

