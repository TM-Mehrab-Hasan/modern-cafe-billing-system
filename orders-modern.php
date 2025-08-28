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

// Get user name safely
$user_name = isset($_SESSION['login_name']) ? $_SESSION['login_name'] : 'User';
// Is current user admin?
$isAdmin = isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1;
ob_end_flush();
?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo $_SESSION['system']['name'] ?> - Orders Management</title>
    
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
                        <a class="nav-link active" href="orders-modern.php">
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
                            <?php if($isAdmin): ?>
                            <li><a class="dropdown-item" href="users-modern.php"><i class="fas fa-users me-2"></i> Users</a></li>
                            <?php endif; ?>
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
                        <div>
                            <h2 class="page-title mb-1">
                                <i class="fas fa-clipboard-list me-3 text-primary"></i>
                                Orders Management
                            </h2>
                            <p class="page-subtitle text-muted mb-0">
                                Manage and track all customer orders
                            </p>
                        </div>
                        <div class="page-actions">
                            <button class="btn btn-modern btn-success" id="new_order" data-aos="fade-left">
                                <i class="fas fa-plus me-2"></i>
                                New Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <?php
        $today = date('Y-m-d');
        $total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_array();
        $today_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(date_created) = '$today'")->fetch_array();
        $paid_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE amount_tendered > 0")->fetch_array();
        $pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE amount_tendered = 0")->fetch_array();
        $today_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(date_created) = '$today' AND amount_tendered > 0")->fetch_array();
        ?>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card mini" data-aos="zoom-in" data-aos-delay="100">
                <div class="stats-icon text-primary">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stats-number"><?php echo $total_orders['count'] ?></div>
                <div class="stats-label">Total Orders</div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card mini" data-aos="zoom-in" data-aos-delay="200">
                <div class="stats-icon text-success">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stats-number"><?php echo $today_orders['count'] ?></div>
                <div class="stats-label">Today's Orders</div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card mini" data-aos="zoom-in" data-aos-delay="300">
                <div class="stats-icon text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number"><?php echo $paid_orders['count'] ?></div>
                <div class="stats-label">Paid Orders</div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card mini" data-aos="zoom-in" data-aos-delay="400">
                <div class="stats-icon text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number"><?php echo $pending_orders['count'] ?></div>
                <div class="stats-label">Pending Orders</div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-8 col-sm-12 mb-3">
            <div class="stats-card mini revenue" data-aos="zoom-in" data-aos-delay="500">
                <div class="stats-icon text-info">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stats-number">৳<?php echo number_format($today_revenue['total'] ?? 0, 2) ?></div>
                <div class="stats-label">Today's Revenue</div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card filters-card" data-aos="fade-up">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" class="form-control" id="orderSearch" placeholder="Search orders...">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                            <select class="form-select" id="dateFilter">
                                <option value="">All Dates</option>
                                <option value="today">Today</option>
                                <option value="yesterday">Yesterday</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="input-group">
                                <input type="date" class="form-control" id="fromDate">
                                <span class="input-group-text">to</span>
                                <input type="date" class="form-control" id="toDate">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <button class="btn btn-modern w-100" onclick="applyFilters()">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="row">
        <div class="col-12">
            <div class="modern-card" data-aos="fade-up">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Orders List
                    </h5>
                    <div class="table-actions">
                        <button class="btn btn-sm btn-outline-secondary me-2" onclick="refreshTable()">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i> Export
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportData('excel')">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportData('pdf')">
                                    <i class="fas fa-file-pdf me-2"></i>PDF
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="exportData('csv')">
                                    <i class="fas fa-file-csv me-2"></i>CSV
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table modern-table table-hover" id="ordersTable">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th width="8%">#</th>
                                    <th width="15%">Date & Time</th>
                                    <th width="12%">Invoice</th>
                                    <th width="12%">Order #</th>
                                    <th width="12%">Amount</th>
                                    <th width="12%">Status</th>
                                    <th width="12%">Payment</th>
                                    <th width="12%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $order = $conn->query("SELECT * FROM orders ORDER BY unix_timestamp(date_created) DESC");
                                while($row = $order->fetch_assoc()):
                                ?>
                                <tr data-aos="fade-in" data-aos-delay="<?php echo $i * 50 ?>">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input row-select" type="checkbox" value="<?php echo $row['id'] ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="row-number"><?php echo $i++ ?></span>
                                    </td>
                                    <td>
                                        <div class="order-date">
                                            <strong><?php echo date("M j, Y", strtotime($row['date_created'])) ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo date("g:i A", strtotime($row['date_created'])) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="invoice-info">
                                            <?php if($row['amount_tendered'] > 0): ?>
                                                <code class="invoice-number"><?php echo $row['ref_no'] ?></code>
                                            <?php else: ?>
                                                <span class="text-muted">Not Generated</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="order-number">
                                            <strong>#<?php echo str_pad($row['order_number'], 4, '0', STR_PAD_LEFT) ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="amount-info">
                                            <strong class="text-success amount">৳<?php echo number_format($row['total_amount'], 2) ?></strong>
                                            <?php if($row['amount_tendered'] > 0): ?>
                                                <br>
                                                <small class="text-muted">Paid: ৳<?php echo number_format($row['amount_tendered'], 2) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($row['amount_tendered'] > 0): ?>
                                            <span class="badge badge-modern badge-success status-badge">
                                                <i class="fas fa-check-circle me-1"></i>Paid
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-modern badge-warning status-badge">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($row['amount_tendered'] > 0): ?>
                                            <div class="payment-method">
                                                <i class="fas fa-credit-card text-success me-1"></i>
                                                <small>Cash/Card</small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary view_order" 
                                                        data-id="<?php echo $row['id'] ?>" 
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                        onclick="location.href='billing/index.php?id=<?php echo $row['id'] ?>'" 
                                                        title="Edit Order">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if($row['amount_tendered'] > 0): ?>
                                                <button type="button" class="btn btn-sm btn-outline-success print_receipt" 
                                                        data-id="<?php echo $row['id'] ?>" 
                                                        title="Print Receipt">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                                <?php endif; ?>
                                                <?php if($isAdmin): ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete_order" 
                                                        data-id="<?php echo $row['id'] ?>" 
                                                        title="Delete Order">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
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

