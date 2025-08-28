<?php
// Enhanced header with modern UI dependencies
?>
<meta content="Modern Cafe Billing System - Enhanced UI" name="description">
<meta content="cafe, billing, restaurant, POS, modern, responsive" name="keywords">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

<!-- Modern Font Loading -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome Pro (Enhanced Icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Animate.css for Enhanced Animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<!-- Bootstrap 5 (Latest) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Legacy Vendor CSS Files (Keep for compatibility) -->
<link href="assets/vendor/icofont/icofont.min.css" rel="stylesheet">
<link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
<link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
<link href="assets/DataTables/datatables.min.css" rel="stylesheet">
<link href="assets/css/jquery.datetimepicker.min.css" rel="stylesheet">
<link href="assets/css/select2.min.css" rel="stylesheet">

<!-- Custom Modern Styles -->
<link href="assets/css/modern-style.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">

<!-- AOS (Animate On Scroll) Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<!-- Particles.js for Background Effects -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<!-- GSAP for Advanced Animations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<!-- Three.js for 3D Effects (Optional) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<!-- Vanilla Tilt for 3D Card Effects -->
<script src="https://cdn.jsdelivr.net/gh/micku7zu/vanilla-tilt.js@1.7.0/dist/vanilla-tilt.min.js"></script>

<!-- Core JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Legacy Scripts (Keep for compatibility) -->
<script src="assets/DataTables/datatables.min.js"></script>
<script src="assets/vendor/jquery.easing/jquery.easing.min.js"></script>
<script src="assets/js/select2.min.js"></script>
<script src="assets/js/jquery.datetimepicker.full.min.js"></script>

<!-- SweetAlert2 for Beautiful Alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Chart.js for Modern Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- AOS Animation Library -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Lottie for Vector Animations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

<!-- Custom Modern Animations -->
<script src="assets/js/modern-animations.js"></script>

<!-- PWA Manifest for Mobile App Feel -->
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#2c3e50">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<style>
/* Critical CSS for faster loading */
.critical-loading {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #2c3e50, #3498db);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: white;
}

.critical-loading .spinner {
    width: 50px;
    height: 50px;
    border: 5px solid rgba(255, 255, 255, 0.3);
    border-top: 5px solid #e67e22;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

.critical-loading .cafe-icon {
    font-size: 3rem;
    margin-bottom: 10px;
    animation: pulse 2s infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}
</style>
