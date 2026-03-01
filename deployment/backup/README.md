# Bookbox – Backup (Google Drive)

---

# Simple Architecture (Works Great)

**Internal Windows Server**

⬇️

Generates `.sql` backup

⬇️

Saves it in a **Google Drive synchronized folder**

⬇️

Google Drive uploads it to the cloud ☁️

👉 No need for external public access — only Google Drive sync is required.

---

# 1️⃣ Install Google Drive on the Server

On the Windows server:

1. Install **Google Drive for Desktop**.
2. Log in with your Google account.
3. Choose (or create) a sync folder, for example:

```
C:\GoogleDrive\Backups_MySQL
```

Any file placed in this folder will automatically be uploaded to Google Drive.

---

# 2️⃣ Configure Your `.env` File (Database Credentials)

Your project must contain a `.env` file with your database credentials.

### Example:

```env
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=bookbox
DB_USERNAME=backup_user
DB_PASSWORD=StrongPassword123
```

⚠️ The backup script will automatically read:

* `DB_DATABASE`
* `DB_USERNAME`
* `DB_PASSWORD`

No need to hardcode credentials inside scripts.

---

# 3️⃣ Setup and Run the Automatic Backup Script (Recommended)

Instead of manually creating a `.bat` file and configuring Task Scheduler, use the provided PowerShell setup script.

This script will automatically:

✔️ Read credentials from `.env`  
✔️ Create the backup directory  
✔️ Generate the `.bat` backup file  
✔️ Create the scheduled task  
✔️ Configure daily execution  

### Run the Script

Open PowerShell as Administrator and execute:

```powershell
Set-ExecutionPolicy RemoteSigned -Scope Process
.\setup\setup-mysql-backup.ps1
```

After execution, everything will be configured automatically.

No manual Task Scheduler configuration is required.

---

# 4️⃣ Backup File Format

Generated files will look like:

```
backup_2026-01-30.7z
```

The `.sql` file is automatically deleted after compression.

Backups older than 15 days are automatically removed.

---

# 🛡️ Best Practices for Internal Servers

✔️ Use a dedicated MySQL user for backups only  
✔️ Restrict permissions of the backup user  
✔️ Use a strong password for `.7z` compression  
✔️ Keep your `.env` file protected  


