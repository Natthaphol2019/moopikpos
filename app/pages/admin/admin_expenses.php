<?php
require __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../config/admin_auth.php';
admin_require_login();
require __DIR__ . '/admin_layout.php';

// ดึงข้อมูลรายจ่ายทั้งหมด (เรียงจากวันที่ล่าสุด)
$stmt = $pdo->query(" 
    SELECT e.*, u.name as recorded_by_name 
    FROM expenses e 
    LEFT JOIN users u ON e.recorded_by = u.id 
    ORDER BY e.expense_date DESC, e.id DESC
");
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// คำนวณยอดรวมรายจ่ายทั้งหมด (เพื่อโชว์สรุป)
$total_expenses = 0;
foreach($expenses as $ex) {
    $total_expenses += $ex['total_price'];
}
$headerActions = '<button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#expenseModal"><i class="fa-solid fa-plus"></i> บันทึกรายจ่ายใหม่</button>';
admin_layout_start(
    'บันทึกรายจ่าย',
    'expenses',
    'บันทึกรายจ่าย / ซื้อวัตถุดิบ',
    'จัดเก็บค่าใช้จ่ายรายวันของร้าน',
    $headerActions
);
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fa-solid fa-file-invoice-dollar text-danger"></i> บันทึกรายจ่าย / ซื้อวัตถุดิบ</h2>
        <span></span>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">บันทึกข้อมูลเรียบร้อยแล้ว!</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-warning">ลบรายการเรียบร้อยแล้ว!</div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">ยอดรวมรายจ่ายทั้งหมด</h5>
                    <h2 class="mb-0"><?= number_format($total_expenses, 2) ?> ฿</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="12%">วันที่จ่าย</th>
                        <th>รายการ</th>
                        <th width="15%">จำนวน/หน่วย</th>
                        <th width="15%" class="text-end">ยอดเงิน (บาท)</th>
                        <th width="15%">ผู้บันทึก</th>
                        <th width="10%" class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($expenses)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">ยังไม่มีข้อมูลรายจ่าย</td></tr>
                    <?php else: ?>
                        <?php foreach($expenses as $ex): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($ex['expense_date'])) ?></td>
                            <td><?= htmlspecialchars($ex['item_name']) ?></td>
                            <td><?= (float)$ex['quantity'] . ' ' . htmlspecialchars($ex['unit']) ?></td>
                            <td class="text-end text-danger fw-bold"><?= number_format($ex['total_price'], 2) ?></td>
                            <td><?= htmlspecialchars($ex['recorded_by_name'] ?? 'ไม่ระบุ') ?></td>
                            <td class="text-center">
                                          <a href="<?= admin_escape(admin_url('expense_delete.php')) ?>?id=<?= $ex['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('แน่ใจหรือไม่ที่จะลบรายการนี้? (ยอดเงินจะถูกหักออกด้วย)')">
                                   <i class="fa-solid fa-trash"></i> ลบ
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= admin_escape(admin_url('expense_save.php')) ?>" method="POST">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">บันทึกรายจ่ายใหม่</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">วันที่จ่ายเงิน</label>
                        <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ชื่อรายการ (เช่น หมูสับ, ผักบุ้ง, ค่าแก๊ส)</label>
                        <input type="text" name="item_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">จำนวน</label>
                            <input type="number" step="0.01" name="quantity" class="form-control" placeholder="เช่น 5, 1.5">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">หน่วย</label>
                            <input type="text" name="unit" class="form-control" placeholder="เช่น กก., ถุง, แพ็ค">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-danger fw-bold">ยอดเงินรวมที่จ่าย (บาท) *</label>
                        <input type="number" step="0.01" name="total_price" class="form-control form-control-lg text-danger" required>
                    </div>
                    
                    <input type="hidden" name="recorded_by" value="1"> 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php admin_layout_end(); ?>