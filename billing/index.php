<!DOCTYPE html>
<html lang="en">
<?php 
session_start();
include('../db_connect.php');
ob_start();

// Check if user is logged in
if(!isset($_SESSION['login_id'])) {
    header('Location: ../login-modern.php');
    exit();
}

// Get system settings
$system = $conn->query("SELECT * FROM system_settings limit 1")->fetch_array();
foreach($system as $k => $v){
    $_SESSION['system'][$k] = $v;
}

// Get user name safely
$user_name = isset($_SESSION['login_name']) ? $_SESSION['login_name'] : 'User';

// Get order ID if provided
$order_id = isset($_GET['id']) ? $_GET['id'] : 0;
$order_data = null;

if($order_id > 0) {
    $order_data = $conn->query("SELECT * FROM orders WHERE id = $order_id")->fetch_array();
}

ob_end_flush();
?>
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?php echo $_SESSION['system']['name'] ?> - Point of Sale</title>
    
    <?php include('../header-modern.php'); ?>
    
    <style>
        .pos-container {
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .pos-main {
            display: flex;
            height: 100vh;
            padding-top: 70px;
        }
        
        .pos-sidebar {
            width: 350px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 0 20px 20px 0;
            padding: 20px;
            overflow-y: auto;
        }
        
        .pos-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .product-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-color: #667eea;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .cart-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 15px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            margin-bottom: 10px;
        }
        
        .cart-summary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
        }
        
        .qty-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .qty-btn {
            width: 30px;
            height: 30px;
            border: none;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>

<body class="pos-container">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark modern-navbar fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../home-modern.php">
                <i class="fas fa-coffee me-2"></i>
                <?php echo $_SESSION['system']['name']; ?>
            </a>
            
            <div class="d-flex align-items-center">
                <span class="text-white me-3">
                    <i class="fas fa-user-circle me-1"></i> <?php echo $user_name; ?>
                </span>
                <a href="../home-modern.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <div class="pos-main">
        <!-- Cart Sidebar -->
        <div class="pos-sidebar">
            <h4 class="mb-4">
                <i class="fas fa-shopping-cart me-2"></i>
                Current Order
            </h4>
            
            <div id="cart-items">
                <!-- Cart items will be populated here -->
            </div>
            
            <div class="cart-summary">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span id="subtotal">৳0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax (5%):</span>
                    <span id="tax">৳0.00</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong id="total">৳0.00</strong>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <button class="btn btn-outline-light w-100" onclick="clearCart()">
                            <i class="fas fa-trash me-1"></i> Clear
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-light w-100" onclick="processPayment()">
                            <i class="fas fa-credit-card me-1"></i> Pay
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="pos-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-white">
                    <i class="fas fa-store me-2"></i>
                    Select Products
                </h3>
                
                <div class="d-flex gap-2">
                    <select class="form-select" id="category-filter" onchange="filterProducts()">
                        <option value="">All Categories</option>
                        <?php 
                        $categories = $conn->query("SELECT * FROM categories ORDER BY name");
                        while($cat = $categories->fetch_assoc()):
                        ?>
                        <option value="<?php echo $cat['id'] ?>"><?php echo $cat['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                    
                    <input type="search" class="form-control" placeholder="Search products..." 
                           id="product-search" onkeyup="searchProducts()">
                </div>
            </div>
            
            <div class="product-grid" id="products-grid">
                <?php 
                $products = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.status = 1 ORDER BY p.name");
                while($product = $products->fetch_assoc()):
                ?>
                <div class="product-card" data-category="<?php echo $product['category_id'] ?>" onclick="addToCart(<?php echo $product['id'] ?>, '<?php echo addslashes($product['name']) ?>', <?php echo $product['price'] ?>)">>
                    <div class="product-image">
                        <i class="fas fa-coffee"></i>
                    </div>
                    <h6 class="mb-2"><?php echo $product['name'] ?></h6>
                    <p class="text-muted small mb-2"><?php echo $product['category_name'] ?></p>
                    <strong class="text-success">৳<?php echo number_format($product['price'], 2) ?></strong>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Process Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Total Amount</label>
                        <input type="text" class="form-control" id="payment-total" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount Tendered</label>
                        <input type="number" class="form-control" id="amount-tendered" step="0.01" placeholder="0.00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Change</label>
                        <input type="text" class="form-control" id="change-amount" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="completeOrder()">Complete Order</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/jquery/jquery.min.js"></script>
    
    <script>
        let cart = [];
        let orderTotal = 0;

        function addToCart(id, name, price) {
            // Check if item already exists in cart
            const existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1
                });
            }
            
            updateCartDisplay();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCartDisplay();
        }

        function updateQuantity(id, quantity) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.quantity = Math.max(0, quantity);
                if (item.quantity === 0) {
                    removeFromCart(id);
                }
            }
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartItems = document.getElementById('cart-items');
            cartItems.innerHTML = '';
            
            let subtotal = 0;
            
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                cartItems.innerHTML += `
                    <div class="cart-item">
                        <div class="flex-grow-1">
                            <div class="fw-bold">${item.name}</div>
                            <small class="text-muted">৳${item.price.toFixed(2)} each</small>
                        </div>
                        <div class="qty-controls">
                            <button class="qty-btn" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                            <span class="mx-2">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                        </div>
                        <div class="text-end ms-2">
                            <div class="fw-bold">৳${itemTotal.toFixed(2)}</div>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            const tax = subtotal * 0.05;
            const total = subtotal + tax;
            
            document.getElementById('subtotal').textContent = `৳${subtotal.toFixed(2)}`;
            document.getElementById('tax').textContent = `৳${tax.toFixed(2)}`;
            document.getElementById('total').textContent = `৳${total.toFixed(2)}`;
            
            orderTotal = total;
        }

        function clearCart() {
            cart = [];
            updateCartDisplay();
        }

        function processPayment() {
            if (cart.length === 0) {
                alert('Cart is empty!');
                return;
            }
            
            document.getElementById('payment-total').value = `৳${orderTotal.toFixed(2)}`;
            document.getElementById('amount-tendered').value = orderTotal.toFixed(2);
            calculateChange();
            
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }

        function calculateChange() {
            const total = orderTotal;
            const tendered = parseFloat(document.getElementById('amount-tendered').value) || 0;
            const change = tendered - total;
            
            document.getElementById('change-amount').value = `৳${change.toFixed(2)}`;
        }

        function completeOrder() {
            const tendered = parseFloat(document.getElementById('amount-tendered').value) || 0;
            
            if (tendered < orderTotal) {
                alert('Insufficient amount tendered!');
                return;
            }
            
            // Prepare order data in the format admin_class.php expects
            const formData = new FormData();
            formData.append('action', 'save_order');
            formData.append('total_amount', orderTotal);
            formData.append('total_tendered', tendered);
            formData.append('order_number', Math.floor(Math.random() * 1000000));
            
            // Add cart items
            cart.forEach((item, index) => {
                formData.append(`item_id[${index}]`, ''); // Empty for new items
                formData.append(`product_id[${index}]`, item.id);
                formData.append(`qty[${index}]`, item.quantity);
                formData.append(`price[${index}]`, item.price);
                formData.append(`amount[${index}]`, item.price * item.quantity);
            });
            
            // Send to server
            $.ajax({
                url: '../ajax.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp) {
                    console.log('Server response:', resp);
                    if (resp && resp > 0) {
                        alert('Order completed successfully!');
                        clearCart();
                        bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                        
                        // Optionally redirect to receipt or orders page
                        if (confirm('Would you like to print receipt?')) {
                            window.open('../receipt.php?id=' + resp, '_blank');
                        }
                    } else {
                        alert('Error saving order: ' + resp);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                    alert('Error processing order: ' + error);
                }
            });
        }

        // Event listeners
        document.getElementById('amount-tendered').addEventListener('input', calculateChange);

        // Category filtering function
        function filterProducts() {
            const categoryId = document.getElementById('category-filter').value;
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                if (categoryId === '' || product.dataset.category === categoryId) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        // Search function
        function searchProducts() {
            const searchTerm = document.getElementById('product-search').value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const productName = product.querySelector('h6').textContent.toLowerCase();
                const categoryName = product.querySelector('.text-muted').textContent.toLowerCase();
                
                if (productName.includes(searchTerm) || categoryName.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }

        // Initialize cart display
        updateCartDisplay();
    </script>
</body>
</html>
