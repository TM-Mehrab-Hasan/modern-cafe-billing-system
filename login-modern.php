<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./db_connect.php');
ob_start();
$system = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
foreach($system as $k => $v){
    $_SESSION['system'][$k] = $v;
}
ob_end_flush();
?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo $_SESSION['system']['name'] ?> - Modern Login</title>
    
    <?php include('./header-modern.php'); ?>
    
    <?php 
    if(isset($_SESSION['login_id']))
        header("location:home-modern.php");
    ?>
</head>

<body class="login-body">
    <!-- Critical Loading Screen -->
    <div class="critical-loading" id="criticalLoader">
        <div class="cafe-icon">☕</div>
        <div class="spinner"></div>
        <p>Loading Modern Cafe System...</p>
    </div>

    <!-- Animated Background Particles -->
    <div id="particles-js"></div>

    <!-- Login Container -->
    <main class="login-main">
        <div class="container-fluid h-100">
            <div class="row h-100">
                <!-- Left Panel - Branding -->
                <div class="col-lg-6 d-none d-lg-flex login-brand-panel">
                    <div class="brand-content" data-aos="fade-right">
                        <div class="brand-logo">
                            <i class="fas fa-coffee"></i>
                        </div>
                        <h1 class="brand-title">
                            <?php echo $_SESSION['system']['name'] ?>
                        </h1>
                        <p class="brand-subtitle">
                            Modern Point of Sale System for Your Cafe
                        </p>
                        <div class="brand-features">
                            <div class="feature-item" data-aos="fade-up" data-aos-delay="100">
                                <i class="fas fa-chart-line"></i>
                                <span>Real-time Analytics</span>
                            </div>
                            <div class="feature-item" data-aos="fade-up" data-aos-delay="200">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Mobile Responsive</span>
                            </div>
                            <div class="feature-item" data-aos="fade-up" data-aos-delay="300">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure & Reliable</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Login Form -->
                <div class="col-lg-6 login-form-panel">
                    <div class="login-form-container" data-aos="fade-left">
                        <div class="login-header">
                            <h2 class="login-title">Welcome Back!</h2>
                            <p class="login-subtitle">Please sign in to your account</p>
                        </div>

                        <form id="login-form" class="modern-login-form">
                            <div class="form-floating mb-3">
                                <input type="text" id="username" name="username" class="form-control" placeholder="Username" required>
                                <label for="username">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <div class="input-border"></div>
                            </div>

                            <div class="form-floating mb-4">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                                <label for="password">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="input-border"></div>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                </button>
                            </div>

                            <div class="row mb-4">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">
                                            Remember me
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <a href="#" class="forgot-password">Forgot Password?</a>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-login w-100 mb-3">
                                <span class="btn-text">Sign In</span>
                                <div class="btn-loader">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </button>

                            <div class="login-divider">
                                <span>or continue with</span>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-2">
                                    <button type="button" class="btn btn-demo w-100" onclick="fillDemoCredentials('admin')">
                                        <i class="fas fa-user-shield me-2"></i>
                                        Demo Admin
                                    </button>
                                </div>
                                <div class="col-6 mb-2">
                                    <button type="button" class="btn btn-demo w-100" onclick="fillDemoCredentials('staff')">
                                        <i class="fas fa-user me-2"></i>
                                        Demo Staff
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="login-footer">
                            <p class="text-center text-muted">
                                <small>
                                    © <?php echo date('Y'); ?> <?php echo $_SESSION['system']['name']; ?>. All rights reserved.
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="errorToast" class="toast" role="alert">
            <div class="toast-header">
                <i class="fas fa-exclamation-circle text-danger me-2"></i>
                <strong class="me-auto">Login Error</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body" id="errorMessage">
                Invalid username or password.
            </div>
        </div>
    </div>

    <style>
    /* Modern Login Styles */
    .login-body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        overflow: hidden;
        font-family: 'Inter', sans-serif;
    }

    #particles-js {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }

    .login-main {
        position: relative;
        z-index: 2;
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .login-brand-panel {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border-right: 1px solid rgba(255, 255, 255, 0.2);
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .login-brand-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%);
        animation: brandGlow 8s ease-in-out infinite alternate;
    }

    @keyframes brandGlow {
        0% { opacity: 0.3; }
        100% { opacity: 0.7; }
    }

    .brand-content {
        text-align: center;
        color: white;
        z-index: 1;
        position: relative;
        padding: 2rem;
    }

    .brand-logo i {
        font-size: 5rem;
        margin-bottom: 1rem;
        background: linear-gradient(45deg, #ffd700, #ffed4e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: logoSpin 10s linear infinite;
    }

    @keyframes logoSpin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .brand-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .brand-subtitle {
        font-size: 1.2rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    .brand-features {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .feature-item {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 1.1rem;
        opacity: 0.8;
    }

    .feature-item i {
        font-size: 1.5rem;
        color: #ffd700;
    }

    .login-form-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
    }

    .login-form-container {
        width: 100%;
        max-width: 400px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .login-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .login-subtitle {
        color: #666;
        font-size: 1rem;
    }

    .modern-login-form .form-floating {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .modern-login-form .form-control {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem 1rem 1rem 3rem;
        font-size: 1rem;
        background: rgba(255, 255, 255, 0.9);
        transition: all 0.3s ease;
        height: auto;
    }

    .modern-login-form .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
    }

    .modern-login-form label {
        color: #666;
        font-weight: 500;
        padding-left: 2.5rem;
        transition: all 0.3s ease;
    }

    .modern-login-form .form-control:focus + label,
    .modern-login-form .form-control:not(:placeholder-shown) + label {
        color: #667eea;
        transform: scale(0.85) translateY(-0.5rem) translateX(-0.2rem);
    }

    .input-border {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        transition: width 0.3s ease;
    }

    .modern-login-form .form-control:focus ~ .input-border {
        width: 100%;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        z-index: 3;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: #667eea;
    }

    .btn-login {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        border-radius: 12px;
        padding: 1rem;
        font-weight: 600;
        font-size: 1.1rem;
        color: white;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .btn-login::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .btn-login:hover::before {
        left: 100%;
    }

    .btn-loader {
        display: none;
    }

    .btn-login.loading .btn-text {
        opacity: 0;
    }

    .btn-login.loading .btn-loader {
        display: block;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .login-divider {
        text-align: center;
        margin: 1.5rem 0;
        position: relative;
    }

    .login-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: #e9ecef;
    }

    .login-divider span {
        background: rgba(255, 255, 255, 0.95);
        padding: 0 1rem;
        color: #666;
        font-size: 0.9rem;
    }

    .btn-demo {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 0.7rem;
        background: rgba(255, 255, 255, 0.7);
        color: #666;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-demo:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        transform: translateY(-1px);
    }

    .forgot-password {
        color: #667eea;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .forgot-password:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .login-footer {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }

    /* Mobile Responsiveness */
    @media (max-width: 991.98px) {
        .login-form-container {
            margin: 1rem;
            padding: 2rem;
        }
        
        .brand-title {
            font-size: 2rem;
        }
        
        .login-title {
            font-size: 1.5rem;
        }
    }

    /* Loading Animation */
    .critical-loading {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }
    </style>

    <script>
    // Initialize particles.js
    particlesJS('particles-js', {
        particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
            color: { value: "#ffffff" },
            shape: { type: "circle" },
            opacity: { value: 0.5, random: false },
            size: { value: 3, random: true },
            line_linked: {
                enable: true,
                distance: 150,
                color: "#ffffff",
                opacity: 0.4,
                width: 1
            },
            move: {
                enable: true,
                speed: 6,
                direction: "none",
                random: false,
                straight: false,
                out_mode: "out",
                bounce: false
            }
        },
        interactivity: {
            detect_on: "canvas",
            events: {
                onhover: { enable: true, mode: "repulse" },
                onclick: { enable: true, mode: "push" },
                resize: true
            }
        },
        retina_detect: true
    });

    // Remove critical loader when page is ready
    $(document).ready(function() {
        setTimeout(() => {
            $('#criticalLoader').fadeOut(500);
        }, 1500);

        // Initialize AOS
        AOS.init({ duration: 800, easing: 'ease-in-out' });
    });

    // Toggle password visibility
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            passwordIcon.classList.remove('fa-eye');
            passwordIcon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            passwordIcon.classList.remove('fa-eye-slash');
            passwordIcon.classList.add('fa-eye');
        }
    }

    // Fill demo credentials
    function fillDemoCredentials(type) {
        if (type === 'admin') {
            $('#username').val('admin');
            $('#password').val('admin');
        } else {
            $('#username').val('staff');
            $('#password').val('staff');
        }
        
        // Trigger focus events to show labels
        $('#username, #password').trigger('focus').trigger('blur');
    }

    // Enhanced login form submission
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        
        const $submitBtn = $('.btn-login');
        const $form = $(this);
        
        // Add loading state
        $submitBtn.addClass('loading').prop('disabled', true);
        
        // Clear previous errors
        $('.form-control').removeClass('is-invalid');
        
        $.ajax({
            url: 'ajax.php?action=login',
            method: 'POST',
            data: $form.serialize(),
            success: function(resp) {
                if (resp == 1) {
                    // Success animation
                    $submitBtn.removeClass('loading').html(`
                        <i class="fas fa-check me-2"></i>
                        Success! Redirecting...
                    `).removeClass('btn-login').addClass('btn-success');
                    
                    setTimeout(() => {
                        window.location.href = 'index.php?page=home';
                    }, 1000);
                } else {
                    // Error handling
                    $submitBtn.removeClass('loading').prop('disabled', false);
                    $('.form-control').addClass('is-invalid');
                    
                    // Show error toast
                    const errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
                    errorToast.show();
                    
                    // Shake animation
                    $form.addClass('animate__animated animate__shakeX');
                    setTimeout(() => {
                        $form.removeClass('animate__animated animate__shakeX');
                    }, 1000);
                }
            },
            error: function() {
                $submitBtn.removeClass('loading').prop('disabled', false);
                $('#errorMessage').text('Connection error. Please try again.');
                const errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
                errorToast.show();
            }
        });
    });

    // Input focus animations
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        if (!$(this).val()) {
            $(this).parent().removeClass('focused');
        }
    });

    // Check if inputs have values on page load
    $('.form-control').each(function() {
        if ($(this).val()) {
            $(this).parent().addClass('focused');
        }
    });
    </script>
</body>
</html>
