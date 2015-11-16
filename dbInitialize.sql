//creates tables
CREATE TABLE transaction
(
  //all transactions are sales
  transactionID INT PRIMARY KEY,
  timestamp TIMESTAMP,
  totalPrice FLOAT,
  totalTax FLOAT,
  gratuity FLOAT,
  receiptNumber VARCHAR,
  amountPaid FLOAT,
  changeDue FLOAT,
  registerNumber SMALLINT
) engine=INNODB ;

CREATE TABLE customer
(
  //needed?
  paymentMethod VARCHAR , //credit, debit, cash, check
  type VARCHAR, //walkin, takeout, drivethrough, delivery
  cardName VARCHAR //'N/A' for cardless
) engine=INNODB ;

CREATE TABLE employee
(
  employeeName VARCHAR PRIMARY KEY,
  
) engine=INNODB ;

CREATE TABLE payment_method
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
) engine=INNODB ;

CREATE TABLE menu_item
(
  menuItemID INT PRIMARY KEY,
  consumerPrice FLOAT,
  costToProduce FLOAT,
  description VARCHAR,
  itemTax FLOAT,
) engine=INNODB ;

CREATE TABLE brand
(
  //needed?
) engine=INNODB ;

CREATE TABLE menu_item_category
(
  //needed?
) engine=INNODB ;

CREATE TABLE menu_item_in_transaction
(
  //many-to-many between menu_item and transaction tables
  transactionID INT REFERENCES transaction(transactionID),
  menuItemID INT REFERENCES menu_item(menuItemID),
  
) engine=INNODB ;
