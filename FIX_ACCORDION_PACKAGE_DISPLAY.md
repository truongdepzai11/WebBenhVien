# ✅ FIX: HIỂN THỊ GÓI KHÁM DẠNG ACCORDION

## 🎯 YÊU CẦU

**Theo hình ảnh:**
- 🟡 **Dòng màu vàng (#PKG1):** Dòng tổng hợp gói khám
- 🔴 **19 dòng màu đỏ:** Chi tiết các dịch vụ trong gói
- 📌 **Mặc định:** Ẩn 19 dòng
- 👆 **Click vào #PKG1:** Hiện/ẩn 19 dòng (accordion)

---

## ✅ GIẢI PHÁP

### **1. Dòng tổng hợp (màu vàng):**

```html
<tr class="bg-yellow-50 cursor-pointer" 
    onclick="togglePackageDetails('pkg-1')">
    <td>
        <i class="fas fa-chevron-right" id="icon-pkg-1"></i>
        #PKG1
    </td>
    <td>huy le dinh tran</td>
    <td>📦 Khám theo gói</td>
    <td>01/11/2025</td>
    <td>Nhiều dịch vụ</td>
    <td>Chờ khám</td>
</tr>
```

---

### **2. Chi tiết dịch vụ (màu đỏ, ẩn mặc định):**

```html
<!-- Dịch vụ 1 -->
<tr class="hidden bg-red-50 package-details-pkg-1">
    <td class="pl-12">APT00245</td>
    <td>huy le</td>
    <td>BS. Trần Thị B</td>
    <td>13/11/2025</td>
    <td>16:00</td>
    <td>HCV Ab miễn dịch tự động</td>
    <td>Chờ xác nhận</td>
</tr>

<!-- Dịch vụ 2 -->
<tr class="hidden bg-red-50 package-details-pkg-1">
    ...
</tr>

<!-- ... 17 dòng nữa -->
```

---

### **3. JavaScript toggle:**

```javascript
function togglePackageDetails(pkgId) {
    // Lấy tất cả dòng chi tiết
    const detailRows = document.querySelectorAll('.package-details-' + pkgId);
    const icon = document.getElementById('icon-' + pkgId);
    
    // Toggle hiển thị
    detailRows.forEach(row => {
        row.classList.toggle('hidden');
    });
    
    // Xoay icon (→ thành ↓)
    if (icon) {
        icon.classList.toggle('fa-chevron-right');
        icon.classList.toggle('fa-chevron-down');
    }
}
```

---

## 📊 FLOW

### **Mặc định (Collapsed):**
```
┌─────────────────────────────────────────┐
│ → #PKG1 | huy le | 📦 Khám theo gói    │ ← Màu vàng
├─────────────────────────────────────────┤
│ APT00001 | Nguyễn A | Khám thường      │ ← Màu trắng
└─────────────────────────────────────────┘
```

### **Sau khi click #PKG1 (Expanded):**
```
┌─────────────────────────────────────────┐
│ ↓ #PKG1 | huy le | 📦 Khám theo gói    │ ← Màu vàng
│   APT00245 | huy le | BS. Trần | 13/11 │ ← Màu đỏ
│   APT00244 | huy le | BS. Trần | 13/11 │ ← Màu đỏ
│   APT00243 | huy le | BS. Trần | 13/11 │ ← Màu đỏ
│   ... (16 dòng nữa)                     │
├─────────────────────────────────────────┤
│ APT00001 | Nguyễn A | Khám thường      │ ← Màu trắng
└─────────────────────────────────────────┘
```

### **Click lại #PKG1 (Collapse):**
```
┌─────────────────────────────────────────┐
│ → #PKG1 | huy le | 📦 Khám theo gói    │ ← Màu vàng
├─────────────────────────────────────────┤
│ APT00001 | Nguyễn A | Khám thường      │ ← Màu trắng
└─────────────────────────────────────────┘
```

---

## 🎨 THIẾT KẾ

### **Màu sắc:**
- 🟡 **Dòng tổng hợp:** `bg-yellow-50`
- 🔴 **Chi tiết dịch vụ:** `bg-red-50`
- ⚪ **Khám thường:** `bg-white`

### **Icon:**
- **→** (`fa-chevron-right`): Collapsed
- **↓** (`fa-chevron-down`): Expanded

### **Cursor:**
- Dòng tổng hợp: `cursor-pointer` (có thể click)
- Chi tiết: Không có cursor đặc biệt

---

## 💡 TÍNH NĂNG

✅ **Accordion:** Click để expand/collapse
✅ **Màu phân biệt:** Vàng (tổng hợp) vs Đỏ (chi tiết)
✅ **Icon động:** Xoay khi expand
✅ **Indent:** Chi tiết thụt vào (pl-12)
✅ **Giữ link:** Click icon "👁️" vẫn mở trang chi tiết

---

## 🔧 KỸ THUẬT

### **1. CSS Classes:**
```css
.hidden { display: none; }
.bg-yellow-50 { background-color: #fefce8; }
.bg-red-50 { background-color: #fef2f2; }
.cursor-pointer { cursor: pointer; }
.pl-12 { padding-left: 3rem; }
```

### **2. Event Handling:**
```html
<!-- Dòng tổng hợp -->
<tr onclick="togglePackageDetails('pkg-1')">
    ...
</tr>

<!-- Link chi tiết (ngăn event bubble) -->
<a onclick="event.stopPropagation();">
    <i class="fas fa-eye"></i>
</a>
```

---

## ✅ ĐÃ SỬA

1. ✅ Dòng tổng hợp màu vàng với icon →
2. ✅ 19 dòng chi tiết màu đỏ (ẩn mặc định)
3. ✅ JavaScript toggle expand/collapse
4. ✅ Icon xoay khi expand
5. ✅ Indent chi tiết để phân biệt

---

## 🚀 TEST

**Bước 1:** Vào `/appointments`

**Kết quả:**
- ✅ Thấy 1 dòng màu vàng: "→ #PKG1"
- ✅ KHÔNG thấy 19 dòng màu đỏ

**Bước 2:** Click vào dòng màu vàng

**Kết quả:**
- ✅ Icon xoay: → thành ↓
- ✅ Hiện 19 dòng màu đỏ bên dưới
- ✅ Mỗi dòng thụt vào (indent)

**Bước 3:** Click lại

**Kết quả:**
- ✅ Icon xoay lại: ↓ thành →
- ✅ Ẩn 19 dòng màu đỏ

---

**REFRESH VÀ TEST NGAY!** 🎉
