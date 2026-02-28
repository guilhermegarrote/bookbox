# ============================================
# LARAVEL PRODUCTION SETUP - WINDOWS
# schedule:run + queue:work (NSSM)
# ============================================

# ====== CONFIGURE HERE ======
$ProjectPath = "C:\xampp\htdocs\bookbox"
$PhpPath = "C:\xampp\php\php.exe"
$NssmPath = "C:\nssm\nssm.exe"
$ServiceName = "BookboxQueueWorker"
# =============================

Write-Host "Starting configuration..." -ForegroundColor Green

# ============================================
# 1️⃣ CREATE schedule:run SCHEDULED TASK
# ============================================

Write-Host "Creating Scheduled Task..." -ForegroundColor Yellow

$Action = New-ScheduledTaskAction `
    -Execute $PhpPath `
    -Argument "artisan schedule:run" `
    -WorkingDirectory $ProjectPath

$Trigger = New-ScheduledTaskTrigger `
    -Once `
    -At (Get-Date) `
    -RepetitionInterval (New-TimeSpan -Minutes 1) `
    -RepetitionDuration ([TimeSpan]::MaxValue)

Register-ScheduledTask `
    -TaskName "LaravelSchedule" `
    -Action $Action `
    -Trigger $Trigger `
    -User "SYSTEM" `
    -RunLevel Highest `
    -Force

Write-Host "✔ schedule:run configured successfully." -ForegroundColor Green

# ============================================
# 2️⃣ INSTALL queue:work SERVICE USING NSSM
# ============================================

Write-Host "Installing queue worker service..." -ForegroundColor Yellow

# Remove if it already exists
& $NssmPath stop $ServiceName 2>$null
& $NssmPath remove $ServiceName confirm 2>$null

# Install service
& $NssmPath install $ServiceName $PhpPath
& $NssmPath set $ServiceName AppDirectory $ProjectPath
& $NssmPath set $ServiceName AppParameters "artisan queue:work --sleep=3 --tries=3 --timeout=90"
& $NssmPath set $ServiceName Start SERVICE_AUTO_START

# Start service
& $NssmPath start $ServiceName

Write-Host "✔ queue:work configured as a Windows service." -ForegroundColor Green

Write-Host "============================================"
Write-Host "CONFIGURATION COMPLETED SUCCESSFULLY" -ForegroundColor Cyan
Write-Host "============================================"
