<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/admin_auth.php';
admin_require_login();

// 1. ยอดขายวันนี้
$stmt = $pdo->prepare("SELECT SUM(total_price) FROM orders WHERE DATE(order_time) = CURDATE() AND status = 'completed'");
$stmt->execute();
$daily_sales = $stmt->fetchColumn() ?: 0;

// 2. จำนวนออเดอร์วันนี้
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE DATE(order_time) = CURDATE()");
$stmt->execute();
$daily_orders = $stmt->fetchColumn() ?: 0;

// 3. 5 อันดับเมนูขายดี
$sql_top = "SELECT p.name, SUM(oi.quantity) as total_qty 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            GROUP BY p.id 
            ORDER BY total_qty DESC 
            LIMIT 5";
$top_menu = $pdo->query($sql_top)->fetchAll();

$labels = [];
$data = [];
foreach ($top_menu as $tm) {
    $labels[] = $tm['name'];
    $data[] = $tm['total_qty'];
}

require __DIR__ . '/admin_layout.php';

$extraHead = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

admin_layout_start(
    'Dashboard เจ้าของร้าน',
    'dashboard',
    'ภาพรวมร้านวันนี้',
    'สรุปยอดขาย, จำนวนออเดอร์ และเมนูขายดี',
    '<a href="' . admin_escape(admin_url('admin_history.php')) . '" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-file-lines me-1"></i>ดูรายงาน</a>',
    $extraHead
);
?>

<div class="row g-3">
    <div class="col-md-6 col-xl-3">
        <div class="admin-surface p-3 h-100">
            <small class="text-muted">ยอดขายวันนี้</small>
            <h3 class="mb-0 text-primary fw-bold"><?php echo number_format($daily_sales, 0); ?> ฿</h3>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-surface p-3 h-100">
            <small class="text-muted">ออเดอร์วันนี้</small>
            <h3 class="mb-0 text-success fw-bold"><?php echo number_format($daily_orders); ?></h3>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="<?php echo admin_escape(admin_url('admin_history.php')); ?>" class="text-decoration-none">
            <div class="admin-surface p-3 h-100">
                <small class="text-muted">รายงาน</small>
                <h5 class="mb-0 text-dark fw-semibold">ประวัติยอดขาย <i class="fa-solid fa-arrow-right ms-1"></i></h5>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-xl-3">
        <a href="<?php echo admin_escape(admin_url('admin_products.php')); ?>" class="text-decoration-none">
            <div class="admin-surface p-3 h-100">
                <small class="text-muted">จัดการร้าน</small>
                <h5 class="mb-0 text-dark fw-semibold">จัดการเมนู <i class="fa-solid fa-arrow-right ms-1"></i></h5>
            </div>
        </a>
    </div>
</div>

<div class="admin-surface p-3 p-md-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fas fa-trophy text-warning"></i> 5 อันดับเมนูขายดีตลอดกาล</h5>
        <a href="<?php echo admin_escape(admin_url('admin_history.php')); ?>" class="btn btn-sm btn-outline-primary">ดูรายละเอียดรายวัน</a>
    </div>
    <canvas id="topMenuChart" height="100"></canvas>
</div>

<?php
$extraScripts = '<script>
const ctx = document.getElementById("topMenuChart");
new Chart(ctx, {
    type: "bar",
    data: {
        labels: ' . json_encode($labels) . ',
        datasets: [{
            label: "จำนวนที่ขายได้ (จาน/แก้ว)",
            data: ' . json_encode($data) . ',
            backgroundColor: "#36a2eb",
            borderRadius: 5,
            barThickness: 50
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>';

admin_layout_end($extraScripts);
?>