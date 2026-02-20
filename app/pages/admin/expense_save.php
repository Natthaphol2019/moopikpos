<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/admin_auth.php';
admin_require_login();
auth_start_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expense_date = $_POST['expense_date'];
    $item_name = trim($_POST['item_name']);
    $quantity = !empty($_POST['quantity']) ? $_POST['quantity'] : 0;
    $unit = trim($_POST['unit']);
    $total_price = $_POST['total_price'];
    
    // ดึง ID ของแอดมินที่กำลัง Login อยู่ (ถ้ามี session แล้วใช้ session, ถ้าไม่มีรับจาก hidden input ก่อน)
    $recorded_by = $_SESSION['admin_id'] ?? $_POST['recorded_by'] ?? null;

    $stmt = $pdo->prepare(" 
        INSERT INTO expenses (expense_date, item_name, quantity, unit, total_price, recorded_by) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$expense_date, $item_name, $quantity, $unit, $total_price, $recorded_by]);
    
    header('Location: ' . auth_url('admin_expenses.php?success=1'));
    exit();
}