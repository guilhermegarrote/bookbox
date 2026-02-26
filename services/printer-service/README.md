# Printer Service (Windows)

## System Requirements

* Windows 10 or Windows 11
* Node.js **18 LTS (recommended)** or **20 LTS**
* Administrator privileges
* Internet connection (first installation only)

---

## 1️⃣ Install Node.js

Install **Node.js 18 LTS (recommended)**.

Download:
[https://nodejs.org/](https://nodejs.org/)

Verify installation:

```bash
node -v
```

Expected output:

```
v18.x.x
or
v20.x.x
```

---

## 2️⃣ Install Dependencies

Inside the `printer-service` folder, run:

```bash
npm install
```

This installs all required `node_modules`.

You need to run this:

* On first installation
* If `node_modules` was deleted
* If `package.json` was updated
* If the project was moved to another machine

---

## 3️⃣ USB Driver Setup (If Printer Is Not Detected)

If the printer is not recognized:

### Option A – Use Zadig

* Download Zadig
* Run as Administrator
* Go to **Options → List All Devices**
* Select your printer
* Choose **WinUSB**
* Click **Install Driver**

If it still does not work:

### Option B – Install the original manufacturer driver.

---

## 4️⃣ Install the Windows Service

Open **PowerShell as Administrator**.

Temporarily allow script execution:

```powershell
Set-ExecutionPolicy RemoteSigned -Scope Process
```

Run:

```powershell
.\install-printer-service.ps1
```

This will:

* Create the Windows service
* Configure it to start automatically
* Start the service
* Create and manage `printer-service.log`

---

## 5️⃣ Uninstall the Service (If Needed)

To remove the service:

```powershell
.\uninstall-printer-service.ps1
```

This will:

* Stop the service
* Remove it from Windows

---

## Important Notes

* Always run install/uninstall scripts as Administrator.
* Do not change the Node.js version after installation unless you reinstall the service.
* Logs are automatically rotated by the system.
* No additional configuration is required after successful installation.
