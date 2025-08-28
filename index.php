<?php
// Modern Cafe Billing System
// Redirect to modern login interface
session_start();

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['login_id'])) {
    header('Location: home-modern.php');
    exit();
}

// Otherwise redirect to modern login
header('Location: login-modern.php');
exit();
?> 
 
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
</script> 
