# Al Rish Al Khaleeji - Automated Oracle Cloud Deployment Script
param(
    [string]$DomainName = "alrish.duckdns.org",
    [string]$RemoteHost = "168.107.79.216",
    [string]$RemoteUser = "ubuntu",
    [string]$RemoteRoot = "/home/ubuntu/alrish-inventory"
)

# 1. Resolve SSH Key location
$SSH_KEY = "C:\Users\lenovo\OneDrive\Documents\Finals SSH KEY\ssh-key-2026-10-08.key"
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "C:\Users\rfer3\Documents\SSH KEY\ssh-key-2026-10-08.key"
}
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "C:\Users\rfer3\Downloads\ssh-key-2026-10-08.key"
}
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "C:\Users\lenovo\OneDrive\Documents\NEW SSH KEY\ssh-key-2026-10-06.key"
}
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "C:\Users\lenovo\.ssh\ssh-key-2026-08-08.key"
}
if (-not (Test-Path $SSH_KEY)) {
    $SSH_KEY = "$env:USERPROFILE\.ssh\id_rsa"
}

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Al Rish Inventory - Deploy to Oracle Always Free  " -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "Target Host   : $RemoteHost" -ForegroundColor Gray
Write-Host "Remote Root   : $RemoteRoot" -ForegroundColor Gray
Write-Host "Primary Domain: $DomainName" -ForegroundColor Gray
Write-Host "SSH Key       : $SSH_KEY" -ForegroundColor Gray
Write-Host ""

if (-not (Test-Path $SSH_KEY)) {
    Write-Host "Error: SSH Key not found!" -ForegroundColor Red
    exit 1
}

$SOURCE_DIR = $PSScriptRoot
if (-not (Test-Path "$SOURCE_DIR\index.php")) {
    $SOURCE_DIR = "C:\laragon\www\alrish"
}

# 2. Package Project Files
Write-Host "[1/5] Packaging project files..." -ForegroundColor Yellow
$tarArchive = "$SOURCE_DIR\alrish_deploy.tar.gz"
if (Test-Path $tarArchive) { Remove-Item $tarArchive -Force }

tar -czf "$tarArchive" -C "$SOURCE_DIR" index.php config.php db.php set_password.php icons.php adminer.php logo.png manifest.json manifest.php sw.js .htaccess icons inventory.db Dockerfile docker-compose.yml
Write-Host "  -> Archive created: $([math]::round((Get-Item $tarArchive).Length / 1KB, 1)) KB" -ForegroundColor Green

# 3. Upload to Remote Server
Write-Host "[2/5] Uploading package to Oracle Cloud VM..." -ForegroundColor Yellow
scp -i "$SSH_KEY" -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no "$tarArchive" "${RemoteUser}@${RemoteHost}:/home/ubuntu/alrish_deploy.tar.gz"
Remove-Item $tarArchive -Force

# 4. Remote Setup: Database, Extraction, Docker & Migration
Write-Host "[3/5] Setting up remote database, Docker container & auto-migration..." -ForegroundColor Yellow
$remoteSetupScript = @'
cd /home/ubuntu/alrish-inventory 2>/dev/null && set -a && . ./.env && set +a  # server-side secrets (DB_PASS, DB_ROOT_PASS)
# Ensure isolated MariaDB Database for Al Rish exists
docker exec -i srh_toda_db mariadb -u root -p"$DB_ROOT_PASS" -e "CREATE DATABASE IF NOT EXISTS alrish_inventory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; GRANT ALL PRIVILEGES ON alrish_inventory.* TO 'srh_user'@'%'; FLUSH PRIVILEGES;"

# Create project root and extract
mkdir -p /home/ubuntu/alrish-inventory
cd /home/ubuntu/alrish-inventory
tar -xzf /home/ubuntu/alrish_deploy.tar.gz
rm -f /home/ubuntu/alrish_deploy.tar.gz

# Start Docker container
docker compose up -d --build

# Trigger database initialization and SQLite -> MariaDB auto-migration
docker exec -i alrish_inventory_app php -r "require_once '/var/www/html/db.php'; getDb();"
'@

$remoteSetupScript = $remoteSetupScript.Replace("`r`n", "`n")
$remoteSetupScript | ssh -i "$SSH_KEY" -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no "${RemoteUser}@${RemoteHost}" "tr -d '\r' | bash"

# 5. Configure Caddy Reverse Proxy & Automatic SSL
Write-Host "[4/5] Configuring Caddy Reverse Proxy with Automatic SSL..." -ForegroundColor Yellow
$caddySetupScript = @"
if ! grep -q '$DomainName' /etc/caddy/Caddyfile; then
    cat << 'EOF' | sudo tee -a /etc/caddy/Caddyfile > /dev/null

$DomainName {
    reverse_proxy 127.0.0.1:8081
}
EOF
fi

caddy validate --config /etc/caddy/Caddyfile && sudo systemctl reload caddy
"@

$caddySetupScript = $caddySetupScript.Replace("`r`n", "`n")
$caddySetupScript | ssh -i "$SSH_KEY" -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no "${RemoteUser}@${RemoteHost}" "tr -d '\r' | bash"

# 6. Verification & Completion
Write-Host "[5/5] Verifying Deployment..." -ForegroundColor Yellow
$verifyScript = @"
cd /home/ubuntu/alrish-inventory && set -a && . ./.env && set +a
echo '=== HTTP Local Status (Container Direct) ==='
curl -sI http://127.0.0.1:8081/ | head -n 5
echo ''
echo '=== Database Check ==='
docker exec -i srh_toda_db mariadb -u srh_user -p"`$DB_PASS" alrish_inventory -e 'SELECT count(*) as categories_count FROM inventory_categories; SELECT count(*) as items_count FROM inventory_items; SELECT count(*) as logs_count FROM inventory_logs;'
"@

$verifyScript = $verifyScript.Replace("`r`n", "`n")
$verifyScript | ssh -i "$SSH_KEY" -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no "${RemoteUser}@${RemoteHost}" "tr -d '\r' | bash"

Write-Host ""
Write-Host "==================================================" -ForegroundColor Green
Write-Host "Deployment Completed Successfully!" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Green
Write-Host "Your system URL        : https://$DomainName" -ForegroundColor Cyan
Write-Host "Adminer Database Manager: https://$DomainName/adminer.php" -ForegroundColor Cyan
Write-Host "Server Public IP       : $RemoteHost" -ForegroundColor Gray
Write-Host ""
Write-Host "IMPORTANT NOTE:" -ForegroundColor Yellow
Write-Host "Ensure that '$DomainName' points to '$RemoteHost' in your DuckDNS dashboard." -ForegroundColor Yellow
Write-Host "Once DNS propagates, Caddy will automatically issue and install the SSL certificate!" -ForegroundColor Yellow
Write-Host "==================================================" -ForegroundColor Green
