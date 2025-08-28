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
    <title><?php echo $_SESSION['system']['name'] ?> - Categories Management</title>
    
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
                        <a class="nav-link active" href="categories-modern.php">
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
                                <i class="fas fa-tags me-3 text-primary"></i>
                                Categories Management
                            </h2>
                            <p class="page-subtitle text-muted mb-0">
                                Organize and manage product categories
                            </p>
                        </div>
                        <div class="page-actions">
                            <?php if($isAdmin): ?>
                            <button class="btn btn-modern btn-success" onclick="openCategoryModal()">
                                <i class="fas fa-plus-circle me-2"></i>
                                Add Category
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row" id="categoriesGrid">
        <?php
        $categories = $conn->query("
            SELECT c.*, 
                   COUNT(p.id) as product_count,
                   COALESCE(AVG(p.price), 0) as avg_price
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            GROUP BY c.id 
            ORDER BY c.name ASC
        ");
        
        $delay = 0;
        while($category = $categories->fetch_assoc()):
            $delay += 100;
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="category-card modern-card" data-aos="zoom-in" data-aos-delay="<?php echo $delay ?>">
                <div class="category-header">
                    <div class="category-icon">
                        <i class="fas fa-<?php echo getCategoryIcon($category['name']) ?>"></i>
                    </div>
                            <div class="category-actions">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if($isAdmin): ?>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="editCategory(<?php echo $category['id'] ?>)">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="viewCategoryProducts(<?php echo $category['id'] ?>)">
                                        <i class="fas fa-eye me-2"></i>View Products
                                    </a>
                                </li>
                                <?php if($isAdmin): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteCategory(<?php echo $category['id'] ?>)">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="category-body">
                    <h5 class="category-name"><?php echo $category['name'] ?></h5>
                    <p class="category-description"><?php echo $category['description'] ?: 'No description available' ?></p>
                    
                    <div class="category-stats">
                        <div class="stat-item">
                            <span class="stat-value"><?php echo $category['product_count'] ?></span>
                            <span class="stat-label">Products</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">৳<?php echo number_format($category['avg_price'], 2) ?></span>
                            <span class="stat-label">Avg Price</span>
                        </div>
                    </div>
                </div>
                
                <div class="category-footer">
                    <button class="btn btn-modern btn-sm" onclick="viewCategoryProducts(<?php echo $category['id'] ?>)">
                        <i class="fas fa-box-open me-1"></i>
                        View Products
                    </button>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        
        <!-- Add New Category Card -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="add-category-card modern-card" data-aos="zoom-in" data-aos-delay="<?php echo $delay + 100 ?>" onclick="openCategoryModal()">
                <div class="add-category-content">
                    <div class="add-category-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h6 class="add-category-text">Add New Category</h6>
                    <p class="add-category-subtitle">Create a new product category</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card" data-aos="fade-up">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2 text-info"></i>
                        Category Analytics
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <canvas id="categoryDistributionChart" height="100"></canvas>
                        </div>
                        <div class="col-lg-4">
                            <div class="category-insights">
                                <div class="insight-item">
                                    <div class="insight-icon text-primary">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div class="insight-content">
                                        <h6>Total Categories</h6>
                                        <span class="insight-value"><?php echo $categories->num_rows ?></span>
                                    </div>
                                </div>
                                
                                <div class="insight-item">
                                    <div class="insight-icon text-success">
                                        <i class="fas fa-boxes"></i>
                                    </div>
                                    <div class="insight-content">
                                        <h6>Total Products</h6>
                                        <span class="insight-value">
                                            <?php 
                                            $total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_array();
                                            echo $total_products['count']; 
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="insight-item">
                                    <div class="insight-icon text-warning">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="insight-content">
                                        <h6>Most Popular</h6>
                                        <span class="insight-value">
                                            <?php
                                            $popular = $conn->query("
                                                SELECT c.name 
                                                FROM categories c 
                                                LEFT JOIN products p ON c.id = p.category_id 
                                                GROUP BY c.id 
                                                ORDER BY COUNT(p.id) DESC 
                                                LIMIT 1
                                            ")->fetch_array();
                                            echo $popular['name'] ?? 'N/A';
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modern-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-tag me-2"></i>
                    <span id="modalTitle">Add New Category</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <input type="hidden" id="categoryId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="categoryName" class="form-label">Category Name *</label>
                                <input type="text" class="form-control modern-input" id="categoryName" name="name" required>
                                <div class="form-hint">Enter a unique category name</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="categoryIcon" class="form-label">Icon</label>
                                <div class="icon-selector">
                                    <div class="icon-grid">
                                        <div class="icon-option" data-icon="coffee">
                                            <i class="fas fa-coffee"></i>
                                        </div>
                                        <div class="icon-option" data-icon="hamburger">
                                            <i class="fas fa-hamburger"></i>
                                        </div>
                                        <div class="icon-option" data-icon="ice-cream">
                                            <i class="fas fa-ice-cream"></i>
                                        </div>
                                        <div class="icon-option" data-icon="pizza-slice">
                                            <i class="fas fa-pizza-slice"></i>
                                        </div>
                                        <div class="icon-option" data-icon="wine-glass">
                                            <i class="fas fa-wine-glass"></i>
                                        </div>
                                        <div class="icon-option" data-icon="cookie">
                                            <i class="fas fa-cookie"></i>
                                        </div>
                                        <div class="icon-option" data-icon="fish">
                                            <i class="fas fa-fish"></i>
                                        </div>
                                        <div class="icon-option" data-icon="apple-alt">
                                            <i class="fas fa-apple-alt"></i>
                                        </div>
                                    </div>
                                    <input type="hidden" id="selectedIcon" name="icon" value="coffee">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="categoryDescription" class="form-label">Description</label>
                        <textarea class="form-control modern-input" id="categoryDescription" name="description" rows="3" placeholder="Optional category description"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="categoryColor" class="form-label">Theme Color</label>
                                <div class="color-selector">
                                    <div class="color-grid">
                                        <div class="color-option" data-color="#3498db" style="background: #3498db;"></div>
                                        <div class="color-option" data-color="#2ecc71" style="background: #2ecc71;"></div>
                                        <div class="color-option" data-color="#e74c3c" style="background: #e74c3c;"></div>
                                        <div class="color-option" data-color="#f39c12" style="background: #f39c12;"></div>
                                        <div class="color-option" data-color="#9b59b6" style="background: #9b59b6;"></div>
                                        <div class="color-option" data-color="#34495e" style="background: #34495e;"></div>
                                    </div>
                                    <input type="hidden" id="selectedColor" name="color" value="#3498db">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="categorySortOrder" class="form-label">Sort Order</label>
                                <input type="number" class="form-control modern-input" id="categorySortOrder" name="sort_order" min="0" value="0">
                                <div class="form-hint">Lower numbers appear first</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="category-preview">
                        <h6>Preview:</h6>
                        <div class="preview-card">
                            <div class="preview-icon">
                                <i class="fas fa-coffee"></i>
                            </div>
                            <div class="preview-content">
                                <h6 class="preview-name">Category Name</h6>
                                <p class="preview-description">Category description</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modern btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Products Modal -->
<div class="modal fade" id="productsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content modern-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="fas fa-boxes me-2"></i>
                    <span id="productsModalTitle">Category Products</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="categoryProductsList">
                    <!-- Products will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.category-card {
    height: 300px;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    overflow: hidden;
}

.category-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
}

.category-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 1.5rem 0 1.5rem;
}

