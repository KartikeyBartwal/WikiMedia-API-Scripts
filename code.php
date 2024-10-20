<?php

/*
    edit.php

    MediaWiki API Demos
    Demo of `Edit` module: POST request to edit a page
    MIT license
*/

require __DIR__ . '/vendor/autoload.php';

// Load the .env file
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "Dotenv loaded successfully.\n"; // Added <br> for line break
} catch (Exception $e) {
    echo "Error loading .env file: " . $e->getMessage() . "\n"; // Added <br> for line break
}

$endPoint = "https://test.wikipedia.org/w/api.php";

$login_Token = getLoginToken(); // Step 1

echo "login token: " . $login_Token . "\n"; // Updated for line break

loginRequest($login_Token); // Step 2
$csrf_Token = getCSRFToken(); // Step 3
// editRequest($csrf_Token); // Step 4

echo "csrf_token: " . $csrf_Token . "\n"; // Updated for line break

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

    $output = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($output, true);
    return $result["query"]["tokens"]["logintoken"];
}

// Step 2: POST request to log in. Use of main account for login is not
// supported. Obtain credentials via Special:BotPasswords
// (https://www.mediawiki.org/wiki/Special:BotPasswords) for lgname & lgpassword
function loginRequest($logintoken) {
    global $endPoint;

    $bot_username = $_ENV['bot_username'];
    $bot_password = $_ENV['bot_password'];

    echo "username: " . $bot_username . "\n"; // Updated for line break
    echo "password: " . $bot_password . "\n"; // Updated for line break

    $params2 = [
        "action" => "login",
        "lgname" => $bot_username,
        "lgpassword" => $bot_password,
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

    $output = curl_exec($ch);
    curl_close($ch);

    echo "output: <br>" . $output . "\n"; // Updated for line break 

    $result = json_decode($output, true);

    // Check login status
    if (isset($result['login']['result']) && $result['login']['result'] === 'Success') {
        echo "Login successful.\n"; // Updated for line break
    } else {
        echo "Login failed: " . ($result['login']['reason'] ?? 'Unknown reason') . "\n"; // Updated for line break
        exit; // Exit if login fails
    }
}

// Step 3: GET request to fetch CSRF token
function getCSRFToken() {
	global $endPoint;

	$params3 = [
		"action" => "query",
		"meta" => "tokens",
		"format" => "json"
	];

	$url = $endPoint . "?" . http_build_query( $params3 );

	$ch = curl_init( $url );

	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_COOKIEJAR, "cookie.txt" );
	curl_setopt( $ch, CURLOPT_COOKIEFILE, "cookie.txt" );

	$output = curl_exec( $ch );
	curl_close( $ch );

	$result = json_decode( $output, true );
	return $result["query"]["tokens"]["csrftoken"];
}

// Step 4: POST request to edit a page
function editRequest( $csrftoken ) {
	global $endPoint;

	$wikiassignment_subpage_text = "
    == Kartikey's section ==
    Greetings everyone! Welcome to the section.

    === Kartikey's subsection ===
    and here is my subsection! To all the wiki tech team, I am loving all of your sessions. Thank you very much for your hospitality!

    See more details on [[User:Kartikey_Singh_Bartwal]].

    External resource: [https://www.mediawiki.org/wiki/API:Edit MediaWiki API Documentation]
    ";

	$params4 = [
		"action" => "edit",
		"title" => "User:Kartikey_Singh_Bartwal/wikiassignment",
		"text" => $wikiassignment_subpage_text,
		"token" => $csrftoken,
		"format" => "json"
	];

	$ch = curl_init();

	curl_setopt( $ch, CURLOPT_URL, $endPoint );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $params4 ) );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_COOKIEJAR, "cookie.txt" );
	curl_setopt( $ch, CURLOPT_COOKIEFILE, "cookie.txt" );

	$output = curl_exec( $ch );
	curl_close( $ch );

	echo ( $output );
}
