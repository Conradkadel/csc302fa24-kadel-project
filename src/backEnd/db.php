<!-- DB Manager FOR Stock viwer 
 
      Created by Conrad Kadel 

-->

<?php

// TODO Change this as needed. SQLite will look for a file with this name, or
// create one if it can't find it.
$dbName = 'data.db';

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
$dbh = new PDO("sqlite:$dataDir/$dbName");
// Set our PDO instance to raise exceptions when errors are encountered.
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/**
 * Returns an associative array with two fields:
 *  - success: false
 *  - error:  $message
 * 
 * @return An associative array describing the error.
 */
function error($message){
    return [
        'success' => false, 
        'error' => $message
    ];
}
/**
 * Creates all of the tables for this project:
 *  - QuizItems
 *  - Submissions
 *  - QuizItemResponses
 */
function createTables(){
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

   
}

?>