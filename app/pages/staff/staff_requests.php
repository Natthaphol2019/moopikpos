<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/admin_auth.php';
staff_require_login();

require __DIR__ . '/staff_layout.php';

$sql = "SELECT id, order_type, table_no, customer_name, total_price, status, payment_status, order_time
        FROM orders
        WHERE table_no LIKE 'WEB-%' AND status != 'completed'
        ORDER BY order_time DESC";
$requests = $pdo->query($sql)->fetchAll();

$itemsByOrder = [];
if (!empty($requests)) {
    $orderIds = array_map(static function ($row) {
        return (int) $row['id'];
    }, $requests);

    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $itemSql = "SELECT oi.order_id, oi.quantity, oi.note, oi.price, p.name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id IN ($placeholders)
                ORDER BY oi.id ASC";
    $stmt = $pdo->prepare($itemSql);
    $stmt->execute($orderIds);

    foreach ($stmt->fetchAll() as $item) {
        $orderId = (int) $item['order_id'];
        if (!isset($itemsByOrder[$orderId])) {
            $itemsByOrder[$orderId] = [];
        }
        $itemsByOrder[$orderId][] = $item;
    }
}

function esc($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function status_badge($status)
{
    if ($status === 'pending') {
        return '<span class="badge bg-warning text-dark">รอคิว</span>';
    }
    if ($status === 'cooking') {
        return '<span class="badge bg-primary">กำลังปรุง</span>';
    }
    if ($status === 'ready') {
        return '<span class="badge bg-success">พร้อมเสิร์ฟ/รับ</span>';
    }
    return '<span class="badge bg-secondary">' . esc($status) . '</span>';
}

$extraHead = '<style>
.request-card{border:0;border-radius:14px;box-shadow:0 4px 14px rgba(0,0,0,.06);} 
.request-meta{font-size:.9rem;color:#6b7280;}
.request-items li{padding:6px 0;border-bottom:1px dashed #e5e7eb;}
.request-items li:last-child{border-bottom:0;}
</style>';

staff_layout_start('คำขอออเดอร์ลูกค้า', 'คำขอจากลูกค้าออนไลน์', 'ดูรายการที่ลูกค้าสมาชิกสั่งเข้ามาผ่านเว็บไซต์', $extraHead);
?>

<div class="container-fluid px-3 px-md-4 pb-4">
    <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
        <div class="alert alert-success py-2">อัปเดตสถานะออเดอร์เรียบร้อยแล้ว</div>
    <?php endif; ?>

    <?php if (empty($requests)): ?>
        <div class="card request-card">
            <div class="card-body text-center py-5 text-muted">
                <i class="fa-regular fa-face-smile-beam fa-2x mb-2"></i>
                <h5 class="mb-1">ยังไม่มีคำขอใหม่จากลูกค้า</h5>
                <small>หน้านี้จะแสดงเฉพาะออเดอร์เว็บที่ยังไม่ completed</small>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($requests as $order): ?>
                <?php $orderId = (int) $order['id']; ?>
                <div class="col-lg-6 col-xl-4">
                    <div class="card request-card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="mb-1">ออเดอร์ #<?php echo $orderId; ?></h5>
                                    <div class="request-meta">
                                        <?php echo $order['order_type'] === 'delivery' ? 'จัดส่ง' : 'รับที่ร้าน'; ?>
                                        • <?php echo esc($order['table_no']); ?>
                                    </div>
                                </div>
                                <?php echo status_badge($order['status']); ?>
                            </div>

                            <div class="mb-2 request-meta">
                                <div><strong>ลูกค้า:</strong> <?php echo esc($order['customer_name']); ?></div>
                                <div><strong>เวลา:</strong> <?php echo esc($order['order_time']); ?></div>
                                <div><strong>ยอดรวม:</strong> <?php echo number_format((float) $order['total_price'], 2); ?> ฿</div>
                            </div>

                            <div class="mb-3">
                                <div class="fw-semibold mb-1">รายการอาหาร</div>
                                <ul class="list-unstyled request-items mb-0">
                                    <?php foreach (($itemsByOrder[$orderId] ?? []) as $item): ?>
                                        <li>
                                            x<?php echo (int) $item['quantity']; ?> <?php echo esc($item['name']); ?>
                                            <span class="text-muted">(<?php echo number_format((float) $item['price'], 2); ?>)</span>
                                            <?php if (!empty($item['note'])): ?>
                                                <div class="small text-danger">โน้ต: <?php echo esc($item['note']); ?></div>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <form method="POST" action="<?php echo esc(staff_url('staff_request_update.php')); ?>" class="mt-auto">
                                <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                                <div class="input-group">
                                    <select class="form-select form-select-sm" name="status">
                                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>รอคิว</option>
                                        <option value="cooking" <?php echo $order['status'] === 'cooking' ? 'selected' : ''; ?>>กำลังปรุง</option>
                                        <option value="ready" <?php echo $order['status'] === 'ready' ? 'selected' : ''; ?>>พร้อมเสิร์ฟ/รับ</option>
                                        <option value="completed">ปิดงาน (completed)</option>
                                    </select>
                                    <button class="btn btn-primary btn-sm" type="submit">อัปเดต</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Auto-polling for new customer orders with notification -->
<script>
(function() {
    let lastCheckTime = Math.floor(Date.now() / 1000);
    let pollInterval = 5000; // 5 seconds
    let hasPlayedSound = false;
    
    // Generate beep sound using Web Audio API
    function playBeep() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800; // Hz
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            console.log('Audio context not available');
        }
    }
    
    // Show toast notification
    function showToast(title, message, type = 'warning') {
        // Create toast element if not exists
        let toastContainer = document.getElementById('notification-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'notification-toast-container';
            toastContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
            `;
            document.body.appendChild(toastContainer);
        }
        
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show`;
        toast.role = 'alert';
        toast.innerHTML = `
            <strong>${title}</strong><br>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        toast.style.cssText = `
            margin-bottom: 10px;
            min-width: 300px;
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto-dismiss after 6 seconds
        setTimeout(() => {
            toast.remove();
        }, 6000);
    }
    
    // Request desktop notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    
    // Show desktop notification
    function showDesktopNotification(title, options = {}) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                icon: 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/></svg>',
                ...options
            });
        }
    }
    
    // Poll for new orders
    function checkNewOrders() {
        const apiUrl = '<?php echo staff_url("api/check_new_orders.php"); ?>';
        fetch(apiUrl + `?last_check=${lastCheckTime}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log('✅ New orders check:', data); // Debug
                if (data.success && data.new_count > 0) {
                    // Play beep sound
                    playBeep();
                    
                    console.log(`New orders detected: ${data.new_count}`);
                    
                    // Show toast
                    showToast(
                        '🔔 ออเดอร์ใหม่!',
                        `มีออเดอร์ใหม่เข้ามา ${data.new_count} รายการ`,
                        'warning'
                    );
                    
                    // Show desktop notification for each new order
                    if (data.new_orders && data.new_orders.length > 0) {
                        const firstOrder = data.new_orders[0];
                        showDesktopNotification(
                            'ออเดอร์ใหม่ - ' + firstOrder.table_no,
                            {
                                body: `ลูกค้า: ${firstOrder.customer_name}`,
                                tag: 'new-order-' + firstOrder.id,
                                requireInteraction: true
                            }
                        );
                    }
                    
                    // Reload page to show new orders
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
                
                // Update lastCheckTime
                if (data.current_time) {
                    lastCheckTime = data.current_time;
                }
            })
            .catch(error => {
                console.error('❌ Staff requests poll error:', error.message);
                console.error('Full error:', error);
            });
    }
    
    // Start polling every 5 seconds
    setInterval(checkNewOrders, pollInterval);
    
    // Initial check when page loads
    checkNewOrders();
})();
</script>

<?php staff_layout_end(); ?>
