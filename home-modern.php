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
ob_end_flush();
?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo $_SESSION['system']['name'] ?> - Dashboard</title>
    
    <?php include('./header-modern.php'); ?>
</head>

<body class="modern-dashboard">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark modern-navbar fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-coffee me-2"></i>
                <?php echo $_SESSION['system']['name']; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="home-modern.php">
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
            <!-- Welcome Section with Modern Design -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="modern-card welcome-card" data-aos="fade-up">
                        <div class="card-body text-center py-5">
                            <div class="welcome-icon mb-3">
                                <i class="fas fa-coffee"></i>
                            </div>
                            <h2 class="welcome-title mb-2">Welcome back, <?php echo $user_name; ?>!</h2>
                            <p class="welcome-subtitle text-muted">Here's what's happening at your cafe today</p>
                            <div class="welcome-date">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <?php echo date('l, F j, Y'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Dashboard Statistics -->
    <div class="row mb-4">
        <?php
        // Get today's statistics
        $today = date('Y-m-d');
        
        // Today's Orders
        $today_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(date_created) = '$today'")->fetch_array();
        
        // Today's Revenue
        $today_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(date_created) = '$today' AND amount_tendered > 0")->fetch_array();
        
        // Total Products
        $total_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 1")->fetch_array();
        
        // Pending Orders
        $pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE amount_tendered = 0")->fetch_array();
        ?>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stats-number" data-target="<?php echo $today_orders['count'] ?>">
                    <?php echo $today_orders['count'] ?>
                </div>
                <div class="stats-label">Today's Orders</div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up text-success me-1"></i>
                    <small class="text-success">+12% from yesterday</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-icon">
                    <i class="fas fa-money-bill"></i>
                </div>
                <div class="stats-number" data-target="<?php echo $today_revenue['total'] ?? 0 ?>">
                                    <div class="stats-number text-success">
                    ৳<?php echo number_format($today_revenue['total'] ?? 0, 2) ?>
                </div>
                </div>
                <div class="stats-label">Today's Revenue</div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up text-success me-1"></i>
                    <small class="text-success">+8% from yesterday</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card" data-aos="fade-up" data-aos-delay="300">
                <div class="stats-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stats-number" data-target="<?php echo $total_products['count'] ?>">
                    <?php echo $total_products['count'] ?>
                </div>
                <div class="stats-label">Active Products</div>
                <div class="stats-trend">
                    <i class="fas fa-minus text-warning me-1"></i>
                    <small class="text-warning">No change</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stats-card" data-aos="fade-up" data-aos-delay="400">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number" data-target="<?php echo $pending_orders['count'] ?>">
                    <?php echo $pending_orders['count'] ?>
                </div>
                <div class="stats-label">Pending Orders</div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-down text-danger me-1"></i>
                    <small class="text-danger">-5% from yesterday</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="row mb-4">
        <!-- Sales Chart -->
        <div class="col-lg-8 mb-4">
            <div class="modern-card" data-aos="fade-right">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Sales Overview
                    </h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Last 7 Days
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Last 7 Days</a></li>
                            <li><a class="dropdown-item" href="#">Last 30 Days</a></li>
                            <li><a class="dropdown-item" href="#">This Month</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="modern-card" data-aos="fade-left">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2 text-warning"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="billing/index.php" class="btn btn-modern btn-success">
                            <i class="fas fa-plus me-2"></i>
                            New Order
                        </a>
                        <a href="products-modern.php?page=products" class="btn btn-modern">
                            <i class="fas fa-box me-2"></i>
                            Manage Products
                        </a>
                        <a href="sales_report-modern.php?page=sales_report" class="btn btn-modern btn-warning">
                            <i class="fas fa-chart-bar me-2"></i>
                            View Reports
                        </a>
                        <a href="categories-modern.php?page=categories" class="btn btn-modern">
                            <i class="fas fa-tags me-2"></i>
                            Categories
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card" data-aos="fade-up">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2 text-info"></i>
                        Recent Orders
                    </h5>
                    <a href="index.php?page=orders" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table modern-table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_orders = $conn->query("SELECT * FROM orders ORDER BY date_created DESC LIMIT 5");
                                while($order = $recent_orders->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo str_pad($order['order_number'], 4, '0', STR_PAD_LEFT) ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($order['date_created'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong class="text-success">৳<?php echo number_format($order['total_amount'], 2) ?></strong>
                                    </td>
                                    <td>
                                        <?php if($order['amount_tendered'] > 0): ?>
                                            <span class="badge badge-modern badge-success">
                                                <i class="fas fa-check-circle me-1"></i>Paid
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-modern badge-warning">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary view_order" data-id="<?php echo $order['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.href='billing/index.php?id=<?php echo $order['id'] ?>'">
                                                <i class="fas fa-edit"></i>
                                            </button>
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

    <!-- Top Products -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="modern-card" data-aos="fade-right">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2 text-warning"></i>
                        Top Selling Products
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    $top_products = $conn->query("
                        SELECT p.name, p.price, SUM(oi.qty) as total_sold, SUM(oi.amount) as total_revenue
                        FROM products p
                        LEFT JOIN order_items oi ON p.id = oi.product_id
                        LEFT JOIN orders o ON oi.order_id = o.id
                        WHERE o.amount_tendered > 0
                        GROUP BY p.id
                        ORDER BY total_sold DESC
                        LIMIT 5
                    ");
                    
                    while($product = $top_products->fetch_assoc()):
                    ?>
                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded" style="background: rgba(52, 152, 219, 0.1);">
                        <div>
                            <h6 class="mb-1"><?php echo $product['name'] ?></h6>
                            <small class="text-muted">৳<?php echo number_format($product['price'], 2) ?></small>
                        </div>
                        <div class="text-end">
                            <div class="text-primary fw-bold"><?php echo $product['total_sold'] ?? 0 ?> sold</div>
                            <small class="text-success">৳<?php echo number_format($product['total_revenue'] ?? 0, 2) ?></small>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-6 mb-4">
            <div class="modern-card" data-aos="fade-left">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2 text-info"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <div class="activity-item">
                            <div class="activity-icon bg-success">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="activity-content">
                                <h6 class="mb-1">New order received</h6>
                                <p class="text-muted mb-1">Order #1234 - ৳45.50</p>
                                <small class="text-muted">5 minutes ago</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-info">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="activity-content">
                                <h6 class="mb-1">Product updated</h6>
                                <p class="text-muted mb-1">Iced Coffee price changed to ৳3.50</p>
                                <small class="text-muted">2 hours ago</small>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="activity-icon bg-warning">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="activity-content">
                                <h6 class="mb-1">Staff login</h6>
                                <p class="text-muted mb-1">Sarah logged into the system</p>
                                <small class="text-muted">3 hours ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<button class="fab" onclick="location.href='billing/index.php'" title="Quick Order">
    <i class="fas fa-plus"></i>
</button>

<style>
.welcome-card {
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(155, 89, 182, 0.1));
    border: none;
}

.welcome-icon i {
    font-size: 4rem;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.welcome-title {
    color: var(--primary-color);
    font-weight: 700;
}

.welcome-date {
    color: #666;
    font-weight: 500;
}

.activity-timeline {
    position: relative;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    position: relative;
}

.activity-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 20px;
    top: 40px;
    width: 2px;
    height: calc(100% + 10px);
    background: #e9ecef;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.activity-content h6 {
    margin-bottom: 5px;
    color: var(--primary-color);
}

.activity-content p {
    margin-bottom: 3px;
    font-size: 0.9rem;
}

.stats-trend {
    margin-top: 10px;
}
</style>

<script>
$(document).ready(function() {
    // Initialize AOS (Animate On Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
    });

    // Initialize Chart.js for sales overview with dynamic data
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Sales (৳)',
                data: [],
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '৳' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Load dynamic chart data
    function loadSalesChartData() {
        $.ajax({
            url: 'ajax.php',
            method: 'POST',
            data: {action: 'get_weekly_sales_data'},
            dataType: 'json',
            success: function(data) {
                salesChart.data.labels = data.labels;
                salesChart.data.datasets[0].data = data.sales;
                salesChart.update();
            },
            error: function() {
                // Fallback with last 7 days data from database
                const last7Days = [];
                const salesData = [];
                
                <?php
                // Generate last 7 days data
                for($i = 6; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $day_sales = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(date_created) = '$date' AND amount_tendered > 0")->fetch_array();
                    $day_name = date('D', strtotime($date));
                    echo "last7Days.push('$day_name');";
                    echo "salesData.push(" . ($day_sales['total'] ?? 0) . ");";
                }
                ?>
                
                salesChart.data.labels = last7Days;
                salesChart.data.datasets[0].data = salesData;
                salesChart.update();
            }
        });
    }

    // Initialize chart data
    loadSalesChartData();

    // View order functionality
    $('.view_order').click(function() {
        uni_modal("Order Details", "view_order.php?id=" + $(this).attr('data-id'), "mid-large");
    });

    // Add real-time clock
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        $('.welcome-date').html(`<i class="fas fa-calendar-alt me-2"></i>${now.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        })} - ${timeString}`);
    }
    
    updateClock();
    setInterval(updateClock, 1000);

    // Add smooth hover effects for stats cards
    $('.stats-card').hover(
        function() {
            $(this).find('.stats-icon').addClass('animate__animated animate__pulse');
        },
        function() {
            $(this).find('.stats-icon').removeClass('animate__animated animate__pulse');
        }
    );
});
</script>

    </main>

    <!-- Modern Scripts -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/gsap.min.js"></script>
    <script src="assets/js/chart.min.js"></script>
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
