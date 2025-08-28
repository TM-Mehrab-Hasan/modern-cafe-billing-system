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
    <title><?php echo $_SESSION['system']['name'] ?> - Product Management</title>
    
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
                        <a class="nav-link active" href="products-modern.php">
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
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card page-header" data-aos="fade-down">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="page-title mb-1">
                                <i class="fas fa-box-open me-3 text-primary"></i>
                                Product Management
                            </h2>
                            <p class="page-subtitle text-muted mb-0">
                                Manage your cafe menu items and pricing
                            </p>
                        </div>
                        <div class="page-actions">
                            <?php if($isAdmin): ?>
                            <button class="btn btn-modern btn-success me-2" id="new_product" data-aos="fade-left">
                                <i class="fas fa-plus me-2"></i>
                                Add Product
                            </button>
                            <button class="btn btn-modern btn-secondary" id="import_products" data-aos="fade-left" data-aos-delay="100">
                                <i class="fas fa-upload me-2"></i>
                                Import CSV
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Statistics -->
    <div class="row mb-4">
        <?php
        $total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_array();
        $active_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 1")->fetch_array();
        $inactive_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE status = 0")->fetch_array();
        $avg_price = $conn->query("SELECT AVG(price) as avg_price FROM products WHERE status = 1")->fetch_array();
        $categories_count = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_array();
        ?>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card mini" data-aos="zoom-in" data-aos-delay="100">
                <div class="stats-icon text-primary">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stats-number"><?php echo $total_products['count'] ?></div>
                <div class="stats-label">Total Products</div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card mini" data-aos="zoom-in" data-aos-delay="200">
                <div class="stats-icon text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number"><?php echo $active_products['count'] ?></div>
                <div class="stats-label">Active Products</div>
            </div>
        </div>
        
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stats-card mini" data-aos="zoom-in" data-aos-delay="300">
                <div class="stats-icon text-warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div class="stats-number"><?php echo $inactive_products['count'] ?></div>
                <div class="stats-label">Inactive Products</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="stats-card mini" data-aos="zoom-in" data-aos-delay="400">
                <div class="stats-icon text-info">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stats-number">৳<?php echo number_format($avg_price['avg_price'] ?? 0, 2) ?></div>
                <div class="stats-label">Average Price</div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
            <div class="stats-card mini" data-aos="zoom-in" data-aos-delay="500">
                <div class="stats-icon text-purple">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stats-number"><?php echo $categories_count['count'] ?></div>
                <div class="stats-label">Categories</div>
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
                                <input type="text" class="form-control" id="productSearch" placeholder="Search products...">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                            <select class="form-select" id="categoryFilter">
                                <option value="">All Categories</option>
                                <?php
                                $categories = $conn->query("SELECT * FROM categories ORDER BY name");
                                while($category = $categories->fetch_assoc()):
                                ?>
                                <option value="<?php echo $category['id'] ?>"><?php echo $category['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                            <select class="form-select" id="priceFilter">
                                <option value="">All Prices</option>
                                <option value="0-10">৳0 - ৳10</option>
                                <option value="10-25">৳10 - ৳25</option>
                                <option value="25-50">৳25 - ৳50</option>
                                <option value="50+">৳50+</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3 mb-lg-0">
                            <select class="form-select" id="sortBy">
                                <option value="name">Sort by Name</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="newest">Newest First</option>
                            </select>
                        </div>
                        <div class="col-lg-1 col-md-6">
                            <button class="btn btn-modern w-100" onclick="applyFilters()">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="view-toggle" data-aos="fade-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary active" id="gridView">
                            <i class="fas fa-th me-1"></i> Grid
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="listView">
                            <i class="fas fa-list me-1"></i> List
                        </button>
                    </div>
                </div>
                <div class="bulk-actions" data-aos="fade-left" style="display: none;">
                    <span class="me-3 text-muted" id="selectedCount">0 selected</span>
                    <button class="btn btn-sm btn-outline-success me-2" onclick="bulkAction('activate')">
                        <i class="fas fa-check"></i> Activate
                    </button>
                    <button class="btn btn-sm btn-outline-warning me-2" onclick="bulkAction('deactivate')">
                        <i class="fas fa-pause"></i> Deactivate
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid/List View -->
    <div class="row" id="productsContainer">
        <?php 
        $products = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.name");
        $delay = 0;
        while($product = $products->fetch_assoc()):
            $delay += 50;
        ?>
        
        <!-- Grid View Card -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 product-card" data-aos="fade-up" data-aos-delay="<?php echo $delay ?>">
            <div class="modern-card product-item" data-product-id="<?php echo $product['id'] ?>">
                <div class="product-image-container">
                    <img src="<?php echo $product['image_url'] ?? 'assets/images/placeholder-product.jpg' ?>" 
                         class="product-image" alt="<?php echo $product['name'] ?>">
                    <div class="product-overlay">
                        <div class="product-actions">
                            <?php if($isAdmin): ?>
                            <button class="btn btn-sm btn-light edit-product" data-id="<?php echo $product['id'] ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-light view-product" data-id="<?php echo $product['id'] ?>" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if($isAdmin): ?>
                            <button class="btn btn-sm btn-light delete-product" data-id="<?php echo $product['id'] ?>" title="Delete">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="product-checkbox">
                            <input type="checkbox" class="form-check-input product-select" value="<?php echo $product['id'] ?>">
                        </div>
                    </div>
                    <div class="product-status-badge">
                        <?php if($product['status'] == 1): ?>
                            <span class="badge badge-modern badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-modern badge-warning">Inactive</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="product-category">
                        <small class="text-muted">
                            <i class="fas fa-tag me-1"></i>
                            <?php echo $product['category_name'] ?? 'Uncategorized' ?>
                        </small>
                    </div>
                    <h5 class="product-name"><?php echo $product['name'] ?></h5>
                    <p class="product-description"><?php echo substr($product['description'], 0, 80) ?>...</p>
                    <div class="product-price-section">
                        <div class="product-price">৳<?php echo number_format($product['price'], 2) ?></div>
                        <div class="product-meta">
                            <small class="text-muted">
                                <?php if(isset($product['stock_quantity'])): ?>
                                    <i class="fas fa-boxes me-1"></i>
                                    Stock: <?php echo $product['stock_quantity'] ?? 'N/A' ?>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php endwhile; ?>
    </div>

    <!-- List View Table (Hidden by default) -->
    <div class="row" id="productsTable" style="display: none;">
        <div class="col-12">
            <div class="modern-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table modern-table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllList">
                                        </div>
                                    </th>
                                    <th width="10%">Image</th>
                                    <th width="25%">Product Name</th>
                                    <th width="15%">Category</th>
                                    <th width="10%">Price</th>
                                    <th width="10%">Stock</th>
                                    <th width="10%">Status</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $products->data_seek(0); // Reset query pointer
                                while($product = $products->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input product-select-list" type="checkbox" value="<?php echo $product['id'] ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <img src="<?php echo $product['image_url'] ?? 'assets/images/placeholder-product.jpg' ?>" 
                                             class="product-thumbnail" alt="<?php echo $product['name'] ?>">
                                    </td>
                                    <td>
                                        <div class="product-info">
                                            <strong><?php echo $product['name'] ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo substr($product['description'], 0, 50) ?>...</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="category-tag">
                                            <?php echo $product['category_name'] ?? 'Uncategorized' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="product-price">৳<?php echo number_format($product['price'], 2) ?></strong>
                                    </td>
                                    <td>
                                        <span class="stock-info">
                                            <?php echo $product['stock_quantity'] ?? 'N/A' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if($product['status'] == 1): ?>
                                            <span class="badge badge-modern badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-modern badge-warning">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <?php if($isAdmin): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-product" data-id="<?php echo $product['id'] ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-outline-secondary view-product" data-id="<?php echo $product['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if($isAdmin): ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-product" data-id="<?php echo $product['id'] ?>">
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

<style>
.product-item {
    height: 100%;
    transition: all 0.3s ease;
    border: none;
    overflow: hidden;
}

.product-item:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.product-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-item:hover .product-image {
    transform: scale(1.1);
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-item:hover .product-overlay {
    opacity: 1;
}

.product-actions {
    display: flex;
    gap: 0.5rem;
}

.product-actions .btn {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-checkbox {
    position: absolute;
    top: 10px;
    left: 10px;
}

.product-status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}

.product-category {
    margin-bottom: 0.5rem;
}

.product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    height: 2.2rem;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-description {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 1rem;
    height: 3rem;
    overflow: hidden;
}

.product-price-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.product-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--success-color);
}

.product-thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
}

.category-tag {
    background: rgba(52, 152, 219, 0.1);
    color: var(--accent-color);
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.stock-info {
    font-weight: 500;
    color: var(--primary-color);
}

.view-toggle .btn {
    border-radius: 8px;
}

.view-toggle .btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.bulk-actions {
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.text-purple {
    color: #9b59b6 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .product-item {
        margin-bottom: 1.5rem;
    }
    
    .page-actions {
        margin-top: 1rem;
    }
    
    .page-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .view-toggle, .bulk-actions {
        margin-bottom: 1rem;
        text-align: center;
    }
}

/* Loading states */
.product-item.loading {
    opacity: 0.6;
    pointer-events: none;
}

.product-item.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
</style>

<script>
$(document).ready(function() {
    // Initialize AOS
    AOS.init({
        duration: 600,
        easing: 'ease-in-out',
        once: true
    });

    // View toggle functionality
    $('#gridView').click(function() {
        $(this).addClass('active');
        $('#listView').removeClass('active');
        $('#productsContainer').show();
        $('#productsTable').hide();
    });

    $('#listView').click(function() {
        $(this).addClass('active');
        $('#gridView').removeClass('active');
        $('#productsContainer').hide();
        $('#productsTable').show();
        
        // Initialize DataTable if not already done
        if (!$.fn.DataTable.isDataTable('#productsTable table')) {
            $('#productsTable table').DataTable({
                responsive: true,
                pageLength: 25,
                order: [[2, 'asc']], // Sort by product name
                columnDefs: [
                    { orderable: false, targets: [0, 1, 7] }
                ]
            });
        }
    });

    // Product selection handling
    $('.product-select, .product-select-list').change(function() {
        updateBulkActions();
    });

    // Select all functionality
    $('#selectAllList').change(function() {
        $('.product-select-list').prop('checked', this.checked);
        updateBulkActions();
    });

    // Search functionality
    $('#productSearch').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterProducts();
    });

    // Filter change handlers
    $('#categoryFilter, #statusFilter, #priceFilter, #sortBy').change(function() {
        filterProducts();
    });

    // Event handlers
    setupEventHandlers();
});

