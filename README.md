# Zimnat Life Assurance – Policy Renewal Reminder System (PRS)

A lightweight internal web application built for Zimnat Life Assurance Company Limited to help staff manage insurance policy renewals, track expiry dates, upload supporting documents, and view summary dashboards.

---

## Table of Contents

1. [Setup Instructions](#setup-instructions)
2. [Project Structure](#project-structure)
3. [Role Permissions](#role-permissions)
4. [Assumptions Made](#assumptions-made)
5. [AI Usage Disclosure](#ai-usage-disclosure)

---

## Setup Instructions

### Prerequisites

| Requirement | Version |
|---|---|
| PHP | 8.0 or higher |
| MySQL / MariaDB | 8.0 / 10.5+ |
| Apache | 2.4+ with `mod_rewrite` enabled |
| Composer | Not required (no third-party dependencies) |

### Step 1 – Clone / Extract the Project

Place the project folder inside your web server root:

```
/var/www/html/zimnat_prs/     ← Linux (Apache)
C:\xampp\htdocs\zimnat_prs\   ← Windows (XAMPP)
```

### Step 2 – Create the Database

Open MySQL and run:

```sql
SOURCE /path/to/zimnat_prs/database.sql;
```

Or import `database.sql` via **phpMyAdmin → Import**.

This creates the `zimnat_prs` database and seeds a default admin account:

| Field | Value |
|---|---|
| Email | admin@zimnat.co.zw |
| Password | Admin@1234 |

> **Change this password immediately after your first login.**

### Step 3 – Configure the Application

Edit `config/config.php` and set your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'zimnat_prs');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
```

Also update `BASE_URL` if the app is not at the web root:

```php
define('BASE_URL', '/zimnat_prs');   // Sub-directory deployment
define('BASE_URL', '');              // Web root deployment
```

### Step 4 – Enable Apache mod_rewrite

Ensure `mod_rewrite` is enabled and `AllowOverride All` is set for the project directory in your Apache virtual host:

```apache
<Directory "/var/www/html/zimnat_prs">
    AllowOverride All
</Directory>
```

Restart Apache: `sudo service apache2 restart`

### Step 5 – Set Folder Permissions

```bash
chmod 755 uploads/documents/
```

### Step 6 – Access the Application

Open your browser and navigate to:

```
http://localhost/zimnat_prs/
```

You will be redirected to the login page.

---

## Project Structure

```
zimnat_prs/
│
├── index.php                   ← Front controller / router
├── .htaccess                   ← URL rewriting, security headers
├── database.sql                ← Full DB schema + seed data
├── README.md
│
├── config/
│   ├── config.php              ← App constants (DB, paths, limits)
│   └── Database.php            ← PDO singleton connection class
│
├── models/
│   ├── BaseModel.php           ← Abstract base with PDO helper methods
│   ├── UserModel.php           ← User CRUD, auth, email validation
│   ├── PolicyModel.php         ← Policy CRUD, status logic, dashboard stats
│   └── DocumentModel.php       ← Document CRUD linked to policies
│
├── services/
│   └── UploadService.php       ← File validation, storage, deletion
│
├── controllers/
│   ├── AuthController.php      ← Login, logout, session management
│   ├── DashboardController.php ← Dashboard stats aggregation
│   ├── PolicyController.php    ← Full policy CRUD routing
│   ├── DocumentController.php  ← Upload, download, delete documents
│   └── UserController.php      ← Admin user management
│
├── middleware/
│   └── AuthMiddleware.php      ← Session guard, RBAC checks, CSRF verification
│
├── views/
│   ├── auth/
│   │   └── login.php           ← Login page (standalone, no layout)
│   ├── partials/
│   │   ├── header.php          ← Shared sidebar + topbar layout
│   │   ├── footer.php          ← Bootstrap JS, script helpers
│   │   ├── flash.php           ← Session flash message display
│   │   ├── 403.php             ← Access denied error page
│   │   └── 404.php             ← Not found error page
│   ├── dashboard/
│   │   └── index.php           ← Dashboard stats + nearing renewal table
│   ├── policies/
│   │   ├── index.php           ← Policy list with search/filter
│   │   ├── create.php          ← Add policy form
│   │   ├── edit.php            ← Edit policy form
│   │   ├── show.php            ← Policy detail + document management
│   │   └── _form.php           ← Shared policy form fields partial
│   └── admin/users/
│       ├── index.php           ← User list
│       ├── create.php          ← Create user form
│       └── edit.php            ← Edit user form
│
└── uploads/
    └── documents/
        └── .htaccess           ← Blocks direct browser access to files
```

---

## Role Permissions

The system implements three roles with clearly defined permissions:

### Admin
Full system access.

| Feature | Permission |
|---|---|
| Dashboard | ✅ View |
| Policies | ✅ View, Add, Edit, Delete |
| Documents | ✅ View, Upload, Delete, Download |
| Users | ✅ Create, Edit, Activate/Deactivate |

### Policy Officer
Operational access for day-to-day policy work.

| Feature | Permission |
|---|---|
| Dashboard | ✅ View |
| Policies | ✅ View, Add, Edit, Delete |
| Documents | ✅ View, Upload, Delete, Download |
| Users | ❌ No access |

### Viewer
Read-only access for staff who need visibility without modification rights.

| Feature | Permission |
|---|---|
| Dashboard | ✅ View |
| Policies | ✅ View list and detail |
| Documents | ✅ Download |
| Add/Edit/Delete | ❌ Blocked |
| Users | ❌ No access |

Role enforcement is implemented at the controller level via `AuthMiddleware::requireRole(...)`, ensuring that unauthorised actions return a 403 response even if a URL is accessed directly.

---

## Assumptions Made

1. **Currency**: All premium amounts are stored and displayed in USD as commonly used in Zimbabwean insurance.

2. **Policy Number Format**: Policy numbers are free-text strings; no specific format is enforced, but each must be unique. Officers are expected to follow the company's internal numbering convention.

3. **Status Auto-computation**: When a policy is saved, its status is automatically recalculated:
   - If `renewal_date < today` → **Expired**
   - If `renewal_date` is within **30 days** → **Pending Renewal**
   - Otherwise → **Active**
   
   The dashboard triggers this refresh on every load (suitable for small datasets; a cron job is recommended for large-scale production).

4. **File Storage**: Uploaded documents are stored on the local server filesystem inside `uploads/documents/`. In a production environment, this should be replaced with cloud object storage (e.g. AWS S3).

5. **Single Email per User**: Each user is identified by a unique email address. Email addresses cannot be duplicated.

6. **No Password Reset Flow**: A password reset / forgot-password flow was not implemented as it was outside the assignment scope. Admin users can update any user's password via the Edit User form.

7. **No Pagination**: Given the prototype nature of the assessment, all records are returned in a single query. Pagination would be added before production deployment.

8. **HTTPS**: The application includes cookie settings for `secure` mode (HTTPS). During local development on HTTP, this flag is automatically disabled by detecting the absence of `$_SERVER['HTTPS']`.

9. **Single-server Deployment**: The system is designed for deployment on a single Apache + PHP + MySQL server. No containerisation or load balancing is assumed.

---

## AI Usage Disclosure

This project was developed with the assistance of **Claude (Anthropic)**, an AI assistant, for the following purposes:

- **Architecture guidance**: Structuring the MVC-style folder layout and class responsibilities.
- **Code generation**: Generating boilerplate PHP classes (models, controllers, middleware) which were then reviewed, adapted, and extended.
- **SQL schema design**: Drafting the relational database schema including foreign keys, indexes, and status enumerations.
- **UI scaffolding**: Generating Bootstrap-based HTML/CSS view templates.
- **Documentation**: Drafting and structuring this README file.

All generated code was reviewed for correctness, security, and alignment with the assignment requirements. Final decisions on design patterns, security practices, and business logic were made by the developer.

---

*Zimnat Life Assurance Company Limited – Internal System*  
*© 2026 – Confidential*
