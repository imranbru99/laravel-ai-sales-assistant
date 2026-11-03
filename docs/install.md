# Installation & Setup Guide

This guide covers setting up **AI Sales Assistant** locally and in production environments.

---

## System Requirements

- **PHP**: 8.3 or 8.4+ with extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_sqlite` or `pdo_mysql`, `tokenizer`, `xml`, `redis`
- **Database**: SQLite 3.35+ (development) or MySQL 8.0+ / MariaDB 10.11+ (production)
- **Node.js**: v20+ with npm
- **Composer**: v2.6+
- **Redis**: v6.2+ (cache, queues, rate limits)

---

## 1. Local Setup

### Step 1: Clone Repository
```bash
git clone https://github.com/imrandevbd/ai-sales-assistant.git
cd ai-sales-assistant
```

### Step 2: Install PHP Dependencies
```bash
composer install
```

### Step 3: Configure Environment
```bash
cp .env.example .env
php artisan key:generate
```

Ensure `.env` contains:
```ini
APP_NAME="AI Sales Assistant"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

CACHE_STORE=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file

# AI Provider Configuration (Default: demo offline driver)
AI_DEFAULT_PROVIDER=demo
OPENAI_API_KEY=
```

For SQLite, ensure the database file exists:
```bash
touch database/database.sqlite
```

### Step 4: Install Front-End Dependencies & Build
```bash
npm install
npm run build
```

### Step 5: Run Enterprise Demo Installation
```bash
php artisan demo:install --fresh
```

This command runs all migrations, creates 76 modules, 11 business presets, seeds 3 enterprise tenants, 100+ scored leads, deals, quotations, invoices, and sets up 8 demo personas.

### Step 6: Start Server
```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000) for the public marketing site and [http://localhost:8000/demo](http://localhost:8000/demo) for 1-click persona logins.

---

## 2. Test Execution

Run the complete Pest test suite:
```bash
./vendor/bin/pest
```
All feature and unit tests will run and assert system functionality.
