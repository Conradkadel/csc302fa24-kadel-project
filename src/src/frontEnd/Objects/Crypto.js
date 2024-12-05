class Crypto {
    constructor(symbol, lastRefreshed, timeSeries, currentPrice) {
        this.symbol = symbol;
        this.lastRefreshed = lastRefreshed;
        this.timeSeries = timeSeries;
        this.currentPrice = currentPrice;
        this.isCrypto = "crypto";
    }

    // Static method to fetch cryptocurrency data
    static async fetchCryptoData(symbol) {
        let data;
        try {
            console.log("Start");
            symbol = symbol.toUpperCase();

            // Fetching intraday data for the cryptocurrency
            const response = await fetch('../backEnd/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=getCryptoData&symbol=${encodeURIComponent(symbol)}`,
            });
            console.log("Start2"); 
            console.log(response);

            if (!response.ok) {
                throw new Error('Failed to fetch stock data');
            }

            console.log(response);
            const data = await response.json();

            console.log(data);
            console.log("finished");

            const currentPrice = 99999;

            return Crypto.parseCryptoData(data, currentPrice);

        } catch (error) {
            console.error("Error fetching cryptocurrency data:", error);
            return null;
        }
    }

    // Method to parse raw data into a Crypto object
    static parseCryptoData(rawData, currentPrice) {
        const metaData = rawData["Meta Data"];
        const timeSeriesKey = Object.keys(rawData).find(key => key.startsWith("Time Series"));
        const timeSeries = rawData[timeSeriesKey];

        if (!metaData || !timeSeries) {
            console.error("Invalid data format");
            return null;
        }

        const symbol = metaData["2. Digital Currency Code"];
        const lastRefreshed = metaData["6. Last Refreshed"];

        return new Crypto(symbol, lastRefreshed, timeSeries, currentPrice);
    }
}
