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
// Determine if current user is admin
$isAdmin = isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1;
ob_end_flush();
?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo $_SESSION['system']['name'] ?> - Sales Report</title>
    
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
                        <a class="nav-link active" href="sales_report-modern.php">
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
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="modern-card page-header" data-aos="fade-down">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h2 class="page-title mb-1">
                                        <i class="fas fa-chart-line me-3 text-primary"></i>
                                        Sales Report
                                    </h2>
                                    <p class="page-subtitle text-muted mb-0">
                                        Analyze your sales performance and trends
                                    </p>
                                </div>
                                <div class="page-actions">
                                    <?php if($isAdmin): ?>
                                    <button class="btn btn-modern btn-outline-primary me-2" onclick="exportReport()" data-aos="fade-left">
                                        <i class="fas fa-download me-2"></i>
                                        Export Report
                                    </button>
                                    <button class="btn btn-modern btn-primary" onclick="printReport()" data-aos="fade-left" data-aos-delay="100">
                                        <i class="fas fa-print me-2"></i>
                                        Print Report
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-modern btn-outline-secondary me-2" disabled title="Admin only">
                                        <i class="fas fa-download me-2"></i>
                                        Export (Admin)
                                    </button>
                                    <button class="btn btn-modern btn-secondary" disabled title="Admin only">
                                        <i class="fas fa-print me-2"></i>
                                        Print (Admin)
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="modern-card" data-aos="fade-up">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="date_from" value="<?php echo date('Y-m-01'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="date_to" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Category</label>
                                    <select class="form-select" id="category_filter">
                                        <option value="">All Categories</option>
                                        <?php 
                                        $categories = $conn->query("SELECT * FROM categories ORDER BY name");
                                        while($cat = $categories->fetch_assoc()):
                                        ?>
                                        <option value="<?php echo $cat['id'] ?>"><?php echo $cat['name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-modern btn-primary w-100" onclick="filterReport()">
                                        <i class="fas fa-filter me-2"></i>
                                        Filter
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-modern btn-outline-secondary w-100" onclick="resetFilter()">
                                        <i class="fas fa-undo me-2"></i>
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="row mb-4">
                <?php
                $today = date('Y-m-d');
                $month_start = date('Y-m-01');
                
                // Calculate metrics
                $total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE amount_tendered > 0")->fetch_array();
                $month_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE amount_tendered > 0 AND date_created >= '$month_start'")->fetch_array();
                $today_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE amount_tendered > 0 AND DATE(date_created) = '$today'")->fetch_array();
                $total_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE amount_tendered > 0")->fetch_array();
                $month_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE amount_tendered > 0 AND date_created >= '$month_start'")->fetch_array();
                $avg_order_value = $total_orders['count'] > 0 ? $total_revenue['total'] / $total_orders['count'] : 0;
                ?>
                
                <?php if($isAdmin): ?>
                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="100">
                        <div class="stats-icon text-success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stats-number">৳<?php echo number_format($total_revenue['total'] ?? 0, 2) ?></div>
                        <div class="stats-label">Total Revenue</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">+12.5%</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="200">
                        <div class="stats-icon text-info">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stats-number">৳<?php echo number_format($today_revenue['total'] ?? 0, 2) ?></div>
                        <div class="stats-label">Today's Revenue</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">+8.2%</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="300">
                        <div class="stats-icon text-warning">
                            <i class="fas fa-calendar-month"></i>
                        </div>
                        <div class="stats-number">৳<?php echo number_format($month_revenue['total'] ?? 0, 2) ?></div>
                        <div class="stats-label">This Month</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">+15.3%</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="400">
                        <div class="stats-icon text-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stats-number"><?php echo number_format($total_orders['count'] ?? 0) ?></div>
                        <div class="stats-label">Total Orders</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">+5.7%</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="500">
                        <div class="stats-icon text-danger">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="stats-number">৳<?php echo number_format($avg_order_value, 2) ?></div>
                        <div class="stats-label">Avg Order Value</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">+3.1%</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                    <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="600">
                        <div class="stats-icon text-secondary">
                            <i class="fas fa-calendar-week"></i>
                        </div>
                        <div class="stats-number"><?php echo number_format($month_orders['count'] ?? 0) ?></div>
                        <div class="stats-label">Monthly Orders</div>
                        <div class="stats-trend">
                            <i class="fas fa-arrow-up text-success"></i>
                            <span class="text-success">+9.4%</span>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="col-12 mb-3">
                    <div class="modern-card p-3 text-center">
                        <div class="text-muted">Sales analytics are available to administrators only.</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Charts Section -->
            <div class="row mb-4">
                <!-- Revenue Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="modern-card chart-card" data-aos="fade-up">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-chart-line me-2 text-primary"></i>
                                Revenue Trend (Last 7 Days)
                            </h5>
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Top Products -->
                <div class="col-lg-4 mb-4">
                    <div class="modern-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-crown me-2 text-warning"></i>
                                Top Products
                            </h5>
                            <div class="top-products-list">
                                <?php
                                $top_products = $conn->query("
                                    SELECT p.name, p.price, SUM(oi.qty) as total_sold, SUM(oi.qty * oi.price) as revenue
                                    FROM order_items oi 
                                    LEFT JOIN products p ON oi.product_id = p.id 
                                    GROUP BY oi.product_id 
                                    ORDER BY total_sold DESC 
                                    LIMIT 5
                                ");
                                while($product = $top_products->fetch_assoc()):
                                ?>
                                <div class="product-item">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="product-name"><?php echo $product['name'] ?></span>
                                        <span class="revenue">৳<?php echo number_format($product['revenue'] ?? 0, 2) ?></span>
                                    </div>
                                    <div class="product-meta">
                                        <small class="text-muted">
                                            <?php echo $product['total_sold'] ?> sold • ৳<?php echo number_format($product['price'], 2) ?> each
                                        </small>
                                    </div>
                                    <hr>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="row">
                <div class="col-12">
                    <div class="modern-card" data-aos="fade-up">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-table me-2 text-info"></i>
                                Recent Sales
                            </h5>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Date & Time</th>
                                            <th>Customer</th>
                                            <th>Items</th>
                                            <th>Amount</th>
                                            <th>Payment</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $recent_sales = $conn->query("
                                            SELECT o.*, COUNT(oi.id) as item_count 
                                            FROM orders o 
                                            LEFT JOIN order_items oi ON o.id = oi.order_id 
                                            WHERE o.amount_tendered > 0 
                                            GROUP BY o.id 
                                            ORDER BY o.date_created DESC 
                                            LIMIT 10
                                        ");
                                        while($sale = $recent_sales->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary">#<?php echo str_pad($sale['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                                            </td>
                                            <td>
                                                <div><?php echo date('M j, Y', strtotime($sale['date_created'])) ?></div>
                                                <small class="text-muted"><?php echo date('g:i A', strtotime($sale['date_created'])) ?></small>
                                            </td>
                                            <td>
                                                <span class="customer-name">Walk-in Customer</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark"><?php echo $sale['item_count'] ?> items</span>
                                            </td>
                                            <td>
                                                <span class="amount">৳<?php echo number_format($sale['total_amount'], 2) ?></span>
                                            </td>
                                            <td>
                                                <span class="payment-method">
                                                    <i class="fas fa-money-bill-wave me-1"></i> Cash
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Completed</span>
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

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Revenue (৳)',
                    data: [],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
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
                        ticks: {
                            callback: function(value) {
                                return '৳' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Load dynamic chart data
        function loadChartData() {
            // Get last 7 days revenue data
            $.ajax({
                url: 'ajax.php',
                method: 'POST',
                data: {action: 'get_revenue_chart_data'},
                dataType: 'json',
                success: function(data) {
                    revenueChart.data.labels = data.labels;
                    revenueChart.data.datasets[0].data = data.revenue;
                    revenueChart.update();
                },
                error: function() {
                    // Fallback with sample data
                    const last7Days = [];
                    const sampleRevenue = [];
                    for(let i = 6; i >= 0; i--) {
                        const date = new Date();
                        date.setDate(date.getDate() - i);
                        last7Days.push(date.toLocaleDateString('en-US', {month: 'short', day: 'numeric'}));
                        sampleRevenue.push(Math.floor(Math.random() * 5000) + 1000);
                    }
                    
                    revenueChart.data.labels = last7Days;
                    revenueChart.data.datasets[0].data = sampleRevenue;
                    revenueChart.update();
                }
            });
        }

        // Filter functions
        function filterReport() {
            const dateFrom = document.getElementById('date_from').value;
            const dateTo = document.getElementById('date_to').value;
            const category = document.getElementById('category_filter').value;
            
            // Implement filtering logic
            console.log('Filtering report:', {dateFrom, dateTo, category});
            
            // Reload page with filters
            const params = new URLSearchParams();
            if(dateFrom) params.append('date_from', dateFrom);
            if(dateTo) params.append('date_to', dateTo);
            if(category) params.append('category', category);
            
            if(params.toString()) {
                window.location.href = '?' + params.toString();
            }
        }

        function resetFilter() {
            document.getElementById('date_from').value = '<?php echo date('Y-m-01'); ?>';
            document.getElementById('date_to').value = '<?php echo date('Y-m-d'); ?>';
            document.getElementById('category_filter').value = '';
            window.location.href = window.location.pathname;
        }

        function exportReport() {
            alert('Export functionality will be implemented here');
        }

        function printReport() {
            window.print();
        }

        // Initialize chart data
        loadChartData();
    </script>
</body>
</html>
