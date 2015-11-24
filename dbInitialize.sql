DROP TABLE IF EXISTS transaction_detail;
DROP TABLE IF EXISTS transaction;
DROP TABLE IF EXISTS employee;
DROP TABLE IF EXISTS food;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS department;
DROP TABLE IF EXISTS operation_type;
DROP TABLE IF EXISTS payment_method;
DROP TABLE IF EXISTS card_type;
DROP TABLE IF EXISTS user;


CREATE TABLE card_type (
  type_id   INT,
  type_name VARCHAR(255),

  PRIMARY KEY (type_id)
);

CREATE TABLE payment_method (
  payment_id   INT,
  type_id      INT,
  payment_name VARCHAR(255),

  PRIMARY KEY (payment_id),
  FOREIGN KEY (type_id) REFERENCES card_type (type_id)
);

-- CREATE TABLE customer_type (
--     customer_type_id INT,
--     customer_type_name VARCHAR(255),

--     PRIMARY KEY (customer_type_id)
-- );

CREATE TABLE operation_type (
  operation_type_id   INT,
  operation_type_name VARCHAR(255),

  PRIMARY KEY (operation_type_id)
);

CREATE TABLE department (
  department_id   INT AUTO_INCREMENT,
  department_name VARCHAR(255) UNIQUE,

  PRIMARY KEY (department_id)
);

CREATE TABLE category (
  category_id   INT AUTO_INCREMENT,
  category_name VARCHAR(255) UNIQUE,

  PRIMARY KEY (category_id)
);

CREATE TABLE food (
  food_id         INT AUTO_INCREMENT,
  category_name   VARCHAR(255),
  department_name VARCHAR(255),
  food_name       VARCHAR(255) UNIQUE,
  supplier        VARCHAR(20),
  price         DECIMAL(10, 2),
  discount      DECIMAL(10, 2),

  PRIMARY KEY (food_ID),
  FOREIGN KEY (department_name) REFERENCES department (department_name),
  FOREIGN KEY (category_name) REFERENCES category (category_name)
);

CREATE TABLE employee (
  employee_id   INT AUTO_INCREMENT,
  employee_name VARCHAR(255) UNIQUE,

  PRIMARY KEY (employee_id)
);

CREATE TABLE transaction (
  transaction_id    CHAR(9),
  transactionDatetime DATETIME,
  employee_name VARCHAR(255),
  -- customer_type_id INT,
  operation_type_id INT,
  payment_id        INT,
  subtotal      DECIMAL(10, 2),
  tips              DECIMAL(10, 2),
  tax           DECIMAL(10, 2),
  tendered_amount   DECIMAL(10, 2),
  returns           DECIMAL(10, 2),

  PRIMARY KEY (transaction_id),
  FOREIGN KEY (employee_name) REFERENCES employee (employee_name),
  -- FOREIGN KEY (customer_type_id) REFERENCES customer_type(customer_type_id),
  FOREIGN KEY (operation_type_id) REFERENCES operation_type (operation_type_id),
  FOREIGN KEY (payment_id) REFERENCES payment_method (payment_id)
);

CREATE TABLE transaction_detail (
  transaction_id CHAR(9),
  food_name VARCHAR(255),
  quantity       INT,

  PRIMARY KEY (transaction_id, food_name),
  FOREIGN KEY (transaction_id) REFERENCES transaction (transaction_id),
  FOREIGN KEY (food_name) REFERENCES food (food_name)
);

CREATE TABLE user (
  username        VARCHAR(20),
  type            INT,
  salt            VARCHAR(20),
  hashed_password VARCHAR(256),

  PRIMARY KEY (username)
);

INSERT INTO card_type VALUES (1, 'VISA');
INSERT INTO card_type VALUES (2, 'MASTERCARD');

INSERT INTO payment_method VALUES (1, 1, 'credit');
INSERT INTO payment_method VALUES (2, 2, 'credit');
INSERT INTO payment_method VALUES (3, NULL, 'cash');

INSERT INTO operation_type VALUES (1, 'sale');

INSERT INTO department VALUES (1, 'Beer');
INSERT INTO department VALUES (2, 'Liquor');

INSERT INTO category VALUES (1, 'Pint');
INSERT INTO category VALUES (2, 'Whiskey');
INSERT INTO category VALUES (3, 'Scotch');


INSERT INTO employee VALUES (1, 'Rachel Dicke');
INSERT INTO employee VALUES (2, 'Kim Burton');

INSERT INTO user VALUES ('admin', 0, '1400851839', '$2y$10$T4UNJEblHNI/r5kO7VEbUOX0.GENdlbXJxuGczj0853yCd7LUHqyK');
