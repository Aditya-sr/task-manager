# Task Management Application

A full-stack Task Management application built with **Laravel 12**, **Inertia.js**, and **Vue 3**.

## Features

- ✅ User Authentication (Register/Login)
- ✅ CRUD operations for Tasks (Create, Read, Update, Delete)
- ✅ Each user can only see their own tasks
- ✅ Task fields: Title, Description, Status (Pending/Completed), Due Date
- ✅ Multiple file uploads with preview (Images & PDFs)
- ✅ Search and filter tasks by status
- ✅ Pagination (10 tasks per page)
- ✅ Task ordering: Latest tasks on top, Completed tasks at bottom
- ✅ Form validation and error handling
- ✅ Responsive UI with Tailwind CSS

## Requirements

- **PHP** 8.2+
- **Composer**
- **Node.js** & NPM
- **MySQL** 5.7+ / MariaDB 10.3+

## Setup Instructions

### 1. Clone the Repository
```bash
git clone https://github.com/YOUR_USERNAME/task-manager.git
cd task-manager# Task Management Application

A full-stack Task Management application built with **Laravel 12**, **Inertia.js**, and **Vue 3**.

## Features

- ✅ User Authentication (Register/Login)
- ✅ CRUD operations for Tasks (Create, Read, Update, Delete)
- ✅ Each user can only see their own tasks
- ✅ Task fields: Title, Description, Status (Pending/Completed), Due Date
- ✅ Multiple file uploads with preview (Images & PDFs)
- ✅ Search and filter tasks by status
- ✅ Pagination (10 tasks per page)
- ✅ Task ordering: Latest tasks on top, Completed tasks at bottom
- ✅ Form validation and error handling
- ✅ Responsive UI with Tailwind CSS

## Requirements

- **PHP** 8.2+
- **Composer**
- **Node.js** & NPM
- **MySQL** 5.7+ / MariaDB 10.3+


```bash
git clone https://github.com/YOUR_USERNAME/task-manager.git
cd task-manager



# .env creds
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:HZiMdbhsTkvLFBb4xvlbayQiGhIYEBfsAP9lmdLvrsk=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

# PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
