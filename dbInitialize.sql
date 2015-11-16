//creates tables
CREATE TABLE transaction
(
  //all transactions are sales
  transactionID INT PRIMARY KEY,
  timestamp TIMESTAMP,
  subTotal FLOAT, //before discount, tax, gratuity
  totalPrice FLOAT, //subtotal+tax+gratuity-discount
  totalTax FLOAT,
  gratuity FLOAT,
  receiptNumber VARCHAR,
  amountPaid FLOAT,
  changeDue FLOAT,
  registerNumber SMALLINT,
  cashier INT REFERENCES employee(employeeID),
  customer INT REFERENCES customer(customerID),
  totalDiscount FLOAT,
) engine=INNODB ;

CREATE TABLE customer //previously 'customer_type'
(
  //aggregate of payment_method, card_type, customer_type...et c
  customerID INT PRIMARY KEY,
  paymentMethod VARCHAR , //credit, debit, cash, check
  customerType VARCHAR, //walkin, takeout, drivethrough, delivery
  cardName VARCHAR, //'N/A' for cardless, 'John Jones'
  cardType VARCHAR, //'N/A' for cardless, 'Visa' 
  bankAuthorization VARCHAR, //needed? also, customer is only bag of money so this goes here
  lastFourCardDigits SMALLINT //needed? nah
) engine=INNODB ;

CREATE TABLE employee
(
  employeeName VARCHAR PRIMARY KEY,
  employeeID INT PRIMARY KEY,
) engine=INNODB ;

/*CREATE TABLE payment_method
(
  //needed?
) engine=INNODB ;

CREATE TABLE card_type
(
  //needed?
) engine=INNODB ;

CREATE TABLE operation_type
(
  type VARCHAR, //there is only 'sale' in the csv...other potential types???
) engine=INNODB ;*/

CREATE TABLE menu_item
(
  menuItemID INT PRIMARY KEY,
  transactionID REFERENCES transaction(transactionID),
  consumerPrice FLOAT,
  costToProduce FLOAT,
  description VARCHAR,
  brand VARCHAR, //needed?
  supplier VARCHAR, //needed?
  category VARCHAR, //drink, food
  drinkSubtype, //alcohol-whiskey, nonalcohol-soda [alcohol/non]-[alsoholType/drinkType]
  itemTax FLOAT,
  discount FLOAT,
  upcBarcode INT //needed? for cost analysis maaaybe
) engine=INNODB ;

/*CREATE TABLE brand
(
  //needed? no
) engine=INNODB ;

CREATE TABLE menu_item_category
(
  //needed? no
) engine=INNODB ;*/

CREATE TABLE menu_item_in_transaction
(
  //if cost needs to be taken into account, include transaction type : buyFromSupplier....
  //many-to-many between menu_item and transaction tables
  transactionID INT REFERENCES transaction(transactionID),
  menuItemID INT REFERENCES menu_item(menuItemID),
  PRIMARY KEY(transactionID, menuItemID),
  quantity SMALLINT //food by item (easy), drink by ounce (convert info from user input into oz)
) engine=INNODB ;
