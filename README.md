# Custom MVC PHP Inventory Management System

This is a premium, highly responsive, and full-featured **Inventory Management Software** designed to manage catalog items, sales, credit collections, and product damage (waste). It is built using a custom **CodeIgniter-style MVC PHP Architecture** with a self-healing **SQLite database** and containerized with **Docker Compose** for instant, one-command deployment.

---

## ⚡ Key System Features

1. **3-Level Role-Based Access Control (RBAC)**:
   - **Super Admin**: Master access (Full financial dashboards, Product CRUD, restock overrides, complete logs, and Staff account creation/termination).
   - **Admin**: Operations manager (Catalog controls, restock triggers, recording product waste, and viewing invoice audits).
   - **Salesman**: Frontline transactions tracker (Dynamic daily sells registry, cash collections from credit accounts, and daily waste entries).
2. **Interactive Daily Sales (POS)**: An elegant multi-row sales desk allowing salespeople to add multiple products to a single sale transaction simultaneously. Backed by client-side validations matching available stock and a live price calculator computing subtotals and pending due credit on the fly.
3. **Daily Cash Collections Register**: Track cash collections from credit customers to reduce outstanding invoices due. Prevents logging amounts exceeding the invoice's remaining dues.
4. **Autonomous Damage & Waste Manager**: Logs product damages (Broken, Expired, Lost, Spoiled) along with reasons and deductions from available inventory stock.
5. **Zero-Configuration Self-Healing Database**: Uses SQLite. If the database file (`inventory.sqlite`) is absent when the application starts, the core connection engine **automatically compiles the tables and seeds them with high-fidelity, professional mock data** instantly!
6. **Premium Dashboard Analytics**: Renders live indicators of totals, collected cash, pending credit due, inventory alerts, and recent log sheets customized for each role.

---

## 📂 Project Structure

```
inventory-system/
├── app/
│   ├── Config/
│   │   └── Database.php         # DB Connection engine & Self-healing hook
│   ├── Controllers/
│   │   ├── Auth.php             # Session authentication (Login/Logout)
│   │   ├── Dashboard.php        # Role-based statistics & alerts compiler
│   │   ├── Products.php         # CRUD Catalog and restocking actions
│   │   ├── Sales.php            # Invoices, multi-row POS and collections logs
│   │   ├── Damages.php          # Waste logs & stock reductions
│   │   └── Users.php            # Super-Admin staff registry
│   ├── Core/
│   │   ├── BaseController.php   # Layout renderer & access level filters
│   │   ├── BaseModel.php        # PDO base driver with Active Record queries
│   │   └── Router.php           # Clean Regex Routing mappings
│   ├── Models/
│   │   ├── UserModel.php, ProductModel.php, SaleModel.php, etc.
│   ├── Views/
│   │   ├── layout/              # Navbars, Headers, and Footers
│   │   ├── auth/login.php       # Security credential portal
│   │   ├── dashboard.php        # Analytics counters & alert cards
│   │   └── products/, sales/, damages/, users/ -- CRUD view templates
│   └── database/
│       ├── setup_db.php         # Database compiler & seeder statements
│       └── inventory.sqlite     # Pre-seeded SQLite datastore file
├── public/
│   ├── css/
│   │   └── style.css            # Premium custom CSS theme
│   ├── .htaccess                # Apache rewrite rules inside public
│   └── index.php                # System entrance Front Controller
├── .htaccess                    # Root redirection directory map
├── Dockerfile                   # PHP 8.2 + Apache + mod_rewrite
└── docker-compose.yml           # Multi-environment compose bindings
```

---

## 🚀 Instant Deployment with Docker

### Prerequisites
- Make sure you have **Docker** and **Docker Compose** installed.

### 1. Launch Container
Navigate to the project directory `/home/soadulislam/Desktop/inventory-system` and run:
```bash
docker-compose up -d --build
```
This builds the Apache/PHP container and deploys the application.

### 2. View in Browser
Open your browser and visit:
👉 **`http://localhost:8080`**

*The self-healing DB loader will immediately run, compile the database tables, and seed them, making the site live and ready.*

### ⚠️ Troubleshooting Volume Permissions (SQLite + Docker)
If you encounter a `Database seeding execution failed: SQLSTATE[HY000] [14] unable to open database file` error, it is due to a standard Docker volume permission mismatch. The container's internal web-server user (`www-data`) requires write access to create files inside the mounted directory `/app/database/`.

To resolve this immediately, run the following command in your terminal from the project root:
```bash
chmod -R 777 app/database
```
This unlocks write access for the Docker container process, allowing the self-healing compiler to create, seed, and update the SQLite file cleanly.

---

## 🔑 Pre-Seeded Demo Credentials

Log in using these credentials to test the different role-based access control (RBAC) clearances:

| Username | Password | Access Level | Permissions / Clearance |
| :--- | :--- | :--- | :--- |
| **`superadmin`** | **`admin123`** | **Super Admin** | Manage Users, View All Reports, Edit Products, Restock, Sales, Damages |
| **`admin`** | **`admin123`** | **Admin** | View Reports, Edit Products, Restock, Sales, Damages |
| **`salesman`** | **`salesman123`** | **Salesman** | Input Sales, Log Collections, Log Damages, View Personal Statistics |

---

## 💻 Running Without Docker (Alternative)

If you have a local PHP web server installed on your host system:
1. Navigate to `/home/soadulislam/Desktop/inventory-system`
2. Spin up PHP's built-in server pointing to the `public/` directory:
   ```bash
   php -S localhost:8000 -t public
   ```
3. Visit `http://localhost:8000` in your web browser!
