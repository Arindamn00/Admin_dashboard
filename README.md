# Admin_dashboard
# 🛡️ Admin Dashboard with Login Authentication

A secure, responsive **Admin Dashboard** built using **PHP**, **HTML**, and **CSS**, designed to help administrators monitor user activity, growth, and registrations in a clean and structured layout.

---

## ✨ Features

### 🔐 Login Page
- Admin-only access
- Simple PHP-based authentication system

### 📊 Dashboard Page
Includes key performance cards and visual user data:

- **Header Section**
  - Title: "Admin Dashboard"
  - Buttons: `Refresh Data` and `Logout`

- **Stat Cards**
  - **Total Users:** Total number of registered users
  - **Active Users:** Users active in the last 30 days
  - **New Users:** Users joined this month
  - **Growth Rate:** Monthly percentage growth in users

- **Analytics Cards**
  - **Users by Role:** Visual breakdown of Admins, Moderators, and Users
  - **User Registration Activity:** Graph showing recent registration trends

- **Recent Users Table**
  - Displays: `ID`, `Username`, `Email`, `Role`, `Last Login`, and `Joined Date`

---

## 🛠️ Tech Stack

| Layer       | Technology        |
|-------------|-------------------|
| Frontend    | HTML, CSS         |
| Backend     | PHP               |
| Database    | MySQL (via phpmyadmin)  |
| Server      | XAMPP / Localhost |

---

## 🚀 How to Run Locally

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Arindamn00/Admin_dashboard.git
   cd Admin_dashboard
2. **Setup Environment**
 - Install XAMPP if not already installed
 -Copy this project folder into htdocs

3. **Create the Database**

  -Open http://localhost/phpmyadmin

  -Create a database (e.g., admin_db)

  -Create a table for users (sample schema below):

```
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(100),
    role enum('admin','user',moderator')
    created_at TIMESTAMP,
    last_login TIMESTAMP
);
```
4. **Configure PHP**

  -Update database credentials in db.php or inside index.php as per your setup.

5. **Start the App**

  -Go to: http://localhost/Admin/admin_v1.php

## 📌 Folder Structure
```
Admin-Dashboard/
├── admin_v1.php         #contains the basic logic for login,loagout and dashboard page
├── /img/                # Images/icons
└── README.md
```

## 📈 Future Enhancements
  - Role-based login (Admin, Moderator, Viewer)
  - Add/Edit/Delete users from dashboard
  - Connect with live database for analytics
  - Responsive mobile-first design
  - Use Chart.js or Highcharts for data visualizations
