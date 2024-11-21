<!-- 3rd Partz API Backend FOR Stock viwer 
 
      Created by Conrad Kadel 

-->

<?php


function getStockInfo($symbol,$apiKey) {
    
    // This is done with help from https://www.alphavantage.co/documentation/ 
    // Set the API endpoint and your API key
    $symbol = "IBM"; // I can change that
    $apiKey = "6PS6LZ5NU5FCXMPD"; 

    // Set the API endpoint and your API key
    $url = "https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol=$symbol&interval=5min&apikey=$apiKey";

    // Fetch data from the URL using file_get_contents
    $json = @file_get_contents($url);

    // Error handling if the request fails
    if ($json === false) {
        echo json_encode(["error" => "Unable to retrieve data"]);
        exit;
    }

    // Convert the response to an associative array
    $data = json_decode($json, true);

    // Send the data back to the frontend as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
}
