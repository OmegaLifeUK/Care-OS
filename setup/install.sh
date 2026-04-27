#!/bin/bash
# ============================================================
# Care OS — One-time setup script
# Run this once after cloning/unzipping the project.
# Prerequisites: PHP 8.1+, MySQL, Composer, Node.js/npm
# ============================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_DIR"

echo ""
echo "========================================="
echo "  Care OS — Setup"
echo "========================================="
echo ""

# --- 1. Check prerequisites ---
echo "[1/7] Checking prerequisites..."

command -v php >/dev/null 2>&1 || { echo "ERROR: PHP is not installed. Install PHP 8.1+ and try again."; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "ERROR: Composer is not installed. Install from https://getcomposer.org"; exit 1; }
command -v npm >/dev/null 2>&1 || { echo "ERROR: npm is not installed. Install Node.js from https://nodejs.org"; exit 1; }
command -v mysql >/dev/null 2>&1 || { echo "ERROR: MySQL client is not installed. Install MySQL 8.0+ and try again."; exit 1; }

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
echo "  PHP $PHP_VERSION ✓"
echo "  Composer ✓"
echo "  npm ✓"
echo "  MySQL client ✓"
echo ""

# --- 2. Install PHP dependencies ---
echo "[2/7] Installing Composer dependencies..."
composer install --no-interaction --prefer-dist
echo ""

# --- 3. Install Node dependencies ---
echo "[3/7] Installing npm dependencies..."
npm install
echo ""

# --- 4. Set up .env ---
echo "[4/7] Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    echo "  Created .env from .env.example"
else
    echo "  .env already exists, skipping"
fi
echo ""

# --- 5. Generate app key ---
echo "[5/7] Generating application key..."
php artisan key:generate --no-interaction
echo ""

# --- 6. Create database and import dump ---
echo "[6/7] Setting up database..."

DB_NAME="scits_v2-35313139b6a7"
DB_USER="${DB_USERNAME:-root}"
DB_PASS="${DB_PASSWORD:-}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"

MYSQL_ARGS="-u $DB_USER -h $DB_HOST -P $DB_PORT"
if [ -n "$DB_PASS" ]; then
    MYSQL_ARGS="$MYSQL_ARGS -p$DB_PASS"
fi

# Check if MySQL server is running
if ! mysql $MYSQL_ARGS -e "SELECT 1" >/dev/null 2>&1; then
    echo "  ERROR: Cannot connect to MySQL at $DB_HOST:$DB_PORT"
    echo "  Make sure MySQL is running and the credentials in .env are correct."
    echo "  You can set DB_USERNAME and DB_PASSWORD environment variables if needed."
    echo ""
    echo "  On macOS:  brew services start mysql"
    echo "  On Linux:  sudo systemctl start mysql"
    exit 1
fi

# Create database if it doesn't exist
mysql $MYSQL_ARGS -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;" 2>/dev/null
echo "  Database '$DB_NAME' ready"

# Check if database already has tables
TABLE_COUNT=$(mysql $MYSQL_ARGS -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME';" 2>/dev/null)

if [ "$TABLE_COUNT" -gt "0" ]; then
    echo "  Database already has $TABLE_COUNT tables — skipping import."
    echo "  (To reimport, drop the database first: mysql -u root -e \"DROP DATABASE \\\`$DB_NAME\\\`;\")"
else
    if [ -f setup/database.sql ]; then
        echo "  Importing database dump (this may take a minute)..."
        mysql $MYSQL_ARGS "$DB_NAME" < setup/database.sql
        NEW_COUNT=$(mysql $MYSQL_ARGS -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB_NAME';" 2>/dev/null)
        echo "  Imported $NEW_COUNT tables ✓"
    else
        echo "  WARNING: setup/database.sql not found — no data imported."
        echo "  The app will run but all pages will be empty."
    fi
fi
echo ""

# --- 7. Create public symlink (Laravel asset path fix) ---
echo "[7/7] Setting up asset symlinks..."
if [ ! -L public/public ]; then
    cd public && ln -sf . public && cd ..
    echo "  Created public/public symlink ✓"
else
    echo "  Symlink already exists ✓"
fi

# Storage symlink
php artisan storage:link --no-interaction 2>/dev/null || true
echo ""

echo "========================================="
echo "  Setup complete!"
echo "========================================="
echo ""
echo "  To start the app:"
echo "    php artisan serve"
echo ""
echo "  Then open: http://127.0.0.1:8000"
echo ""
echo "  Login credentials:"
echo "    Username: komal"
echo "    Password: 123456"
echo "    House:    Aries"
echo ""
