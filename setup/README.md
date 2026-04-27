# Care OS — Quick Start

## 1. Install Prerequisites

You need PHP, MySQL, Composer, and Node.js. If you don't have them:

### macOS (using Homebrew)

```bash
# Install Homebrew if you don't have it
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install everything
brew install php mysql composer node

# Start MySQL
brew services start mysql
```

### Windows

1. **PHP** — Download from https://windows.php.net/download (Thread Safe ZIP). Extract to `C:\php`, add to PATH.
2. **MySQL** — Download installer from https://dev.mysql.com/downloads/installer/. Use default settings, set root password to empty or remember it.
3. **Composer** — Download installer from https://getcomposer.org/download/. It auto-detects PHP.
4. **Node.js** — Download LTS from https://nodejs.org. Includes npm.

### Linux (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install php php-mysql php-xml php-mbstring php-curl php-zip mysql-server nodejs npm composer
sudo systemctl start mysql
```

### Verify everything is installed

```bash
php -v          # Should show 8.1+
mysql --version # Should show 8.0+
composer -V     # Should show 2.x
node -v         # Should show 18+
npm -v          # Should show 9+
```

## 2. Setup (one-time)

```bash
npm run setup
```

This will:
1. Install Composer and npm dependencies
2. Create your `.env` file
3. Generate the Laravel app key
4. Create the database and import the data dump (182 tables)
5. Set up asset symlinks

## 3. Run the app

```bash
npm run dev
```

Then open **http://127.0.0.1:8000**

## 4. Login

| Field    | Value    |
|----------|----------|
| Username | komal    |
| Password | 123456   |
| House    | Aries    |

## Troubleshooting

**"Cannot connect to MySQL"** — Make sure MySQL is running:
- macOS: `brew services start mysql`
- Linux: `sudo systemctl start mysql`
- Windows: Open Services app, find MySQL, click Start

**Different MySQL credentials?** — Set environment variables before running setup:
```bash
DB_USERNAME=myuser DB_PASSWORD=mypass npm run setup
```
Or on Windows, edit the `.env` file manually after running setup.

**Deprecation warnings?** — Normal on PHP 8.5. They're suppressed in `public/index.php`.

**"php not found" / "composer not found"** — Make sure they're in your system PATH. Restart your terminal after installing.
