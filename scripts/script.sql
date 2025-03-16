CREATE DATABASE project_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE project_db;

CREATE TABLE products (
    product_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    product_category VARCHAR(100) NOT NULL,
    product_description VARCHAR(250) NOT NULL,
    product_image VARCHAR(250) NOT NULL,
    product_image2 VARCHAR(250) NULL,
    product_image3 VARCHAR(250) NULL,
    product_image4 VARCHAR(250) NULL,
    product_price DECIMAL(6,2) NOT NULL,
    product_special_offer INT(2) DEFAULT 0,
    product_color VARCHAR(100) NOT NULL
);


CREATE TABLE users (
    user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) UNIQUE NOT NULL,
    user_password VARCHAR(100) NOT NULL
);

CREATE TABLE orders (
    order_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_cost DECIMAL(6,2) NOT NULL,
    order_status VARCHAR(100) NOT NULL,
    user_id INT(11) NOT NULL,
    shipping_city VARCHAR(255) NOT NULL,
    shipping_uf VARCHAR(2) NOT NULL,
    shipping_address VARCHAR(255) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE order_items (
    item_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    qnt INT(11) NOT NULL CHECK (qnt > 0),
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE payments (
    payment_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE admins (
    admin_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    admin_email VARCHAR(255) UNIQUE NOT NULL,
    admin_name VARCHAR(255) NOT NULL,
    admin_password VARCHAR(100) NOT NULL
);

INSERT into admins VALUES(null,"admin", "admin@shop.com.br",  "e10adc3949ba59abbe56e057f20f883e");