# 📱 MooPik POS - PWA Implementation Guide

## ✅ สิ่งที่ทำเสร็จแล้ว

### 🔧 TASK 1: Progressive Web App (PWA) ✓

#### ไฟล์ที่สร้างใหม่:
1. **`/manifest.json`** - PWA manifest file
   - ชื่อแอป: "MooPik POS"
   - Theme color: `#dc3545` (สีแดง Bootstrap)
   - Display mode: `standalone` (แสดงแบบ full screen เหมือน native app)
   - รองรับไอคอนหลายขนาด (72px - 512px)

2. **`/sw.js`** - Service Worker
   - Cache static resources (CSS, JS, Fonts)
   - Network-first strategy สำหรับ dynamic content
   - Auto-cleanup old caches

3. **`/app/assets/icons/README.md`** - คู่มือสร้างไอคอน PWA
   - แนะนำ 3 วิธีสร้างไอคอน
   - รายการขนาดไอคอนที่ต้องมี
   - วิธีทดสอบ PWA

---

### 🎨 TASK 2: Mobile Bottom Navigation ✓

#### ไฟล์ที่อัพเดท:

**1. Staff System** - [staff_layout.php](app/pages/staff/staff_layout.php)
```
พนักงานหน้าร้าน - Bottom Nav มี 4 ปุ่ม:
├─ 📋 โต๊ะ (staff_tables.php)
├─ 🛒 รับออเดอร์ (staff_order.php)  
├─ 🔔 คำขอลูกค้า (staff_requests.php)
└─ 🚪 ออกจากระบบ (staff_logout.php)
```

**2. Chef/Kitchen System** - [chef_layout.php](app/pages/chef/chef_layout.php)
```
เชฟ/ครัว - Bottom Nav มี 2 ปุ่ม:
├─ 🔥 คิวครัว (chef_kitchen.php) [สีส้ม #ff6f00]
└─ 🚪 ออกจากระบบ (chef_logout.php)
```

**3. Customer System**
- [customer_menu.php](app/pages/customer/customer_menu.php)
- [customer_orders.php](app/pages/customer/customer_orders.php)
```
ลูกค้า - Bottom Nav มี 4 ปุ่ม:
├─ 🍽️ เมนูอาหาร (customer_menu.php)
├─ 🛒 ตะกร้า (scroll to cart)
├─ 🕐 ประวัติ (customer_orders.php) [สีแดง #dc3545]
└─ 🚪 ออกจากระบบ (customer_logout.php)
```

---

## 🎯 คุณสมบัติหลัก

### PWA Meta Tags (ทุกหน้า):
```html
<!-- Mobile App Capable -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

<!-- Theme Colors -->
<meta name="theme-color" content="#dc3545"> <!-- Customer: red -->
<meta name="theme-color" content="#0d6efd"> <!-- Staff: blue -->
<meta name="theme-color" content="#ff6f00"> <!-- Chef: orange -->

<!-- PWA Links -->
<link rel="manifest" href="/manifest.json">
<link rel="apple-touch-icon" href="/app/assets/icons/icon-192x192.png">

<!-- Viewport with Safe Areas (iOS) -->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, viewport-fit=cover">
```

### Mobile Bottom Nav Features:
- ✅ **Responsive**: ซ่อนบน Desktop (> 768px), แสดงเฉพาะ Mobile
- ✅ **Fixed Position**: ติดด้านล่างหน้าจอตลอดเวลา
- ✅ **iOS Safe Area**: รองรับ `env(safe-area-inset-bottom)` สำหรับ iPhone X+
- ✅ **Active State**: Highlight ปุ่มหน้าปัจจุบันด้วยสี + scale animation
- ✅ **Icon + Text**: ใช้ FontAwesome 6 + ข้อความภาษาไทย
- ✅ **Touch-Friendly**: ปุ่มขนาดใหญ่ กดง่าย (48px+ touch target)

---

## 📋 การทดสอบ

### 1️⃣ ทดสอบบนมือถือ (Recommended):

**Android:**
1. เปิด Chrome → ไปที่ `http://localhost/moopikpos` หรือ IP ของเครื่อง
2. กด Menu (⋮) → "Install app" หรือ "Add to Home screen"
3. ไอคอนจะปรากฏบน Home Screen
4. เปิดแอปจาก Home Screen → ต้องแสดงแบบ full screen (ไม่มี URL bar)

**iOS (Safari):**
1. เปิด Safari → ไปที่ `http://localhost/moopikpos`
2. กด Share button (⬆️) → "Add to Home Screen"
3. ตั้งชื่อ "MooPik POS" → Add
4. เปิดจาก Home Screen → ต้องเป็น standalone app

### 2️⃣ ทดสอบบน Desktop (Chrome DevTools):

1. เปิด Chrome → กด F12 (DevTools)
2. กด Toggle Device Toolbar (Ctrl+Shift+M) → เลือก iPhone/Android
3. ไปที่แท็บ "Application"
   - **Manifest**: ต้องแสดง "MooPik POS" พร้อมไอคอน
   - **Service Workers**: ต้องเห็น "activated and is running"
   - **Cache Storage**: ต้องมี "moopik-pos-v1"
4. ทดสอบ Bottom Nav:
   - ลดขนาดหน้าจอ < 768px → Bottom Nav ต้องปรากฏ
   - Top navbar ต้องหายไป
   - กดปุ่มต่างๆ → ปุ่มปัจจุบันต้อง active (สีเปลี่ยน)

