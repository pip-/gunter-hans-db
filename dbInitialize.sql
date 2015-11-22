DROP TABLE IF EXISTS transaction;
DROP TABLE IF EXISTS customer;
DROP TABLE IF EXISTS employee;
DROP TABLE IF EXISTS menu_item;
DROP TABLE IF EXISTS menu_item_in_transaction;
DROP TABLE IF EXISTS user;

-- creates tables
CREATE TABLE transaction
(
  -- all transactions are sales, unless we need to implement transactins with suppliers
  transactionID INT PRIMARY KEY,
  transactionTimestamp TIMESTAMP,
  subTotal FLOAT, -- before discount, tax, gratuity
  totalPrice FLOAT, -- subtotal+tax+gratuity-discount
  totalTax FLOAT, -- sum of all individual item taxes, if applicable, or entered by user
  gratuity FLOAT,
  receiptNumber VARCHAR(20),
  amountPaid FLOAT,
  changeDue FLOAT, -- needed?
  registerNumber SMALLINT,
  cashier INT REFERENCES employee(employeeID),
  customer INT REFERENCES customer(customerID),
  totalDiscount FLOAT -- sum of individual item discounts and special deals
) engine=INNODB ;

CREATE TABLE customer -- previously 'customer_type'
(
  -- aggregate of payment_method, card_type, customer_type...et c
  customerID INT PRIMARY KEY,
  paymentMethod VARCHAR(10) , -- credit, debit, cash, check
  customerType VARCHAR(10), -- walkin, takeout, drivethrough, delivery
  cardName VARCHAR(15), -- 'N/A' for cardless, 'John Jones'
  cardType VARCHAR(15), -- 'N/A' for cardless, 'Visa' 
  bankAuthorization VARCHAR(15), -- needed? also, customer is only bag of money so this goes here
  lastFourCardDigits SMALLINT -- needed? nah
) engine=INNODB ;

CREATE TABLE employee
(
  employeeName VARCHAR(25),
  employeeID INT PRIMARY KEY
) engine=INNODB ;

CREATE TABLE menu_item
(
  menuItemID INT PRIMARY KEY,
  transactionID INT REFERENCES transaction(transactionID),
  consumerPrice FLOAT,
  costToProduce FLOAT, 
  description VARCHAR(50),
  brand VARCHAR(20), -- needed?
  supplier VARCHAR(20), -- needed?
  category VARCHAR(20), -- drink, food
  drinkSubtype VARCHAR(20), -- alcohol-whiskey, nonalcohol-soda [alcohol/non]-[alsoholType/drinkType]
  itemTax FLOAT,
  discount FLOAT,
  discountDescription VARCHAR(50),
  upcBarcode INT -- needed? for cost analysis maaaybe
) engine=INNODB ;

CREATE TABLE user
(
  username        VARCHAR(20) PRIMARY KEY,
  salt            VARCHAR(20),
  hashed_password VARCHAR(256),
  user_type       ENUM('admin', 'reg')
)
  ENGINE = INNODB;

CREATE TABLE menu_item_in_transaction
(
  -- if cost needs to be taken into account, include transaction type : buyFromSupplier....
  -- many-to-many between menu_item and transaction tables
  transactionID INT REFERENCES transaction(transactionID),
  menuItemID INT REFERENCES menu_item(menuItemID),
  PRIMARY KEY(transactionID, menuItemID),
  quantity SMALLINT -- food by item (easy), drink by ounce (convert info from user input into oz)
) engine=INNODB ;

/* CREATE TABLE brand
(
  -- needed? no
) engine=INNODB ;

CREATE TABLE menu_item_category
(
  -- needed? no
) engine=INNODB ;

CREATE TABLE payment_method
(
  -- needed?
) engine=INNODB ;

CREATE TABLE card_type
(
  -- needed?
) engine=INNODB ;

CREATE TABLE operation_type
(
  type VARCHAR, -- there is only 'sale' in the csv...other potential types???
) engine=INNODB ; */
