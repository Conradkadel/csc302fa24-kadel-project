class Stock {
    constructor(symbol, lastRefreshed, timeSeries) {
        this.symbol = symbol;
        this.lastRefreshed = lastRefreshed;
        this.timeSeries = timeSeries;
    }

    getLatestData() {
        const timestamps = Object.keys(this.timeSeries);
        if (timestamps.length > 0) {
            const latestTimestamp = timestamps[0];
            return {
                timestamp: latestTimestamp,
                ...this.timeSeries[latestTimestamp],
            };
        }
        return null;
    }

    // Static method to fetch stock data
    static async fetchStockData(symbol) {
        try {
            const response = await fetch('../backEnd/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=getStockData&symbol=${encodeURIComponent(symbol)}`,
            });

            if (!response.ok) {
                throw new Error('Failed to fetch stock data');
            }

            const data = await response.json();
            return Stock.parseStockData(data);
        } catch (error) {
            console.error("Error fetching stock data:", error);
            return null;
        }
    }

    // Method to parse raw data into a Stock object
    static parseStockData(rawData) {
        const metaData = rawData["Meta Data"];
        const timeSeries = rawData["Time Series (5min)"];

        if (!metaData || !timeSeries) {
            console.error("Invalid data format");
            return null;
        }

        const symbol = metaData["2. Symbol"];
        const lastRefreshed = metaData["3. Last Refreshed"];

        return new Stock(symbol, lastRefreshed, timeSeries);
    }
}
