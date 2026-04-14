# 📚 OldBook Exchange

A full-stack web application to buy and sell old books easily, built using PHP and MySQL.

---

## 🚀 Features

* User Authentication (Login & Registration)
* Sell old books with image upload
* Search books by title/keywords
* Cart system
* Wishlist functionality
* Basic chat system
* User profile management
* Order management
* Admin dashboard

---

## 🛠 Tech Stack

* Frontend: HTML, CSS, Bootstrap
* Backend: PHP (MySQLi with prepared statements)
* Database: MySQL
* Server: Apache (LAMP / Docker)

---

## 📂 Project Structure

oldbook/
├── assets/
├── includes/
├── pages/
├── uploads/
├── index.php
└── .htaccess

---

## ⚙️ Setup Instructions

1. Clone Repository
   git clone https://github.com/maheshkrcs-dev/oldbook-exchange.git

2. Move Project
   sudo mv oldbook-exchange /var/www/html/oldbook

3. Setup Database

* Open phpMyAdmin
* Create database: oldbook_exchange
* Import SQL file

4. Configure Database
   Edit includes/db.php

$host = "localhost";
$user = "root";
$pass = "";
$db = "oldbook_exchange";

5. Run Project
   http://localhost/oldbook/

---

## 🔐 Security Features

* Password hashing
* Prepared statements
* Input validation
* XSS protection
* Secure file upload

---


## 👨‍💻 Author

Mahesh Kumar
[maheshkr.cs@gmail.com](mailto:maheshkr.cs@gmail.com)
