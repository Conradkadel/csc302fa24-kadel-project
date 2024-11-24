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
        $stockData = getStockInfo($symbol);
        echo $stockData;
    } else if ($action == 'addFavouriteStock') {
        signedInOrDie($_POST);
        $username = $_SESSION['username'];
        $symbol = $_POST['symbol'];
        echo json_encode(addFavourite($username,$symbol));
    } else if ($action == 'remove-stock-item') {
        signedInOrDie($_POST);
        echo json_encode(removeStock($_POST['username']));
    } else if ($action == 'update-stock-items') {
        signedInOrDie($_POST);
        echo json_encode(updateStockList($_POST['username']));

    }else if($action == 'addUser'){
        $saltedHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        echo json_encode(addUser($_POST['username'], $saltedHash));

    //SIGN IN SIGN OUT
    } else if ($action == 'signIn') {
       // Authenticate user and generate JWT if successful
       $response = signin($_POST);
       if ($response['success']) {
        $_SESSION['signed-in'] = true;  // Set this after successful sign-in
        $_SESSION['username'] = $username;
        $jwt = makeJWT([
            'user-id' => $response['id'],
            'is-admin' => $response['isAdmin'],
            'exp' => (new DateTime('NOW'))->modify('+1 day')->format('c')
        ], $SECRET);
        echo json_encode(['success' => true, 'jwt' => $jwt]);    
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

function signin($data) {
    global $dbh;
    $username = $data['username'];
    $password = $data['password'];
    try {
        $statement = $dbh->prepare('SELECT id, password, isAdmin FROM Users WHERE username = :username');
        $statement->execute([':username' => $username]);
        $user = $statxement->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return [
                'success' => true,
                'id' => $user['id'],
                'isAdmin' => $user['isAdmin']
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