function updateBulkActions() {
    const selectedCount = $('.product-select:checked, .product-select-list:checked').length;
    
    if (selectedCount > 0) {
        $('.bulk-actions').show();
        $('#selectedCount').text(selectedCount + ' selected');
    } else {
        $('.bulk-actions').hide();
    }
}

function filterProducts() {
    const searchTerm = $('#productSearch').val().toLowerCase();
    const categoryFilter = $('#categoryFilter').val();
    const statusFilter = $('#statusFilter').val();
    const priceFilter = $('#priceFilter').val();
    const sortBy = $('#sortBy').val();

    let visibleProducts = $('.product-card');
    
    // Apply filters
    visibleProducts.each(function() {
        let show = true;
        const $card = $(this);
        const productName = $card.find('.product-name').text().toLowerCase();
        const categoryName = $card.find('.product-category').text().toLowerCase();
        
        // Search filter
        if (searchTerm && !productName.includes(searchTerm) && !categoryName.includes(searchTerm)) {
            show = false;
        }
        
        // Add more filter logic here
        
        if (show) {
            $card.show();
        } else {
            $card.hide();
        }
    });
    
    // Apply sorting
    applySorting(sortBy);
}

function applySorting(sortBy) {
    const container = $('#productsContainer');
    const products = container.children('.product-card').get();
    
    products.sort(function(a, b) {
        switch(sortBy) {
            case 'price_low':
                return parseFloat($(a).find('.product-price').text().replace('$', '')) - 
                       parseFloat($(b).find('.product-price').text().replace('$', ''));
            case 'price_high':
                return parseFloat($(b).find('.product-price').text().replace('$', '')) - 
                       parseFloat($(a).find('.product-price').text().replace('$', ''));
            case 'name':
            default:
                return $(a).find('.product-name').text().localeCompare($(b).find('.product-name').text());
        }
    });
    
    $.each(products, function(index, item) {
        container.append(item);
    });
}

