CREATE DATABASE market;

USE market;

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_type_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL
);

CREATE TABLE product_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE tax_rates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_type_id INT NOT NULL,
    tax_rate DECIMAL(5, 2) NOT NULL,
    FOREIGN KEY (product_type_id) REFERENCES product_types(id)
);

CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_date DATETIME NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL
);

CREATE TABLE sale_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    item_amount DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);


CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password, active) VALUES ('admin', '12345678', 1);
INSERT INTO product_types (id, name) VALUES (1, 'Higiene');
INSERT INTO product_types (id, name) VALUES (2, 'Decoração');
INSERT INTO product_types (id, name) VALUES (3, 'Bebidas');

INSERT INTO tax_rates (id, product_type_id, tax_rate) VALUES (1, 1, 2.5);
INSERT INTO tax_rates (id, product_type_id, tax_rate) VALUES (2, 2, 4.5);
INSERT INTO tax_rates (id, product_type_id, tax_rate) VALUES (3, 3, 4);

INSERT INTO products (id, product_type_id, name, price) VALUES (1, 1, 'Desodorante', 11.99 );
INSERT INTO products (id, product_type_id, name, price) VALUES (2, 1, 'Pasta de Dente', 9.99 );
INSERT INTO products (id, product_type_id, name, price) VALUES (3, 2, 'Luminária', 70.00 );
INSERT INTO products (id, product_type_id, name, price) VALUES (4, 3, 'Refrigerante', 12 );
INSERT INTO products (id, product_type_id, name, price) VALUES (5, 3, 'Água sem gás', 3 );
INSERT INTO products (id, product_type_id, name, price) VALUES (6, 3, 'Água com gás', 3 );
INSERT INTO products (id, product_type_id, name, price) VALUES (7, 3, 'Suco', 5 );