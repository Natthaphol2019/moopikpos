<?php
if (!function_exists('staff_escape')) {
    function staff_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('staff_base_path')) {
    function staff_base_path()
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = rtrim(dirname($scriptName), '/');

        if ($basePath === '' || $basePath === '.') {
            return '';
        }

        return $basePath;
    }
}

if (!function_exists('staff_url')) {
    function staff_url($path)
    {
        $normalizedPath = '/' . ltrim((string) $path, '/');
        return staff_base_path() . $normalizedPath;
    }
}

if (!function_exists('staff_layout_start')) {
    function staff_layout_start($title, $pageHeading, $pageSubheading = '', $extraHead = '')
    {
        $safeTitle = staff_escape($title);
        $safeHeading = staff_escape($pageHeading);
        $safeSubheading = staff_escape($pageSubheading);

        $currentPage = basename(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH));
        $isOrderPage = $currentPage === 'staff_order.php';
        $isRequestPage = $currentPage === 'staff_requests.php';
        $isTablePage = $currentPage === 'staff_tables.php';

        echo '<!DOCTYPE html>';
        echo '<html lang="th">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        echo '<title>' . $safeTitle . '</title>';
        echo '<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
        echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
        echo '<style>';
        echo 'body{font-family:\'Prompt\',sans-serif;background:#f4f6f9;color:#1f2937;}';
        echo '.staff-topbar{position:sticky;top:0;z-index:1030;background:#fff;border-bottom:1px solid #e5e7eb;padding:12px 0;}';
        echo '.staff-shell{padding:18px 0 0;}';
        echo '.staff-title{font-size:1.2rem;font-weight:600;margin:0;}';
        echo '.staff-subtitle{margin:2px 0 0;color:#6b7280;font-size:.9rem;}';
        echo '</style>';
        echo $extraHead;
        echo '</head>';
        echo '<body>';
        echo '<header class="staff-topbar">';
        echo '<div class="container-fluid px-3 px-md-4 d-flex justify-content-between align-items-center">';
        echo '<div>';
        echo '<h1 class="staff-title">' . $safeHeading . '</h1>';
        if ($safeSubheading !== '') {
            echo '<p class="staff-subtitle">' . $safeSubheading . '</p>';
        }
        echo '</div>';
        echo '<div class="d-flex align-items-center gap-2">';
        echo '<span class="badge bg-light text-dark border"><i class="fa-regular fa-user me-1"></i>' . staff_escape(function_exists('staff_current_user_name') ? staff_current_user_name() : 'พนักงาน') . '</span>';
        echo '<a class="btn btn-sm btn-outline-danger" href="' . staff_escape(staff_url('staff_logout.php')) . '"><i class="fa-solid fa-right-from-bracket me-1"></i>ออกจากระบบ</a>';
        echo '</div>';
        echo '</div>';
        echo '</header>';
        echo '<div class="container-fluid px-3 px-md-4 mb-2">';
        echo '<div class="d-flex gap-2 flex-wrap">';
        echo '<a class="btn btn-sm ' . ($isOrderPage ? 'btn-primary' : 'btn-outline-primary') . '" href="' . staff_escape(staff_url('staff_order.php')) . '"><i class="fa-solid fa-cart-plus me-1"></i>รับออเดอร์หน้าร้าน</a>';
        echo '<a class="btn btn-sm ' . ($isTablePage ? 'btn-primary' : 'btn-outline-primary') . '" href="' . staff_escape(staff_url('staff_tables.php')) . '"><i class="fa-solid fa-table-cells me-1"></i>แผนผังโต๊ะ</a>';
        echo '<a class="btn btn-sm ' . ($isRequestPage ? 'btn-primary' : 'btn-outline-primary') . '" href="' . staff_escape(staff_url('staff_requests.php')) . '"><i class="fa-solid fa-bell-concierge me-1"></i>คำขอลูกค้าออนไลน์</a>';
        echo '</div>';
        echo '</div>';
        echo '<main class="staff-shell">';
    }
}

if (!function_exists('staff_layout_end')) {
    function staff_layout_end($extraScripts = '')
    {
        echo '</main>';
        echo '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>';
        echo $extraScripts;
        echo '</body>';
        echo '</html>';
    }
}
