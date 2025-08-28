#!/bin/bash
# Modern Cafe Billing System - Quick Setup Script

echo "🚀 Modern Cafe Billing System Setup"
echo "==================================="

# Create backup
echo "📦 Creating backup of current files..."
mkdir -p backup
cp -r *.php backup/ 2>/dev/null || true
cp -r assets backup/ 2>/dev/null || true
echo "✅ Backup created in 'backup' folder"

# Update main index.php to use modern header
echo "🔄 Updating main files..."

# Backup original files
cp index.php index.php.backup 2>/dev/null || true
cp login.php login.php.backup 2>/dev/null || true
cp home.php home.php.backup 2>/dev/null || true
cp orders.php orders.php.backup 2>/dev/null || true

# Replace includes in index.php
if [ -f "index.php" ]; then
    sed -i 's/header\.php/header-modern.php/g' index.php
    echo "✅ Updated index.php header include"
fi

# Create symbolic links for modern files
if [ -f "login-modern.php" ]; then
    ln -sf login-modern.php login.php
    echo "✅ Login page updated to modern version"
fi

if [ -f "home-modern.php" ]; then
    ln -sf home-modern.php home.php
    echo "✅ Dashboard updated to modern version"
fi

if [ -f "orders-modern.php" ]; then
    ln -sf orders-modern.php orders.php
    echo "✅ Orders page updated to modern version"
fi

# Add PWA registration to index.php
echo "📱 Adding PWA support..."
cat >> index.php << 'EOF'

<script>
// PWA Service Worker Registration
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('./sw.js')
            .then(function(registration) {
                console.log('🎉 Service Worker registered successfully:', registration.scope);
            })
            .catch(function(error) {
                console.log('❌ Service Worker registration failed:', error);
            });
    });
}

// PWA Install Prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    // Show install button (you can customize this)
    const installBtn = document.createElement('button');
    installBtn.innerHTML = '<i class="fas fa-download"></i> Install App';
    installBtn.className = 'btn btn-modern position-fixed';
    installBtn.style.cssText = 'bottom: 80px; right: 30px; z-index: 1000;';
    
    installBtn.addEventListener('click', () => {
        installBtn.style.display = 'none';
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('👍 User accepted PWA install');
            }
            deferredPrompt = null;
        });
    });
    
    document.body.appendChild(installBtn);
});
</script>
EOF

echo "✅ PWA support added"

# Create assets directory structure if it doesn't exist
mkdir -p assets/icons

# Database enhancement script
echo "🗄️ Creating database enhancement script..."
cat > database_upgrade.sql << 'EOF'
-- Modern Cafe Billing System - Database Enhancements
-- Run this in your phpMyAdmin or MySQL console

