<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/admin_auth.php';
staff_require_login();

$orderId = (int) ($_POST['order_id'] ?? 0);
$status = trim((string) ($_POST['status'] ?? ''));

$allowedStatuses = ['pending', 'cooking', 'ready', 'completed'];

if ($orderId > 0 && in_array($status, $allowedStatuses, true)) {
    $sql = "UPDATE orders SET status = ? WHERE id = ? AND table_no LIKE 'WEB-%'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $orderId]);
}

header('Location: ' . auth_url('staff_requests.php?updated=1'));
exit;
