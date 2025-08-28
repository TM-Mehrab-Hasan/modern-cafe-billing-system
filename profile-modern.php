<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('./db_connect.php');
ob_start();

// Check if user is logged in
if(!isset($_SESSION['login_id'])) {
    header('Location: login-modern.php');
    exit();
}

// Get system settings
$system = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
foreach($system as $k => $v){
    $_SESSION['system'][$k] = $v;
}

// Get current user data
$user_id = $_SESSION['login_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_array();

// Get user name safely
$user_name = isset($_SESSION['login_name']) ? $_SESSION['login_name'] : 'User';
ob_end_flush();
?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo $_SESSION['system']['name'] ?> - Profile Management</title>
    
    <?php include('./header-modern.php'); ?>
</head>

<body class="modern-dashboard">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark modern-navbar fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="home-modern.php">
                <i class="fas fa-coffee me-2"></i>
                <?php echo $_SESSION['system']['name']; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home-modern.php">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders-modern.php">
                            <i class="fas fa-shopping-cart me-1"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products-modern.php">
                            <i class="fas fa-box me-1"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories-modern.php">
                            <i class="fas fa-tags me-1"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sales_report-modern.php">
                            <i class="fas fa-chart-bar me-1"></i> Reports
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?php echo $user_name; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item active" href="profile-modern.php"><i class="fas fa-user-cog me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="users-modern.php"><i class="fas fa-users me-2"></i> Users</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="ajax.php?action=logout"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid px-4 py-3">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="modern-card page-header" data-aos="fade-down">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="page-icon me-3">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <div>
                                    <h2 class="page-title mb-1">Profile Management</h2>
                                    <p class="page-subtitle text-muted mb-0">Manage your account settings and personal information</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="row">
                <!-- Profile Information -->
                <div class="col-lg-4 mb-4">
                    <div class="modern-card" data-aos="fade-right">
                        <div class="card-body text-center">
                            <div class="profile-avatar mb-3">
                                <div class="avatar-circle">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <h4 class="text-primary mb-1"><?php echo $user['name']; ?></h4>
                            <p class="text-muted mb-2">@<?php echo $user['username']; ?></p>
                            <span class="badge badge-modern <?php echo $user['type'] == 1 ? 'badge-success' : 'badge-primary'; ?>">
                                <i class="fas fa-<?php echo $user['type'] == 1 ? 'crown' : 'user'; ?> me-1"></i>
                                <?php echo $user['type'] == 1 ? 'Administrator' : 'Staff'; ?>
                            </span>
                            
                            <div class="mt-4">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="profile-stat">
                                            <h5 class="text-primary mb-0">
                                                <?php 
                                                $login_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE id = $user_id")->fetch_array();
                                                echo date('j'); // Day of month as stat
                                                ?>
                                            </h5>
                                            <small class="text-muted">Days Active</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="profile-stat">
                                            <h5 class="text-success mb-0">
                                                <?php 
                                                if($user['type'] == 1) {
                                                    $orders_count = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(date_created) = CURDATE()")->fetch_array();
                                                    echo $orders_count['count'];
                                                } else {
                                                    echo "N/A";
                                                }
                                                ?>
                                            </h5>
                                            <small class="text-muted">Today's Orders</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="modern-card mt-4" data-aos="fade-right" data-aos-delay="100">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2 text-info"></i>
                                Quick Stats
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if($user['type'] == 1): ?>
                            <div class="stat-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Total Users</span>
                                    <strong class="text-primary">
                                        <?php 
                                        $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_array();
                                        echo $total_users['count'];
                                        ?>
                                    </strong>
                                </div>
                            </div>
                            <div class="stat-item mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Total Products</span>
                                    <strong class="text-success">
                                        <?php 
                                        $total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_array();
                                        echo $total_products['count'];
                                        ?>
                                    </strong>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Total Orders</span>
                                    <strong class="text-warning">
                                        <?php 
                                        $total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_array();
                                        echo $total_orders['count'];
                                        ?>
                                    </strong>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="text-center text-muted">
                                <i class="fas fa-user-tie mb-2" style="font-size: 2rem;"></i>
                                <p>Staff member access</p>
                                <small>Limited statistics available</small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <div class="col-lg-8 mb-4">
                    <div class="modern-card" data-aos="fade-left">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-edit me-2 text-primary"></i>
                                Edit Profile Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="msg"></div>
                            <form id="manage-profile" class="needs-validation" novalidate>
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-user me-1"></i> Full Name
                                        </label>
                                        <input type="text" name="name" id="name" class="form-control modern-input" 
                                               value="<?php echo $user['name']; ?>" required>
                                        <div class="invalid-feedback">
                                            Please provide a valid name.
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">
                                            <i class="fas fa-at me-1"></i> Username
                                        </label>
                                        <input type="text" name="username" id="username" class="form-control modern-input" 
                                               value="<?php echo $user['username']; ?>" required autocomplete="off">
                                        <div class="invalid-feedback">
                                            Please choose a username.
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-1"></i> New Password
                                        </label>
                                        <input type="password" name="password" id="password" class="form-control modern-input" 
                                               autocomplete="new-password">
                                        <small class="text-muted">Leave blank to keep current password</small>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label">
                                            <i class="fas fa-lock me-1"></i> Confirm Password
                                        </label>
                                        <input type="password" id="confirm_password" class="form-control modern-input" 
                                               autocomplete="new-password">
                                        <div class="invalid-feedback">
                                            Passwords do not match.
                                        </div>
                                    </div>
                                </div>

                                <?php if($user['type'] == 1): ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="type" class="form-label">
                                            <i class="fas fa-user-tag me-1"></i> User Type
                                        </label>
                                        <select name="type" id="type" class="form-select modern-input">
                                            <option value="1" <?php echo $user['type'] == 1 ? 'selected' : ''; ?>>Administrator</option>
                                            <option value="2" <?php echo $user['type'] == 2 ? 'selected' : ''; ?>>Staff</option>
                                        </select>
                                    </div>
                                </div>
                                <?php else: ?>
                                <input type="hidden" name="type" value="<?php echo $user['type']; ?>">
                                <?php endif; ?>

                                <hr class="my-4">

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-modern btn-secondary" onclick="window.history.back()">
                                        <i class="fas fa-arrow-left me-2"></i> Back
                                    </button>
                                    <div>
                                        <button type="button" class="btn btn-modern btn-outline-warning me-2" onclick="resetForm()">
                                            <i class="fas fa-undo me-2"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-modern btn-primary">
                                            <i class="fas fa-save me-2"></i> Update Profile
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Custom Styles -->
    <style>
    .profile-avatar {
        position: relative;
    }
    
    .avatar-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
        font-size: 2.5rem;
        box-shadow: 0 10px 30px rgba(52, 152, 219, 0.3);
    }
    
    .profile-stat h5 {
        font-weight: 700;
    }
    
    .stat-item {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .stat-item:last-child {
        border-bottom: none;
    }
    
    .modern-input {
        border: 2px solid #e3f2fd;
        border-radius: 10px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    
    .modern-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }
    
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    
    .page-header {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(155, 89, 182, 0.1));
        border: none;
    }
    
    .page-icon i {
        font-size: 2.5rem;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    </style>

    <!-- Scripts -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/aos.js"></script>

    <script>
    $(document).ready(function() {
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });

        // Form validation
        $('#manage-profile').on('submit', function(e) {
            e.preventDefault();
            
            // Check password confirmation
            const password = $('#password').val();
            const confirmPassword = $('#confirm_password').val();
            
            if (password && password !== confirmPassword) {
                $('#confirm_password').addClass('is-invalid');
                return false;
            } else {
                $('#confirm_password').removeClass('is-invalid');
            }
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> Updating...').prop('disabled', true);
            
            $.ajax({
                url: 'ajax.php?action=update_account',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp) {
                    if (resp == 1) {
                        $('#msg').html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-check-circle me-2"></i>Profile updated successfully!' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        
                        // Update display name if changed
                        const newName = $('#name').val();
                        $('.navbar .dropdown-toggle').html('<i class="fas fa-user-circle me-1"></i> ' + newName.split(' ')[0]);
                        
                        // Scroll to message
                        $('html, body').animate({
                            scrollTop: $('#msg').offset().top - 100
                        }, 500);
                        
                    } else if (resp == 2) {
                        $('#msg').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-exclamation-triangle me-2"></i>Username already exists!' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                    } else {
                        $('#msg').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<i class="fas fa-times-circle me-2"></i>An error occurred while updating profile.' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                    }
                },
                error: function() {
                    $('#msg').html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>Connection error. Please try again.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                },
                complete: function() {
                    // Restore button state
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Password confirmation validation
        $('#confirm_password').on('input', function() {
            const password = $('#password').val();
            const confirmPassword = $(this).val();
            
            if (password && confirmPassword && password !== confirmPassword) {
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    });

    function resetForm() {
        $('#manage-profile')[0].reset();
        $('#msg').empty();
        $('.is-invalid').removeClass('is-invalid');
    }
    </script>
</body>
</html>
