# csc302fa24-kadel-project
Final project of my csc302 Web Programming 2

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
             └── (PHP files will be developed here)
```

## Features
- [ ] Having a Dashboard, Search, Friend, Stockviewer and Settings Site (90% Done)
- [ ] Connect to 3rd party api (Currently in the Work / 70% done)
- [ ] Display of market information (In work / 10% done)
- [ ] Displaying favorite asset holdings
- [ ] Search for stock (Currently in the Work / 50% done)
- [ ] Analysis on a chart
- [ ] Get more information and news
- [ ] Connect with friends and chat

**Completion Percentage**: 25%

## Live Version

https://digdug.cs.endicott.edu/~ckadel/csc302fa24-kadel-project-main/src/frontEnd/index.html

## API Actions
- **GET /stockInfo**: Retrieves detailed information about a specific stock.
  - **Request Parameters**: `symbol` (string, required)
  - **Response**: `symbol`, `name`, `price`, `dayHigh`, `dayLow`, `volume`, `marketCap`

- **GET /marketInfo**: Fetches current market information for various assets.
  - **Request Parameters**: `assets` (array of strings, optional)
  - **Response**: `market` (array of asset objects with `symbol`, `price`, `change`, `dayHigh`, `dayLow`)

- **POST /favoriteStock**: Adds a stock to the user's favorites.
  - **Request Parameters**: `userId` (string, required), `symbol` (string, required)
  - **Response**: `status`, `message`

- **POST /createUser**: Registers a new user in the system.
  - **Request Parameters**: `username` (string, required), `password` (string, required), `email` (string, required)
  - **Response**: `status`, `userId`, `message`

- **POST /loginUser**: Authenticates a user and starts a session.
  - **Request Parameters**: `username` (string, required), `password` (string, required)
  - **Response**: `status`, `token`, `message`

- **POST /logoutUser**: Ends the session for the current user.
  - **Request Parameters**: `userId` (string, required), `token` (string, required)
  - **Response**: `status`, `message`

## Data Model Description
The application utilizes four main tables:

1. **Users**
   - `id`: Unique identifier for each user
   - `name`: User's name
   - `password`: Hashed user password

2. **Stocks**
   - `id`: Unique identifier for each stock
   - `name`: Name of the stock
   - `price`: Current price of the stock
   - `information`: Additional information about the stock

3. **StockFavorites**
   - `userId`: Identifier linking the favorite to a user (foreign key referencing `Users.id`)
   - `stockId`: Identifier linking the favorite to a stock (foreign key referencing `Stocks.id`)

4. **StockHistory**
   - `stockId`: Identifier for the stock (foreign key referencing `Stocks.id`)
   - `timestamp`: Date/time when the price was recorded
   - `price`: Price of the stock at the given `timestamp`

## Error Handling / Issues

- Getting connection for 3rd Party API is causing some problems.
  Also don't know how to correctly set it up. Will need some trial
  and error.



---

For more information, please refer to the documentation within the repository.
