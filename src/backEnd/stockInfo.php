
<?php

/**
 * Stock Information Fetcher for Stock/Crypto Viewer
 * Author: Conrad Kadel
 *
 * Description:
 * Contains functions to fetch stock data from third-party APIs such as Alpha Vantage.
 * Provides functions that return detailed stock information to the frontend in JSON format.
 * Citations:
 * - Alpha Vantage API (https://www.alphavantage.co/documentation/)
 * 
 * MORE TO ADD HERE AS WE NEED TO FETCH MORE INFORMATION FROM API
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if(!file_exists($dataDir)){
    $dataDir = __DIR__;
}

function getStockInfo($symbol) {
    
    // This is done with help from https://www.alphavantage.co/documentation/ 
    // Set the API endpoint and your API key
    $symbol = "IBM"; // I can change that
    $apiKey = "6PS6LZ5NU5FCXMPD"; 

    // Set the API endpoint and your API key
    $url = "https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol=$symbol&interval=5min&apikey=$apiKey";

    // Fetch data from the URL using file_get_contents
    $json = file_get_contents($url);

    // Error handling if the request fails
    if ($json === false) {
        echo json_encode(["error" => "Unable to retrieve data"]);
        exit;
    }

    // Convert the response to an associative array
    
    // Send the data back to the frontend as JSON
    echo $json;
}
