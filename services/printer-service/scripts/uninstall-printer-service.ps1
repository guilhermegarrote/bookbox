# ==========================================
# Printer Service Uninstallation Script
# ==========================================
# This script must be executed as Administrator.

# ==============================
# Configuration
# ==============================
$ServiceName = "PrinterService"
$NSSMPath = "C:\nssm\nssm.exe"

Write-Host "Checking service status..."

# ==============================
# Check if service exists
# ==============================
$svc = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if (-not $svc) {
    Write-Host "Service '$ServiceName' is not installed."
    exit
}

# ==============================
# Stop service if running
# ==============================
if ($svc.Status -eq "Running") {
    Write-Host "Stopping service..."
    try {
        Stop-Service -Name $ServiceName -Force -ErrorAction Stop
        Start-Sleep -Seconds 2
        Write-Host "Service stopped successfully."
    }
    catch {
        Write-Host "Failed to stop service normally. Attempting forced stop via NSSM..."
        if (Test-Path $NSSMPath) {
            & $NSSMPath stop $ServiceName
            Start-Sleep -Seconds 2
        }
    }
}

# ==============================
# Remove service
# ==============================
Write-Host "Removing service..."

try {
    if (Test-Path $NSSMPath) {
        & $NSSMPath remove $ServiceName confirm
    } else {
        sc.exe delete $ServiceName | Out-Null
    }
}
catch {
    Write-Host "Error occurred while removing the service."
}

Start-Sleep -Seconds 2

# ==============================
# Verify removal
# ==============================
$svcCheck = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if (-not $svcCheck) {
    Write-Host "Service '$ServiceName' successfully uninstalled."
} else {
    Write-Host "Failed to uninstall service '$ServiceName'. Please check manually."
}
