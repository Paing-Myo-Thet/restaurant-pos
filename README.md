<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



# 🍽️ Restaurant POS & Management System

A complete Restaurant Point of Sale (POS) and Management System built with **Laravel**, **MySQL**, **Blade**, **Tailwind CSS**, and **JavaScript**.

The system manages restaurant operations from taking customer orders to kitchen preparation, serving, payment, inventory management, and business reports.

---

## 📌 Project Overview

### Main Workflow

```text
Table
  ↓
Waiter creates Order
  ↓
Kitchen receives Order
  ↓
Kitchen prepares Food
  ↓
Kitchen marks Order as Ready
  ↓
Waiter sees Ready Order
  ↓
Waiter serves Food
  ↓
Cashier receives Payment
  ↓
Order Completed
  ↓
Inventory is Updated
  ↓
Reports


🛠️Technology Stack
Backend
Laravel
PHP 8.4+
MySQL
Composer
Frontend
Blade
Tailwind CSS
JavaScript
Vite
Development Environment
Laravel Herd
MySQL Server
Windows
👥 User Roles

The system will have five main roles.

Role	Responsibilities
Admin	Full system access
Manager	Restaurant management and reports
Waiter	Tables, orders, and serving
Kitchen	Food preparation and order status
Cashier	Payments and receipts
🔐 Role Permissions
Admin

Admin has full access to the system.

Can:
Manage users
Manage roles
Manage categories
Manage products
Manage tables
Manage customers
Manage inventory
Manage recipes
Manage orders
Manage payments
Manage expenses
View reports
Manage system settings
Manager

Manager manages daily restaurant operations.

Can:
Manage products
Manage categories
Manage tables
Manage inventory
Manage recipes
View orders
View sales
Manage expenses
View reports
View employees
Waiter

Waiter handles customer orders and serving.

Can:
View tables
View table status
Create orders
Add products to orders
Update order quantities
Send orders to kitchen
View order status
See when food is ready
Mark food as served
Cannot:
Manage products
Change product prices
Manage inventory
Process payments
View financial reports
Kitchen

Kitchen staff handles food preparation.

Can:
View new orders
View order items
Start preparing orders
Mark orders as ready
Order Status
Pending
   ↓
Preparing
   ↓
Ready
Cannot:
Create payments
Change prices
Manage products
Access financial reports
Cashier

Cashier handles customer payments.

Can:
View served orders
View order details
Receive payments
Select payment method
Print receipts
View payment history
Payment Methods
Cash
KBZ Pay
Wave Pay
Card
Other


🍽️ Main Modules
1. Dashboard

The dashboard will display information based on the logged-in user's role.

Admin / Manager Dashboard
Today's sales
Today's orders
Total revenue
Pending orders
Preparing orders
Ready orders
Low-stock ingredients
Recent orders
Best-selling products
Expenses
Estimated profit
2. User Management

Users table:

users
-----
id
name
email
password
role
status
created_at
updated_at
Roles
admin
manager
waiter
kitchen
cashier
3. Categories

Categories organize restaurant menu products.

Examples
Main Food
Drinks
Desserts
Snacks
Coffee
Database
categories
----------
id
name
description
status
created_at
updated_at
4. Products / Menu

Restaurant menu items.

Examples
Fried Rice
Chicken Curry
Coffee
Cola
Ice Cream
Database
products
--------
id
category_id
name
description
price
image
status
created_at
updated_at
Relationship
Category
   ↓
hasMany
   ↓
Products
5. Restaurant Tables

Manage restaurant tables.

Table Status
Available
Occupied
Reserved
Database
tables
------
id
table_number
capacity
status
created_at
updated_at


6. Customers

Customer information.

Database
customers
---------
id
name
phone
address
created_at
updated_at

Customers can be connected to orders.

7. Orders

Orders are the core of the POS system.

Example
Order #1001

Table: 5
Waiter: Mg Aung

2 × Fried Rice      10,000
1 × Cola             2,000
---------------------------
Subtotal            12,000
Discount               500
---------------------------
Total               11,500
Database
orders
------
id
order_number
table_id
customer_id
waiter_id
status
subtotal
discount
total
payment_status
created_at
updated_at
8. Order Items

Each order contains one or more products.

Database
order_items
-----------
id
order_id
product_id
quantity
unit_price
subtotal
created_at
updated_at
Relationship
Order
  ↓
hasMany
  ↓
Order Items
  ↓
belongsTo
  ↓
Product
9. Order Status

The main order workflow is:

Pending
   ↓
Preparing
   ↓
Ready
   ↓
Served
   ↓
Completed
Who changes the status?
Waiter
   ↓
Creates Order

Kitchen
   ↓
Pending → Preparing → Ready

Waiter
   ↓
Ready → Served

Cashier
   ↓
Payment → Completed
10. Kitchen System

Kitchen Dashboard displays incoming orders.

Example
NEW ORDER

Order #1001
Table 5

2 × Fried Rice
1 × Cola

[ Start Preparing ]

After preparation:

Preparing
    ↓
Ready

The waiter will then see that the order is ready.

11. Waiter System

Waiters can:

View tables
Create orders
Add products
Send orders to kitchen
View order status
See ready orders
Serve food
Example
Order #1001

Status: READY

[ Serve Order ]

After serving:

Ready
  ↓
Served
12. Cashier System

Cashier receives payment after the order has been served.

Payment Workflow
Served
   ↓
Cashier
   ↓
Payment
   ↓
Paid
   ↓
Completed
13. Payments
Payment Methods
Cash
KBZ Pay
Wave Pay
Card
Other
Database
payments
--------
id
order_id
amount
payment_method
payment_status
paid_at
received_by
created_at
updated_at
14. Inventory / Stock

Inventory manages restaurant ingredients.

Examples
Rice
Chicken
Egg
Cooking Oil
Vegetables
Coffee
Sugar
Database
ingredients
-----------
id
name
unit
current_stock
minimum_stock
status
created_at
updated_at
Units
kg
g
liter
ml
pcs
15. Stock In

Stock increases when the restaurant purchases ingredients.

Example
Rice       +20 kg
Chicken    +10 kg
Egg        +100 pcs
16. Stock Out

Stock can be reduced for:

Wastage
Spoiled ingredients
Damaged ingredients
Manual usage
Example
Chicken
-2 kg

Reason:
Spoiled
17. Stock Movements

All inventory changes should be recorded.

Database
stock_movements
---------------
id
ingredient_id
type
quantity
reason
reference_type
reference_id
created_by
created_at
updated_at
Movement Types
IN
OUT
WASTE
SALE
ADJUSTMENT
18. Recipes

Recipes connect menu products with ingredients.

Example
Fried Rice
Rice          200 g
Egg           1 pc
Oil           10 ml
Vegetables    50 g
Example Order
2 × Fried Rice

Inventory consumption:

Rice          -400 g
Egg             -2 pcs
Oil             -20 ml
Vegetables     -100 g
19. Recipe Database
recipes
-------
id
product_id
name
description
created_at
updated_at
recipe_items
------------
id
recipe_id
ingredient_id
quantity
unit
created_at
updated_at
Relationship
Product
   ↓
Recipe
   ↓
Recipe Items
   ↓
Ingredients
20. Low Stock Alert

The system checks:

current_stock <= minimum_stock
Example
⚠️ LOW STOCK

Rice

Current: 8 kg
Minimum: 10 kg

The alert should appear on the Admin/Manager dashboard.

21. Automatic Inventory Deduction

When an order reaches the appropriate completed/served stage, the system calculates ingredient consumption from the recipe.

Example
Order
2 × Fried Rice
       ↓
Recipe
       ↓
Rice -400 g
Egg -2 pcs
Oil -20 ml
       ↓
Inventory Updated

Inventory should NOT be deducted simply because a waiter created an order.

This prevents incorrect stock deductions when orders are cancelled.

22. Expenses

Restaurant expenses can be recorded.

Examples
Electricity
Water
Rent
Gas
Transportation
Maintenance
Other
Database
expenses
--------
id
title
amount
category
expense_date
description
created_by
created_at
updated_at
23. Reports
Sales Reports
Daily sales
Weekly sales
Monthly sales
Total revenue
Total orders
Product Reports
Best-selling products
Quantity sold
Product revenue
Inventory Reports
Current stock
Stock in
Stock out
Wastage
Ingredient usage
Low-stock ingredients
Financial Reports
Revenue
Expenses
Estimated profit
24. Main Database Structure
users

categories

products

tables

customers

ingredients
stock_movements

recipes
recipe_items

orders
order_items

payments

expenses
25. Database Relationships
Category
   │
   └── hasMany → Products

Product
   │
   └── hasOne → Recipe

Recipe
   │
   └── hasMany → Recipe Items

Recipe Item
   │
   └── belongsTo → Ingredient

Order
   ├── belongsTo → Table
   ├── belongsTo → Customer
   ├── belongsTo → User (Waiter)
   └── hasMany → Order Items

Order Item
   └── belongsTo → Product

Order
   └── hasOne → Payment
26. Complete System Flow
                         RESTAURANT POS
                               │
          ┌────────────────────┼────────────────────┐
          │                    │                    │
        USERS                MENU               INVENTORY
          │                    │                    │
    ┌─────┼─────┐       ┌─────┴─────┐        ┌─────┴─────┐
    │     │     │       │           │        │           │
  Admin Manager Staff Categories Products Ingredients Stock
                    │           │       │
                    │        Recipes    │
                    │           │       │
                    └─────┬─────┘       │
                          │             │
                        WAITER          │
                          │             │
                          ↓             │
                        ORDER ──────────┘
                          │
                          ↓
                       KITCHEN
                          │
                      Preparing
                          │
                        Ready
                          │
                          ↓
                       WAITER
                          │
                        Served
                          │
                          ↓
                       CASHIER
                          │
                       Payment
                          │
                          ↓
                      Completed
                          │
                          ↓
                       REPORTS
27. Development Roadmap

We will build the system step by step.

[ ] Step 1  - Laravel Project Setup
[ ] Step 2  - MySQL Database Setup
[ ] Step 3  - Authentication
[ ] Step 4  - Users & Roles
[ ] Step 5  - Role Permissions
[ ] Step 6  - Categories
[ ] Step 7  - Products / Menu
[ ] Step 8  - Restaurant Tables
[ ] Step 9  - Customers
[ ] Step 10 - Ingredients
[ ] Step 11 - Stock In / Stock Out
[ ] Step 12 - Recipes
[ ] Step 13 - Orders
[ ] Step 14 - Order Items
[ ] Step 15 - Kitchen Dashboard
[ ] Step 16 - Waiter Dashboard
[ ] Step 17 - Cashier Dashboard
[ ] Step 18 - Payments
[ ] Step 19 - Automatic Stock Deduction
[ ] Step 20 - Expenses
[ ] Step 21 - Admin Dashboard
[ ] Step 22 - Reports
[ ] Step 23 - Testing
[ ] Step 24 - UI Improvements
[ ] Step 25 - Final Deployment
28. Development Environment

Current environment:

OS: Windows

Laravel Herd: 1.25
PHP: 8.4.16
Composer: 2.9.3
Laravel Installer: 5.24
Node.js: 22.13.1
npm: 11.8

Database:
MySQL Server
Port: 3306
Host: localhost
29. First Development Goal

Before building the restaurant features, make sure the development environment works.

Laravel Herd
     ↓
PHP
     ↓
Laravel
     ↓
MySQL
     ↓
restaurant_pos

Then begin:

Users
   ↓
Roles
   ↓
Permissions

After that:

Categories
   ↓
Products
   ↓
Tables
   ↓
Inventory
   ↓
Recipes
   ↓
Orders
   ↓
Kitchen
   ↓
Waiter
   ↓
Cashier
   ↓
Payments
   ↓
Reports
🎯 Project Goal

Build a reliable restaurant POS system that allows:

Waiters to take orders, kitchen staff to prepare food, waiters to serve ready orders, cashiers to process payments, and managers/admins to manage inventory, expenses, sales, and reports from one Laravel application.