<?php

/**
 * API Manager for Stock/Crypto Viewer
 * Author: Conrad Kadel
 *
 * Description:
 * Handles API requests for signing in, signing up, fetching stock data, and adding favorite stocks.
 * Utilizes session storage to manage user authentication securely.
 * Citations:
 * - Took Code from Class examples ( Quizzer )
 */

/// NEED TO ADD
 // Sign in SHOULD WORK
 // Display favoruties

session_start();
// For debugging:
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: application/json');

/// API KEY 6PS6LZ5NU5FCXMPD

$dbName = 'data.db';

require_once('db.php');
require_once('stockInfo.php');

$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if(!file_exists($dataDir)){
    $dataDir = __DIR__;
}
$dbh = new PDO("sqlite:$dataDir/$dbName")   ;
// Set our PDO instance to raise exceptions when errors are encountered.
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


createTables();

// Handle incoming requests.
if(array_key_exists('action', $_POST)){
    $action = $_POST['action'];
    if($action == 'getStockData'){
        $symbol = $_POST['symbol'];
        $interval = $_POST['interval'];
        $stockData = getStockInfo($symbol,$interval);
        $stockInfo = getStockOverview($symbol);
        $combinedData = array_merge($stockData, $stockInfo);
        echo json_encode($combinedData);
    } else if ($action == 'getCryptoData') {
        $symbol = $_POST['symbol'];
        $cryptoData = getCryptoInfo($symbol);
        echo json_encode($cryptoData);
    } else if ($action == 'addFavouriteStock') {
        signedInOrDie($_POST);
        $username = $_SESSION['username'];
        $symbol = $_POST['symbol'];
        $isCrypto = $_POST['isCrypto'];
        echo json_encode(addFavourite($username,$symbol,$isCrypto));
    } else if ($action == 'getUserFavorites') {
        $username = $_SESSION['username'];
        $user = getUserByUsername($username);
        echo json_encode(getUserFavorites($username));
    } else if ($action == 'getMarketInfo') {
        echo json_encode(getMarketInfo());
    }
     else if($action == 'addUser'){
        $saltedHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        echo json_encode(addUser($_POST['username'], $saltedHash));

    //SIGN IN SIGN OUT
    } else if ($action == 'signIn') {
   
       $username = $_POST['username'];
       $password = $_POST['password'];
       $response = signin($username,$password);
       if ($response['success']) {
        $_SESSION['signed-in'] = true;  // Set this after successful sign-in
        echo json_encode($response);    
       } else {
           echo json_encode(['success' => false, 'message' => 'Sign in failed.']);
       }

    } else if ($action == 'signOut') {
        signedInOrDie();
        echo json_encode(signout());
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid action: '. $action
        ]);
    }
}



// SIGN IN SIGN OUT FUNCTIONS 

function signin($username,$password) {
    global $dbh;
    try {
        $statement = $dbh->prepare('SELECT id, password, username FROM Users WHERE username = :username');
        $statement->execute([':username' => $username]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return [
                'success' => true,
                'username' => $user['username']
            ];
        } else {
            return ['success' => false];
        }
    } catch (PDOException $e) {
        error("Error during sign-in: $e");
        return ['success' => false];
    }
}

/**
 * Signs the user out.
 */
function signout(){
  
    session_destroy();

    return ['success' => true];
}

/**
 * Authenticates the user based on the stored credentials.
 * 
 * I am not using the JWT here so maybe i can change that 
 */
function signedInOrDie(){
    // This is a good way to do authenticated sessions and uses PHP sessions.
    if(array_key_exists('signed-in', $_SESSION) && $_SESSION['signed-in']){
        return true;
    } else {
        http_response_code(401);
        die(json_encode([
            'success' => false,
            'error' => 'You must be signed in to perform this action.'
        ]));
    }
}


?>