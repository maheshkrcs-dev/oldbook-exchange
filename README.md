# 📚 OldBook Exchange

A full-stack web application to **buy and sell old books easily**, built using PHP and MySQL.  
It provides a simple marketplace where users can list, search, and purchase second-hand books.

---

## 🚀 Features

- 🔐 User Authentication (Login & Registration)
- 📚 Sell old books with image upload
- 🔍 Search books by title/keywords
- 🛒 Cart system
- ❤️ Wishlist functionality
- 💬 Basic chat system
- 👤 User profile management
- 📦 Order management
- 🛠 Admin dashboard

---

## 🛠 Tech Stack

- **Frontend:** HTML, CSS, Bootstrap  
- **Backend:** PHP (MySQLi with prepared statements)  
- **Database:** MySQL  
- **Server:** Apache (LAMP / Docker)

---

## 📂 Project Structure


oldbook/
│── assets/ # CSS, JS, images
│── includes/ # DB connection, functions, auth
│── pages/ # Application pages
│── uploads/ # User uploaded images (ignored in Git)
│── index.php
│── .htaccess


---

## ⚙️ Setup Instructions

### 1️⃣ Clone Repository
```bash
git clone https://github.com/maheshkrcs-dev/oldbook-exchange.git

2️⃣ Move Project
sudo mv oldbook-exchange /var/www/html/oldbook

3️⃣ Setup Database
Open phpMyAdmin
Create database:
oldbook_exchange
Import SQL file

4️⃣ Configure Database

Edit file:

includes/db.php

Update credentials:

$host = "localhost";
$user = "root";
$pass = "";
$db   = "oldbook_exchange";

5️⃣ Run Project
http://localhost/oldbook/
🔐 Security Features
Password hashing (password_hash)
MySQLi prepared statements
Input validation
XSS protection (htmlspecialchars)
Secure file upload handling
⚠️ Important Notes
uploads/ folder is excluded using .gitignore
Set proper permissions:
sudo chown -R www-data:www-data uploads
sudo chmod -R 755 uploads
📸 Screenshots

(Add screenshots here)

👨‍💻 Author

Mahesh Kumar
📧 maheshkr.cs@gmail.com

🔗 https://github.com/maheshkrcs-dev

⭐ Future Improvements
Google Drive image upload integration
Payment gateway integration
Real-time chat system
Notification system
