# 🚀 BƯỚC 4: THUẬT TOÁN PHÂN CÔNG BÁC SĨ THÔNG MINH

## 🎯 MỤC TIÊU

Nâng cấp thuật toán phân công bác sĩ từ **đơn giản** → **thông minh**:
- ✅ Phân công theo **chuyên môn** phù hợp
- ✅ Tối ưu **thời gian** (cách nhau 30 phút)
- ✅ Tránh **trùng lịch**
- ✅ Phân bổ **đều** giữa các bác sĩ

---

## 📊 PHÂN LOẠI DỊCH VỤ THEO CHUYÊN KHOA

### **Mapping dịch vụ → Chuyên khoa:**

```php
$serviceSpecializationMap = [
    // Khám lâm sàng
    'Khám nội tổng quát' => 'Nội khoa',
    'Khám tim mạch' => 'Tim mạch',
    'Khám hô hấp' => 'Hô hấp',
    'Khám tiêu hóa' => 'Tiêu hóa',
    'Khám thần kinh' => 'Thần kinh',
    'Khám mắt' => 'Mắt',
    'Khám tai mũi họng' => 'Tai Mũi Họng',
    'Khám răng hàm mặt' => 'Răng Hàm Mặt',
    'Khám da liễu' => 'Da liễu',
    'Khám cơ xương khớp' => 'Cơ Xương Khớp',
    
    // Xét nghiệm & Chẩn đoán hình ảnh
    'Xét nghiệm máu' => 'Xét nghiệm',
    'Xét nghiệm nước tiểu' => 'Xét nghiệm',
    'Siêu âm bụng' => 'Chẩn đoán hình ảnh',
    'X-quang phổi' => 'Chẩn đoán hình ảnh',
    'Điện tim' => 'Tim mạch',
];
```

---

## 🧠 THUẬT TOÁN THÔNG MINH

### **Bước 1: Phân loại dịch vụ**

```php
function categorizeServices($services) {
    $clinical = [];      // Khám lâm sàng (cần bác sĩ chuyên khoa)
    $laboratory = [];    // Xét nghiệm (bác sĩ xét nghiệm)
    $imaging = [];       // Chẩn đoán hình ảnh (bác sĩ CĐHA)
    
    foreach ($services as $service) {
        if (strpos($service['service_name'], 'Xét nghiệm') !== false) {
            $laboratory[] = $service;
        } elseif (strpos($service['service_name'], 'Siêu âm') !== false || 
                  strpos($service['service_name'], 'X-quang') !== false) {
            $imaging[] = $service;
        } else {
            $clinical[] = $service;
        }
    }
    
    return [$clinical, $laboratory, $imaging];
}
```

---

### **Bước 2: Tìm bác sĩ phù hợp theo chuyên môn**

```php
function findDoctorBySpecialization($serviceName, $date, $time) {
    global $serviceSpecializationMap;
    
    // Lấy chuyên khoa phù hợp
    $requiredSpec = $serviceSpecializationMap[$serviceName] ?? null;
    
    if (!$requiredSpec) {
        // Nếu không map được, lấy bác sĩ đa khoa
        $requiredSpec = 'Nội khoa';
    }
    
    // Tìm bác sĩ có chuyên khoa phù hợp
    $query = "SELECT d.*, u.full_name 
              FROM doctors d
              LEFT JOIN users u ON d.user_id = u.id
              LEFT JOIN specializations s ON d.specialization_id = s.id
              WHERE s.name = :specialization
              AND d.is_available = 1
              ORDER BY d.total_patients ASC"; // Ưu tiên bác sĩ ít bệnh nhân
    
    $doctors = executeQuery($query, ['specialization' => $requiredSpec]);
    
    // Kiểm tra từng bác sĩ xem có rảnh không
    foreach ($doctors as $doctor) {
        if (isDoctorAvailable($doctor['id'], $date, $time)) {
            return $doctor;
        }
    }
    
    return null;
}
```

---

### **Bước 3: Phân bổ thời gian thông minh**