function setupEventHandlers() {
    // New product
    $('#new_product').click(function() {
        uni_modal("New Product", "manage_product.php", "large");
    });

    // Import products
    $('#import_products').click(function() {
        uni_modal("Import Products", "import_products.php", "mid-large");
    });

    // Edit product
    $('.edit-product').click(function() {
        uni_modal("Edit Product", "manage_product.php?id=" + $(this).attr('data-id'), "large");
    });

    // View product
    $('.view-product').click(function() {
        uni_modal("Product Details", "view_product.php?id=" + $(this).attr('data-id'), "mid-large");
    });

    // Delete product
    $('.delete-product').click(function() {
        const productId = $(this).attr('data-id');
        const productName = $(this).closest('.product-card, tr').find('.product-name, .product-info strong').text();
        
        _conf(`Are you sure you want to delete "${productName}"?`, "delete_product", [productId]);
    });
}

function bulkAction(action) {
    const selectedProducts = $('.product-select:checked, .product-select-list:checked').map(function() {
        return this.value;
    }).get();
    
    if (selectedProducts.length === 0) {
        alert_toast('Please select products first', 'warning');
        return;
    }
    
    let message = '';
    switch(action) {
        case 'activate':
            message = `Activate ${selectedProducts.length} products?`;
            break;
        case 'deactivate':
            message = `Deactivate ${selectedProducts.length} products?`;
            break;
        case 'delete':
            message = `Delete ${selectedProducts.length} products? This action cannot be undone.`;
            break;
    }
    
    _conf(message, "executeBulkAction", [action, selectedProducts]);
}

function executeBulkAction(action, productIds) {
    start_load();
    
    $.ajax({
        url: 'ajax.php?action=bulk_product_action',
        method: 'POST',
        data: {
            action: action,
            product_ids: productIds
        },
        success: function(resp) {
            if (resp == 1) {
                alert_toast(`Products ${action}d successfully`, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                alert_toast('Failed to perform bulk action', 'error');
                end_load();
            }
        },
        error: function() {
            alert_toast('An error occurred', 'error');
            end_load();
        }
    });
}

function delete_product(id) {
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_product',
        method: 'POST',
        data: { id: id },
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Product deleted successfully", 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                alert_toast("Failed to delete product", 'error');
                end_load();
            }
        },
        error: function() {
            alert_toast("An error occurred", 'error');
            end_load();
        }
    });
}

function applyFilters() {
    filterProducts();
    alert_toast('Filters applied', 'success');
}
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
