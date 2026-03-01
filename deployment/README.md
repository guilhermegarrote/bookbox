# Bookbox – Installation & Deployment Guide

---

## 📌 System Requirements

Make sure the server (or local environment) meets the following requirements:

* PHP 8.1+
* Composer
* Node.js **LTS (18 or 20 recommended)**
* MySQL / MariaDB
* Git
* A web server (Apache or Nginx)

**Windows users:** Laragon, WAMP, or XAMPP

---

## 1️⃣ Clone the Repository

```bash
git clone https://github.com/guilhermegarrote/bookbox
cd bookbox
```

---

## 2️⃣ Install PHP Dependencies

```bash
composer install
```

---

## 3️⃣ Configure Environment File

Copy the example file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

---

## 4️⃣ Configure Database

1. Create a new database.
2. Update the `.env` file:

```env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

3. Run migrations and seeders:

```bash
php artisan migrate --seed
```

---

## 5️⃣ Configure Email Service

Update `.env` with your mail provider settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailprovider.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@yourdomain.com
MAIL_FROM_NAME="BookBox System"
```

---

## 6️⃣ Install Frontend & Node Dependencies

```bash
npm install
```

If using Puppeteer:

```bash
npm install puppeteer
```

Build frontend assets:

```bash
npm run build
```

---

## 7️⃣ Scheduler and Queue Setup (Windows)

For Windows production environments, you do **not** need to manually configure Task Scheduler or CRON.

The project includes an automated PowerShell script that sets up:

* Laravel scheduler (runs every minute)
* Queue worker as a Windows service via NSSM

### ✅ How to Configure

From the project root, run as Administrator:

```powershell
deployment\scripts\windows-production-setup.ps1
```

### ⚠️ Requirements

Before running the script, make sure:

* PHP is installed
* NSSM is installed
* Paths inside the script are correct:

```powershell
$ProjectPath
$PhpPath
$NssmPath
```

After running, your Laravel scheduler and queue worker will run automatically.

---

## 8️⃣ GitHub Self-Hosted Runner (Windows)

To enable automatic deployment:

1. Go to your GitHub repository:
   **Settings → Actions → Runners → New self-hosted runner**
2. Choose **Windows**
3. Follow the instructions provided by GitHub:

```powershell
mkdir C:\actions-runner
cd C:\actions-runner
```

* Download runner package from GitHub
* Configure:

```powershell
config.cmd
```

* Install as service:

```powershell
svc install
svc start
```

Now your server is connected to GitHub and ready to run Actions pipelines.

### 9️⃣ Backup

To configure the backup service with Google Drive, please refer to the [Backup Guide](backup/README.md).
