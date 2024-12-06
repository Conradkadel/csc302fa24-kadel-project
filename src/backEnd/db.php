<?php
/**
 * Database Manager for Stock/Crypto Viewer
 * Author: Conrad Kadel
 *
 * Description:
 * Connects to the SQLite database (`data.db`) and provides functions for user management, stock management, and creating tables.
 * Handles all the CRUD operations needed for users and stocks.
 * 
 * Citations:
 * - Help from Class code (Quizzer)
 * - AI for Comments
 */

$dbName = 'data.db';
header('Content-Type: application/json');

// Set up the data directory based on the environment
$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if (!file_exists($dataDir)) {
    $dataDir = __DIR__;
}

// Database connection with error handling
$dbh = new PDO("sqlite:$dataDir/$dbName");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/**
 * Returns an error response.
 *
 * @param string $message The error message to return.
 * @return array An associative array containing success status and the error message.
 */
function error($message) {
    return [
        'success' => false,
        'error' => $message
    ];
}

/**
 * Creates necessary tables (Users, Stocks, Favorites) in the database if they do not exist.
 */
function createTables() {
    global $dbh;

    try {
        $dbh->exec('CREATE TABLE IF NOT EXISTS Users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE,
            password TEXT,
            createdAt DATETIME DEFAULT (datetime()),
            updatedAt DATETIME DEFAULT (datetime())
        )');
    } catch (PDOException $e) {
        echo "There was an error creating the Users table: " . $e->getMessage();
    }

    try {
        $dbh->exec('CREATE TABLE IF NOT EXISTS Stocks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ticker TEXT,
            name TEXT,
            price TEXT
        )');
    } catch (PDOException $e) {
        echo "There was an error creating the Stocks table: " . $e->getMessage();
    }

    try {
        $dbh->exec('CREATE TABLE IF NOT EXISTS Favorites (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT,
            ticker TEXT,
            isCrypto BOOLEAN,
            createdAt DATETIME DEFAULT (datetime()),
            FOREIGN KEY(username) REFERENCES Users(username)
        )');
    } catch (PDOException $e) {
        echo "There was an error creating the Favorites table: " . $e->getMessage();
    }
}

/**
 * Adds a new user to the Users table in the database.
 *
 * @param string $username The username of the new user.
 * @param string $hashedPassword The hashed password of the new user.
 * 
 * @return array An associative array with the success status and user ID, or an error message.
 */
function addUser($username, $hashedPassword) {
    global $dbh;
    $id = null;
    try {
        $statement = $dbh->prepare(
            'INSERT INTO Users(username, password) VALUES (:username, :password)'
        );
        $statement->execute([
            ':username' => $username,
            ':password' => $hashedPassword
        ]);

        $id = $dbh->lastInsertId();
    } catch (PDOException $e) {
        return error("There was an error adding a user: $e");
    }
    $_SESSION['signed-in'] = true;
    $_SESSION['username'] = $username;
    return [
        'success' => true,
        'id' => $id
    ];
}

/**
 * Retrieves a user's data from the Users table based on the given username.
 *
 * @param string $username The username to search for.
 * 
 * @return array An associative array containing the user's data or an error message.
 */
function getUserByUsername($username) {
    global $dbh;
    $userData = null;
    try {
        $statement = $dbh->prepare('SELECT * FROM Users WHERE username = :username');
        $statement->execute([
            ':username' => $username
        ]);
        $userData = $statement->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return error("There was an error retrieving the user: $e");
    }

    if ($userData) {
        $userData['success'] = true;
    } else {
        return error("User not found");
    }

    return $userData;
}

/**
 * Adds a stock or cryptocurrency to the user's favorites.
 *
 * @param string $username The username of the user.
 * @param string $ticker The ticker symbol of the stock or cryptocurrency.
 * @param bool $isCrypto True if the favorite is a cryptocurrency, false otherwise.
 * 
 * @return array An associative array indicating success or failure with an error message if applicable.
 */
function addFavourite($username, $ticker, $isCrypto) {
    global $dbh;
    try {
        $user = getUserByUsername($username);
        if (!$user['success']) {
            return $user;
        }
        
        // Check if the ticker already exists for this user
        $checkStatement = $dbh->prepare('SELECT COUNT(*) FROM Favorites WHERE username = :username AND ticker = :ticker');
        $checkStatement->execute([
            ':username' => $username,
            ':ticker' => strtoupper($ticker) // Consistency: Convert to uppercase
        ]);

        $exists = $checkStatement->fetchColumn();
        if ($exists > 0) {
            return [
                'success' => false,
                'message' => 'This ticker is already in your favorites.'
            ];
        }

        $statement = $dbh->prepare('INSERT INTO Favorites (username, ticker, isCrypto) VALUES (:username, :ticker, :isCrypto)');
        $statement->execute([
            ':username' => $username,
            ':ticker' => strtoupper($ticker), // Converting symbol to uppercase for consistency
            ':isCrypto' => $isCrypto
        ]);
        return ['success' => true];
    } catch (PDOException $e) {
        return error("There was an error adding the favorite: $e");
    }
}

/**
 * Retrieves the list of a user's favorite stocks or cryptocurrencies.
 *
 * @param string $username The username of the user.
 * 
 * @return array An associative array containing the success status and a list of favorites, or an error message.
 */
function getUserFavorites($username) {
    global $dbh;
    try {
        $statement = $dbh->prepare('SELECT ticker, isCrypto FROM Favorites WHERE username = :username');
        $statement->execute([':username' => $username]);
        $favorites = $statement->fetchAll(PDO::FETCH_ASSOC);
        return [
            'success' => true,
            'favorites' => $favorites
        ];
    } catch (PDOException $e) {
        return error("Error retrieving favorite stocks: $e");
    }
}

?>
