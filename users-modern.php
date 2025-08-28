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

// Check if user is admin
if($_SESSION['login_type'] != 1) {
    header('Location: home-modern.php');
    exit();
}

// Get system settings
$system = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
foreach($system as $k => $v){
    $_SESSION['system'][$k] = $v;
}

// Get user name safely
$user_name = isset($_SESSION['login_name']) ? $_SESSION['login_name'] : 'User';
ob_end_flush();
?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo $_SESSION['system']['name'] ?> - User Management</title>
    
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
                            <li><a class="dropdown-item" href="profile-modern.php"><i class="fas fa-user-cog me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item active" href="users-modern.php"><i class="fas fa-users me-2"></i> Users</a></li>
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
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="page-icon me-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h2 class="page-title mb-1">User Management</h2>
                                        <p class="page-subtitle text-muted mb-0">Manage system users and their permissions</p>
                                    </div>
                                </div>
                                <button class="btn btn-modern btn-primary" onclick="addNewUser()">
                                    <i class="fas fa-plus me-2"></i> Add New User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="row">
                <div class="col-12">
                    <div class="modern-card" data-aos="fade-up">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2 text-primary"></i>
                                System Users
                            </h5>
                            <div class="d-flex gap-2">
                                <input type="text" id="searchUsers" class="form-control form-control-sm" placeholder="Search users..." style="width: 200px;">
                                <select id="filterType" class="form-select form-select-sm" style="width: 150px;">
                                    <option value="">All Types</option>
                                    <option value="1">Admin</option>
                                    <option value="2">Staff</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table modern-table table-hover" id="usersTable">
                                    <thead>
                                        <tr>
                                            <th width="50">#</th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>User Type</th>
                                            <th width="120">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $users = $conn->query("SELECT * FROM users ORDER BY name ASC");
                                        while($row = $users->fetch_assoc()):
                                        ?>
                                        <tr data-type="<?php echo $row['type'] ?>">
                                            <td><strong><?php echo $i++; ?></strong></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-3">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $row['name']; ?></h6>
                                                        <small class="text-muted">ID: <?php echo $row['id']; ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-primary">@<?php echo $row['username']; ?></span>
                                            </td>
                                            <td>
                                                <?php if($row['type'] == 1): ?>
                                                    <span class="badge badge-modern badge-success">
                                                        <i class="fas fa-crown me-1"></i>Administrator
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-modern badge-primary">
                                                        <i class="fas fa-user me-1"></i>Staff
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary edit_user" 
                                                            data-id="<?php echo $row['id'] ?>" title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if($row['id'] != $_SESSION['login_id']): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-danger delete_user" 
                                                            data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['name'] ?>" title="Delete User">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>
                        <span id="modalTitle">Add New User</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="userMsg"></div>
                    <form id="manage-user" class="needs-validation" novalidate>
                        <input type="hidden" name="id" id="userId">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="userName" class="form-label">
                                    <i class="fas fa-user me-1"></i> Full Name
                                </label>
                                <input type="text" name="name" id="userName" class="form-control modern-input" required>
                                <div class="invalid-feedback">Please provide a valid name.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="userUsername" class="form-label">
                                    <i class="fas fa-at me-1"></i> Username
                                </label>
                                <input type="text" name="username" id="userUsername" class="form-control modern-input" required autocomplete="off">
                                <div class="invalid-feedback">Please choose a username.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="userPassword" class="form-label">
                                    <i class="fas fa-lock me-1"></i> Password
                                </label>
                                <input type="password" name="password" id="userPassword" class="form-control modern-input" autocomplete="new-password">
                                <small class="text-muted" id="passwordHelp">Minimum 6 characters required</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="userType" class="form-label">
                                    <i class="fas fa-user-tag me-1"></i> User Type
                                </label>
                                <select name="type" id="userType" class="form-select modern-input" required>
                                    <option value="">Select User Type</option>
                                    <option value="1">Administrator</option>
                                    <option value="2">Staff</option>
                                </select>
                                <div class="invalid-feedback">Please select a user type.</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-modern btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="submit" form="manage-user" class="btn btn-modern btn-primary">
                        <i class="fas fa-save me-2"></i> Save User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
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
    
    .modern-table th {
        border-top: none;
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .modern-table tbody tr {
        transition: all 0.3s ease;
    }
    
    .modern-table tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

        // Search functionality
        $('#searchUsers').on('keyup', function() {
            filterTable();
        });

        // Filter functionality
        $('#filterType').on('change', function() {
            filterTable();
        });

        // Edit user
        $('.edit_user').click(function() {
            const userId = $(this).data('id');
            
            $.ajax({
                url: 'ajax.php',
                method: 'POST',
                data: {action: 'get_user', id: userId},
                dataType: 'json',
                success: function(data) {
                    $('#modalTitle').text('Edit User');
                    $('#userId').val(data.id);
                    $('#userName').val(data.name);
                    $('#userUsername').val(data.username);
                    $('#userType').val(data.type);
                    $('#passwordHelp').text('Leave blank to keep current password');
                    $('#userModal').modal('show');
                },
                error: function() {
                    alert('Error loading user data');
                }
            });
        });

        // Delete user
        $('.delete_user').click(function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            
            if (confirm(`Are you sure you want to delete user "${userName}"?`)) {
                $.ajax({
                    url: 'ajax.php?action=delete_user',
                    method: 'POST',
                    data: {id: userId},
                    success: function(resp) {
                        if (resp == 1) {
                            alert('User deleted successfully!');
                            location.reload();
                        } else {
                            alert('Error deleting user');
                        }
                    }
                });
            }
        });

        // Form submission
        $('#manage-user').on('submit', function(e) {
            e.preventDefault();
            
            const isEdit = $('#userId').val() !== '';
            const password = $('#userPassword').val();
            
            // Validate password for new users
            if (!isEdit && password.length < 6) {
                $('#userPassword').addClass('is-invalid');
                return false;
            }
            
            const submitBtn = $('button[form="manage-user"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i> Saving...').prop('disabled', true);
            
            $.ajax({
                url: 'ajax.php?action=save_user',
                method: 'POST',
                data: $(this).serialize(),
                success: function(resp) {
                    if (resp == 1) {
                        $('#userMsg').html('<div class="alert alert-success">' +
                            '<i class="fas fa-check-circle me-2"></i>User saved successfully!' +
                            '</div>');
                        
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                        
                    } else if (resp == 2) {
                        $('#userMsg').html('<div class="alert alert-danger">' +
                            '<i class="fas fa-exclamation-triangle me-2"></i>Username already exists!' +
                            '</div>');
                    } else {
                        $('#userMsg').html('<div class="alert alert-danger">' +
                            '<i class="fas fa-times-circle me-2"></i>An error occurred while saving user.' +
                            '</div>');
                    }
                },
                error: function() {
                    $('#userMsg').html('<div class="alert alert-danger">' +
                        '<i class="fas fa-exclamation-triangle me-2"></i>Connection error. Please try again.' +
                        '</div>');
                },
                complete: function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });
    });

    function addNewUser() {
        $('#modalTitle').text('Add New User');
        $('#manage-user')[0].reset();
        $('#userId').val('');
        $('#userMsg').empty();
        $('#passwordHelp').text('Minimum 6 characters required');
        $('.is-invalid').removeClass('is-invalid');
        $('#userModal').modal('show');
    }

    function filterTable() {
        const searchText = $('#searchUsers').val().toLowerCase();
        const filterType = $('#filterType').val();
        
        $('#usersTable tbody tr').each(function() {
            const row = $(this);
            const name = row.find('td:nth-child(2)').text().toLowerCase();
            const username = row.find('td:nth-child(3)').text().toLowerCase();
            const type = row.data('type').toString();
            
            const matchesSearch = name.includes(searchText) || username.includes(searchText);
            const matchesType = filterType === '' || type === filterType;
            
            if (matchesSearch && matchesType) {
                row.show();
            } else {
                row.hide();
            }
        });
    }
    </script>
</body>
</html>
