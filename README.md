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
             └── api.php
             └── db.php
             └── stockInfo.php
             └── data.db 
```

## Features
- [Done] Having a Dashboard, Search, Friend, Stockviewer and Settings Site 
- [Done] Connect to 3rd party api for Stock Information
- [ ] Display of market information for each Stock (In work / 65% done / Displays some information and graph cant be modified)
- [ ] Displaying favorite asset holdings (50% done got the DB to work and created functions. Change the way it is displayed in index.html instead of a table i can fromat it          nicer)
- [Done] Search for stock and find Information
- [Done] Analysis on a chart ( Charts is beeing displayed but cant add a lot fo analysis options
- [ ] Get more information and news ( Need to ask for more information from the API)
- [ ] Connect with friends and chat (This might fall out as most of the time will be used to get the main features running)

**Completion Percentage**: 75%

## Live Version

https://digdug.cs.endicott.edu/~ckadel/csc302fa24-kadel-project-main/src/frontEnd/index.html

## API Actions
- **POST /stockInfo**: Retrieves detailed information about a specific stock.
  - **Request Parameters**: `symbol` (string, required)
  - **Response**: `symbol`, `name`, `price`, `dayHigh`, `dayLow`, `volume`, `marketCap`

- **POST /marketInfo**: Fetches current market information for various assets.
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
  and error.(Fixed Now)
- Making sure the User can Sing in and Sing up was causing issues. It didnt correctly display it and showed the SignIN when user was already SingedIN (Fixed Now)
- Making sure all Information dispalys correctly on StockView Page. Maybe I need to add some extra API calls to retrive some more information
- I was having trouble with the connection to the DB and uploading things to our Database. Turned out I was doing the wrong requests to the backend (Fixed)

## Testing

- User Authentication: The Sign In and Sign Up workflows were manually tested to ensure users could create accounts, log in, and maintain a session.
- Debugging: Extensive use of console.log statements and echo commands helped verify data flow and catch issues during the development process.
- Database Interactions: Tested database functions, such as adding users and adding favorites, to ensure proper data was being saved and retrieved.

---

For more information, please refer to the documentation within the repository.
