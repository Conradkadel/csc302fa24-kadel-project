
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
require_once("db.php");
error_reporting(E_ALL);
ini_set('display_errors', '1');
$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if(!file_exists($dataDir)){
    $dataDir = __DIR__;
}

/**
 * getStockInfo
 *
 * This function retrieves intraday stock data using the Alpha Vantage API.
 * It makes an API call to fetch the stock's time series data in 5-minute intervals
 * and returns the data as a JSON response to the frontend.
 *
 * @param string $symbol The stock symbol (e.g., IBM, AAPL). The symbol is converted to uppercase for consistency.
 * @return void Outputs a JSON response containing intraday stock data or an error message.
 *
 * Usage:
 * - This function is intended to be used as an API endpoint, where the $symbol parameter is received from a client-side request.
 *
 * References:
 * - Alpha Vantage API documentation was used for making API calls:
 *   https://www.alphavantage.co/documentation/
 * 
 * Example:
 * - If a user requests data for a specific stock symbol like "IBM", the function will return the intraday time series
 *   data for that stock.
 */
function getStockInfo($symbol,$interval) {
    
    // This is done with help from https://www.alphavantage.co/documentation/ 
    // Set the API endpoint and your API key
    $symbol = strtoupper($symbol);

    $apiKey = "5I76DVDO0NODXJ78"; 

    // Set the API endpoint and your API key

    // Check if the interval is "1day" to determine the correct API endpoint
    if ($interval === "1day") {
        // Use the TIME_SERIES_DAILY endpoint for daily data
        $url = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=$symbol&outputsize=full&apikey=$apiKey";
    } else {
        // Use the TIME_SERIES_INTRADAY endpoint for other intervals
        $url = "https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol=$symbol&interval=$interval&outputsize=full&apikey=$apiKey";
    }
    // Fetch data from the URL using file_get_contents
    $json = file_get_contents($url);

    // Error handling if the request fails
    if ($json === false) {
        echo json_encode(["error" => "Unable to retrieve data"]);
        exit;
    }

    // Convert the response to an associative array
    $data = json_decode($json, true);
    
    // Send the data back to the frontend as JSON
    return $data;
}

/**
 * Fetches stock overview information for the given symbol.
 * Uses Alpha Vantage API's Overview function.
 *
 * @param string $symbol The stock symbol for which the overview needs to be retrieved.
 * @return void Outputs the JSON response of the overview information.
 * 
 * @link https://www.alphavantage.co/documentation/ for more information on the API used.
 */
function getStockOverview($symbol) {
    // Convert symbol to uppercase to ensure consistency
    $symbol = strtoupper($symbol);

    $apiKey = "5I76DVDO0NODXJ78"; // Replace with your actual API key

    // Set the API endpoint for the overview function
    $url = "https://www.alphavantage.co/query?function=OVERVIEW&symbol=$symbol&apikey=$apiKey";

    // Fetch data from the URL using file_get_contents
    $json = file_get_contents($url);

    // Error handling if the request fails
    if ($json === false) {
        echo json_encode(["error" => "Unable to retrieve overview data"]);
        exit;
    }

    // Convert the response to an associative array
    $data = json_decode($json, true);

    // Error handling if the response is empty or contains an error message
    if (empty($data) || isset($data["Note"])) {
        echo json_encode(["success" => false, "error" => "Overview information is currently unavailable."]);
        exit;
    }

    // Send the overview data back to the frontend as JSON
    return $data;
}


/**
 * getCryptoInfo
 * 
 * This function retrieves intraday data and the current price of a cryptocurrency using the Alpha Vantage API. 
 * It performs two API calls: one to fetch intraday price data, and another to get the current exchange rate.
 * The resulting data is returned as a JSON response to the frontend.
 *
 * @param string $symbol The cryptocurrency symbol (e.g., BTC, ETH). It will be converted to uppercase.
 * @return void Outputs a JSON response containing intraday data and the current price.
 *
 * Usage:
 * - This function is intended to be used as an API endpoint, where the $symbol parameter is received from a client-side request.
 *
 *
 * References:
 * - The API calls were made possible using the Alpha Vantage API:
 *   https://www.alphavantage.co/documentation/
 */
function getCryptoInfo($symbol) {
    // Convert symbol to uppercase to ensure consistency
    $symbol = strtoupper($symbol);

    $apiKey = "5I76DVDO0NODXJ78"; // Replace with your actual API key

    // API endpoint for intraday cryptocurrency data
    $intradayUrl = "https://www.alphavantage.co/query?function=CRYPTO_INTRADAY&symbol=$symbol&market=USD&interval=5min&apikey=$apiKey";

    // Fetch intraday data from the URL using file_get_contents
    $intradayJson = file_get_contents($intradayUrl);
    
    // Error handling if the request fails
    if ($intradayJson === false) {
        echo json_encode(["success" => false, "error" => "Unable to retrieve intraday data for the specified cryptocurrency"]);
        exit;
    }

    // Convert the response to an associative array
    $data = json_decode($intradayJson, true);

    // Error handling if the response is empty or contains an error message
    if (empty($data) || isset($data["Note"])) {
        echo json_encode(["success" => false, "error" => "Overview information is currently unavailable."]);
        exit;
    }
    
    return $data;
}

function getMarketInfo() {
    $apiKey = "5I76DVDO0NODXJ78"; // Replace with your actual API key
    $username = $_SESSION['username'];
    $userFavorites = getUserFavorites($username);


    if ($userFavorites['success']) {
        $tickers = [];
        foreach ($userFavorites['favorites'] as $favorite) {
            $ticker = $favorite['ticker'];
    
            if ($favorite['isCrypto'] == "crypto") {
                $ticker = "CRYPTO:$ticker";
            }
            $tickers[] = $ticker; // Add the formatted ticker to the list
        }
    
        // Join all tickers into a single string
        $formattedTickers = implode(',', $tickers);
    
        // Construct the API URL
        $intradayUrl = "https://www.alphavantage.co/query?function=NEWS_SENTIMENT&tickers=$formattedTickers&limit=20&apikey=$apiKey";
    
    
        // Optional: Fetch API response
        $response = file_get_contents($intradayUrl);

        $newsData = json_decode($response, true);

        // HAVE to split it here manually as for some reason the API doesnt care about the limit
        if (isset($newsData['feed'])) {
            $newsData['feed'] = array_slice($newsData['feed'], 0, 3); // Limit to 1 result
        }
        
        return $newsData;
      
    } else {
        echo "Error retrieving favorites: " . $userFavorites['message'];
    }
    
}
