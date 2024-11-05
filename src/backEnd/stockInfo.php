<!-- 3rd Partz API Backend FOR Stock viwer 
 
      Created by Conrad Kadel 

-->

<?php


function getStockInfo($symbol,$apiKey) {
    // Set the API endpoint and your API key
    //$symbol = "IBM"; // You can dynamically change this based on frontend input
    $apiKey = "6PS6LZ5NU5FCXMPD"; // Replace with your actual Alpha Vantage API key
    $url = "https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol=$symbol&interval=5min&apikey=$apiKey";

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);

    // Check for errors
    if ($response === false) {
        echo json_encode(["error" => "Unable to retrieve data"]);
        exit;
    }

    // Close cURL
    curl_close($ch);

    // Convert the response to an associative array
    $data = $response;

    debug_to_console($data);
    // Send the data back to the frontend as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
}

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
?>
