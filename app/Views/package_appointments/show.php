<?php 
$page_title = 'Chi tiết Gói khám - ' . $packageAppointment['package_name'];
ob_start(); 
?>

<div class="mb-6">
    <a href="<?= APP_URL ?>/package-appointments" 
       class="text-purple-600 hover:text-purple-800 flex items-center mb-4">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
    </a>
    
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">
                Chi tiết Gói khám #<?= $packageAppointment['id'] ?>
            </h3>
            <p class="text-gray-600 mt-1"><?= htmlspecialchars($packageAppointment['package_name']) ?></p>
        </div>
        
        <?php
        $statusColors = [
            'scheduled' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800'
        ];
        $statusLabels = [
            'scheduled' => 'Chờ phân công',
            'in_progress' => 'Đang thực hiện',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy'
        ];
        $colorClass = $statusColors[$packageAppointment['status']] ?? 'bg-gray-100 text-gray-800';
        $label = $statusLabels[$packageAppointment['status']] ?? $packageAppointment['status'];
        ?>
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full <?= $colorClass ?>">
            <?= $label ?>
        </span>
    </div>
</div>

<!-- Thông tin bệnh nhân & gói khám -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Thông tin bệnh nhân -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-user mr-2 text-purple-600"></i>Thông tin bệnh nhân
        </h4>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-600">Họ tên</p>
                <p class="text-base font-medium text-gray-900">
                    <?= htmlspecialchars($packageAppointment['patient_name']) ?>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Mã bệnh nhân</p>
                <p class="text-base font-medium text-gray-900">
                    <?= htmlspecialchars($packageAppointment['patient_code']) ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Thông tin gói khám -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-box-open mr-2 text-green-600"></i>Thông tin gói khám
        </h4>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-600">Tên gói</p>
                <p class="text-base font-medium text-gray-900">
                    <?= htmlspecialchars($packageAppointment['package_name']) ?>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Ngày khám dự kiến</p>
                <p class="text-base font-medium text-gray-900">
                    <i class="fas fa-calendar mr-1 text-gray-400"></i>
                    <?= date('d/m/Y', strtotime($packageAppointment['appointment_date'])) ?>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Số dịch vụ</p>
                <p class="text-base font-medium text-gray-900">
                    <?= count($packageServices) ?> dịch vụ
                </p>
            </div>
        </div>
    </div>
    
    <!-- Thông tin đăng ký -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2 text-blue-600"></i>Thông tin đăng ký
        </h4>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-600">Người đăng ký</p>
                <p class="text-base font-medium text-gray-900">
                    <?= htmlspecialchars($packageAppointment['created_by_name'] ?? 'N/A') ?>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Ngày đăng ký</p>
                <p class="text-base font-medium text-gray-900">
                    <?= date('d/m/Y H:i', strtotime($packageAppointment['created_at'])) ?>
                </p>
            </div>
            <?php if ($packageAppointment['notes']): ?>
            <div>
                <p class="text-sm text-gray-600">Ghi chú</p>
                <p class="text-base text-gray-900">
                    <?= nl2br(htmlspecialchars($packageAppointment['notes'])) ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Nút phân công tự động -->
