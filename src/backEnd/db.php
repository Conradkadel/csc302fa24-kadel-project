<?php
/**
 * Database Manager for Stock/Crypto Viewer
 * Author: Conrad Kadel
 *
 * Description:
 * Connects to the SQLite database (`data.db`) and provides functions for user management, stock management, and creating tables.
 * Handles all the CRUD operations needed for users and stocks.
 * Citations:
 * - Help from Class code ( Quizzer )
 */

$dbName = 'data.db';
header('Content-Type: application/json');

$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if (!file_exists($dataDir)) {
    $dataDir = __DIR__;
}

$dbh = new PDO("sqlite:$dataDir/$dbName");
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function error($message) {
    return [
        'success' => false,
        'error' => $message
    ];
}

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

    // Create the Stocks table.
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

    // Create the Favourites table.
    try {
        $dbh->exec('CREATE TABLE IF NOT EXISTS Favourites (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            userId INTEGER,
            ticker TEXT,
            addedAt DATETIME DEFAULT (datetime()),
            FOREIGN KEY(userId) REFERENCES Users(id)
        )');
    } catch (PDOException $e) {
        echo "There was an error creating the Favourites table: " . $e->getMessage();
    }
}
/**
 * Adds a new user to the Users table in the database.
 *
 * @param string $username The username of the new user.
 * @param string $hashedPassword The hashed password of the new user.
 * 
 * @return array Returns an associative array with the success status and the user ID if successful, or an error message if an exception occurs.
 */

function addUser($username, $hashedPassword) {
    global $dbh;
    $id = null;
    try {
        $statement = $dbh->prepare(
            'insert into Users(username, password) values (:username, :password)'
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
 * @param string $username The username of the user to retrieve.
 * 
 * @return array Returns an associative array containing the user's data if found, or an error message if the user is not found or an exception occurs.
 * 
 */
function getUserByUsername($username) {
    global $dbh;
    $userData = null;
    try {
        $statement = $dbh->prepare('select * from Users where username = :username');
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

function addFavourite($username, $ticker) {
    global $dbh;
    try {
        // Retrieve the user by username
        $user = getUserByUsername($username);
        if (!$user['success']) {
            return $user;
        }

        // Insert the favourite stock into the Favourites table
        $statement = $dbh->prepare(
            'insert into Favourites(userId, ticker) values (:userId, :ticker)'
        );
        $statement->execute([
            ':userId' => $user['id'],
            ':ticker' => $ticker
        ]);
    } catch (PDOException $e) {
        return error("There was an error adding the favourite: $e");
    }
    return [
        'success' => true
    ];
}
?>