.category-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 8px 20px rgba(52, 152, 219, 0.3);
}

.category-actions .btn {
    padding: 0.25rem 0.5rem;
}

.category-body {
    flex: 1;
    padding: 1rem 1.5rem;
}

.category-name {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.category-description {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.category-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 1rem;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary-color);
}

.stat-label {
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.category-footer {
    padding: 0 1.5rem 1.5rem 1.5rem;
}

.add-category-card {
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px dashed rgba(52, 152, 219, 0.3);
    background: rgba(52, 152, 219, 0.05);
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-category-card:hover {
    border-color: var(--primary-color);
    background: rgba(52, 152, 219, 0.1);
    transform: translateY(-2px);
}

.add-category-content {
    text-align: center;
}

.add-category-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    margin: 0 auto 1rem auto;
    opacity: 0.8;
}

.add-category-text {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.add-category-subtitle {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

.icon-selector .icon-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.icon-option {
    width: 50px;
    height: 50px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #666;
    cursor: pointer;
    transition: all 0.3s ease;
}

.icon-option:hover,
.icon-option.active {
    border-color: var(--primary-color);
    background: rgba(52, 152, 219, 0.1);
    color: var(--primary-color);
}

.color-selector .color-grid {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.color-option {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s ease;
}

.color-option:hover,
.color-option.active {
    border-color: white;
    box-shadow: 0 0 0 2px #333;
    transform: scale(1.1);
}

.category-preview {
    margin-top: 2rem;
    padding: 1rem;
    background: rgba(52, 152, 219, 0.05);
    border-radius: 8px;
    border: 1px solid rgba(52, 152, 219, 0.2);
}

.preview-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.preview-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.preview-content h6 {
    margin: 0 0 0.25rem 0;
    color: var(--primary-color);
}

.preview-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.insight-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    margin-bottom: 1rem;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.insight-item:hover {
    background: rgba(52, 152, 219, 0.05);
    border-color: var(--primary-color);
}

.insight-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1.25rem;
    background: rgba(255, 255, 255, 0.8);
}

.insight-content h6 {
    margin: 0 0 0.25rem 0;
    color: #666;
    font-size: 0.9rem;
}

.insight-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

/* Animation keyframes */
@keyframes fadeInScale {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.category-card {
    animation: fadeInScale 0.6s ease-out;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .category-card {
        height: auto;
        min-height: 250px;
    }
    
    .category-stats {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .stat-value {
        font-size: 1rem;
    }
    
    .icon-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
    
    .color-grid {
        flex-wrap: wrap;
    }
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

    // Initialize category distribution chart
    initializeCategoryChart();
    
    // Icon selector
    $('.icon-option').click(function() {
        $('.icon-option').removeClass('active');
        $(this).addClass('active');
        const icon = $(this).data('icon');
        $('#selectedIcon').val(icon);
        updatePreview();
    });
    
    // Color selector
    $('.color-option').click(function() {
        $('.color-option').removeClass('active');
        $(this).addClass('active');
        const color = $(this).data('color');
        $('#selectedColor').val(color);
        updatePreview();
    });
    
    // Form inputs change
    $('#categoryName, #categoryDescription').on('input', updatePreview);
    
    // Category form submission
    $('#categoryForm').submit(function(e) {
        e.preventDefault();
        saveCategoryData();
    });
});

function initializeCategoryChart() {
    const ctx = document.getElementById('categoryDistributionChart').getContext('2d');
    
    // Get category data from PHP
    const categoryData = [
        <?php
        $chart_categories = $conn->query("
            SELECT c.name, COUNT(p.id) as product_count
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            GROUP BY c.id 
            ORDER BY product_count DESC
        ");
        
        $chart_data = [];
        while($cat = $chart_categories->fetch_assoc()) {
            $chart_data[] = "{ label: '" . addslashes($cat['name']) . "', value: " . $cat['product_count'] . " }";
        }
        echo implode(',', $chart_data);
        ?>
    ];
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categoryData.map(item => item.label),
            datasets: [{
                label: 'Products',
                data: categoryData.map(item => item.value),
                backgroundColor: [
                    'rgba(52, 152, 219, 0.8)',
                    'rgba(46, 204, 113, 0.8)',
                    'rgba(241, 196, 15, 0.8)',
                    'rgba(231, 76, 60, 0.8)',
                    'rgba(155, 89, 182, 0.8)',
                    'rgba(52, 73, 94, 0.8)'
                ],
                borderColor: [
                    'rgb(52, 152, 219)',
                    'rgb(46, 204, 113)',
                    'rgb(241, 196, 15)',
                    'rgb(231, 76, 60)',
                    'rgb(155, 89, 182)',
                    'rgb(52, 73, 94)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
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
                    borderColor: 'rgba(255, 255, 255, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y + ' products';
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
                        stepSize: 1
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
}

function openCategoryModal(categoryId = null) {
    $('#categoryModal').modal('show');
    
    if (categoryId) {
        $('#modalTitle').text('Edit Category');
        loadCategoryData(categoryId);
    } else {
        $('#modalTitle').text('Add New Category');
        resetCategoryForm();
    }
}

function resetCategoryForm() {
    $('#categoryForm')[0].reset();
    $('#categoryId').val('');
    $('.icon-option').removeClass('active');
    $('.icon-option[data-icon="coffee"]').addClass('active');
    $('.color-option').removeClass('active');
    $('.color-option[data-color="#3498db"]').addClass('active');
    $('#selectedIcon').val('coffee');
    $('#selectedColor').val('#3498db');
    updatePreview();
}

function loadCategoryData(categoryId) {
    start_load();
    
    $.ajax({
        url: 'ajax.php?action=get_category',
        method: 'POST',
        data: { id: categoryId },
        dataType: 'json',
        success: function(resp) {
            end_load();
            
            if (resp.status == 1) {
                const category = resp.data;
                $('#categoryId').val(category.id);
                $('#categoryName').val(category.name);
                $('#categoryDescription').val(category.description);
                $('#categorySortOrder').val(category.sort_order || 0);
                
                // Set icon
                $('.icon-option').removeClass('active');
                $(`.icon-option[data-icon="${category.icon || 'coffee'}"]`).addClass('active');
                $('#selectedIcon').val(category.icon || 'coffee');
                
                // Set color
                $('.color-option').removeClass('active');
                $(`.color-option[data-color="${category.color || '#3498db'}"]`).addClass('active');
                $('#selectedColor').val(category.color || '#3498db');
                
                updatePreview();
            } else {
                alert_toast('Error loading category data', 'error');
            }
        },
        error: function() {
            end_load();
            alert_toast('Error loading category data', 'error');
        }
    });
}

function saveCategoryData() {
    start_load();
    
    const formData = new FormData($('#categoryForm')[0]);
    const action = $('#categoryId').val() ? 'update_category' : 'save_category';
    
    $.ajax({
        url: `ajax.php?action=${action}`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(resp) {
            end_load();
            
            if (resp.status == 1) {
                alert_toast(resp.msg, 'success');
                $('#categoryModal').modal('hide');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                alert_toast(resp.msg, 'error');
            }
        },
        error: function() {
            end_load();
            alert_toast('Error saving category', 'error');
        }
    });
}

function editCategory(categoryId) {
    openCategoryModal(categoryId);
}

function deleteCategory(categoryId) {
    Swal.fire({
        title: 'Delete Category',
        text: 'Are you sure you want to delete this category? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#e74c3c',
        background: 'rgba(255, 255, 255, 0.95)',
        backdrop: 'rgba(0, 0, 0, 0.8)'
    }).then((result) => {
        if (result.isConfirmed) {
            start_load();
            
            $.ajax({
                url: 'ajax.php?action=delete_category',
                method: 'POST',
                data: { id: categoryId },
                dataType: 'json',
                success: function(resp) {
                    end_load();
                    
                    if (resp.status == 1) {
                        alert_toast(resp.msg, 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        alert_toast(resp.msg, 'error');
                    }
                },
                error: function() {
                    end_load();
                    alert_toast('Error deleting category', 'error');
                }
            });
        }
    });
}

function viewCategoryProducts(categoryId) {
    $('#productsModal').modal('show');
    
    start_load();
    
    $.ajax({
        url: 'ajax.php?action=get_category_products',
        method: 'POST',
        data: { category_id: categoryId },
        success: function(resp) {
            end_load();
            $('#categoryProductsList').html(resp);
        },
        error: function() {
            end_load();
            $('#categoryProductsList').html('<div class="text-center p-4"><p>Error loading products</p></div>');
        }
    });
}

function updatePreview() {
    const name = $('#categoryName').val() || 'Category Name';
    const description = $('#categoryDescription').val() || 'Category description';
    const icon = $('#selectedIcon').val() || 'coffee';
    const color = $('#selectedColor').val() || '#3498db';
    
    $('.preview-name').text(name);
    $('.preview-description').text(description);
    $('.preview-icon i').attr('class', `fas fa-${icon}`);
    $('.preview-icon').css('background', `linear-gradient(135deg, ${color}, ${adjustColor(color, -20)})`);
}

function adjustColor(color, amount) {
    const num = parseInt(color.replace("#", ""), 16);
    const amt = Math.round(2.55 * amount);
    const R = (num >> 16) + amt;
    const B = (num >> 8 & 0x00FF) + amt;
    const G = (num & 0x0000FF) + amt;
    return "#" + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 + 
                   (B < 255 ? B < 1 ? 0 : B : 255) * 0x100 + 
                   (G < 255 ? G < 1 ? 0 : G : 255)).toString(16).slice(1);
}

// Initialize first icon and color as active
$(document).ready(function() {
    $('.icon-option[data-icon="coffee"]').addClass('active');
    $('.color-option[data-color="#3498db"]').addClass('active');
    updatePreview();
});
</script>

<?php
function getCategoryIcon($categoryName) {
    $icons = [
        'beverages' => 'coffee',
        'coffee' => 'coffee',
        'drinks' => 'wine-glass',
        'meals' => 'hamburger',
        'food' => 'hamburger',
        'desserts' => 'ice-cream',
        'dessert' => 'ice-cream',
        'snacks' => 'cookie',
        'snack' => 'cookie',
        'pizza' => 'pizza-slice',
        'seafood' => 'fish',
        'fruits' => 'apple-alt',
        'fruit' => 'apple-alt',
        'default' => 'tag'
    ];
    
    $name = strtolower($categoryName);
    foreach ($icons as $key => $icon) {
        if (strpos($name, $key) !== false) {
            return $icon;
        }
    }
    
    return $icons['default'];
}
?>

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
