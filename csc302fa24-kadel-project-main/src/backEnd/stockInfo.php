<?php

/**
 * Stock Information Fetcher for Stock/Crypto Viewer
 * Author: Conrad Kadel
 *
 * Description:
 * Contains functions to fetch stock data from third-party APIs such as Alpha Vantage.
 * Provides functions that return detailed stock and cryptocurrency information to the frontend in JSON format.
 * 
 * Citations:
 * - Alpha Vantage API (https://www.alphavantage.co/documentation/)
 */

require_once("db.php");

// Enable error reporting for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set up data directory based on the server environment
$matches = [];
preg_match('#^/~([^/]*)#', $_SERVER['REQUEST_URI'], $matches);
$homeDir = count($matches) > 1 ? $matches[1] : '';
$dataDir = "/home/$homeDir/www-data";
if (!file_exists($dataDir)) {
    $dataDir = __DIR__;
}

/**
 * Retrieves intraday or daily stock data using the Alpha Vantage API.
 * 
 * @param string $symbol The stock symbol (e.g., IBM, AAPL).
 * @param string $interval The interval for the stock data (e.g., '5min', '1day').
 * @return array JSON-decoded response containing stock data, or an error message if the request fails.
 */
function getStockInfo($symbol, $interval) {
    $symbol = strtoupper($symbol); // Convert symbol to uppercase for consistency
    $apiKey = "5I76DVDO0NODXJ78"; // Replace with your actual API key

    // Determine the appropriate API endpoint based on the interval
    if ($interval === "1day") {
        $url = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=$symbol&outputsize=full&apikey=$apiKey";
    } else {
        $url = "https://www.alphavantage.co/query?function=TIME_SERIES_INTRADAY&symbol=$symbol&interval=$interval&outputsize=full&apikey=$apiKey";
    }

    // Fetch data from the API
    $json = file_get_contents($url);

    if ($json === false) {
        echo json_encode(["error" => "Unable to retrieve data"]);
        exit;
    }

    // Decode the JSON response
    $data = json_decode($json, true);

    return $data;
}

/**
 * Fetches stock overview information for the given symbol using Alpha Vantage API.
 *
 * @param string $symbol The stock symbol for which the overview is retrieved.
 * @return array JSON-decoded response containing stock overview data, or an error message if unavailable.
 */
function getStockOverview($symbol) {
    $symbol = strtoupper($symbol); // Convert symbol to uppercase for consistency
    $apiKey = "5I76DVDO0NODXJ78"; // Replace with your actual API key

    $url = "https://www.alphavantage.co/query?function=OVERVIEW&symbol=$symbol&apikey=$apiKey";

    $json = file_get_contents($url);

    if ($json === false) {
        echo json_encode(["error" => "Unable to retrieve overview data"]);
        exit;
    }

    $data = json_decode($json, true);

    if (empty($data) || isset($data["Note"])) {
        echo json_encode(["success" => false, "error" => "Overview information is currently unavailable."]);
        exit;
    }

    return $data;
}

/**
 * Retrieves intraday data and the current price of a cryptocurrency using the Alpha Vantage API.
 * 
 * @param string $symbol The cryptocurrency symbol (e.g., BTC, ETH).
 * @return array JSON-decoded response containing cryptocurrency data or an error message if the request fails.
 */
function getCryptoInfo($symbol) {
    $symbol = strtoupper($symbol); // Convert symbol to uppercase for consistency
    $apiKey = "5I76DVDO0NODXJ78"; // Replace with your actual API key

    $intradayUrl = "https://www.alphavantage.co/query?function=CRYPTO_INTRADAY&symbol=$symbol&market=USD&interval=5min&apikey=$apiKey";

    $intradayJson = file_get_contents($intradayUrl);

    if ($intradayJson === false) {
        echo json_encode(["success" => false, "error" => "Unable to retrieve intraday data for the specified cryptocurrency"]);
        exit;
    }

    $data = json_decode($intradayJson, true);

    if (empty($data) || isset($data["Note"])) {
        echo json_encode(["success" => false, "error" => "Overview information is currently unavailable."]);
        exit;
    }

    return $data;
}

/**
 * Retrieves market news sentiment for user-favorite stocks and cryptocurrencies.
 * 
 * @return array JSON-decoded response containing news sentiment data, limited to three articles per user favorite.
 */
function getMarketInfo() {
    $apiKey = "5I76DVDO0NODXJ78"; // Replace with your actual API key
    $username = $_SESSION['username']; // Get the currently signed-in username

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

        $formattedTickers = implode(',', $tickers);

        $url = "https://www.alphavantage.co/query?function=NEWS_SENTIMENT&tickers=$formattedTickers&limit=20&apikey=$apiKey";

        $response = file_get_contents($url);

        $newsData = json_decode($response, true);

        if (isset($newsData['feed'])) {
            $newsData['feed'] = array_slice($newsData['feed'], 0, 3); // Limit to 3 results
        }

        return $newsData;
    } else {
        echo "Error retrieving favorites: " . $userFavorites['message'];
    }
}

?>
