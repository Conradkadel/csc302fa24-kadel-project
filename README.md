# csc302fa24-kadel-project
Final project of my CSC302 Web Programming 2

# Stock/Crypto Asset Viewer

## Project Description
This program is a comprehensive Stock/Crypto Asset Viewer that enables users to view and manage their favorite stocks and cryptocurrencies. Users can analyze market trends, read news articles, and connect with friends to discuss investment ideas and strategies. This application aims to provide a user-friendly interface for asset management and social interaction in the financial space.

## File Structure
```
         src/  
         ├── frontend/   
         │   ├── dashboard.html     
         │   ├── searchViewer.html     
         │   ├── friendPage.html     
         │   └── stockViewer.html     
         └── backend/       
             ├── api.php
             ├── db.php
             ├── stockInfo.php
             └── data.db
```

## Features
- [x] Dashboard, Search, Friend, Stock Viewer, and Settings pages implemented
- [x] Integration with 3rd-party API for stock and cryptocurrency data
- [x] Display detailed market information for each stock
- [x] Display favorite assets with improved frontend formatting
- [x] Search functionality for stocks with detailed results
- [x] Analysis chart display with basic charts
- [x] Fetch additional news and financial information from APIs
- [x] Friend connection and chat functionality

**Completion Percentage**: 100%

## Live Version
         https://digdug.cs.endicott.edu/~ckadel/csc302fa24-kadel-project-main/src/frontEnd/index.html

## API Actions

### Stock Data
- **POST /stockInfo**
  - Retrieves detailed information about a specific stock.
  - **Request Parameters**:
    - `symbol` (required): The stock symbol (e.g., AAPL, TSLA).
    - `interval` (optional): Data interval (e.g., "5min", "1day").
  - **Response**:
    - Includes `symbol`, `name`, `price`, `dayHigh`, `dayLow`, `volume`, `marketCap`.

- **POST /marketInfo**
  - Fetches current market information for various assets.
  - **Request Parameters**:
    - `assets` (optional): Array of asset symbols.
  - **Response**:
    - Array of market details including `symbol`, `price`, `change`, `dayHigh`, `dayLow`.

### User Authentication
- **POST /createUser**
  - Registers a new user.
  - **Request Parameters**:
    - `username` (required)
    - `password` (required)
    - `email` (required)
  - **Response**:
    - `status`, `userId`, `message`.

- **POST /loginUser**
  - Authenticates a user and starts a session.
  - **Request Parameters**:
    - `username` (required)
    - `password` (required)
  - **Response**:
    - `status`, `token`, `message`.

- **POST /logoutUser**
  - Logs out the current user.
  - **Request Parameters**:
    - `userId` (required)
    - `token` (required)
  - **Response**:
    - `status`, `message`.

### Favorites
- **POST /favoriteStock**
  - Adds a stock to a user's favorites.
  - **Request Parameters**:
    - `userId` (required)
    - `symbol` (required)
  - **Response**:
    - `status`, `message`.

## Data Model Description
The application utilizes the following tables:

1. **Users**
   - `id`: Unique identifier
   - `username`: User's login name
   - `password`: Hashed password

2. **Stocks**
   - `id`: Unique identifier
   - `name`: Stock name
   - `price`: Current price
   - `information`: Additional details about the stock

3. **Favorites**
   - `userId`: User reference (foreign key to `Users.id`)
   - `stockId`: Stock reference (foreign key to `Stocks.id`)

4. **StockHistory**
   - `stockId`: Reference to a stock
   - `timestamp`: Recorded date and time
   - `price`: Price at the recorded time

## Error Handling / Issues

### Challenges Encountered
1. **Third-party API integration**:
   - Initially struggled with connecting to the Alpha Vantage API due to incorrect endpoint parameters.
   - Solution: Refined API request structure and validated keys (Resolved).

2. **User Authentication Bugs**:
   - Login and logout issues occurred when session variables weren't properly set.
   - Solution: Adjusted session logic to ensure correct authentication state (Resolved).

3. **Database Issues**:
   - Faced issues saving data due to malformed SQL queries and mismatched table schemas.

5. **Graph Rendering Errors**:
   - Debugging JavaScript chart rendering was challenging due to asynchronous API calls and improper handling of data formats.
   - Solution: Added safeguards for missing data and asynchronous loading (Resolved).

## Testing

1. **User Authentication**:
   - Verified account creation, login, and logout processes.
   - Tested session persistence and ensured unauthorized actions return appropriate errors.

2. **API Integration**:
   - Manually tested all API endpoints with different parameters to verify correct responses and error handling.



---

For more information, please refer to the documentation within the repository.