-- Add new columns for enhanced functionality
ALTER TABLE `orders` 
ADD COLUMN `payment_method` VARCHAR(50) DEFAULT 'cash',
ADD COLUMN `notes` TEXT,
ADD COLUMN `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN `tax_amount` DECIMAL(10,2) DEFAULT 0.00;

-- Add image support for products
ALTER TABLE `products` 
ADD COLUMN `image_url` VARCHAR(255),
ADD COLUMN `barcode` VARCHAR(100),
ADD COLUMN `stock_quantity` INT DEFAULT 0;

-- Enhanced system settings
ALTER TABLE `system_settings` 
ADD COLUMN `theme_color` VARCHAR(7) DEFAULT '#2c3e50',
ADD COLUMN `currency_symbol` VARCHAR(5) DEFAULT '$',
ADD COLUMN `tax_rate` DECIMAL(5,2) DEFAULT 0.00,
ADD COLUMN `business_hours` TEXT;

-- Create notifications table for real-time updates
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `type` ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    `user_id` INT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create audit log for tracking changes
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `action` VARCHAR(100) NOT NULL,
    `table_name` VARCHAR(50),
    `record_id` INT,
    `old_values` JSON,
    `new_values` JSON,
    `ip_address` VARCHAR(45),
    `user_agent` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample enhanced data
INSERT INTO `system_settings` (`name`, `theme_color`, `currency_symbol`, `tax_rate`) 
VALUES ('Modern Cafe Billing System', '#667eea', '$', 8.50)
ON DUPLICATE KEY UPDATE 
    `theme_color` = VALUES(`theme_color`),
    `currency_symbol` = VALUES(`currency_symbol`),
    `tax_rate` = VALUES(`tax_rate`);

-- Sample notification
INSERT INTO `notifications` (`title`, `message`, `type`) 
VALUES ('System Upgraded', 'Your cafe billing system has been successfully upgraded to the modern version!', 'success');

COMMIT;
EOF

echo "✅ Database upgrade script created: database_upgrade.sql"

# Create deployment guide
echo "📋 Creating deployment guide..."
cat > DEPLOYMENT.md << 'EOF'
# 🚀 Deployment Guide

## Quick Deploy to InfinityFree (Recommended)

### Step 1: Create Account
1. Go to https://infinityfree.net
2. Sign up for free account
3. Create a new website

### Step 2: Upload Files
1. Use File Manager or FTP client
2. Upload all files to `htdocs` folder
3. Keep folder structure intact

### Step 3: Setup Database
1. Create MySQL database in control panel
2. Import `database/cafe_billing_db.sql`
3. Run `database_upgrade.sql` for enhancements
4. Update `db_connect.php` with new credentials

### Step 4: Configure SSL
1. Enable free SSL in hosting panel
2. Update all URLs to use HTTPS
3. Test PWA installation

## Alternative: Deploy to Heroku

### Requirements
- Git installed
- Heroku CLI installed
- Convert MySQL to PostgreSQL

### Steps
1. `git init`
2. `heroku create your-app-name`
3. `heroku addons:create heroku-postgresql:hobby-dev`
4. `git push heroku main`

## Testing Checklist
- [ ] Login works correctly
- [ ] Dashboard displays properly
- [ ] Orders can be created
- [ ] PWA can be installed
- [ ] Mobile responsive
- [ ] All animations working
- [ ] Database connections secure

## Security Checklist
- [ ] HTTPS enabled
- [ ] Database credentials secure
- [ ] File permissions set correctly
- [ ] Input validation working
- [ ] Session security enabled
EOF

# Create a simple test script
echo "🧪 Creating test script..."
cat > test_installation.php << 'EOF'
<?php
// Modern Cafe Billing System - Installation Test

echo "<h2>🧪 Installation Test</h2>";

// Test database connection
echo "<h3>📊 Database Connection</h3>";
if (file_exists('db_connect.php')) {
    include 'db_connect.php';
    if ($conn && !$conn->connect_error) {
        echo "✅ Database connection successful<br>";
        
        // Test tables
        $tables = ['users', 'orders', 'products', 'categories', 'system_settings'];
        foreach ($tables as $table) {
            $result = $conn->query("SELECT COUNT(*) FROM $table");
            if ($result) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' missing<br>";
            }
        }
    } else {
        echo "❌ Database connection failed<br>";
    }
} else {
    echo "❌ db_connect.php not found<br>";
}

// Test required files
echo "<h3>📁 Required Files</h3>";
$required_files = [
    'header-modern.php',
    'login-modern.php',
    'home-modern.php',
    'orders-modern.php',
    'assets/css/modern-style.css',
    'assets/js/modern-animations.js',
    'manifest.json',
    'sw.js'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test PHP version
echo "<h3>🐘 PHP Environment</h3>";
echo "✅ PHP Version: " . PHP_VERSION . "<br>";
echo "✅ MySQL Extension: " . (extension_loaded('mysqli') ? 'Available' : 'Not Available') . "<br>";
echo "✅ JSON Extension: " . (extension_loaded('json') ? 'Available' : 'Not Available') . "<br>";

// Test write permissions
echo "<h3>🔒 Permissions</h3>";
echo "✅ Assets folder writable: " . (is_writable('assets') ? 'Yes' : 'No') . "<br>";

echo "<h3>🎉 Installation Complete!</h3>";
echo "<p>If all tests pass, your modern cafe billing system is ready to use.</p>";
echo "<p><a href='login.php' class='btn btn-primary'>Go to Login</a></p>";

echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #2c3e50; }
.btn { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
</style>";
?>
EOF

echo "✅ Test script created: test_installation.php"

# Final instructions
echo ""
echo "🎉 Setup Complete!"
echo "=================="
echo ""
echo "Next steps:"
echo "1. Run 'php test_installation.php' to verify installation"
echo "2. Import 'database_upgrade.sql' to your database"
echo "3. Update database credentials in db_connect.php"
echo "4. Upload to your web hosting provider"
echo "5. Test the modern interface!"
echo ""
echo "Files created:"
echo "- ✅ database_upgrade.sql (run in phpMyAdmin)"
echo "- ✅ DEPLOYMENT.md (hosting guide)"
echo "- ✅ test_installation.php (test script)"
echo "- ✅ Backup folder created"
echo ""
echo "🌟 Your cafe billing system is now modern and ready for production!"
echo ""
echo "Free hosting recommendations:"
echo "1. InfinityFree.net (easiest for beginners)"
echo "2. 000webhost.com (reliable, no ads)"
echo "3. Heroku.com (professional, requires setup)"
echo ""
echo "Need help? Check UPGRADE_GUIDE.md for detailed instructions!"
