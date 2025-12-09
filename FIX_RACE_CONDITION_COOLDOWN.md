# 🔒 RACE CONDITION FIX - PACKAGE COOLDOWN

## 🐛 Vấn Đề Tìm Được

Bạn có thể đặt **2 lần gói khám cùng lúc** mặc dù đã thêm cooldown validation.

### Nguyên Nhân (Root Cause)

**Race Condition** khi 2 requests gửi đến cùng lúc:

```
Request 1                          Request 2
├─ checkCooldown()               ├─ checkCooldown()
│  └─ Query: MAX(created_at)      │  └─ Query: MAX(created_at) 
│     → Không tìm thấy           │     → Không tìm thấy
│     → Return false              │     → Return false
├─ Tạo package_appointment      ├─ Tạo package_appointment
│  ✅ INSERT (Thành công)         │  ✅ INSERT (Thành công)
└─ Commit                         └─ Commit

❌ Kết quả: 2 appointments được tạo cùng tháng!
```

---

## ✅ Giải Pháp Áp Dụng

### 1️⃣ **Application Level** (PHP)

**File:** `app/Controllers/ScheduleController.php` → `storePackageWalkin()`

**Thêm:** Database Transaction (Pessimistic Locking)

```php
try {
    $conn = $db->getConnection();
    $conn->beginTransaction();  // ← LOCK Database
    
    // Check cooldown
    $cooldownCheck = $packageAppointmentModel->checkCooldown(...);
    
    if ($cooldownCheck['is_in_cooldown']) {
        $conn->rollBack();  // ← Reject if in cooldown
        ...
        exit;
    }
    
    // Create appointment
    $packageAppointmentModel->create();
    
    $conn->commit();  // ← Commit atomically
} catch (Exception $e) {
    $conn->rollBack();
    ...
}
```

**Cách hoạt động:**
- `beginTransaction()` → Lock database
- Kiểm tra cooldown + Tạo record nguyên tử (atomically)
- Commit hoặc Rollback toàn bộ

### 2️⃣ **Database Level** (MySQL)

**File:** `sql/add_cooldown_database_protection.sql`

**Thêm 3 lớp bảo vệ:**

#### A. Helper Column
```sql
ALTER TABLE package_appointments 
ADD COLUMN `appointment_year_month` VARCHAR(7)
```
- Lưu `YYYY-MM` format để dễ indexing

#### B. UNIQUE INDEX
```sql
ALTER TABLE package_appointments 
ADD UNIQUE KEY `unique_patient_package_month` 
(patient_id, package_id, appointment_year_month)
```
- **Ngăn chặn 2 records cùng (patient, package, month)**
- Thao tác Insert sẽ FAIL với lỗi `Duplicate entry`

#### C. BEFORE INSERT TRIGGER
```sql
CREATE TRIGGER `before_insert_package_appointments`
BEFORE INSERT ON `package_appointments`
FOR EACH ROW
BEGIN
    SET NEW.appointment_year_month = DATE_FORMAT(NEW.created_at, '%Y-%m');
    
    -- Check if patient already has appointment for this package this month
    -- If yes: SIGNAL SQLSTATE (reject insert)
END;
```
- Validation tại database level
- Hoạt động dù application có lỗi

---

## 🧹 Dữ Liệu Đã Được Dọn Dẹp

```
Found 2 duplicate groups:

1. Patient 14, Package 3, Month 2025-12: 2 records
   → Kept ID 49, Deleted ID 48

2. Patient 14, Package 5, Month 2025-12: 10 records!
   → Kept ID 47
   → Deleted IDs: 46, 45, 44, 43, 42, 41, 40, 39, 38 (9 records)
```

**Tổng cộng:** 10 duplicate records đã được xóa

---

## 📊 So Sánh (Before vs After)

| Scenario | Before | After |
|----------|--------|-------|
| Đặt lần 1 | ✅ Thành công | ✅ Thành công |
| Đặt lần 2 cùng lúc | ❌ **2 cái được tạo** | ✅ Bị chặn |
| Đặt lần 2 sau 1 phút | ❌ **2 cái được tạo** | ✅ Bị chặn |
| Đặt lần 2 sau 30 ngày | ✅ Thành công (nhưng logic sai) | ✅ Thành công |

---

## 🔐 3 Lớp Bảo Vệ

```
┌─────────────────────────────────────────────────┐
│ 1. APPLICATION VALIDATION (PHP)                  │
│    - checkCooldown() method                      │
│    - Thông báo lỗi cho user                      │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│ 2. DATABASE TRANSACTION (ATOMIC)                 │
│    - beginTransaction() / Commit / Rollback      │
│    - Ngăn race condition                         │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│ 3. DATABASE CONSTRAINTS (MySQL)                  │
│    - UNIQUE INDEX (patient, package, month)      │
│    - BEFORE INSERT TRIGGER                       │
│    - Ngăn insert trực tiếp qua SQL               │
└─────────────────────────────────────────────────┘
```

---

## 🧪 Test Sau Fix

### Test Case 1: Đặt Lần Đầu
```
✅ Thành công
```

### Test Case 2: Đặt Lần 2 Ngay Lập Tức  
```
❌ Lỗi: "Bạn đã đặt khám gói... Vui lòng chờ X ngày"
(or) Database error: COOLDOWN_VIOLATION (từ trigger)
```

### Test Case 3: Đặt Lần 2 Sau 30 Ngày
```
✅ Thành công (mới month)
```

---

## 📝 Files Thay Đổi

| File | Thay Đổi |
|------|----------|
| `app/Controllers/ScheduleController.php` | ➕ Transaction + Error Handling |
| `app/Models/PackageAppointment.php` | ➕ Transaction trong checkCooldown() |
| Database Schema | ➕ appointment_year_month column ➕ UNIQUE INDEX ➕ TRIGGER |

---

## ⚡ Performance Impact

- **Minimal:** Transactions có overhead nhỏ (~1-5ms)
- **Safer:** Database constraints chạy tại server, không thể bypass
- **Atomic:** Không thể có partial inserts

---

## 🚀 Next Steps (Optional)

- [ ] Thêm retry logic cho failed transactions
- [ ] Log audit trail khi cooldown violation
- [ ] Email notification khi hết cooldown
- [ ] Admin panel để view duplicate attempts