```php
function scheduleAppointments($services, $startDate) {
    list($clinical, $laboratory, $imaging) = categorizeServices($services);
    
    $schedule = [];
    $currentDate = clone $startDate;
    $currentTime = new DateTime('08:00:00');
    
    // NGÀY 1 BUỔI SÁNG (8h-12h): Khám lâm sàng
    foreach ($clinical as $service) {
        $doctor = findDoctorBySpecialization(
            $service['service_name'], 
            $currentDate, 
            $currentTime
        );
        
        if ($doctor) {
            $schedule[] = [
                'service' => $service,
                'doctor' => $doctor,
                'date' => $currentDate->format('Y-m-d'),
                'time' => $currentTime->format('H:i:s')
            ];
            
            $currentTime->modify('+30 minutes');
            
            // Nếu quá 12h, nghỉ trưa
            if ($currentTime->format('H') >= 12) {
                $currentTime = new DateTime('13:00:00');
            }
        }
    }
    
    // NGÀY 1 BUỔI CHIỀU (13h-17h): Xét nghiệm
    foreach ($laboratory as $service) {
        $doctor = findDoctorBySpecialization(
            $service['service_name'], 
            $currentDate, 
            $currentTime
        );
        
        if ($doctor) {
            $schedule[] = [
                'service' => $service,
                'doctor' => $doctor,
                'date' => $currentDate->format('Y-m-d'),
                'time' => $currentTime->format('H:i:s')
            ];
            
            $currentTime->modify('+20 minutes'); // Xét nghiệm nhanh hơn
            
            // Nếu quá 17h, chuyển sang ngày hôm sau
            if ($currentTime->format('H') >= 17) {
                $currentDate->modify('+1 day');
                $currentTime = new DateTime('08:00:00');
            }
        }
    }
    
    // NGÀY 2: Chẩn đoán hình ảnh
    foreach ($imaging as $service) {
        $doctor = findDoctorBySpecialization(
            $service['service_name'], 
            $currentDate, 
            $currentTime
        );
        
        if ($doctor) {
            $schedule[] = [
                'service' => $service,
                'doctor' => $doctor,
                'date' => $currentDate->format('Y-m-d'),
                'time' => $currentTime->format('H:i:s')
            ];
            
            $currentTime->modify('+30 minutes');
        }
    }
    
    return $schedule;
}
```

---

## 📅 VÍ DỤ PHÂN CÔNG

**Gói khám tổng quát - Nam (15 dịch vụ):**

### **NGÀY 1 - 05/11/2025:**

**Buổi sáng (8h-12h) - Khám lâm sàng:**
| Giờ | Dịch vụ | Bác sĩ | Chuyên khoa |
|-----|---------|--------|-------------|
| 08:00 | Khám nội tổng quát | BS. Nguyễn Văn A | Nội khoa |
| 08:30 | Khám tim mạch | BS. Trần Thị B | Tim mạch |
| 09:00 | Khám mắt | BS. Lê Văn C | Mắt |
| 09:30 | Khám tai mũi họng | BS. Phạm Thị D | TMH |
| 10:00 | Khám răng hàm mặt | BS. Hoàng Văn E | RHM |
| 10:30 | Khám da liễu | BS. Vũ Thị F | Da liễu |
| 11:00 | Khám cơ xương khớp | BS. Đỗ Văn G | CXK |

**Buổi chiều (13h-17h) - Xét nghiệm:**
| Giờ | Dịch vụ | Bác sĩ | Chuyên khoa |
|-----|---------|--------|-------------|
| 13:00 | Xét nghiệm máu tổng quát | BS. Ngô Thị H | Xét nghiệm |
| 13:20 | Xét nghiệm đường huyết | BS. Ngô Thị H | Xét nghiệm |
| 13:40 | Xét nghiệm mỡ máu | BS. Ngô Thị H | Xét nghiệm |
| 14:00 | Xét nghiệm chức năng gan | BS. Ngô Thị H | Xét nghiệm |
| 14:20 | Xét nghiệm chức năng thận | BS. Ngô Thị H | Xét nghiệm |

### **NGÀY 2 - 06/11/2025:**

**Buổi sáng (8h-12h) - Chẩn đoán hình ảnh:**
| Giờ | Dịch vụ | Bác sĩ | Chuyên khoa |
|-----|---------|--------|-------------|
| 08:00 | Siêu âm bụng tổng quát | BS. Bùi Văn I | CĐHA |
| 08:30 | X-quang phổi | BS. Bùi Văn I | CĐHA |
| 09:00 | Điện tim | BS. Trần Thị B | Tim mạch |

---

## 💡 ƯU ĐIỂM THUẬT TOÁN MỚI

### **1. Phân công theo chuyên môn:**
✅ Khám tim mạch → Bác sĩ Tim mạch
✅ Khám mắt → Bác sĩ Mắt
✅ Xét nghiệm → Bác sĩ Xét nghiệm

### **2. Tối ưu thời gian:**
✅ Khám lâm sàng: 30 phút/dịch vụ
✅ Xét nghiệm: 20 phút/dịch vụ (nhanh hơn)
✅ Chẩn đoán hình ảnh: 30 phút/dịch vụ

### **3. Phân bổ hợp lý:**
✅ Buổi sáng: Khám lâm sàng (cần tập trung)
✅ Buổi chiều: Xét nghiệm (có thể làm nhanh)
✅ Ngày 2: Chẩn đoán hình ảnh

### **4. Cân bằng tải:**
✅ Ưu tiên bác sĩ có ít bệnh nhân hơn
✅ Tránh 1 bác sĩ bị quá tải

---

## 🔧 TRIỂN KHAI

Tôi sẽ cập nhật method `autoAssignDoctors()` và `findSuitableDoctor()` trong `PackageAppointmentController` với thuật toán mới này.

**Bạn muốn tôi triển khai ngay không?** 🚀