<?php if ((Auth::isAdmin() || Auth::isReceptionist()) && $packageAppointment['status'] == 'scheduled' && empty($appointments)): ?>
<div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="text-white">
            <h4 class="text-xl font-bold mb-2">
                <i class="fas fa-magic mr-2"></i>Phân công bác sĩ tự động
            </h4>
            <p class="text-purple-100">
                Hệ thống sẽ tự động phân công <?= count($packageServices) ?> bác sĩ phù hợp cho các dịch vụ trong gói khám
            </p>
        </div>
        <form action="<?= APP_URL ?>/package-appointments/<?= $packageAppointment['id'] ?>/auto-assign" 
              method="POST"
              onsubmit="return confirm('Bạn có chắc muốn phân công tự động?\n\nHệ thống sẽ tạo <?= count($packageServices) ?> lịch khám với các bác sĩ rảnh.')">
            <button type="submit" 
                    class="px-8 py-3 bg-white text-purple-600 font-semibold rounded-lg hover:bg-purple-50 transition shadow-lg">
                <i class="fas fa-robot mr-2"></i>Phân công ngay
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Danh sách dịch vụ & lịch khám -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h4 class="text-lg font-bold text-gray-800 flex items-center">
            <i class="fas fa-list-check mr-2"></i>
            Danh sách dịch vụ & Lịch khám
            <span class="ml-2 text-sm font-normal text-gray-600">
                (<?= count($appointments) ?>/<?= count($packageServices) ?> đã phân công)
            </span>
        </h4>
    </div>
    
    <div class="p-6">
        <?php if (empty($packageServices)): ?>
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Gói khám không có dịch vụ nào</p>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php 
            // Tạo map appointments theo service (match linh hoạt)
            $appointmentMap = [];
            foreach ($appointments as $apt) {
                // Lưu theo reason chính xác
                $appointmentMap[$apt['reason']] = $apt;
                
                // Cũng lưu theo reason đã trim và lowercase để match dễ hơn
                $cleanReason = strtolower(trim($apt['reason']));
                $appointmentMap[$cleanReason] = $apt;
            }
            
            foreach ($packageServices as $index => $service): 
                // Thử match chính xác trước
                $hasAppointment = isset($appointmentMap[$service['service_name']]);
                $appointment = $hasAppointment ? $appointmentMap[$service['service_name']] : null;
                
                // Nếu không match, thử match linh hoạt
                if (!$hasAppointment) {
                    $cleanServiceName = strtolower(trim($service['service_name']));
                    $hasAppointment = isset($appointmentMap[$cleanServiceName]);
                    $appointment = $hasAppointment ? $appointmentMap[$cleanServiceName] : null;
                }
                
                // Nếu vẫn không match, thử tìm appointment có chứa tên service
                if (!$hasAppointment) {
                    foreach ($appointments as $apt) {
                        if (stripos($apt['reason'], $service['service_name']) !== false ||
                            stripos($service['service_name'], $apt['reason']) !== false) {
                            $hasAppointment = true;
                            $appointment = $apt;
                            break;
                        }
                    }
                }
            ?>
            <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-600 font-semibold text-sm mr-3">
                                <?= $index + 1 ?>
                            </span>
                            <div>
                                <h5 class="text-base font-semibold text-gray-900">
                                    <?= htmlspecialchars($service['service_name']) ?>
                                </h5>
                                <?php if ($service['service_category']): ?>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-tag mr-1"></i><?= htmlspecialchars($service['service_category']) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($hasAppointment): ?>
                        <!-- Đã phân công -->
                        <div class="ml-11 mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-green-800 mb-1">
                                        <i class="fas fa-check-circle mr-1"></i>Đã phân công
                                    </p>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600">Bác sĩ</p>
                                            <p class="font-medium text-gray-900">
                                                <?= htmlspecialchars($appointment['doctor_name']) ?>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Ngày khám</p>
                                            <p class="font-medium text-gray-900">
                                                <?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Giờ khám</p>
                                            <p class="font-medium text-gray-900">
                                                <?= date('H:i', strtotime($appointment['appointment_time'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>" 
                                   class="ml-4 text-green-600 hover:text-green-800"
                                   title="Xem chi tiết">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Chưa phân công - Form phân công thủ công -->
                        <div class="ml-11 mt-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm font-medium text-yellow-800 mb-3">
                                <i class="fas fa-clock mr-1"></i>Chưa phân công bác sĩ
                            </p>
                            
                            <?php if (Auth::isAdmin() || Auth::role() === 'receptionist'): ?>
                            <!-- Form phân công thủ công -->
                            <form action="<?= APP_URL ?>/package-appointments/assign-doctor" method="POST" class="space-y-3">
                                <input type="hidden" name="package_appointment_id" value="<?= $packageAppointment['id'] ?>">
                                <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                <input type="hidden" name="service_name" value="<?= htmlspecialchars($service['service_name']) ?>">
                                
                                <div class="grid grid-cols-3 gap-3">
                                    <!-- Chọn bác sĩ -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Bác sĩ</label>
                                        <select name="doctor_id" required 
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                            <option value="">-- Chọn bác sĩ --</option>
                                            <?php foreach ($doctors as $doctor): ?>
                                            <option value="<?= $doctor['id'] ?>">
                                                <?= htmlspecialchars($doctor['full_name']) ?> - <?= htmlspecialchars($doctor['specialization']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <!-- Chọn ngày -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Ngày khám</label>
                                        <input type="date" name="appointment_date" required
                                               min="<?= date('Y-m-d') ?>"
                                               value="<?= $packageAppointment['appointment_date'] ?>"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    </div>
                                    
                                    <!-- Chọn giờ -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Giờ khám</label>
                                        <select name="appointment_time" required
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                            <option value="">-- Chọn giờ --</option>
                                            <?php 
                                            for ($h = 8; $h < 17; $h++) {
                                                for ($m = 0; $m < 60; $m += 30) {
                                                    $time = sprintf('%02d:%02d', $h, $m);
                                                    echo "<option value='$time'>$time</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" 
                                            class="px-4 py-2 text-sm bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                                        <i class="fas fa-user-plus mr-1"></i>Phân công
                                    </button>
                                </div>
                            </form>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="ml-4 text-right">
                        <p class="text-sm text-gray-600">Giá dịch vụ</p>
                        <p class="text-lg font-bold text-purple-600">
                            <?= number_format($service['service_price']) ?> VNĐ
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Tổng giá -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <p class="text-lg font-semibold text-gray-800">Tổng giá trị gói khám:</p>
                <p class="text-2xl font-bold text-purple-600">
                    <?php
                    $totalPrice = array_sum(array_column($packageServices, 'service_price'));
                    echo number_format($totalPrice);
                    ?> VNĐ
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
