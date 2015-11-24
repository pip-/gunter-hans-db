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
  department_id   INT,
  department_name VARCHAR(255),

  PRIMARY KEY (department_id)
);

CREATE TABLE category (
  category_id   INT,
  category_name VARCHAR(255),

  PRIMARY KEY (category_id)
);

CREATE TABLE food (
  food_id       INT,
  category_id   INT,
  department_id INT,
  food_name     VARCHAR(255),
  price         DECIMAL(10, 2),
  discount      DECIMAL(10, 2),

  PRIMARY KEY (food_id),
  FOREIGN KEY (department_id) REFERENCES department (department_id),
  FOREIGN KEY (category_id) REFERENCES category (category_id)
);

CREATE TABLE employee (
  employee_id   INT,
  employee_name VARCHAR(255),

  PRIMARY KEY (employee_id)
);

CREATE TABLE transaction (
  transaction_id    CHAR(9),
  time              DATETIME,
  dayofWeek         VARCHAR(8),
  employee_id       INT,
  -- customer_type_id INT,
  operation_type_id INT,
  payment_id        INT,
  tips              DECIMAL(10, 2),
  tendered_amount   DECIMAL(10, 2),
  returns           DECIMAL(10, 2),

  PRIMARY KEY (transaction_id),
  FOREIGN KEY (employee_id) REFERENCES employee (employee_id),
  -- FOREIGN KEY (customer_type_id) REFERENCES customer_type(customer_type_id),
  FOREIGN KEY (operation_type_id) REFERENCES operation_type (operation_type_id),
  FOREIGN KEY (payment_id) REFERENCES payment_method (payment_id)
);

CREATE TABLE transaction_detail (
  transaction_id CHAR(9),
  food_id        INT,
  quantity       INT,

  PRIMARY KEY (transaction_id, food_id),
  FOREIGN KEY (transaction_id) REFERENCES transaction (transaction_id),
  FOREIGN KEY (food_id) REFERENCES food (food_id)
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

INSERT INTO food VALUES (1, 1, 1, 'Hirter Morchl- pint', 5.5, 0);
INSERT INTO food VALUES (2, 2, 2, 'Jim Bean- Single (1.5 oz)', 3.5, 0);
INSERT INTO food VALUES (3, 1, 1, 'Shiphead-Pint', 5, 0);
INSERT INTO food VALUES (4, 1, 1, 'Schnicklefritz- pint', 6.5, 0);
INSERT INTO food VALUES (5, 3, 2, 'Balvenie 14- Single (2oz)', 14, 7);

INSERT INTO employee VALUES (1, 'Rachel Dicke');
INSERT INTO employee VALUES (2, 'Kim Burton');

INSERT INTO transaction VALUES ('428594725', '2015-08-11 0:17:0', 1, 1, 1, 4.4, 28.26, 0);
INSERT INTO transaction VALUES ('428596273', '2015-08-11 0:27:0', 1, 1, 1, 1.26, 8.85, 0);
INSERT INTO transaction VALUES ('428596799', '2015-08-11 0:31:0', 1, 1, 3, 0, 8, 2.58);
INSERT INTO transaction VALUES ('428600199', '2015-08-11 0:54:0', 2, 1, 1, 1.17, 8.22, 0);
INSERT INTO transaction VALUES ('428604686', '2015-08-11 1:31:0', 2, 1, 2, 0, 7.59, 0);

INSERT INTO transaction_detail VALUES ('428594725', 1, 4);
INSERT INTO transaction_detail VALUES ('428596273', 2, 2);
INSERT INTO transaction_detail VALUES ('428596799', 3, 1);
INSERT INTO transaction_detail VALUES ('428600199', 4, 1);
INSERT INTO transaction_detail VALUES ('428604686', 5, 1);

INSERT INTO user VALUES ('admin', 0, '1400851839', '$2y$10$T4UNJEblHNI/r5kO7VEbUOX0.GENdlbXJxuGczj0853yCd7LUHqyK');
