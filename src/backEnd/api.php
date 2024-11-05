<?php
header('Content-type: application/json');
session_start();
// For debugging:
error_reporting(E_ALL);
ini_set('display_errors', '1');


/// API KEY 6PS6LZ5NU5FCXMPD
// TODO Change this as needed. SQLite will look for a file with this name, or
// create one if it can't find it.
$dbName = 'data.db';

require_once('db.php');
require_once('stockInfo.php');

// Leave this alone. It checks if you have a directory named www-data in
// you home directory (on a *nix server). If so, the database file is
// sought/created there. Otherwise, it uses the current directory.
// The former works on digdug where I've set up the www-data folder for you;
// the latter should work on your computer.
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

// Put your other code here.

createTables();

// Handle incoming requests.
if(array_key_exists('action', $_POST)){
    $action = $_POST['action'];
    if($action == 'getStockData'){
        authenticateOrDie($_POST);
        echo json_encode(getStockInfo($_POST['symbol']));
    } else if ($action == 'add-stock-item') {
        authenticateOrDie($_POST);
        echo json_encode(addStock($_POST['username']));
    } else if ($action == 'remove-stock-item') {
        authenticateOrDie($_POST);
        echo json_encode(removeStock($_POST['username']));
    } else if ($action == 'update-stock-items') {
        authenticateOrDie($_POST);
        echo json_encode(updateStockList($_POST['username']));

    }else if($action == 'addUser'){
        $saltedHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        echo json_encode(addUser($_POST['username'], $saltedHash));

    //SIGN IN SIGN OUT
    } else if ($action == 'signIn') {
        echo signIn($_POST['username'], $_POST['password']);
    
    } else if ($action == 'signOut') {
        echo signOut(); 
    
    } else {
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid action: '. $action
        ]);
    }
}


// SIGN IN SIGN OUT FUNCTIONS 

function signIn($username, $password) {
    $userInfo = getUserByUsername($username);

    if ($userInfo['success'] && password_verify($password, $userInfo['password'])) {
        $_SESSION['signed_in'] = true;
        $_SESSION['username'] = $username; 
        return json_encode(['success' => true, 'message' => 'Signed in successfully']);
    } else {
        return json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
}

function signOut() {
    if (isset($_SESSION['signed_in'])) {
        session_destroy();
        return json_encode(['success' => true, 'message' => 'Signed out successfully']);
    } else {
        return json_encode(['success' => false, 'message' => 'You are not signed in']);
    }
}

/**
 * Authenticates the user based on the stored credentials.
 * 
 * @param data An associative array holding parameters and their values. Should
 *             have these keys:
 *              - username
 *              - password
 */
function authenticateOrDie($data){
    // TODO: add code to check that username and password params are
    //       present.

    $userInfo = getUserByUsername($data['username']);
    if($userInfo['success'] && password_verify($data['password'], $userInfo['password'])){
        return true;

    } else {
        http_response_code(401);
        die(json_encode([
            'success' => false,
            'error' => 'Invalid username or password'
        ]));

    }
}


?>