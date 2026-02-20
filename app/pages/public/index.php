<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบบริหารจัดการร้านอาหาร - Restaurant POS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .system-card {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border: none;
            border-radius: 15px;
            overflow: hidden;
        }
        .system-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .icon-box {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .card-title {
            font-weight: bold;
            font-size: 1.2rem;
        }
        .hero-title {
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            color: #2c3e50;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

    <div class="container text-center">
        <div class="hero-title">
            <h1>🍽️ Restaurant POS System</h1>
            <p class="lead text-muted">ระบบบริหารจัดการร้านอาหารแบบครบวงจร</p>
        </div>

        <div class="row justify-content-center g-4">
            
            <div class="col-md-4 col-lg-3">
                <a href="staff_login.php" class="text-decoration-none">
                    <div class="card system-card h-100 p-4 text-center">
                        <div class="card-body">
                            <div class="icon-box text-primary">
                                <i class="fas fa-tablet-alt"></i>
                            </div>
                            <h5 class="card-title text-dark">พนักงานหน้าร้าน</h5>
                            <p class="card-text text-muted small">รับออเดอร์ สั่งอาหาร และเช็คบิล</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <button class="btn btn-outline-primary w-100">เข้าสู่ระบบ</button>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="kitchen_queue.php" class="text-decoration-none">
                    <div class="card system-card h-100 p-4 text-center">
                        <div class="card-body">
                            <div class="icon-box text-warning">
                                <i class="fas fa-fire-alt"></i>
                            </div>
                            <h5 class="card-title text-dark">ระบบครัว (KDS)</h5>
                            <p class="card-text text-muted small">ดูรายการคิว จัดเตรียมอาหาร</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <button class="btn btn-outline-warning w-100">ดูคิวอาหาร</button>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="admin_login.php" class="text-decoration-none">
                    <div class="card system-card h-100 p-4 text-center">
                        <div class="card-body">
                            <div class="icon-box text-success">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5 class="card-title text-dark">ผู้จัดการ / เจ้าของ</h5>
                            <p class="card-text text-muted small">จัดการเมนู ดูยอดขาย และสต็อก</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <button class="btn btn-outline-success w-100">จัดการหลังร้าน</button>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4 col-lg-3">
                <a href="customer_login.php" class="text-decoration-none">
                    <div class="card system-card h-100 p-4 text-center bg-light">
                        <div class="card-body">
                            <div class="icon-box text-secondary">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <h5 class="card-title text-dark">ลูกค้า (Scan)</h5>
                            <p class="card-text text-muted small">สั่งอาหารผ่านเว็บโดยตรง</p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <button class="btn btn-outline-secondary w-100">เมนูลูกค้า</button>
                        </div>
                    </div>
                </a>
            </div>

        </div>

        <div class="mt-5 text-muted small">
            <div class="mb-3">
                <a href="register.php" class="btn btn-sm btn-dark">สมัครบัญชีผู้ใช้งาน</a>
            </div>
            &copy; <?php echo date("Y"); ?> IT Gen Restaurant Project. All rights reserved.<br>
            พัฒนาโดย: [ชื่อ-นามสกุล ของคุณ]
        </div>
    </div>

</body>
</html>