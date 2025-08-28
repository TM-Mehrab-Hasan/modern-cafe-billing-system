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
                            <li><a class="dropdown-item" href="manage_user.php"><i class="fas fa-user-cog me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="users.php"><i class="fas fa-users me-2"></i> Users</a></li>
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
                                <i class="fas fa-chart-bar me-3 text-primary"></i>
                                Sales Analytics
                            </h2>
                            <p class="page-subtitle text-muted mb-0">
                                Comprehensive sales reporting and analytics
                            </p>
                        </div>
                        <div class="page-actions">
                            <div class="dropdown me-2">
                                <button class="btn btn-modern dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-calendar me-2"></i>
                                    This Month
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-period="today">Today</a></li>
                                    <li><a class="dropdown-item" href="#" data-period="yesterday">Yesterday</a></li>
                                    <li><a class="dropdown-item" href="#" data-period="week">This Week</a></li>
                                    <li><a class="dropdown-item" href="#" data-period="month">This Month</a></li>
                                    <li><a class="dropdown-item" href="#" data-period="year">This Year</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" data-period="custom">Custom Range</a></li>
                                </ul>
                            </div>
                            <button class="btn btn-modern btn-success" onclick="exportReport()">
                                <i class="fas fa-download me-2"></i>
                                Export Report
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
                <div class="stats-number"><?php echo number_format(today_revenue['total'] ?? 0, 2) ?></div>
                <div class="stats-label">Today's Revenue</div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="text-success">+8.2%</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="300">
                <div class="stats-icon text-primary">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stats-number"><?php echo total_orders['count'] ?></div>
                <div class="stats-label">Total Orders</div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="text-success">+15.3%</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="400">
                <div class="stats-icon text-warning">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stats-number"><?php echo number_format(avg_order_value, 2) ?></div>
                <div class="stats-label">Avg Order Value</div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-down text-danger"></i>
                    <span class="text-danger">-2.1%</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="500">
                <div class="stats-icon text-purple">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stats-number"><?php echo month_orders['count'] ?></div>
                <div class="stats-label">Month Orders</div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="text-success">+24.7%</span>
                </div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card analytics" data-aos="zoom-in" data-aos-delay="600">
                <div class="stats-icon text-danger">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stats-number">94.2%</div>
                <div class="stats-label">Success Rate</div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <span class="text-success">+1.2%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8 mb-4">
            <div class="modern-card" data-aos="fade-right">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-area me-2 text-primary"></i>
                        Revenue Trends
                    </h5>
                    <div class="chart-controls">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary active" data-chart="daily">Daily</button>
                            <button type="button" class="btn btn-outline-secondary" data-chart="weekly">Weekly</button>
                            <button type="button" class="btn btn-outline-secondary" data-chart="monthly">Monthly</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Sales Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="modern-card" data-aos="fade-left">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-success"></i>
                        Sales by Category
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <!-- Top Products -->
        <div class="col-lg-6 mb-4">
            <div class="modern-card" data-aos="fade-up">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy me-2 text-warning"></i>
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
                        LIMIT 10
                    ");
                    
                    rank = 1;
                    while(product = top_products->fetch_assoc()):
                    ?>
                    <div class="product-rank-item" data-aos="fade-in" data-aos-delay="<?php echo rank * 100 ?>">
                        <div class="rank-number">
                            <span class="rank"><?php echo rank ?></span>
                        </div>
                        <div class="product-details">
                            <h6 class="product-name"><?php echo product['name'] ?></h6>
                            <div class="product-stats">
                                <span class="qty-sold"><?php echo product['total_sold'] ?? 0 ?> sold</span>
                                <span class="revenue"><?php echo number_format(product['total_revenue'] ?? 0, 2) ?></span>
                            </div>
                        </div>
                        <div class="product-progress">
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo (rank == 1) ? 100 : (100 - (rank * 10)) ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <?php 
                    rank++;
                    endwhile; 
                    ?>
                </div>
            </div>
        </div>

        <!-- Sales Timeline -->
        <div class="col-lg-6 mb-4">
            <div class="modern-card" data-aos="fade-up" data-aos-delay="200">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2 text-info"></i>
                        Sales Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php
                        recent_sales = conn->query("
                            SELECT o.*, COUNT(oi.id) as item_count
                            FROM orders o
                            LEFT JOIN order_items oi ON o.id = oi.order_id
                            WHERE o.amount_tendered > 0
                            GROUP BY o.id
                            ORDER BY o.date_created DESC
                            LIMIT 8
                        ");
                        
                        while(sale = recent_sales->fetch_assoc()):
                        ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <strong>Order #<?php echo str_pad(sale['order_number'], 4, '0', STR_PAD_LEFT) ?></strong>
                                    <span class="timeline-time"><?php echo date('g:i A', strtotime(sale['date_created'])) ?></span>
                                </div>
                                <div class="timeline-body">
                                    <span class="amount"><?php echo number_format(sale['total_amount'], 2) ?></span>
                                    <span class="items"><?php echo sale['item_count'] ?> items</span>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports Table -->
    <div class="row">
        <div class="col-12">
            <div class="modern-card" data-aos="fade-up">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>
                        Detailed Sales Report
                    </h5>
                    <div class="table-actions">
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="refreshReport()">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="exportTableData()">
                            <i class="fas fa-file-excel"></i> Export
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table modern-table table-hover" id="salesReportTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order #</th>
                                    <th>Items</th>
                                    <th>Subtotal</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                detailed_orders = conn->query("
                                    SELECT o.*, COUNT(oi.id) as item_count
                                    FROM orders o
                                    LEFT JOIN order_items oi ON o.id = oi.order_id
                                    WHERE o.amount_tendered > 0
                                    GROUP BY o.id
                                    ORDER BY o.date_created DESC
                                    LIMIT 100
                                ");
                                
                                while(order = detailed_orders->fetch_assoc()):
                                    tax_amount = order['total_amount'] * 0.08; // Assuming 8% tax
                                    subtotal = order['total_amount'] - tax_amount;
                                ?>
                                <tr>
                                    <td>
                                        <div class="order-date">
                                            <?php echo date('M j, Y', strtotime(order['date_created'])) ?>
                                            <br>
                                            <small class="text-muted"><?php echo date('g:i A', strtotime(order['date_created'])) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>#<?php echo str_pad(order['order_number'], 4, '0', STR_PAD_LEFT) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-light"><?php echo order['item_count'] ?> items</span>
                                    </td>
                                    <td><?php echo number_format(subtotal, 2) ?></td>
                                    <td><?php echo number_format(tax_amount, 2) ?></td>
                                    <td>
                                        <strong class="text-success"><?php echo number_format(order['total_amount'], 2) ?></strong>
                                    </td>
                                    <td><?php echo number_format(order['amount_tendered'], 2) ?></td>
                                    <td>
                                        <span class="badge badge-modern badge-success">Completed</span>
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
.stats-card.analytics {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.8));
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stats-card.analytics::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.stats-card.analytics:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.stats-trend {
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

.stats-trend i {
    margin-right: 0.25rem;
}

.chart-controls .btn {
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
}

.chart-controls .btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.product-rank-item {
    display: flex;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
    transition: all 0.3s ease;
}

.product-rank-item:hover {
    background: rgba(52, 152, 219, 0.05);
    border-radius: 8px;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
}

.rank-number {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.rank-number .rank {
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
}

.product-details {
    flex: 1;
}

.product-name {
    margin: 0 0 0.25rem 0;
    color: var(--primary-color);
    font-weight: 600;
}

.product-stats {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
}

.qty-sold {
    color: #666;
}

.revenue {
    color: var(--success-color);
    font-weight: 600;
}

.product-progress {
    width: 100px;
}

.progress {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(90deg, var(--success-color), var(--warning-color));
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.timeline {
    max-height: 400px;
    overflow-y: auto;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 7px;
    top: 30px;
    width: 2px;
    height: calc(100% + 0.5rem);
    background: #e9ecef;
}

.timeline-marker {
    width: 16px;
    height: 16px;
    background: var(--accent-color);
    border-radius: 50%;
    margin-right: 1rem;
    margin-top: 0.25rem;
    flex-shrink: 0;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.timeline-content {
    flex: 1;
    background: rgba(255, 255, 255, 0.7);
    padding: 0.75rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.timeline-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.timeline-time {
    font-size: 0.8rem;
    color: #666;
    margin-left: auto;
}

.timeline-body {
    display: flex;
    gap: 1rem;
}

.timeline-body .amount {
    color: var(--success-color);
    font-weight: 600;
}

.timeline-body .items {
    color: #666;
    font-size: 0.9rem;
}

.order-date strong {
    color: var(--primary-color);
}

.text-purple {
    color: #9b59b6 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .page-actions {
        margin-top: 1rem;
    }
    
    .chart-controls {
        margin-top: 1rem;
    }
    
    .product-rank-item {
        flex-direction: column;
        text-align: center;
    }
    
    .product-progress {
        width: 100%;
        margin-top: 0.5rem;
    }
    
    .timeline-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .timeline-time {
        margin-left: 0;
        margin-top: 0.25rem;
    }
}

/* Animation for chart loading */
@keyframes chartLoad {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.chart-container {
    animation: chartLoad 0.8s ease-out;
}
</style>

<script>
(document).ready(function() {
    // Initialize AOS
    AOS.init({
        duration: 600,
        easing: 'ease-in-out',
        once: true
    });

    // Initialize DataTable
    ('#salesReportTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'desc']], // Sort by date
        language: {
            search: "",
            searchPlaceholder: "Search orders...",
        },
        columnDefs: [
            { type: 'date', targets: 0 }
        ]
    });

    // Initialize Charts
    initializeCharts();
    
    // Chart control handlers
    ('.chart-controls .btn').click(function() {
        ('.chart-controls .btn').removeClass('active');
        (this).addClass('active');
        
        const chartType = (this).data('chart');
        updateRevenueChart(chartType);
    });

    // Period selection handlers
    ('[data-period]').click(function(e) {
        e.preventDefault();
        const period = (this).data('period');
        updateReportPeriod(period);
    });
});

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    window.revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue',
                data: [450, 520, 380, 650, 720, 890, 670],
                borderColor: 'rgb(52, 152, 219)',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(52, 152, 219)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgb(52, 152, 219)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '' + value;
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    window.categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Beverages', 'Meals', 'Desserts', 'Snacks'],
            datasets: [{
                data: [35, 30, 20, 15],
                backgroundColor: [
                    'rgb(52, 152, 219)',
                    'rgb(46, 204, 113)',
                    'rgb(241, 196, 15)',
                    'rgb(231, 76, 60)'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
}

function updateRevenueChart(type) {
    let newData, newLabels;
    
    switch(type) {
        case 'weekly':
            newLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            newData = [2800, 3200, 2900, 3500];
            break;
        case 'monthly':
            newLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            newData = [12000, 15000, 13500, 16000, 18000, 17500];
            break;
        default: // daily
            newLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            newData = [450, 520, 380, 650, 720, 890, 670];
    }
    
    window.revenueChart.data.labels = newLabels;
    window.revenueChart.data.datasets[0].data = newData;
    window.revenueChart.update('active');
}

function updateReportPeriod(period) {
    start_load();
    
    // Simulate API call to update data
    setTimeout(() => {
        end_load();
        alert_toast(`Report updated for {period}`, 'success');
        
        // In real implementation, this would update all charts and tables
        // with new data from the server
    }, 1000);
}

function exportReport() {
    start_load();
    
    // Simulate export process
    setTimeout(() => {
        end_load();
        alert_toast('Report exported successfully', 'success');
        
        // In real implementation, this would generate and download the report
        const link = document.createElement('a');
        link.href = 'data:text/plain;charset=utf-8,Sales Report - ' + new Date().toISOString();
        link.download = 'sales-report-' + new Date().toISOString().split('T')[0] + '.csv';
        link.click();
    }, 2000);
}

function refreshReport() {
    start_load();
    
    setTimeout(() => {
        location.reload();
    }, 500);
}

function exportTableData() {
    const table = ('#salesReportTable').DataTable();
    const data = table.buttons.exportData();
    
    // Convert to CSV
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add headers
    csvContent += data.header.join(",") + "\r\n";
    
    // Add data rows
    data.body.forEach(row => {
        csvContent += row.join(",") + "\r\n";
    });
    
    // Download
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "sales-report-detailed.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    alert_toast('Table data exported successfully', 'success');
}

// Real-time data updates (simulate with WebSocket)
function startRealTimeUpdates() {
    setInterval(() => {
        // In real implementation, this would receive data via WebSocket
        // and update the charts and metrics in real-time
        
        // Example: Update today's revenue randomly
        const todayRevenue = document.querySelector('.stats-card:nth-child(2) .stats-number');
        if (todayRevenue) {
            const currentValue = parseFloat(todayRevenue.textContent.replace('', '').replace(',', ''));
            const newValue = currentValue + (Math.random() * 50 - 25); // Random change
            todayRevenue.textContent = '' + newValue.toFixed(2);
        }
    }, 30000); // Update every 30 seconds
}

// Start real-time updates
startRealTimeUpdates();
</script>
