<?php

/**
 * API Manager for Stock/Crypto Viewer
 * Author: Conrad Kadel
 *
 * Description:
 * Handles API requests for signing in, signing up, fetching stock data, and adding favorite stocks.
 * Utilizes session storage to manage user authentication securely.
 * 
 * Citations:
 * - Took code from class examples (Quizzer)
 * 
 * Notes:
 * AI used for Comments
 */

session_start();

// Debugging: Enable error reporting for development purposes.
error_reporting(E_ALL);
ini_set('display_errors', '1');
header('Content-Type: application/json');

/// API KEY 6PS6LZ5NU5FCXMPD

// Database and required files
$dbName = 'data.db';
require_once('db.php');
require_once('stockInfo.php');

// Set up data directory based on environment.
$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if (!file_exists($dataDir)) {
    $dataDir = __DIR__;
}

// Database connection with error handling
$dbh = new PDO("sqlite:$dataDir/$dbName");
// Set our PDO instance to raise exceptions when errors are encountered.
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

createTables(); // Initialize database tables if needed.

/**
 * Handles incoming requests based on the 'action' parameter in POST data.
 */
if (array_key_exists('action', $_POST)) {
    $action = $_POST['action'];
    if ($action == 'getStockData') {
        // Fetch stock data based on symbol and interval
        $symbol = $_POST['symbol'];
        $interval = $_POST['interval'];
        $stockData = getStockInfo($symbol, $interval);
        $stockInfo = getStockOverview($symbol);
        $combinedData = array_merge($stockData, $stockInfo);
        echo json_encode($combinedData);
    } else if ($action == 'getCryptoData') {
        // Fetch cryptocurrency data based on symbol
        $symbol = $_POST['symbol'];
        $cryptoData = getCryptoInfo($symbol);
        echo json_encode($cryptoData);
    } else if ($action == 'addFavouriteStock') {
        // Add a stock or cryptocurrency to the user's favorites
        signedInOrDie();
        $username = $_SESSION['username'];
        $symbol = $_POST['symbol'];
        $isCrypto = $_POST['isCrypto'];
        echo json_encode(addFavourite($username, $symbol, $isCrypto));
    } else if ($action == 'getUserFavorites') {
        // Fetch the list of user's favorite stocks and cryptocurrencies
        $username = $_SESSION['username'];
        echo json_encode(getUserFavorites($username));
    } else if ($action == 'getMarketInfo') {
        // Fetch market overview information
        echo json_encode(getMarketInfo());
    } else if ($action == 'addUser') {
        // Register a new user with a hashed password
        $saltedHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
        echo json_encode(addUser($_POST['username'], $saltedHash));
    } else if ($action == 'signIn') {
        // Handle user sign-in
        $username = $_POST['username'];
        $password = $_POST['password'];
        $response = signin($username, $password);
        if ($response['success']) {
            $_SESSION['signed-in'] = true; // Set this after successful sign-in
            echo json_encode($response);
        } else {
            echo json_encode(['success' => false, 'message' => 'Sign in failed.']);
        }
    } else if ($action == 'signOut') {
        // Handle user sign-out
        signedInOrDie();
        echo json_encode(signout());
    } else {
        // Invalid action handling
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action: ' . $action
        ]);
    }
}

/**
 * Handles user sign-in.
 * 
 * @param string $username The username provided by the user.
 * @param string $password The password provided by the user.
 * @return array Response indicating success or failure of sign-in.
 */
function signin($username, $password) {
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
 * Handles user sign-out.
 * 
 * @return array Response indicating success of sign-out.
 */
function signout() {
    session_destroy();
    return ['success' => true];
}

/**
 * Ensures the user is authenticated; otherwise, terminates the script with an error.
 */
function signedInOrDie() {
    if (array_key_exists('signed-in', $_SESSION) && $_SESSION['signed-in']) {
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
