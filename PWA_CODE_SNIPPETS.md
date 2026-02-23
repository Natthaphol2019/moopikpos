# 📋 Quick Reference - PWA & Bottom Navigation Code

## 📱 TASK 1: PWA Implementation

### 1.1 - manifest.json (Root Directory)
```json
{
  "name": "MooPik POS - ระบบจัดการร้านอาหาร",
  "short_name": "MooPik",
  "description": "ระบบ POS สำหรับร้านอาหาร",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#dc3545",
  "orientation": "portrait-primary",
  "icons": [
    {
      "src": "/app/assets/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/app/assets/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

---

### 1.2 - HTML Head Tags (Put in <head> section)

```html
<!-- ===== PWA Meta Tags ===== -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, viewport-fit=cover">

<!-- Mobile Web App Capable -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="MooPik">

<!-- Theme Color (change per role) -->
<meta name="theme-color" content="#dc3545">  <!-- Customer: Red -->
<!-- <meta name="theme-color" content="#0d6efd"> --> <!-- Staff: Blue -->
<!-- <meta name="theme-color" content="#ff6f00"> --> <!-- Chef: Orange -->

<meta name="description" content="MooPik POS - ระบบจัดการร้านอาหาร">

<!-- PWA Manifest & Icons -->
<link rel="manifest" href="/manifest.json">
<link rel="apple-touch-icon" href="/app/assets/icons/icon-192x192.png">
<link rel="icon" type="image/png" sizes="192x192" href="/app/assets/icons/icon-192x192.png">
```

---

## 🎨 TASK 2: Mobile Bottom Navigation

### 2.1 - Custom CSS

```css
/* ===== MOBILE BOTTOM NAVIGATION STYLES ===== */

/* Hide bottom nav on desktop, show only on mobile */
@media (max-width: 768px) {
    /* Hide desktop navbar */
    .staff-topbar, 
    .navbar.sticky-top,
    .container-fluid > .d-flex.gap-2 {
        display: none !important;
    }
    
    /* Add bottom padding to body */
    body {
        padding-bottom: 80px;
    }
    
    /* Show bottom nav */
    .mobile-bottom-nav {
        display: flex !important;
    }
}

/* Bottom Navigation Container */
.mobile-bottom-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1050;
    background: #ffffff;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    padding: 8px 0;
    
    /* iOS Safe Area (iPhone X+) */
    padding-bottom: max(8px, env(safe-area-inset-bottom));
}

/* Navigation Items Container */
.mobile-bottom-nav .nav-container {
    display: flex;
    justify-content: space-around;
    align-items: center;
    width: 100%;
}

/* Individual Navigation Item */
.mobile-bottom-nav .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #6b7280;
    transition: all 0.2s;
    padding: 6px 12px;
    border-radius: 8px;
}

/* Icon Styling */
.mobile-bottom-nav .nav-item i {
    font-size: 20px;
    margin-bottom: 4px;
}

/* Text Label Styling */
.mobile-bottom-nav .nav-item span {
    font-size: 11px;
    font-weight: 500;
}

/* Hover & Active States */
.mobile-bottom-nav .nav-item:hover,
.mobile-bottom-nav .nav-item.active {
    color: #dc3545;  /* Red for Customer */
    background: rgba(220, 53, 69, 0.1);
}

/* For Staff (Blue) */
/* .mobile-bottom-nav .nav-item.active { color: #0d6efd; } */

/* For Chef (Orange) */
/* .mobile-bottom-nav .nav-item.active { color: #ff6f00; } */

/* Active Icon Animation */
.mobile-bottom-nav .nav-item.active i {
    transform: scale(1.1);
}

/* Dark Theme (for Chef/Kitchen pages) */
.mobile-bottom-nav.dark {
    background: #343a40;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.3);
}

.mobile-bottom-nav.dark .nav-item {
    color: #adb5bd;
}

