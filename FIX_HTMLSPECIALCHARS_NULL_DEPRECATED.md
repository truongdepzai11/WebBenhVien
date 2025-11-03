# ✅ FIX: DEPRECATED HTMLSPECIALCHARS() NULL

## 🎯 VẤN ĐỀ

**Lỗi:**
```
Deprecated: htmlspecialchars(): Passing null to parameter #1 ($string) 
of type string is deprecated
```

**Nguyên nhân:**
- Khi đặt gói khám: `doctor_name` = NULL, `appointment_time` = NULL
- View gọi `htmlspecialchars(NULL)` → Warning!

---

## ✅ GIẢI PHÁP

### **Kiểm tra NULL trước khi hiển thị:**

```php
// TRƯỚC (SAI):
<div><?= htmlspecialchars($apt['doctor_name']) ?></div>
// → Lỗi nếu doctor_name = NULL

// SAU (ĐÚNG):
<?php if (!empty($apt['doctor_name'])): ?>
    <div><?= htmlspecialchars($apt['doctor_name']) ?></div>
<?php else: ?>
    <div class="text-gray-400 italic">Chưa phân công</div>
<?php endif; ?>
```

---

## 📊 CÁC TRƯỜNG HỢP

### **1. Bác sĩ (doctor_name):**

**Khám thường:**
```php
$apt['doctor_name'] = 'BS. Nguyễn Văn A'; // ✅ Có giá trị
→ Hiển thị: "BS. Nguyễn Văn A"
```

**Khám gói (chưa phân công):**
```php
$apt['doctor_name'] = NULL; // ❌ NULL
→ Hiển thị: "Chưa phân công" (màu xám, italic)
```

---

### **2. Giờ khám (appointment_time):**

**Khám thường:**
```php
$apt['appointment_time'] = '10:00:00'; // ✅ Có giá trị
→ Hiển thị: "10:00"
```

**Khám gói (chưa phân công):**
```php
$apt['appointment_time'] = NULL; // ❌ NULL
→ Hiển thị: "Chưa xác định" (màu xám, italic)
```

---

## 💻 CODE ĐÃ SỬA

### **1. Cột Bác sĩ:**

```php
<?php if (!Auth::isDoctor()): ?>
<td class="px-6 py-4 whitespace-nowrap">
    <?php if (!empty($apt['doctor_name'])): ?>
        <div class="text-sm text-gray-900">
            <?= htmlspecialchars($apt['doctor_name']) ?>
        </div>
        <div class="text-xs text-gray-500">
            <?= htmlspecialchars($apt['specialization']) ?>
        </div>
    <?php else: ?>
        <div class="text-sm text-gray-400 italic">Chưa phân công</div>
    <?php endif; ?>
</td>
<?php endif; ?>
```

---

### **2. Cột Giờ khám:**

```php
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
    <?php if (!empty($apt['appointment_time'])): ?>
        <?= date('H:i', strtotime($apt['appointment_time'])) ?>
    <?php else: ?>
        <span class="text-gray-400 italic">Chưa xác định</span>
    <?php endif; ?>
</td>
```

---

## 🎨 GIAO DIỆN

### **Khám thường:**
```
┌──────────────────────────────────────────┐
│ APT001 | Nguyễn A | BS. Trần | 10:00 | ... │
│                     ↑          ↑           │
│                  Có bác sĩ   Có giờ       │
└──────────────────────────────────────────┘
```

### **Khám gói (chưa phân công):**
```
┌──────────────────────────────────────────────────┐
│ APT002 | huy le | Chưa phân công | Chưa xác định │
│                   ↑ (màu xám)     ↑ (màu xám)    │
└──────────────────────────────────────────────────┘
```

---

## 🔍 TẠI SAO CẦN FIX?

### **PHP 8.1+:**
- `htmlspecialchars(NULL)` → Deprecated warning
- Trong tương lai sẽ là lỗi fatal

### **Best Practice:**
```php
// ❌ SAI:
htmlspecialchars($value); // Có thể NULL

// ✅ ĐÚNG:
if (!empty($value)) {
    htmlspecialchars($value);
}

// HOẶC:
htmlspecialchars($value ?? ''); // Default empty string
```

---

## ✅ ĐÃ SỬA

1. ✅ Kiểm tra `doctor_name` trước khi `htmlspecialchars()`
2. ✅ Kiểm tra `specialization` trước khi `htmlspecialchars()`
3. ✅ Kiểm tra `appointment_time` trước khi `date()`
4. ✅ Hiển thị text thay thế khi NULL ("Chưa phân công", "Chưa xác định")

---

## 📁 FILES ĐÃ SỬA

1. ✅ `app/Views/appointments/index.php`

---

## 🚀 TEST

### **Test 1: Xem danh sách appointments**
```
1. Vào: /appointments
2. Kết quả: 
   - Khám thường: Hiển thị bác sĩ và giờ ✅
   - Khám gói: Hiển thị "Chưa phân công" và "Chưa xác định" ✅
   - KHÔNG có warning ✅
```

### **Test 2: Sau khi phân công**
```
1. Admin phân công bác sĩ cho gói khám
2. Vào: /appointments
3. Kết quả:
   - Hiển thị bác sĩ đã phân công ✅
   - Hiển thị giờ khám ✅
```

---

**REFRESH VÀ XEM KẾT QUẢ!** 🎉
