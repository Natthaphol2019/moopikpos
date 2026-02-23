<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/admin_auth.php';
require __DIR__ . '/../../config/customer_db.php';

ensure_customers_table($pdo);
customer_require_login();

$customer = customer_current_user();
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
$products = $pdo->query("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 'active' ORDER BY p.category_id, p.id")->fetchAll();

function app_url($path)
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = rtrim(dirname($scriptName), '/');
    if ($basePath === '' || $basePath === '.') {
        $basePath = '';
    }

    return $basePath . '/' . ltrim((string) $path, '/');
}

function esc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$fullName = trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''));
$nickname = trim((string) ($customer['nickname'] ?? ''));
$displayName = $nickname !== '' ? $fullName . ' (' . $nickname . ')' : $fullName;
$shippingLat = $customer['shipping_latitude'] ?? '';
$shippingLng = $customer['shipping_longitude'] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, viewport-fit=cover">
    
    <!-- PWA Meta Tags -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MooPik Order">
    <meta name="theme-color" content="#dc3545">
    <meta name="description" content="MooPik POS - สั่งอาหารออนไลน์">
    
    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="<?php echo app_url('manifest.json'); ?>">
    <link rel="apple-touch-icon" href="<?php echo app_url('app/assets/icons/icon-192x192.png'); ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo app_url('app/assets/icons/icon-192x192.png'); ?>">
    
    <title>เมนูลูกค้า - สั่งผ่านเว็บ</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Prompt', sans-serif; background: #f4f6f9; padding-bottom: 70px; }
        .menu-card { cursor: pointer; border: none; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(0,0,0,0.06); transition: 0.2s; }
        .menu-card:hover { transform: translateY(-4px); }
        .menu-img { height: 130px; object-fit: cover; width: 100%; }
        .cart-panel { position: sticky; top: 16px; }
        .cart-item { border-bottom: 1px dashed #dee2e6; padding: 10px 0; }
        
        /* Mobile Bottom Navigation */
        @media (max-width: 768px) {
            .navbar.sticky-top { display: none !important; }
            body { padding-bottom: 80px; }
            .mobile-bottom-nav { display: flex !important; }
            .cart-panel { position: relative; top: 0; margin-top: 20px; }
        }
        .mobile-bottom-nav {
            display: none; position: fixed; bottom: 0; left: 0; right: 0; z-index: 1050;
            background: #ffffff; box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            padding: 8px 0; padding-bottom: max(8px, env(safe-area-inset-bottom));
        }
        .mobile-bottom-nav .nav-container {
            display: flex; justify-content: space-around; align-items: center; width: 100%;
        }
        .mobile-bottom-nav .nav-item {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-decoration: none; color: #6b7280; transition: all 0.2s; padding: 6px 12px; border-radius: 8px;
        }
        .mobile-bottom-nav .nav-item i { font-size: 20px; margin-bottom: 4px; }
        .mobile-bottom-nav .nav-item span { font-size: 11px; font-weight: 500; }
        .mobile-bottom-nav .nav-item:hover, .mobile-bottom-nav .nav-item.active {
            color: #dc3545; background: rgba(220, 53, 69, 0.1);
        }
        .mobile-bottom-nav .nav-item.active i { transform: scale(1.1); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand fw-bold text-primary" href="<?php echo app_url('index.php'); ?>"><i class="fa-solid fa-bowl-food me-1"></i>สั่งอาหารออนไลน์</a>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border"><i class="fa-regular fa-user me-1"></i><?php echo esc($displayName); ?></span>
                <a class="btn btn-sm btn-outline-primary" href="<?php echo app_url('customer_orders.php'); ?>" title="ดูสถานะออเดอร์">
                    <i class="fa-solid fa-receipt me-1"></i>ออเดอร์ของฉัน
                </a>
                <a class="btn btn-sm btn-outline-danger" href="<?php echo app_url('customer_logout.php'); ?>">ออกจากระบบ</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success">สั่งอาหารสำเร็จแล้ว ออเดอร์ของคุณถูกส่งเข้าครัวเรียบร้อย #<?php echo (int)($_GET['order'] ?? 0); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'empty'): ?>
            <div class="alert alert-danger">กรุณาเลือกสินค้าอย่างน้อย 1 รายการ</div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <h4 class="fw-bold mb-3">เลือกเมนูอาหาร</h4>

                <div class="mb-3 d-flex gap-2 flex-wrap" id="catFilters">
                    <button class="btn btn-sm btn-primary" onclick="filterCat('all', this)">ทั้งหมด</button>
                    <?php foreach ($cats as $c): ?>
                        <button class="btn btn-sm btn-outline-primary" onclick="filterCat('<?php echo htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8'); ?>', this)"><?php echo htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8'); ?></button>
                    <?php endforeach; ?>
                </div>

                <div class="row g-3" id="productGrid">
                    <?php foreach ($products as $p): ?>
                        <div class="col-6 col-md-4 menu-item" data-cat="<?php echo htmlspecialchars($p['cat_name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="menu-card h-100" onclick='addToCart(<?php echo json_encode(['id' => (int)$p['id'], 'name' => $p['name'], 'price' => (float)$p['price']]); ?>)'>
                                <img src="<?php echo strpos($p['image_url'], 'http') === 0 ? htmlspecialchars($p['image_url'], ENT_QUOTES, 'UTF-8') : 'uploads/' . htmlspecialchars($p['image_url'], ENT_QUOTES, 'UTF-8'); ?>" class="menu-img" alt="menu">
                                <div class="p-3">
                                    <h6 class="fw-semibold mb-1"><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-primary fw-bold"><?php echo number_format($p['price']); ?> ฿</span>
                                        <i class="fa-solid fa-circle-plus text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card cart-panel border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="fa-solid fa-cart-shopping me-1"></i>ตะกร้าของคุณ</h5>
                        <div id="cartItems" class="mb-3 text-muted">ยังไม่มีรายการ</div>

                        <form action="<?php echo app_url('customer_order_submit.php'); ?>" method="POST" id="customerOrderForm">
                            <input type="hidden" name="cart_data" id="cartDataInput">

                            <div class="mb-2">
                                <label class="form-label">ประเภทคำสั่งซื้อ</label>
                                <select class="form-select" name="order_type" id="orderType" onchange="toggleAddress()">
                                    <option value="takeaway">รับที่ร้าน</option>
                                    <option value="delivery">จัดส่ง</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">ชื่อผู้สั่ง</label>
                                <input class="form-control" type="text" name="customer_name" value="<?php echo esc($fullName); ?>" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">เบอร์โทร</label>
                                <input class="form-control" type="text" name="customer_phone" value="<?php echo esc($customer['phone'] ?? ''); ?>" readonly>
                            </div>
                            <div class="mb-3 d-none" id="addressWrap">
                                <label class="form-label">ที่อยู่จัดส่ง</label>
                                <textarea class="form-control" name="customer_address" id="customerAddress" rows="2"><?php echo esc($customer['shipping_address'] ?? ''); ?></textarea>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="captureDeliveryGps()">ใช้ตำแหน่งปัจจุบัน (GPS)</button>
                                    <small id="gpsStatus" class="text-muted">ไม่บังคับ</small>
                                </div>
                            </div>
                            <input type="hidden" name="customer_latitude" id="customerLatitude" value="<?php echo esc($shippingLat); ?>">
                            <input type="hidden" name="customer_longitude" id="customerLongitude" value="<?php echo esc($shippingLng); ?>">

                            <div class="d-flex justify-content-between fs-5 fw-bold mb-3">
                                <span>รวมทั้งหมด</span>
                                <span id="totalPrice">0 ฿</span>
                            </div>
                            <button class="btn btn-success w-100" type="button" onclick="submitCustomerOrder()">ยืนยันคำสั่งซื้อ</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cart = [];

        function addToCart(product) {
            const found = cart.find(item => item.id === product.id);
            if (found) {
                found.qty += 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: Number(product.price),
                    qty: 1
                });
            }
            renderCart();
        }

        function decreaseQty(index) {
            cart[index].qty -= 1;
            if (cart[index].qty <= 0) {
                cart.splice(index, 1);
            }
            renderCart();
        }

        function increaseQty(index) {
            cart[index].qty += 1;
            renderCart();
        }

        function renderCart() {
            const cartItems = document.getElementById('cartItems');
            const totalPrice = document.getElementById('totalPrice');

            if (cart.length === 0) {
                cartItems.innerHTML = 'ยังไม่มีรายการ';
                totalPrice.innerText = '0 ฿';
                return;
            }

            let html = '';
            let total = 0;
            cart.forEach((item, index) => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;
                html += `
                    <div class="cart-item">
                        <div class="fw-semibold">${item.name}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small>${item.price.toLocaleString()} ฿ x ${item.qty}</small>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQty(${index})">-</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="increaseQty(${index})">+</button>
                            </div>
                        </div>
                    </div>`;
            });

            cartItems.innerHTML = html;
            totalPrice.innerText = total.toLocaleString() + ' ฿';
        }

        function filterCat(cat, element) {
            document.querySelectorAll('.menu-item').forEach(item => {
                item.style.display = (cat === 'all' || item.dataset.cat === cat) ? 'block' : 'none';
            });

            document.querySelectorAll('#catFilters .btn').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            element.classList.remove('btn-outline-primary');
            element.classList.add('btn-primary');
        }

        function toggleAddress() {
            const orderType = document.getElementById('orderType').value;
            const addressWrap = document.getElementById('addressWrap');
            const addressInput = document.getElementById('customerAddress');

            if (orderType === 'delivery') {
                addressWrap.classList.remove('d-none');
                addressInput.setAttribute('required', 'required');
            } else {
                addressWrap.classList.add('d-none');
                addressInput.removeAttribute('required');
            }
        }

        toggleAddress();

        function submitCustomerOrder() {
            if (cart.length === 0) {
                alert('กรุณาเลือกสินค้าอย่างน้อย 1 รายการ');
                return;
            }

            document.getElementById('cartDataInput').value = JSON.stringify(cart);
            document.getElementById('customerOrderForm').submit();
        }

        function captureDeliveryGps() {
            const statusEl = document.getElementById('gpsStatus');
            if (!navigator.geolocation) {
                statusEl.textContent = 'เบราว์เซอร์ไม่รองรับ GPS';
                statusEl.className = 'text-danger';
                return;
            }

            statusEl.textContent = 'กำลังดึงพิกัด...';
            statusEl.className = 'text-primary';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById('customerLatitude').value = position.coords.latitude.toFixed(7);
                    document.getElementById('customerLongitude').value = position.coords.longitude.toFixed(7);
                    statusEl.textContent = `ได้พิกัดแล้ว (${position.coords.latitude.toFixed(5)}, ${position.coords.longitude.toFixed(5)})`;
                    statusEl.className = 'text-success';
                },
                () => {
                    statusEl.textContent = 'ไม่สามารถดึงพิกัดได้ ใช้ที่อยู่ข้อความแทนได้';
                    statusEl.className = 'text-danger';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        }
    </script>
    
    <!-- Mobile Bottom Navigation (Customer) -->
    <nav class="mobile-bottom-nav">
        <div class="nav-container">
            <a href="<?php echo app_url('customer_menu.php'); ?>" class="nav-item active">
                <i class="fa-solid fa-utensils"></i>
                <span>เมนูอาหาร</span>
            </a>
            <a href="#" class="nav-item" onclick="document.getElementById('customerOrderForm').scrollIntoView({behavior: 'smooth'}); return false;">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>ตะกร้า</span>
            </a>
            <a href="<?php echo app_url('customer_orders.php'); ?>" class="nav-item">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>ประวัติ</span>
            </a>
            <a href="<?php echo app_url('customer_logout.php'); ?>" class="nav-item">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>ออกจากระบบ</span>
            </a>
        </div>
    </nav>
    
    <!-- PWA Service Worker Registration -->
    <script>
        if ("serviceWorker" in navigator) {
            window.addEventListener("load", () => {
                navigator.serviceWorker.register("<?php echo app_url('sw.js'); ?>")
                    .then(reg => console.log("SW registered:", reg.scope))
                    .catch(err => console.log("SW registration failed:", err));
            });
        }
    </script>
</body>
</html>