.mobile-bottom-nav.dark .nav-item.active {
    color: #ff6f00;
    background: rgba(255, 111, 0, 0.15);
}
```

---

### 2.2 - Bottom Navigation HTML (Snippet A: Staff)

```html
<!-- ===== MOBILE BOTTOM NAVIGATION - STAFF ===== -->
<nav class="mobile-bottom-nav">
    <div class="nav-container">
        <!-- โต๊ะ/หน้าหลัก -->
        <a href="staff_tables.php" class="nav-item active">
            <i class="fa-solid fa-table-cells"></i>
            <span>โต๊ะ</span>
        </a>
        
        <!-- รับออเดอร์ -->
        <a href="staff_order.php" class="nav-item">
            <i class="fa-solid fa-cart-plus"></i>
            <span>รับออเดอร์</span>
        </a>
        
        <!-- คำขอลูกค้า -->
        <a href="staff_requests.php" class="nav-item">
            <i class="fa-solid fa-bell-concierge"></i>
            <span>คำขอลูกค้า</span>
        </a>
        
        <!-- ออกจากระบบ -->
        <a href="staff_logout.php" class="nav-item">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>ออกจากระบบ</span>
        </a>
    </div>
</nav>
```

---

### 2.3 - Bottom Navigation HTML (Snippet B: Customer)

```html
<!-- ===== MOBILE BOTTOM NAVIGATION - CUSTOMER ===== -->
<nav class="mobile-bottom-nav">
    <div class="nav-container">
        <!-- เมนูอาหาร -->
        <a href="customer_menu.php" class="nav-item active">
            <i class="fa-solid fa-utensils"></i>
            <span>เมนูอาหาร</span>
        </a>
        
        <!-- ตะกร้าของฉัน -->
        <a href="#cart" class="nav-item" onclick="scrollToCart(); return false;">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>ตะกร้า</span>
        </a>
        
        <!-- ประวัติสั่งซื้อ -->
        <a href="customer_orders.php" class="nav-item">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>ประวัติ</span>
        </a>
        
        <!-- ออกจากระบบ -->
        <a href="customer_logout.php" class="nav-item">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>ออกจากระบบ</span>
        </a>
    </div>
</nav>

<script>
function scrollToCart() {
    document.getElementById('cartItems').scrollIntoView({ behavior: 'smooth' });
}
</script>
```

---

### 2.4 - Service Worker Registration (Put before </body>)

```html
<!-- ===== PWA SERVICE WORKER REGISTRATION ===== -->
<script>
if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
        navigator.serviceWorker.register("/sw.js")
            .then(reg => console.log("✅ SW registered:", reg.scope))
            .catch(err => console.log("❌ SW registration failed:", err));
    });
}
</script>
```

---

## 🎯 Dynamic Active State (PHP Example)

```php
<?php
$currentPage = basename($_SERVER['REQUEST_URI'] ?? '');
$isOrderPage = $currentPage === 'staff_order.php';
$isTablePage = $currentPage === 'staff_tables.php';
?>

<a href="staff_order.php" class="nav-item <?= $isOrderPage ? 'active' : '' ?>">
    <i class="fa-solid fa-cart-plus"></i>
    <span>รับออเดอร์</span>
</a>
```

---

## 🌈 Color Variants

### Staff (Blue Theme)
```css
.mobile-bottom-nav .nav-item.active {
    color: #0d6efd;
    background: rgba(13, 110, 253, 0.1);
}
```

### Customer (Red Theme)
```css
.mobile-bottom-nav .nav-item.active {
    color: #dc3545;
    background: rgba(220, 53, 69, 0.1);
}
```

### Chef (Orange Theme)
```css
.mobile-bottom-nav .nav-item.active {
    color: #ff6f00;
    background: rgba(255, 111, 0, 0.15);
}
```

---

## 📱 Testing Checklist

- [ ] Desktop (> 768px): Bottom nav hidden, top navbar shows
- [ ] Mobile (< 768px): Bottom nav shows, top navbar hidden
- [ ] Active state highlights current page
- [ ] Touch targets are at least 48x48px
- [ ] iOS safe area padding works on iPhone X+
- [ ] Service Worker registers successfully (check DevTools)
- [ ] Manifest loads without errors
- [ ] "Add to Home Screen" option appears on mobile

---

## 🚀 Quick Deploy

1. Copy `manifest.json` to root
2. Copy `sw.js` to root
3. Add PWA meta tags to all pages
4. Add bottom nav HTML before `</body>`
5. Add mobile CSS to stylesheets
6. Generate icons (72px - 512px)
7. Test on real device

---

**Note:** All code is production-ready and follows Bootstrap 5 conventions.
