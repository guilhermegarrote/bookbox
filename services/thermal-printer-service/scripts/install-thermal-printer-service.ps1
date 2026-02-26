# ==============================
# Configuration
# ==============================
$ServiceName = "ThermalPrinterService"
$DisplayName = "Thermal Printer Service"
$Description = "Background thermal printing service for the BookBox system"
$NodePath = "C:\nvm4w\nodejs\node.exe"
$ScriptPath = "C:\xampp\htdocs\bookbox\services\thermal-printer-service\src\index.js"
$NSSMPath = "C:\nssm\nssm.exe"

# ==============================
# Remove existing service
# ==============================
$svc = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
if ($svc) {
    & $NSSMPath stop $ServiceName
    & $NSSMPath remove $ServiceName confirm
    Write-Host "Existing service removed."
    Start-Sleep -Seconds 2
}

# ==============================
# Install new service
# ==============================
& $NSSMPath install $ServiceName $NodePath $ScriptPath
& $NSSMPath set $ServiceName DisplayName $DisplayName
& $NSSMPath set $ServiceName Description $Description
& $NSSMPath set $ServiceName Start SERVICE_AUTO_START

# IMPORTANT: Set working directory
$AppDirectory = "C:\xampp\htdocs\bookbox\services\thermal-printer-service"
& $NSSMPath set $ServiceName AppDirectory $AppDirectory

# ==============================
# Start service
# ==============================
Start-Service $ServiceName -ErrorAction SilentlyContinue
Start-Sleep -Seconds 2

$service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if ($service -and $service.Status -eq "Running") {
    Write-Host "Service installed and started successfully!"
} else {
    Write-Host "Service installed, but failed to start."
    Write-Host "Check Event Viewer or application logs."
}