### 3️⃣ ทดสอบ Offline Mode:

1. เปิด DevTools → Network tab
2. เลือก "Offline" จาก dropdown
3. Reload หน้า → ยังโหลดได้ (จาก cache)
4. หน้า dynamic อาจจะเสีย แต่ CSS/JS/Fonts ต้องแสดง

---

## ⚠️ สิ่งที่ต้องทำเพิ่ม

### 🔴 สำคัญ! - สร้างไอคอน PWA:

**ตอนนี้ยังไม่มีไอคอนจริง** ต้องสร้างเอง 3 วิธี:

**วิธีที่ 1: ใช้ Online Tool (แนะนำ)**
```
1. ไปที่ https://www.pwabuilder.com/imageGenerator
2. อัปโหลดโลโก้ MooPik (512x512px)
3. Generate → ดาวน์โหลด ZIP
4. แตกไฟล์ → คัดลอกไปยัง /app/assets/icons/
```

**วิธีที่ 2: Adobe Photoshop/Figma**
```
สร้างไอคอน PNG ขนาด:
- 72x72, 96x96, 128x128, 144x144
- 152x152, 192x192, 384x384, 512x512

บันทึกไปยัง /app/assets/icons/icon-{size}.png
```

**วิธีที่ 3: Canva (ฟรี)**
```
1. สร้างดีไซน์ 512x512px
2. เพิ่มโลโก้ + พื้นหลังสีแดง (#dc3545)
3. Download → Resize เป็นขนาดต่างๆ
```

### 📁 โครงสร้างไฟล์ `/app/assets/icons/`:
```
app/assets/icons/
├── icon-72x72.png
├── icon-96x96.png
├── icon-128x128.png
├── icon-144x144.png
├── icon-152x152.png
├── icon-192x192.png   ← สำคัญ! (Apple touch icon)
├── icon-384x384.png
├── icon-512x512.png   ← สำคัญ! (PWA splash screen)
├── icon.svg           ← Placeholder (มีอยู่แล้ว)
└── README.md          ← คู่มือ (มีอยู่แล้ว)
```

---

## 🚀 ฟีเจอร์เพิ่มเติม (ถ้าต้องการ)

### 1. Push Notifications (แจ้งเตือนออเดอร์ใหม่):
- ต้องเพิ่ม `permission` request ใน Service Worker
- Backend ต้องส่ง push notification ผ่าน Firebase/OneSignal

### 2. Offline Data Sync:
- ใช้ IndexedDB เก็บ orders cache
- Sync กลับ server เมื่อ online

### 3. Install Prompt (Banner):
- เพิ่ม `beforeinstallprompt` event listener
- แสดงปุ่ม "ติดตั้งแอป" แบบ custom

---

## 🐛 Troubleshooting

### ❌ ไอคอนไม่โหลด
**แก้:** ตรวจสอบว่าไฟล์อยู่ที่ `/app/assets/icons/icon-192x192.png` และ manifest.json เรียกถูก path

### ❌ Service Worker ไม่ทำงาน
**แก้:** ต้องใช้ **HTTPS** หรือ **localhost** เท่านั้น (HTTP ธรรมดาไม่ได้)

### ❌ Bottom Nav ไม่แสดงบนมือถือ
**แก้:** 
1. Clear Browser Cache (Ctrl+Shift+Delete)
2. ตรวจสอบว่าหน้าจอ < 768px (ดูใน DevTools)
3. ตรวจสอบ Console มี CSS error หรือไม่

### ❌ iOS Safari ไม่แสดง Install Prompt
**แก้:** iOS ไม่มี auto-prompt ต้องเพิ่มด้วยตัวเอง (Share button → Add to Home Screen)

---

## 📊 สรุปการเปลี่ยนแปลง

| ไฟล์ | เปลี่ยนแปลง | PWA | Bottom Nav |
|------|-------------|-----|-----------|
| `manifest.json` | ✅ สร้างใหม่ | ✅ | - |
| `sw.js` | ✅ สร้างใหม่ | ✅ | - |
| `app/routes/web.php` | ✅ เพิ่ม routes | ✅ | - |
| `staff_layout.php` | ✅ อัพเดท | ✅ | ✅ 4 ปุ่ม |
| `chef_layout.php` | ✅ อัพเดท | ✅ | ✅ 2 ปุ่ม |
| `customer_menu.php` | ✅ อัพเดท | ✅ | ✅ 4 ปุ่ม |
| `customer_orders.php` | ✅ อัพเดท | ✅ | ✅ 4 ปุ่ม |
| `app/assets/icons/` | ⚠️ ต้องสร้าง | ⚠️ | - |

---

## 🎓 อ้างอิง

- [PWA Documentation - MDN](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)
- [Web App Manifest](https://web.dev/add-manifest/)
- [iOS PWA Support](https://webkit.org/blog/7929/service-workers-and-pwa/)
- [Safe Area Insets (iOS)](https://webkit.org/blog/7929/designing-websites-for-iphone-x/)

---

**Created:** February 20, 2026  
**Developer:** Frontend Expert (PHP + Bootstrap 5 + PWA)  
**Status:** ✅ Production Ready (ยกเว้นไอคอน)
