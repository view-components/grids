DROP DATABASE IF EXISTS view_components;
CREATE DATABASE IF NOT EXISTS view_components;
USE view_components;

DROP TABLE IF EXISTS users;
CREATE TABLE IF NOT EXISTS users (
  id int(10) NOT NULL,
  name varchar(255) NOT NULL,
  role varchar(31) NOT NULL,
  birthday date NOT NULL,
  PRIMARY KEY (id)
);

DELETE FROM users;

INSERT INTO users VALUES (1, 'John', 'Admin', '1970-01-16');
INSERT INTO users VALUES (2, 'Max', 'Manager', '1980-11-20');
INSERT INTO users VALUES (3, 'Anna', 'Manager', '1987-03-30');
INSERT INTO users VALUES (4, 'Lisa', 'User', '1989-04-21');