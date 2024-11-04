# -csc302fa24-kadel-project
Final project of my csc302 Web Programming 2

# Stock/Crypto Asset Viewer

## Project Description
This program is a comprehensive Stock/Crypto Asset Viewer that enables users to view and manage their favorite stocks and cryptocurrencies. Users can analyze market trends, read news articles, and connect with friends to discuss investment ideas and strategies. This application aims to provide a user-friendly interface for asset management and social interaction in the financial space.

## File Structure
src/ ├── frontend/ 
         │ 
         ├── dashboard.html 
         │ 
         ├── searchViewer.html 
         │ 
         ├── friendPage.html 
         │ 
         └── stockViewer.html 
     └── backend/ 
         └── (PHP files will be developed here)
         
## Features
- [x] Display of market information
- [x] Displaying favorite asset holdings
- [x] Search for stock
- [x] Analysis on a chart
- [ ] Get more information and news
- [ ] Connect with friends and chat

**Completion Percentage**: 10%

## Live Version
*Live URL will be provided later.*

## API Actions
- **GET /stockInfo**: Retrieves detailed information about a specific stock.
- **GET /marketInfo**: Fetches current market information for various assets.
- **POST /favoriteStock**: Adds a stock to the user's favorites.
- **POST /createUser**: Registers a new user in the system.
- **POST /loginUser**: Authenticates a user and starts a session.
- **POST /logoutUser**: Ends the session for the current user.

## Data Model Description
The application utilizes two databases:

1. **PeopleDB**
   - `id`: Unique identifier for each user
   - `name`: User's name
   - `password`: Hashed user password
   - `friendsList`: List of friends connected to the user
   - `stockId`: Identifier for favorite stocks

2. **StockDB**
   - `id`: Unique identifier for each stock
   - `userID`: Identifier linking the stock to the user
   - `name`: Name of the stock
   - `price`: Current price of the stock
   - `priceHistory`: Historical price data
   - `information`: Additional information about the stock

---

For more information, please refer to the documentation within the repository.
