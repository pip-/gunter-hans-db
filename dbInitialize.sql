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
  type_id   INT AUTO_INCREMENT,
  type_name VARCHAR(255) UNIQUE,

  PRIMARY KEY (type_id)
);

CREATE TABLE payment_method (
  payment_id   INT AUTO_INCREMENT,
  type_id      INT,
  payment_name VARCHAR(255) UNIQUE,

  PRIMARY KEY (payment_id),
  FOREIGN KEY (type_id) REFERENCES card_type (type_id)
);

-- CREATE TABLE customer_type (
--     customer_type_id INT,
--     customer_type_name VARCHAR(255),

--     PRIMARY KEY (customer_type_id)
-- );

CREATE TABLE operation_type (
  operation_type_id   INT AUTO_INCREMENT,
  operation_type_name VARCHAR(255) UNIQUE,

  PRIMARY KEY (operation_type_id)
);

CREATE TABLE department (
  department_id INT AUTO_INCREMENT,
  department_name VARCHAR(255) UNIQUE,

  PRIMARY KEY (department_id)
);

CREATE TABLE category (
  category_id   INT AUTO_INCREMENT,
  category_name VARCHAR(255),

  PRIMARY KEY (category_id)
);

CREATE TABLE food (
  food_id       INT AUTO_INCREMENT,
  category_id   INT,
  department_id INT,
  food_name     VARCHAR(255) UNIQUE,
  price         DECIMAL(10, 2),
  discount      DECIMAL(10, 2),

  PRIMARY KEY (food_id),
  FOREIGN KEY (department_id) REFERENCES department (department_id),
  FOREIGN KEY (category_id) REFERENCES category (category_id)
);

CREATE TABLE employee (
  employee_id   INT AUTO_INCREMENT,
  employee_name VARCHAR(255) NOT NULL UNIQUE,

  PRIMARY KEY (employee_id)
);

CREATE TABLE transaction (
  transaction_id  CHAR(9),
  time            DATETIME,
  employee_id     INT,
  -- customer_type_id INT,
  operation_type_id INT,
  payment_id      INT,
  tips            DECIMAL(10, 2),
  tendered_amount DECIMAL(10, 2),
  returns         DECIMAL(10, 2),

  PRIMARY KEY (transaction_id),
  FOREIGN KEY (employee_id) REFERENCES employee (employee_id),
  -- FOREIGN KEY (customer_type_id) REFERENCES customer_type(customer_type_id),
  FOREIGN KEY (operation_type_id) REFERENCES operation_type (operation_type_id),
  FOREIGN KEY (payment_id) REFERENCES payment_method (payment_id)
);

CREATE TABLE transaction_detail (
  transaction_id CHAR(9),
  food_id  INT,
  quantity INT,

  PRIMARY KEY (transaction_id, food_id),
  FOREIGN KEY (transaction_id) REFERENCES transaction (transaction_id),
  FOREIGN KEY (food_id) REFERENCES food (food_id)
);

CREATE TABLE user (
  username VARCHAR(20),
  type     INT,
  salt     VARCHAR(20),
  hashed_password VARCHAR(256),

  PRIMARY KEY (username)
);

INSERT INTO operation_type VALUES (1, "sale");