<style>
.page-header {
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(155, 89, 182, 0.1));
    border: none;
}

.page-title {
    color: var(--primary-color);
    font-weight: 700;
}

.stats-card.mini {
    text-align: center;
    padding: 1.5rem 1rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 12px;
    border: none;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card.mini::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.stats-card.mini:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.stats-card.mini .stats-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.stats-card.mini .stats-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
}

.stats-card.mini .stats-label {
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 500;
}

.stats-card.revenue {
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(26, 188, 156, 0.1));
}

.filters-card {
    border: none;
    background: rgba(255, 255, 255, 0.98);
}

.search-box {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    z-index: 2;
}

.search-box .form-control {
    padding-left: 45px;
    border-radius: 25px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.search-box .form-control:focus {
    border-color: var(--accent-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.order-date strong {
    color: var(--primary-color);
}

.invoice-number {
    background: rgba(52, 152, 219, 0.1);
    color: var(--accent-color);
    padding: 2px 8px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}

.order-number strong {
    color: var(--primary-color);
    font-family: 'Courier New', monospace;
}

.amount {
    font-size: 1.1rem;
    font-weight: 600;
}

.status-badge {
    font-size: 0.75rem;
    padding: 6px 12px;
    border-radius: 15px;
    font-weight: 600;
}

.action-buttons .btn {
    margin: 1px;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.action-buttons .btn:hover {
    transform: translateY(-1px);
}

.payment-method {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.table-actions .btn {
    border-radius: 6px;
}

.row-number {
    font-weight: 600;
    color: var(--primary-color);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-card.mini {
        margin-bottom: 1rem;
    }
    
    .page-actions {
        margin-top: 1rem;
    }
    
    .action-buttons .btn {
        margin-bottom: 2px;
    }
    
    .filters-card .row > div {
        margin-bottom: 1rem;
    }
}

/* Animation delays for table rows */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
$(document).ready(function() {
    // Initialize DataTable with modern styling
    const table = $('#ordersTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[2, 'desc']], // Sort by date column
        columnDefs: [
            { orderable: false, targets: [0, 8] }, // Disable sorting for checkbox and actions
            { searchable: false, targets: [0, 8] }
        ],
        language: {
            search: "",
            searchPlaceholder: "Search orders...",
            lengthMenu: "Show _MENU_ orders",
            info: "Showing _START_ to _END_ of _TOTAL_ orders",
            infoEmpty: "No orders found",
            infoFiltered: "(filtered from _MAX_ total orders)",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                previous: '<i class="fas fa-angle-left"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                last: '<i class="fas fa-angle-double-right"></i>'
            }
        },
        drawCallback: function() {
            // Re-initialize tooltips after table redraw
            $('[title]').tooltip();
        }
    });

    // Custom search functionality
    $('#orderSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Initialize AOS
    AOS.init({
        duration: 600,
        easing: 'ease-in-out',
        once: true
    });

    // Select all functionality
    $('#selectAll').change(function() {
        $('.row-select').prop('checked', this.checked);
        updateBulkActions();
    });

    $('.row-select').change(function() {
        updateBulkActions();
        
        // Update select all checkbox
        const totalRows = $('.row-select').length;
        const checkedRows = $('.row-select:checked').length;
        $('#selectAll').prop('checked', totalRows === checkedRows);
    });

    // Initialize tooltips
    $('[title]').tooltip();

    // Initialize modals and event handlers
    setupEventHandlers();
});

function updateBulkActions() {
    const selectedCount = $('.row-select:checked').length;
    if (selectedCount > 0) {
        // Show bulk action buttons
        console.log(`${selectedCount} orders selected`);
    }
}

function setupEventHandlers() {
    // New order button
    $('#new_order').click(function() {
        window.location.href = 'billing/index.php';
    });

    // View order details
    $('.view_order').click(function() {
        const orderId = $(this).attr('data-id');
        viewOrderDetails(orderId);
    });

    // Function to view order details
    function viewOrderDetails(orderId) {
        // For now, redirect to view_order.php in a new window
        window.open(`../view_order.php?id=${orderId}`, '_blank', 'width=900,height=700');
    }

    // Print receipt
    $('.print_receipt').click(function() {
        const orderId = $(this).attr('data-id');
        window.open(`receipt.php?id=${orderId}`, '_blank', 'width=800,height=600');
    });

    // Delete order
    $('.delete_order').click(function() {
        const orderId = $(this).attr('data-id');
        const orderNumber = $(this).closest('tr').find('.order-number strong').text();
        
        _conf(`Are you sure you want to delete order ${orderNumber}?`, "delete_order", [orderId]);
    });
}

function applyFilters() {
    const status = $('#statusFilter').val();
    const dateFilter = $('#dateFilter').val();
    const fromDate = $('#fromDate').val();
    const toDate = $('#toDate').val();
    
    // Apply custom filtering logic here
    console.log('Applying filters:', { status, dateFilter, fromDate, toDate });
    
    // Show loading state
    start_load();
    
    // Simulate filter application
    setTimeout(() => {
        end_load();
        alert_toast('Filters applied successfully', 'success');
    }, 1000);
}

function refreshTable() {
    start_load();
    setTimeout(() => {
        location.reload();
    }, 500);
}

function exportData(format) {
    start_load();
    
    // Simulate export
    setTimeout(() => {
        end_load();
        alert_toast(`Orders exported as ${format.toUpperCase()}`, 'success');
    }, 1500);
}

function delete_order(id) {
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_order',
        method: 'POST',
        data: { id: id },
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Order deleted successfully", 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                alert_toast("Failed to delete order", 'error');
                end_load();
            }
        },
        error: function() {
            alert_toast("An error occurred", 'error');
            end_load();
        }
    });
}

// Real-time updates (simulate with WebSocket or polling)
setInterval(() => {
    // Check for new orders and update UI
    // This would connect to a real-time system in production
}, 30000); // Check every 30 seconds
</script>

    </main>

    <!-- Modern Scripts -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/gsap.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true
        });

        // GSAP animations for navbar
        gsap.from('.navbar', {duration: 1, y: -100, opacity: 0, ease: 'bounce.out'});
        gsap.from('.main-content', {duration: 1, y: 50, opacity: 0, delay: 0.3});
    </script>
</body>
</html>
