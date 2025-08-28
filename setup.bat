@echo off
echo 🚀 Modern Cafe Billing System Setup for Windows
echo ================================================

echo.
echo 📦 Creating backup of current files...
if not exist backup mkdir backup
xcopy *.php backup\ /y >nul 2>&1
xcopy assets backup\assets\ /s /e /y >nul 2>&1
echo ✅ Backup created in 'backup' folder

echo.
echo 🔄 Updating main files...

REM Backup original files
if exist index.php copy index.php index.php.backup >nul
if exist login.php copy login.php login.php.backup >nul
if exist home.php copy home.php home.php.backup >nul
if exist orders.php copy orders.php orders.php.backup >nul

REM Update index.php to use modern header
if exist index.php (
    powershell -Command "(gc index.php) -replace 'header\.php', 'header-modern.php' | Out-File -encoding UTF8 index.php"
    echo ✅ Updated index.php header include
)

REM Create copies with modern versions
if exist login-modern.php (
    copy login-modern.php login.php >nul
    echo ✅ Login page updated to modern version
)

if exist home-modern.php (
    copy home-modern.php home.php >nul
    echo ✅ Dashboard updated to modern version
)

if exist orders-modern.php (
    copy orders-modern.php orders.php >nul
    echo ✅ Orders page updated to modern version
)

echo.
echo 📱 Adding PWA support...

REM Add PWA script to index.php
echo. >> index.php
echo ^<script^> >> index.php
echo // PWA Service Worker Registration >> index.php
echo if ^('serviceWorker' in navigator^) { >> index.php
echo     window.addEventListener^('load', function^(^) { >> index.php
echo         navigator.serviceWorker.register^('./sw.js'^) >> index.php
echo             .then^(function^(registration^) { >> index.php
echo                 console.log^('🎉 Service Worker registered successfully:', registration.scope^); >> index.php
echo             }^) >> index.php
echo             .catch^(function^(error^) { >> index.php
echo                 console.log^('❌ Service Worker registration failed:', error^); >> index.php
echo             }^); >> index.php
echo     }^); >> index.php
echo } >> index.php
echo ^</script^> >> index.php

echo ✅ PWA support added

echo.
echo 🗄️ Creating database enhancement script...

REM Create database upgrade script
echo -- Modern Cafe Billing System - Database Enhancements > database_upgrade.sql
echo -- Run this in your phpMyAdmin or MySQL console >> database_upgrade.sql
echo. >> database_upgrade.sql
echo -- Add new columns for enhanced functionality >> database_upgrade.sql
echo ALTER TABLE `orders`  >> database_upgrade.sql
echo ADD COLUMN `payment_method` VARCHAR^(50^) DEFAULT 'cash', >> database_upgrade.sql
echo ADD COLUMN `notes` TEXT, >> database_upgrade.sql
echo ADD COLUMN `discount_amount` DECIMAL^(10,2^) DEFAULT 0.00, >> database_upgrade.sql
echo ADD COLUMN `tax_amount` DECIMAL^(10,2^) DEFAULT 0.00; >> database_upgrade.sql
echo. >> database_upgrade.sql
echo -- Add image support for products >> database_upgrade.sql
echo ALTER TABLE `products`  >> database_upgrade.sql
echo ADD COLUMN `image_url` VARCHAR^(255^), >> database_upgrade.sql
echo ADD COLUMN `barcode` VARCHAR^(100^), >> database_upgrade.sql
echo ADD COLUMN `stock_quantity` INT DEFAULT 0; >> database_upgrade.sql
echo. >> database_upgrade.sql
echo -- Enhanced system settings >> database_upgrade.sql
echo ALTER TABLE `system_settings`  >> database_upgrade.sql
echo ADD COLUMN `theme_color` VARCHAR^(7^) DEFAULT '#2c3e50', >> database_upgrade.sql
echo ADD COLUMN `currency_symbol` VARCHAR^(5^) DEFAULT '$', >> database_upgrade.sql
echo ADD COLUMN `tax_rate` DECIMAL^(5,2^) DEFAULT 0.00; >> database_upgrade.sql

echo ✅ Database upgrade script created: database_upgrade.sql

echo.
echo 🧪 Creating test script...

REM Create test installation script
echo ^<?php > test_installation.php
echo // Modern Cafe Billing System - Installation Test >> test_installation.php
echo echo "^<h2^>🧪 Installation Test^</h2^>"; >> test_installation.php
echo // Test database connection >> test_installation.php
echo if ^(file_exists^('db_connect.php'^)^) { >> test_installation.php
echo     include 'db_connect.php'; >> test_installation.php
echo     if ^($conn ^&^& !$conn-^>connect_error^) { >> test_installation.php
echo         echo "✅ Database connection successful^<br^>"; >> test_installation.php
echo     } else { >> test_installation.php
echo         echo "❌ Database connection failed^<br^>"; >> test_installation.php
echo     } >> test_installation.php
echo } >> test_installation.php
echo ?^> >> test_installation.php

echo ✅ Test script created: test_installation.php

echo.
echo 📋 Creating deployment guide...

echo # 🚀 Deployment Guide > DEPLOYMENT.md
echo. >> DEPLOYMENT.md
echo ## Quick Deploy to InfinityFree ^(Recommended^) >> DEPLOYMENT.md
echo. >> DEPLOYMENT.md
echo ### Step 1: Create Account >> DEPLOYMENT.md
echo 1. Go to https://infinityfree.net >> DEPLOYMENT.md
echo 2. Sign up for free account >> DEPLOYMENT.md
echo 3. Create a new website >> DEPLOYMENT.md
echo. >> DEPLOYMENT.md
echo ### Step 2: Upload Files >> DEPLOYMENT.md
echo 1. Use File Manager or FTP client >> DEPLOYMENT.md
echo 2. Upload all files to `htdocs` folder >> DEPLOYMENT.md
echo 3. Keep folder structure intact >> DEPLOYMENT.md

echo ✅ Deployment guide created: DEPLOYMENT.md

REM Create assets directory if it doesn't exist
if not exist assets\icons mkdir assets\icons

echo.
echo 🎉 Setup Complete!
echo ==================
echo.
echo Next steps:
echo 1. Open test_installation.php in your browser to verify
echo 2. Import 'database_upgrade.sql' to your database
echo 3. Update database credentials in db_connect.php
echo 4. Upload to your web hosting provider
echo 5. Test the modern interface!
echo.
echo Files created:
echo - ✅ database_upgrade.sql ^(run in phpMyAdmin^)
echo - ✅ DEPLOYMENT.md ^(hosting guide^)
echo - ✅ test_installation.php ^(test script^)
echo - ✅ Backup folder created
echo.
echo 🌟 Your cafe billing system is now modern and ready for production!
echo.
echo Free hosting recommendations:
echo 1. InfinityFree.net ^(easiest for beginners^)
echo 2. 000webhost.com ^(reliable, no ads^)
echo 3. Heroku.com ^(professional, requires setup^)
echo.
echo Need help? Check UPGRADE_GUIDE.md for detailed instructions!
echo.
pause
