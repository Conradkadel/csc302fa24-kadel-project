// Stock.js - Stock Class Implementation
// Modified by Conrad Kadel to include additional data from Alpha Vantage

class Stock {
  
    constructor(symbol, lastRefreshed, timeSeries, name, marketCap, peRatio, dividendYield, description) {
        this.symbol = symbol;
        this.lastRefreshed = lastRefreshed;
        this.timeSeries = timeSeries;
        this.name = name;
        this.marketCap = marketCap;
        this.peRatio = peRatio;
        this.dividendYield = dividendYield;
        this.description = description;
        this.isCrypto = "stock"; // Flag to indicate this represents a stock
    }

    /**
     * Fetches stock data, including overview information, from the backend API.
     * 
     * @param symbol - The stock symbol (e.g., AAPL, TSLA).
     * @param interval - The time interval for the stock data (e.g., "5min", "1day").
     * @returns A Stock object with the fetched data, or null if an error occurs.
     */
    static async fetchStockData(symbol, interval) {
        try {
            symbol = symbol.toUpperCase(); // Ensure the symbol is uppercase for consistency

            const response = await fetch('../backEnd/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=getStockData&symbol=${encodeURIComponent(symbol)}&interval=${encodeURIComponent(interval)}`, // API call with parameters
            });

            if (!response.ok) {
                throw new Error('Failed to fetch stock data'); // Handle failed API requests
            }

            const data = await response.json(); // Parse the JSON response
            console.log(data);

            // Validate the data returned by the API
            if (data.error || !data["Meta Data"]) {
                console.error("API returned an error or invalid data:", data.error || "No Meta Data or Time Series available");
                throw new Error("No data found for the symbol provided");
            }

            return Stock.parseStockData(data); // Parse and return the stock data
        } catch (error) {
            console.error("Error fetching stock data:", error); // Log errors for debugging
            return null;
        }
    }

    /**
     * Parses raw data from the API into a Stock object.
     * 
     * @param rawData - The raw JSON data received from the API.
     * @returns A Stock object with the parsed data, or null if the data is invalid.
     */
    static parseStockData(rawData) {
        console.log("The raw data" + rawData); // Log raw data for debugging

        // Extract metadata and time series data from the API response
        const metaData = rawData["Meta Data"];
        const timeSeriesKey = Object.keys(rawData).find(key => key.startsWith("Time Series"));
        const timeSeries = rawData[timeSeriesKey];
        
        if (!metaData || !timeSeries) {
            console.error("Invalid data format"); // Log an error if the format is invalid
            return null;
        }

        // Extract relevant fields from the raw data
        const symbol = metaData["2. Symbol"];
        const lastRefreshed = metaData["3. Last Refreshed"];
        const name = rawData.Name || ""; // Default to an empty string if data is unavailable
        const marketCap = rawData.MarketCapitalization || "";
        const peRatio = rawData.PERatio || "";
        const dividendYield = rawData.DividendYield || "";
        const description = rawData.Description || "";

        // Return a new Stock object with the parsed data
        return new Stock(symbol, lastRefreshed, timeSeries, name, marketCap, peRatio, dividendYield, description);
    }
}
