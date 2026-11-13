# HopeGiving – Online Donation Platform

**Project Name:** HopeGiving  
**Project Type:** Online Donation Platform  
**Student:** IZERE AIMEE MARIE  
**Registration Number:** 224012041  
**Repository:** `BIT-Y3-G1-224012041-OnlineDonationPlatform`

---

## 1. Project Overview

HopeGiving is a web-based donation platform designed to connect donors with Non-Governmental Organizations (NGOs) and their fundraising campaigns.

The platform allows donors to discover campaigns and make donations, NGOs to manage fundraising campaigns, and administrators to oversee users and approve NGO registrations.

The main goal is to make the donation process more accessible, organized, transparent, and convenient.

## 2. Main Objectives

- Provide a centralized platform for charitable donations.
- Connect donors with registered NGOs.
- Allow NGOs to create and manage fundraising campaigns.
- Enable administrators to manage users and approve NGOs.
- Track donations and campaign fundraising progress.
- Provide a responsive and user-friendly interface.

## 3. User Roles

### Donor
- Register and log in.
- View available fundraising campaigns.
- Make donations to campaigns.
- View donation history.
- Monitor personal donation statistics.

### NGO
- Register an NGO account.
- Wait for administrator approval.
- Access the NGO dashboard after approval.
- Create and manage fundraising campaigns.
- Monitor campaign progress and donations.

### Administrator
- Access the administration dashboard.
- View platform statistics.
- Review NGO registration requests.
- Approve or reject NGO applications.
- Manage user accounts.
- Suspend or reactivate accounts.

## 4. Main Features

- User registration and authentication.
- Role-based access control.
- Donor dashboard.
- NGO dashboard.
- Administrator dashboard.
- NGO approval system.
- Campaign creation and management.
- Donation processing and recording.
- Donation history.
- Campaign fundraising progress.
- Responsive user interface.
- Form validation and security protections.

## 5. Technologies Used

| Technology | Purpose |
|---|---|
| HTML5 | Web page structure |
| CSS3 | Styling and responsive design |
| JavaScript | Client-side interactions |
| PHP | Backend development |
| MySQL | Database management |
| PDO | Secure database operations |
| XAMPP | Local development environment |

## 6. System Requirements

- Windows, Linux, or macOS.
- XAMPP or another PHP/MySQL development environment.
- PHP with PDO MySQL support.
- MySQL or MariaDB.
- Modern web browser.
- Code editor such as Visual Studio Code.

## 7. Installation and Setup

### Step 1: Install XAMPP

Download and install XAMPP.

Start the following services from the XAMPP Control Panel:

- Apache
- MySQL

### Step 2: Copy the Project

Place the project folder inside the XAMPP `htdocs` directory.

Expected location on Windows:

`C:\xampp\htdocs\OnlineDonationPlatform`

### Step 3: Create the Database

1. Open your browser.
2. Visit `http://localhost/phpmyadmin`.
3. Create a database named:

`online_donation_platform`

4. Import the SQL files from the project's `database` folder in the correct order:
   - `schema.sql`
   - `seed.sql`
   - Any required upgrade or migration scripts for an existing database.

### Step 4: Configure Database Connection

Open:

`config/database.php`

Configure the database connection settings to match your local environment.

Example XAMPP configuration:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'online_donation_platform');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/OnlineDonationPlatform');
```

### Step 5: Run the Application

Open your browser and visit:

`http://localhost/OnlineDonationPlatform`

The application should display the HopeGiving landing page.

## 8. Database Structure

The platform uses a relational MySQL database.

Main database tables:

### users
Stores user account information, login credentials, roles, and account status.

### ngos
Stores NGO information, registration details, and approval status.

### campaigns
Stores fundraising campaign information, targets, amounts raised, and campaign end dates.

### donations
Stores donation records, donor information, donation amounts, and donation dates.

The database uses primary keys, foreign keys, and relational constraints to maintain data integrity.

## 9. Security Features

The application includes security measures such as:

- Password hashing using PHP password functions.
- Password verification during login.
- Prepared statements for database operations.
- Output escaping using `htmlspecialchars()`.
- CSRF token validation for protected form submissions.
- PHP session-based authentication.
- Role-based authorization.
- Server-side input validation.
- Account status checks.
- NGO approval checks before accessing protected NGO functionality.

## 10. Testing

The following test cases can be used to verify the platform.

| No. | Test Case | Expected Result |
|---|---|---|
| 1 | Register a donor account | Donor account is created |
| 2 | Register an NGO account | NGO application is submitted |
| 3 | Log in with valid credentials | User accesses the correct dashboard |
| 4 | Log in with incorrect credentials | Login is rejected |
| 5 | Access a dashboard without logging in | Access is denied or redirected |
| 6 | Approve an NGO as administrator | NGO becomes approved |
| 7 | Reject an NGO application | NGO application is rejected |
| 8 | Create a fundraising campaign | Campaign is saved |
| 9 | Make a valid donation | Donation is recorded |
| 10 | View donation history | Recorded donations are displayed |
| 11 | Suspend a user account | Suspended account cannot access the platform |
| 12 | Submit an invalid form | Validation prevents invalid submission |

## 11. Project Folder Structure

```text
OnlineDonationPlatform/
│
├── app/
│   ├── controllers/
│   ├── core/
│   ├── helpers/
│   ├── models/
│   └── views/
│
├── config/
│   └── database.php
│
├── database/
│   ├── schema.sql
│   ├── seed.sql
│   └── upgrade_approval_system.sql
│
├── public/
│   ├── api/
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
│
├── scripts/
│   └── create_admin.php
│
├── tests/
│
├── index.php
├── .htaccess
└── README.md
```

## 12. Future Improvements

Possible future enhancements include:

- Integration with secure online payment gateways.
- Email notifications for donations and campaign updates.
- Automated donation receipts.
- Advanced campaign reporting and analytics.
- Improved donor and NGO communication.
- Additional reporting and export functionality.

## 13. Conclusion

HopeGiving provides a centralized platform for connecting donors with NGOs and fundraising campaigns. Through role-based dashboards, campaign management, donation tracking, and administrator oversight, the platform aims to make charitable giving more accessible and organized.

**Developed by:**  
IZERE AIMEE MARIE  
**Registration Number:** 224012041
