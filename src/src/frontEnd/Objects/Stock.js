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
        this.isCrypto = "stock";
    }

    // Static method to fetch stock data, including overview
    static async fetchStockData(symbol,interval) {
        try {
            symbol = symbol.toUpperCase();

            const response = await fetch('../backEnd/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=getStockData&symbol=${encodeURIComponent(symbol)}&interval=${encodeURIComponent(interval)}`,
            });

            if (!response.ok) {
                throw new Error('Failed to fetch stock data');
            }

            console.log(response);
            const data = await response.json();

            console.log(data);
            // If the data contains an error or is not as expected, handle it
            if (data.error || !data["Meta Data"]) {
                console.error("API returned an error or invalid data:", data.error || "No Meta Data or Time Series available");
                throw new Error("No data found for the symbol provided");
            }

            return Stock.parseStockData(data);
        } catch (error) {
            console.error("Error fetching stock data:", error);
            return null;
        }
    }

    // Method to parse raw data into a Stock object
    static parseStockData(rawData) {
        console.log("The raw data" + rawData);
        const metaData = rawData["Meta Data"];
        const timeSeriesKey = Object.keys(rawData).find(key => key.startsWith("Time Series"));
        const timeSeries = rawData[timeSeriesKey];
        
        if (!metaData || !timeSeries) {
            console.error("Invalid data format");
            return null;
        }

        const symbol = metaData["2. Symbol"];
        const lastRefreshed = metaData["3. Last Refreshed"];
        const name = rawData.Name || "";
        const marketCap = rawData.MarketCapitalization || "";
        const peRatio = rawData.PERatio || "";
        const dividendYield = rawData.DividendYield || "";
        const description = rawData.Description || "";

        return new Stock(symbol, lastRefreshed, timeSeries, name, marketCap, peRatio, dividendYield, description);
    }
}

