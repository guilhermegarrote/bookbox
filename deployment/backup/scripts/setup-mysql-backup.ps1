# ================================
# MySQL + Google Drive Auto Setup
# Using .env credentials
# ================================

# ==== CONFIGURE HERE ====
$BackupDir = "C:\GoogleDrive\Backups_MySQL"
$EnvFile = "C:\PATH\TO\YOUR\PROJECT\.env"   # <-- CHANGE THIS
$MySQLPath = "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe"
$SevenZipPath = "C:\Program Files\7-Zip\7z.exe"
$TaskName = "MySQL_Auto_Backup"
$BackupTime = "02:00"
# ================================

Write-Host "Loading .env file..."

# Read .env file
Get-Content $EnvFile | ForEach-Object {
    if ($_ -match "^\s*([^#][^=]+)=(.*)$") {
        $name = $matches[1].Trim()
        $value = $matches[2].Trim()
        Set-Variable -Name $name -Value $value
    }
}

# Map variables from .env
$MySQLUser = $DB_USERNAME
$MySQLPassword = $DB_PASSWORD
$DatabaseName = $DB_DATABASE

Write-Host "Creating backup directory..."
New-Item -ItemType Directory -Force -Path $BackupDir | Out-Null

Write-Host "Creating backup .bat file..."

$BatContent = @"
@echo off
set DATESTAMP=%DATE:~6,4%-%DATE:~3,2%-%DATE:~0,2%
set BACKUP_DIR=$BackupDir
set MYSQL=`"$MySQLPath`"

%MYSQL% -u $MySQLUser -p$MySQLPassword $DatabaseName > %BACKUP_DIR%\backup_%DATESTAMP%.sql

`"$SevenZipPath`" a -p$MySQLPassword %BACKUP_DIR%\backup_%DATESTAMP%.7z %BACKUP_DIR%\backup_%DATESTAMP%.sql

del %BACKUP_DIR%\backup_%DATESTAMP%.sql

forfiles /p $BackupDir /m *.7z /d -15 /c "cmd /c del @file"
"@

$BatPath = "$BackupDir\backup_mysql.bat"
Set-Content -Path $BatPath -Value $BatContent -Encoding ASCII

Write-Host "Creating scheduled task..."

$Action = New-ScheduledTaskAction -Execute $BatPath
$Trigger = New-ScheduledTaskTrigger -Daily -At $BackupTime
Register-ScheduledTask -TaskName $TaskName -Action $Action -Trigger $Trigger -User "SYSTEM" -RunLevel Highest -Force

Write-Host ""
Write-Host "======================================="
Write-Host "Backup system installed successfully!"
Write-Host "Backup folder: $BackupDir"
Write-Host "Scheduled daily at: $BackupTime"
Write-Host "======================================="
