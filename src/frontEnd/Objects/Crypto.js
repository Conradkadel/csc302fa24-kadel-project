class Crypto {
    /**
     * Constructor for the Crypto class.
     * 
     * @param symbol - The symbol of the cryptocurrency (e.g., BTC, ETH).
     * @param lastRefreshed - The timestamp of the last data refresh.
     * @param timeSeries - The time series data for the cryptocurrency.
     * @param currentPrice - The current price of the cryptocurrency.
     */
    constructor(symbol, lastRefreshed, timeSeries, currentPrice) {
        this.symbol = symbol;
        this.lastRefreshed = lastRefreshed;
        this.timeSeries = timeSeries;
        this.currentPrice = currentPrice;
        this.isCrypto = "crypto"; // A flag to denote the instance represents a cryptocurrency
    }

    /**
     * Fetches cryptocurrency data from the backend.
     * 
     * @param symbol - The symbol of the cryptocurrency (e.g., BTC, ETH).
     * @returns A Crypto object with parsed data, or null if an error occurs.
     */
    static async fetchCryptoData(symbol) {
        try {
            console.log("Start");
            symbol = symbol.toUpperCase(); // Ensure the symbol is uppercase for consistency

            // Fetching intraday data for the cryptocurrency from the backend API
            const response = await fetch('../backEnd/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=getCryptoData&symbol=${encodeURIComponent(symbol)}`, // API call with symbol parameter
            });

            console.log("Start2"); 
            console.log(response);

            if (!response.ok) {
                throw new Error('Failed to fetch stock data'); // Error handling for a failed API response
            }

            const data = await response.json(); // Parse JSON response
            console.log(data);
            console.log("finished");

            const currentPrice = 99999; // Placeholder for the current price; should be updated with real data

            return Crypto.parseCryptoData(data, currentPrice); // Parse and return the data as a Crypto object

        } catch (error) {
            console.error("Error fetching cryptocurrency data:", error);
            return null; // Return null if an error occurs
        }
    }

    /**
     * Parses raw data from the API into a Crypto object.
     * 
     * @param rawData - The raw JSON data received from the API.
     * @param currentPrice - The current price of the cryptocurrency.
     * @returns A Crypto object with the parsed data, or null if the data is invalid.
     */
    static parseCryptoData(rawData, currentPrice) {
        // Extract metadata and time series data from the API response
        const metaData = rawData["Meta Data"];
        const timeSeriesKey = Object.keys(rawData).find(key => key.startsWith("Time Series"));
        const timeSeries = rawData[timeSeriesKey];

        // Validate the existence of metadata and time series data
        if (!metaData || !timeSeries) {
            console.error("Invalid data format"); // Log an error if the format is invalid
            return null;
        }

        // Extract required fields from the metadata
        const symbol = metaData["2. Digital Currency Code"];
        const lastRefreshed = metaData["6. Last Refreshed"];

        // Return a new Crypto object with the parsed data
        return new Crypto(symbol, lastRefreshed, timeSeries, currentPrice);
    }
}
