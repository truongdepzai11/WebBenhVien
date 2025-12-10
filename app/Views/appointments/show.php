<?php 
$page_title = 'Chi tiết Lịch hẹn';
ob_start(); 
?>

<!-- Session Messages -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        <?= $_SESSION['success'] ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['info'])): ?>
    <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded-lg">
        <?= $_SESSION['info'] ?>
        <?php unset($_SESSION['info']); ?>
    </div>
<?php endif; ?>

<div class="mb-6">
    <a href="<?= APP_URL ?>/appointments" class="text-purple-600 hover:text-purple-700">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại danh sách
    </a>
</div>

<!-- Appointment Details -->
<div class="bg-white rounded-lg shadow-md p-8 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Lịch hẹn #<?= htmlspecialchars($appointment['appointment_code']) ?></h1>
            <p class="text-gray-600">Ngày khám: <?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?><?php if (!empty($appointment['appointment_time'])): ?> - <?= date('H:i', strtotime($appointment['appointment_time'])) ?><?php endif; ?></p>
        </div>
            <?php
            $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'confirmed' => 'bg-blue-100 text-blue-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-gray-100 text-gray-800',
                'late_cancelled' => 'bg-orange-100 text-orange-800',
                'no_show' => 'bg-red-100 text-red-800'
            ];
            $statusLabels = [
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã hủy',
                'late_cancelled' => 'Hủy muộn',
                'no_show' => 'Vắng mặt'
            ];
            ?>
            <span class="px-4 py-2 rounded-full text-sm font-bold <?= $statusColors[$appointment['status']] ?>">
                <?= $statusLabels[$appointment['status']] ?>
            </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Thông tin bệnh nhân -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-user mr-2"></i>Thông tin bệnh nhân
            </h3>
            <div class="space-y-2 text-gray-700">
                <p><strong>Họ tên:</strong> <?= htmlspecialchars($appointment['patient_name']) ?></p>
                <p><strong>Mã BN:</strong> <?= htmlspecialchars($appointment['patient_code']) ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($appointment['patient_phone']) ?></p>
            </div>
        </div>

        <!-- Thông tin bác sĩ / Gói khám -->
        <div>
            <?php 
            // Xác định đây có phải là lịch TỔNG HỢP của gói hay không
            $reason = $appointment['reason'] ?? '';
            $isPackageSummary = (
                ($appointment['appointment_type'] ?? '') === 'package' &&
                empty($appointment['doctor_id']) &&
                stripos($reason, 'Khám theo gói') === 0
            );
            ?>
            <?php if ($isPackageSummary): ?>
                <!-- Thông tin gói khám (lịch tổng hợp) -->
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-box-open mr-2"></i>Thông tin gói khám
                </h3>
                <div class="space-y-2 text-gray-700">
                    <p><strong>Gói khám:</strong> <?= htmlspecialchars($appointment['package_name'] ?? 'Gói khám sức khỏe') ?></p>
                    <p><strong>Tổng giá trị:</strong> <?= isset($appointment['total_price']) && $appointment['total_price'] !== null ? number_format((float)$appointment['total_price']) . ' VNĐ' : '<span class="text-gray-400">-</span>' ?></p>
                </div>
            <?php else: ?>
                <!-- Thông tin bác sĩ (lịch dịch vụ trong gói hoặc khám thường) -->
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-md mr-2"></i>Thông tin bác sĩ
                </h3>
                <div class="space-y-2 text-gray-700">
                    <p><strong>Bác sĩ:</strong> <?= !empty($appointment['doctor_name']) ? htmlspecialchars($appointment['doctor_name']) : '<span class="text-gray-400 italic">Chưa phân công</span>' ?></p>
                    <p><strong>Chuyên khoa:</strong> <?= !empty($appointment['specialization']) ? htmlspecialchars($appointment['specialization']) : '<span class="text-gray-400 italic">-</span>' ?></p>
                    <p><strong>Giá dịch vụ:</strong> <?= isset($appointment['total_price']) && $appointment['total_price'] !== null ? number_format((float)$appointment['total_price']) . ' VNĐ' : '<span class="text-gray-400">-</span>' ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($appointment['reason'])): ?>
    <div class="mt-6 pt-6 border-t">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Dịch vụ khám</h3>
        <p class="text-gray-700"><?= nl2br(htmlspecialchars($appointment['reason'])) ?></p>
    </div>
    <?php endif; ?>

    

    <?php if (!empty($appointment['symptoms'])): ?>
    <div class="mt-4">
        <h3 class="text-lg font-bold text-gray-800 mb-2">Triệu chứng</h3>
        <p class="text-gray-700"><?= nl2br(htmlspecialchars($appointment['symptoms'])) ?></p>
    </div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="mt-8 pt-6 border-t flex items-center justify-between">
        <div class="flex space-x-3">
            <?php if (!empty($isPackageSummary) && $isPackageSummary && !empty($appointment['package_appointment_id'])): ?>
            <a href="<?= APP_URL ?>/package-appointments/<?= $appointment['package_appointment_id'] ?>"
               class="px-6 py-2 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition">
                <i class="fas fa-external-link-alt mr-2"></i>Xem chi tiết gói khám
            </a>
            <?php endif; ?>
            <?php if (Auth::isDoctor() && $appointment['status'] === 'confirmed' && !$isPackageSummary): ?>
            <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/complete" 
               class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-check mr-2"></i>Hoàn thành khám
            </a>
            <?php endif; ?>

            <?php if ($appointment['status'] === 'pending' && (Auth::isDoctor() || Auth::isAdmin()) && !$isPackageSummary): ?>
            <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/confirm" 
               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-check-circle mr-2"></i>Xác nhận lịch
            </a>
            <?php endif; ?>

            <?php if (in_array($appointment['status'], ['pending', 'confirmed']) && !$isPackageSummary): ?>
            <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/cancel" 
               class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-times-circle mr-2"></i>Hủy lịch
            </a>
            <?php endif; ?>
        </div>

        <!-- Nút tạo hóa đơn - CHỈ HIỆN KHI ĐÃ HOÀN THÀNH -->
        <?php if ($appointment['status'] === 'completed' && (Auth::isDoctor() || Auth::isAdmin())): ?>
            <!-- Tạo hồ sơ bệnh án từ lịch hẹn -->
            <a href="<?= APP_URL ?>/medical-records/create?appointment_id=<?= $appointment['id'] ?>" 
               class="px-6 py-2 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition">
                <i class="fas fa-file-medical mr-2"></i>Tạo hồ sơ bệnh án
            </a>

            <?php if (isset($invoice) && $invoice): ?>
                <!-- Đã có hóa đơn - Xem hóa đơn -->
                <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>" 
                   class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-file-invoice mr-2"></i>Xem hóa đơn
                </a>
            <?php else: ?>
                <!-- Chưa có hóa đơn - Tạo mới -->
                <a href="<?= APP_URL ?>/invoices/create-from-appointment/<?= $appointment['id'] ?>" 
                   class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Tạo hóa đơn
                </a>
            <?php endif; ?>

        <!-- Bệnh nhân xem hóa đơn -->
        <?php if (Auth::isPatient() && isset($invoice) && $invoice): ?>
        <a href="<?= APP_URL ?>/invoices/<?= $invoice['id'] ?>" 
           class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
            <i class="fas fa-file-invoice mr-2"></i>Xem hóa đơn
        </a>
        <?php endif; ?>
    </div>
</div>

<?php 
// Chỉ hiển thị kết quả khi lịch đã hoàn thành
if (($appointment['status'] ?? '') === 'completed'):
    // BÁC SĨ phụ trách mới được chỉnh sửa
    $canEditResults = false;
    if (Auth::isDoctor()) {
        require_once APP_PATH . '/Models/Doctor.php';
        $docModelTmp = new Doctor();
        $me = $docModelTmp->findByUserId(Auth::id());
        $canEditResults = !empty($appointment['package_appointment_id']) && !empty($appointment['doctor_id']) && $me && ((int)$me['id'] === (int)$appointment['doctor_id']);
    }
    $state = $resultState ?? null;
    $isLocked = in_array($state, ['submitted','approved'], true);
?>

<?php if ($canEditResults): ?>
<div class="bg-white rounded-lg shadow-md p-8 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">Kết quả dịch vụ</h3>
        <div>
            <?php if ($state): ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $state === 'approved' ? 'bg-green-100 text-green-800' : ($state === 'submitted' ? 'bg-blue-100 text-blue-800' : ($state === 'returned' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')) ?>">
                    Trạng thái: <?= htmlspecialchars($state) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($reviewNote) && $state === 'returned'): ?>
        <div class="mb-4 p-3 rounded bg-orange-50 text-orange-800 text-sm">
            Lý do trả về: <?= nl2br(htmlspecialchars($reviewNote)) ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" action="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/results/save" onsubmit="console.log('Package form submitted'); alert('Form submitting!'); return true;">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="metrics-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chỉ số</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kết quả</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Khoảng tham chiếu</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tình trạng</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ảnh</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">File xét nghiệm</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                        <th class="px-2"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $rows = !empty($serviceMetrics) ? $serviceMetrics : [[]]; ?>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="px-4 py-2"><input <?= $isLocked ? 'readonly' : '' ?> type="text" name="metric_name[]" value="<?= htmlspecialchars($row['metric_name'] ?? '') ?>" class="w-full border rounded px-2 py-1"></td>
                        <td class="px-4 py-2"><input <?= $isLocked ? 'readonly' : '' ?> type="text" name="result_value[]" value="<?= htmlspecialchars($row['result_value'] ?? '') ?>" class="w-full border rounded px-2 py-1"></td>
                        <td class="px-4 py-2"><input <?= $isLocked ? 'readonly' : '' ?> type="text" name="reference_range[]" value="<?= htmlspecialchars($row['reference_range'] ?? '') ?>" class="w-full border rounded px-2 py-1"></td>
                        <td class="px-4 py-2">
                            <select <?= $isLocked ? 'disabled' : '' ?> name="result_status[]" class="w-full border rounded px-2 py-1">
                                <option value="normal" <?= (isset($row['result_status']) && $row['result_status']==='normal') ? 'selected' : '' ?>>Bình thường</option>
                                <option value="abnormal" <?= (isset($row['result_status']) && $row['result_status']==='abnormal') ? 'selected' : '' ?>>Bất thường</option>
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <?php if (!empty($row['image_path'])): ?>
                                <div class="text-sm">
                                    <?php 
                                        // Lấy tên file gốc, bỏ prefix result_[timestamp]_[index]_
                                        $originalName = basename($row['image_path']);
                                        if (preg_match('/result_\d+_\d+_(.+)$/', $originalName, $matches)) {
                                            $displayName = $matches[1];
                                        } else {
                                            $displayName = $originalName;
                                        }
                                    ?>
                                    <div class="text-gray-600 mb-1"><?= htmlspecialchars($displayName) ?></div>
                                    <a href="<?= str_replace('/public', '', APP_URL) ?>/<?= htmlspecialchars($row['image_path']) ?>" target="_blank" class="text-blue-600 text-xs hover:text-blue-800">
                                        <i class="fas fa-eye mr-1"></i>Xem ảnh
                                    </a>
                                </div>
                            <?php else: ?>
                                <input <?= $isLocked ? 'readonly' : '' ?> type="file" name="image_path[]" accept="image/*" class="w-full border rounded px-2 py-1 text-sm">
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2">
                            <?php if (!empty($row['file_path'])): ?>
                                <div class="text-sm">
                                    <?php 
                                        // Lấy tên file gốc, bỏ prefix file_[timestamp]_[index]_
                                        $originalName = basename($row['file_path']);
                                        if (preg_match('/file_\d+_\d+_(.+)$/', $originalName, $matches)) {
                                            $displayName = $matches[1];
                                        } else {
                                            $displayName = $originalName;
                                        }
                                    ?>
                                    <div class="text-gray-600 mb-1"><?= htmlspecialchars($displayName) ?></div>
                                    <a href="<?= str_replace('/public', '', APP_URL) ?>/<?= htmlspecialchars($row['file_path']) ?>" download="<?= htmlspecialchars($displayName) ?>" class="text-blue-600 text-xs hover:text-blue-800">
                                        <i class="fas fa-download mr-1"></i>Xem file
                                    </a>
                                </div>
                            <?php else: ?>
                                <input <?= $isLocked ? 'readonly' : '' ?> type="file" name="file_path[]" accept=".pdf,.doc,.docx,.xls,.xlsx" class="w-full border rounded px-2 py-1 text-sm">
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2"><input <?= $isLocked ? 'readonly' : '' ?> type="text" name="notes[]" value="<?= htmlspecialchars($row['notes'] ?? '') ?>" class="w-full border rounded px-2 py-1"></td>
                        <td class="px-2 text-center">
                            <?php if (!$isLocked): ?>
                            <button type="button" class="text-red-600" onclick="removeMetricRow(this)"><i class="fas fa-trash"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (!$isLocked): ?>
        <div class="flex items-center justify-between mt-4">
            <div>
                <button type="button" onclick="addMetricRow()" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50"><i class="fas fa-plus mr-1"></i>Thêm dòng</button>
            </div>
            <div class="space-x-2">
                <button type="submit" class="px-6 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">Lưu nháp</button>
                <button type="button" onclick="validateAndSubmitResults()" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nộp kết quả</button>
            </div>
        </div>
        <?php else: ?>
        <div class="mt-4 text-sm text-gray-500">Kết quả đã <?= $state === 'approved' ? 'được duyệt' : 'được nộp, chờ duyệt' ?>.</div>
        <?php endif; ?>
    </form>

    <script>
    function addMetricRow(){
        const tbody = document.querySelector('#metrics-table tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="px-4 py-2"><input type="text" name="metric_name[]" class="w-full border rounded px-2 py-1"></td>
            <td class="px-4 py-2"><input type="text" name="result_value[]" class="w-full border rounded px-2 py-1"></td>
            <td class="px-4 py-2"><input type="text" name="reference_range[]" class="w-full border rounded px-2 py-1"></td>
            <td class="px-4 py-2">
                <select name="result_status[]" class="w-full border rounded px-2 py-1">
                    <option value="normal">Bình thường</option>
                    <option value="abnormal">Bất thường</option>
                </select>
            </td>
            <td class="px-4 py-2">
                <input type="file" name="image_path[]" accept="image/*" class="w-full border rounded px-2 py-1 text-sm">
            </td>
            <td class="px-4 py-2">
                <input type="file" name="file_path[]" accept=".pdf,.doc,.docx,.xls,.xlsx" class="w-full border rounded px-2 py-1 text-sm">
            </td>
            <td class="px-4 py-2"><input type="text" name="notes[]" class="w-full border rounded px-2 py-1"></td>
            <td class="px-2 text-center"><button type="button" class="text-red-600" onclick="removeMetricRow(this)"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
    }
    function removeMetricRow(btn){
        const tr = btn.closest('tr');
        tr.parentNode.removeChild(tr);
    }
    
    function validateAndSubmitResults() {
        const tbody = document.querySelector('#metrics-table tbody');
        const rows = tbody.querySelectorAll('tr');
        let hasData = false;
        
        for (const tr of rows) {
            const metricName = tr.querySelector('input[name="metric_name[]"]').value.trim();
            const resultValue = tr.querySelector('input[name="result_value[]"]').value.trim();
            
            if (metricName !== '' || resultValue !== '') {
                hasData = true;
                break;
            }
        }
        
        if (!hasData) {
            alert('Vui lòng nhập ít nhất một chỉ số và kết quả trước khi nộp!');
            return;
        }
        
        // Bỏ qua kiểm tra findings và conclusion vì không có field này
        // Nếu hợp lệ, submit form
        console.log('Submitting package results form...');
        const form = document.querySelector('form[action*="/results/save"]');
        form.action = '<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/results/submit';
        form.enctype = 'multipart/form-data';
        form.submit();
    }
    
    function validateAndSubmitDiagnosis() {
        const primarySection = document.querySelector('input[name="primary_section"]').value.trim();
        const clinicalFindings = document.querySelector('textarea[name="clinical_findings"]').value.trim();
        const officialFindings = document.querySelector('textarea[name="official_findings"]').value.trim();
        
        if (primarySection === '') {
            alert('Vui lòng nhập chẩn đoán chính!');
            return;
        }
        
        if (clinicalFindings === '') {
            alert('Vui lòng nhập dấu hiệu & lâm sàng!');
            return;
        }
        
        if (officialFindings === '') {
            alert('Vui lòng nhập kết luận chính thức!');
            return;
        }
        
        // Nếu hợp lệ, submit form
        const form = document.querySelector('form[action*="/diagnoses/save"]');
        form.submit();
    }
    </script>
</div>
<?php endif; ?>

<!-- PACKAGE SERVICE RESULTS - PATIENT VIEW -->
<?php if (!empty($appointment['package_appointment_id']) && !empty($appointment['doctor_id']) && Auth::isPatient()): ?>
<div class="bg-white rounded-lg shadow-md p-8 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">Kết quả dịch vụ</h3>
        <div>
            <?php if ($state): ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $state === 'approved' ? 'bg-green-100 text-green-800' : ($state === 'submitted' ? 'bg-blue-100 text-blue-800' : ($state === 'returned' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')) ?>">
                    Trạng thái: <?= htmlspecialchars($state) ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($reviewNote) && $state === 'returned'): ?>
        <div class="mb-4 p-3 rounded bg-orange-50 text-orange-800 text-sm">
            Lý do trả về: <?= nl2br(htmlspecialchars($reviewNote)) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($serviceMetrics)): ?>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chỉ số</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kết quả</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Khoảng tham chiếu</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tình trạng</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ảnh</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">File xét nghiệm</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($serviceMetrics as $row): ?>
                <tr>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['metric_name'] ?? '') ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['result_value'] ?? '') ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['reference_range'] ?? '') ?></td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?= ($row['result_status'] ?? 'normal') === 'normal' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= ($row['result_status'] ?? 'normal') === 'normal' ? 'Bình thường' : 'Bất thường' ?>
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <?php if (!empty($row['image_path'])): ?>
                            <?php 
                                // Lấy tên file gốc, bỏ prefix result_[timestamp]_[index]_
                                $originalName = basename($row['image_path']);
                                if (preg_match('/result_\d+_\d+_(.+)$/', $originalName, $matches)) {
                                    $displayName = $matches[1];
                                } else {
                                    $displayName = $originalName;
                                }
                            ?>
                            <a href="<?= str_replace('/public', '', APP_URL) ?>/<?= htmlspecialchars($row['image_path']) ?>" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-image mr-1"></i><?= htmlspecialchars($displayName) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2">
                        <?php if (!empty($row['file_path'])): ?>
                            <?php 
                                // Lấy tên file gốc, bỏ prefix file_[timestamp]_[index]_
                                $originalName = basename($row['file_path']);
                                if (preg_match('/file_\d+_\d+_(.+)$/', $originalName, $matches)) {
                                    $displayName = $matches[1];
                                } else {
                                    $displayName = $originalName;
                                }
                            ?>
                            <a href="<?= str_replace('/public', '', APP_URL) ?>/<?= htmlspecialchars($row['file_path']) ?>" download="<?= htmlspecialchars($displayName) ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-download mr-1"></i><?= htmlspecialchars($displayName) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 text-sm">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['notes'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-vial text-4xl mb-4"></i>
            <p>Chưa có kết quả xét nghiệm</p>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($resultJson)): ?>
        <div class="mt-6 pt-6 border-t">
            <h4 class="text-md font-semibold text-gray-800 mb-3">Kết luận</h4>
            <?php 
            $resultData = json_decode($resultJson, true);
            if ($resultData):
            ?>
                <?php if (!empty($resultData['findings'])): ?>
                    <div class="mb-3">
                        <span class="font-semibold">Phát hiện:</span><br>
                        <p class="text-gray-700"><?= nl2br(htmlspecialchars($resultData['findings'])) ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($resultData['conclusion'])): ?>
                    <div>
                        <span class="font-semibold">Kết luận:</span><br>
                        <p class="text-gray-700"><?= nl2br(htmlspecialchars($resultData['conclusion'])) ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

    <!-- PACKAGE SERVICE DIAGNOSIS - DOCTOR ONLY -->
    <?php if (!empty($appointment['package_appointment_id']) && !empty($appointment['doctor_id']) && Auth::isDoctor()): ?>
    <div class="bg-white rounded-lg shadow-md p-8 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Chẩn đoán</h3>
            <?php 
                $pkgDx = $packageDiagnosis ?? null;
                $pkgDxStatus = $pkgDx['status'] ?? null;
                $pkgDxLocked = ($pkgDxStatus === 'approved');
                if ($pkgDxStatus === 'approved'): 
            ?>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Trạng thái: approved</span>
            <?php endif; ?>
        </div>

        <form method="post" action="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/diagnoses/save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Chẩn đoán chính</label>
                    <input type="text" name="primary_section" value="<?= htmlspecialchars($pkgDx['primary_icd10'] ?? '') ?>" class="w-full border rounded px-3 py-2" <?= $pkgDxLocked ? 'readonly' : '' ?>>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Dấu hiệu & lâm sàng</label>
                    <textarea name="clinical_findings" rows="3" class="w-full border rounded px-3 py-2" <?= $pkgDxLocked ? 'readonly' : '' ?>><?= htmlspecialchars($pkgDx['clinical_findings'] ?? '') ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Kết luận chính thức</label>
                    <textarea name="official_findings" rows="3" class="w-full border rounded px-3 py-2" <?= $pkgDxLocked ? 'readonly' : '' ?>><?= htmlspecialchars($pkgDx['assessment'] ?? '') ?></textarea>
                </div>
            </div>
            <?php if (!$pkgDxLocked): ?>
            <div class="flex items-center justify-end mt-4 space-x-3">
                <label class="text-sm text-gray-600"><input type="checkbox" name="submit_after" value="1" class="mr-1">Lưu xong duyệt luôn</label>
                <button type="button" onclick="validateAndSubmitDiagnosis()" class="px-6 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Lưu chẩn đoán</button>
            </div>
            <?php else: ?>
            <div class="mt-2 text-sm text-gray-500">Chẩn đoán đã được duyệt.</div>
            <?php endif; ?>
        </form>

        <?php if (!empty($pkgDx)): ?>
        <div class="mt-6">
            <h4 class="text-md font-semibold text-gray-800 mb-2">Chẩn đoán mới nhất</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div><span class="font-semibold">Chẩn đoán chính:</span> <?= htmlspecialchars($pkgDx['primary_icd10'] ?? '') ?></div>
                <div><span class="font-semibold">Trạng thái:</span> <?= htmlspecialchars($pkgDx['status'] ?? '') ?></div>
                <div class="md:col-span-2"><span class="font-semibold">Dấu hiệu & lâm sàng:</span><br><?= nl2br(htmlspecialchars($pkgDx['clinical_findings'] ?? '')) ?></div>
                <div class="md:col-span-2"><span class="font-semibold">Kết luận chính thức:</span><br><?= nl2br(htmlspecialchars($pkgDx['assessment'] ?? '')) ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

<?php if (empty($appointment['package_appointment_id']) && !Auth::isAdmin()): ?>
    <?php
        $regularResult = $regularResult ?? null;
        $regularResultItems = $regularResultItems ?? [];
        $regularResultStatus = $regularResult['status'] ?? null;
        $regularResultLocked = ($regularResultStatus === 'submitted');
        $regularResultNote = $regularResult['review_note'] ?? '';

        $dx = $regularDiagnosis ?? null;
        $dxStatus = $dx['status'] ?? null;

        $assignedDoctorId = (int)($appointment['doctor_id'] ?? 0);
        $canEditRegularResults = false;
        if (Auth::isDoctor() && $assignedDoctorId > 0) {
            if (!class_exists('Doctor')) { require_once APP_PATH . '/Models/Doctor.php'; }
            $doctorModelRegular = new Doctor();
            $currentDoctorRegular = $doctorModelRegular->findByUserId(Auth::id());
            $canEditRegularResults = $currentDoctorRegular && ((int)$currentDoctorRegular['id'] === $assignedDoctorId);
        }

        $regularResultRows = !empty($regularResultItems) ? $regularResultItems : [[]];
        $regularStatusBadge = $regularResultStatus === 'submitted' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700';
    ?>

    <div class="bg-white rounded-lg shadow-md p-8 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-stethoscope text-purple-500"></i>
                Kết quả khám thường
            </h3>
            <?php if ($regularResultStatus): ?>
            <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $regularStatusBadge ?>">
                <?= strtoupper(htmlspecialchars($regularResultStatus)) ?>
            </span>
            <?php endif; ?>
        </div>

        <?php if ($canEditRegularResults && !$regularResultLocked): ?>
        <form method="post" id="regular-results-form" action="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/regular-results/save">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="regular-results-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chỉ số</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kết quả</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Khoảng tham chiếu</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tình trạng</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ghi chú</th>
                            <th class="px-2"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($regularResultRows as $row):
                            $rrMetric = htmlspecialchars($row['metric_name'] ?? '');
                            $rrValue = htmlspecialchars($row['result_value'] ?? '');
                            $rrReference = htmlspecialchars($row['reference_range'] ?? '');
                            $rrStatus = strtolower($row['result_status'] ?? 'normal');
                            if (!in_array($rrStatus, ['normal', 'abnormal', 'pending'], true)) { $rrStatus = 'normal'; }
                            $rrNote = htmlspecialchars($row['notes'] ?? '');
                        ?>
                        <tr>
                            <td class="px-3 py-2"><input type="text" name="metric_name[]" value="<?= $rrMetric ?>" class="w-full border rounded px-2 py-1"></td>
                            <td class="px-3 py-2"><input type="text" name="result_value[]" value="<?= $rrValue ?>" class="w-full border rounded px-2 py-1"></td>
                            <td class="px-3 py-2"><input type="text" name="reference_range[]" value="<?= $rrReference ?>" class="w-full border rounded px-2 py-1"></td>
                            <td class="px-3 py-2">
                                <select name="result_status[]" class="w-full border rounded px-2 py-1">
                                    <option value="normal" <?= $rrStatus === 'normal' ? 'selected' : '' ?>>Bình thường</option>
                                    <option value="abnormal" <?= $rrStatus === 'abnormal' ? 'selected' : '' ?>>Bất thường</option>
                                    <option value="pending" <?= $rrStatus === 'pending' ? 'selected' : '' ?>>Đang xử lý</option>
                                </select>
                            </td>
                            <td class="px-3 py-2"><input type="text" name="notes[]" value="<?= $rrNote ?>" class="w-full border rounded px-2 py-1"></td>
                            <td class="px-2 text-center"><button type="button" class="text-red-600" onclick="removeRegularResultRow(this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <label class="block text-sm text-gray-600 mb-1" for="regular-general-note">Ghi chú chung (tùy chọn)</label>
                <textarea name="general_note" id="regular-general-note" rows="3" class="w-full border rounded px-3 py-2"><?= htmlspecialchars($regularResultNote) ?></textarea>
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="button" onclick="addRegularResultRow()" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50"><i class="fas fa-plus mr-1"></i>Thêm dòng</button>
                <div class="space-x-2">
                    <button type="submit" class="px-6 py-2 bg-gray-700 text-white rounded hover:bg-gray-800">Lưu nháp</button>
                    <button type="submit" formaction="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/regular-results/submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nộp kết quả</button>
                </div>
            </div>
        </form>

        <script>
        function addRegularResultRow(){
            const tbody = document.querySelector('#regular-results-table tbody');
            if (!tbody) return;
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-3 py-2"><input type="text" name="metric_name[]" class="w-full border rounded px-2 py-1"></td>
                <td class="px-3 py-2"><input type="text" name="result_value[]" class="w-full border rounded px-2 py-1"></td>
                <td class="px-3 py-2"><input type="text" name="reference_range[]" class="w-full border rounded px-2 py-1"></td>
                <td class="px-3 py-2">
                    <select name="result_status[]" class="w-full border rounded px-2 py-1">
                        <option value="normal">Bình thường</option>
                        <option value="abnormal">Bất thường</option>
                        <option value="pending">Đang xử lý</option>
                    </select>
                </td>
                <td class="px-3 py-2"><input type="text" name="notes[]" class="w-full border rounded px-2 py-1"></td>
                <td class="px-2 text-center"><button type="button" class="text-red-600" onclick="removeRegularResultRow(this)"><i class="fas fa-trash"></i></button></td>`;
            tbody.appendChild(tr);
        }
        function removeRegularResultRow(btn){ const tr = btn.closest('tr'); if (tr) tr.remove(); }
        </script>
        <?php elseif (!empty($regularResultItems)): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Chỉ số</th>
                        <th class="px-3 py-2 text-left">Kết quả</th>
                        <th class="px-3 py-2 text-left">Khoảng tham chiếu</th>
                        <th class="px-3 py-2 text-left">Tình trạng</th>
                        <th class="px-3 py-2 text-left">Ghi chú</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php foreach ($regularResultItems as $row): ?>
                    <tr>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($row['metric_name'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($row['result_value'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($row['reference_range'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700 text-sm uppercase"><?= htmlspecialchars($row['result_status'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($row['notes'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <?php
        try {
            $dxLocked = ($dxStatus === 'approved');
        } catch (\Throwable $e) {
            $dx = null;
            $dxLocked = false;
            $dxStatus = null;
        }
    ?>
    <div class="bg-white rounded-lg shadow-md p-8 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Chẩn đoán</h3>
            <?php if ($dxStatus === 'approved'): ?>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Trạng thái: approved</span>
            <?php endif; ?>
        </div>
        <div class="mb-3"></div>

        <form method="post" action="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/diagnoses/save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Chẩn đoán chính</label>
                    <input type="text" name="primary_section" value="<?= htmlspecialchars($dx['primary_icd10'] ?? '') ?>" class="w-full border rounded px-3 py-2" <?= $dxLocked ? 'readonly' : '' ?>>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Dấu hiệu & lâm sàng</label>
                    <textarea name="clinical_findings" rows="3" class="w-full border rounded px-3 py-2" <?= $dxLocked ? 'readonly' : '' ?>><?= htmlspecialchars($dx['clinical_findings'] ?? '') ?></textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Kết luận chính thức</label>
                    <textarea name="official_findings" rows="3" class="w-full border rounded px-3 py-2" <?= $dxLocked ? 'readonly' : '' ?>><?= htmlspecialchars($dx['assessment'] ?? '') ?></textarea>
                </div>
            </div>
            <?php if (!$dxLocked): ?>
            <div class="flex items-center justify-end mt-4 space-x-3">
                <label class="text-sm text-gray-600"><input type="checkbox" name="submit_after" value="1" class="mr-1">Lưu xong duyệt luôn</label>
                <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Lưu chẩn đoán</button>
            </div>
            <?php else: ?>
            <div class="mt-2 text-sm text-gray-500">Chẩn đoán đã được duyệt.</div>
            <?php endif; ?>
        </form>

        <?php if (!empty($dx)): ?>
        <div class="mt-6">
            <h4 class="text-md font-semibold text-gray-800 mb-2">Chẩn đoán mới nhất</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div><span class="font-semibold">Chẩn đoán chính:</span> <?= htmlspecialchars($dx['primary_icd10'] ?? '') ?></div>
                <div><span class="font-semibold">Trạng thái:</span> <?= htmlspecialchars($dx['status'] ?? '') ?></div>
                <div class="md:col-span-2"><span class="font-semibold">Dấu hiệu & lâm sàng:</span><br><?= nl2br(htmlspecialchars($dx['clinical_findings'] ?? '')) ?></div>
                <div class="md:col-span-2"><span class="font-semibold">Kết luận chính thức:</span><br><?= nl2br(htmlspecialchars($dx['assessment'] ?? '')) ?></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Prescription (Doctor Only for Regular Appointments and Package Services) -->
    <?php if (Auth::isDoctor() && (!$isPackageSummary || !empty($appointment['doctor_id']))): ?>
    <div class="bg-white rounded-lg shadow-md p-8 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800">Đơn thuốc</h3>
            <?php 
            // Tải đơn thuốc gần nhất cho appointment để hiển thị trạng thái + nút nộp
            try {
                if (!class_exists('Database')) { require_once APP_PATH . '/../config/database.php'; }
                $dbTmp = new Database(); $connTmp = $dbTmp->getConnection();
                $stmRx = $connTmp->prepare('SELECT id, status, prescription_code FROM prescriptions WHERE appointment_id = ? ORDER BY id DESC LIMIT 1');
                $stmRx->execute([(int)$appointment['id']]);
                $currentRx = $stmRx->fetch(PDO::FETCH_ASSOC) ?: null;
            } catch (\Throwable $e) { $currentRx = null; }
            $rxStatus = $currentRx['status'] ?? null;
            if ($rxStatus) {
                $cls = $rxStatus==='approved' ? 'bg-green-100 text-green-800' : ($rxStatus==='submitted' ? 'bg-blue-100 text-blue-800' : ($rxStatus==='dispensed' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'));
                echo '<span class="px-3 py-1 text-xs font-semibold rounded-full '.$cls.'">Trạng thái: '.htmlspecialchars($rxStatus).'</span>';
            }
            ?>
        </div>
        <?php if (!empty($currentRx) && in_array(($currentRx['status'] ?? ''), ['approved','dispensed'], true) && !$isPackageSummary): ?>
        <div class="mb-3 flex items-center gap-2">
            <a href="<?= APP_URL ?>/prescriptions/<?= (int)$currentRx['id'] ?>/export-pdf" class="px-4 py-2 border border-purple-600 text-purple-700 rounded hover:bg-purple-50">
                <i class="fas fa-file-pdf mr-1"></i>Xuất PDF đơn thuốc
            </a>
            <?php if (!empty($currentRx['pdf_path']) && is_file($currentRx['pdf_path'])): ?>
            <a href="<?= APP_URL ?>/prescriptions/<?= (int)$currentRx['id'] ?>/download-pdf" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                <i class="fas fa-download mr-1"></i>Tải PDF
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php
            $rxLocked = false; $rxStatusSafe = $currentRx['status'] ?? null;
            if ($rxStatusSafe && $rxStatusSafe !== 'draft') { $rxLocked = true; }
        ?>
        <?php if (!empty($currentRx) && Auth::isDoctor() && ($currentRx['status'] ?? '') === 'draft'): ?>
        <form method="post" action="<?= APP_URL ?>/prescriptions/<?= (int)$currentRx['id'] ?>/submit" class="mb-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"><i class="fas fa-paper-plane mr-1"></i>Nộp đơn</button>
        </form>
        <?php endif; ?>

        <?php if (!empty($currentRx) && ($currentRx['status'] ?? '') === 'submitted' && (Auth::isAdmin() || Auth::isDoctor()) && !$isPackageSummary): ?>
        <form method="post" action="<?= APP_URL ?>/prescriptions/<?= (int)$currentRx['id'] ?>/approve" class="mb-3">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"><i class="fas fa-check mr-1"></i>Duyệt đơn</button>
        </form>
        <?php endif; ?>
        <?php if (!empty($currentRx) && ($currentRx['status'] ?? '') === 'approved' && Auth::isAdmin() && !$isPackageSummary): ?>
        <form method="post" action="<?= APP_URL ?>/prescriptions/<?= (int)$currentRx['id'] ?>/dispense" class="mb-3">
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"><i class="fas fa-pills mr-1"></i>Phát thuốc</button>
        </form>
        <?php endif; ?>

        <?php 
        // Hiển thị danh sách mục thuốc đã lưu/nộp (read-only)
        try {
            if (!class_exists('Database')) { require_once APP_PATH . '/../config/database.php'; }
            if (!empty($currentRx)) {
                $dbv = new Database(); $cv = $dbv->getConnection();
                $stmIt = $cv->prepare('SELECT pi.*, m.name AS med_name, m.dosage_form AS med_form, m.strength AS med_strength
                                        FROM prescription_items pi
                                        LEFT JOIN medicines m ON m.id = pi.medicine_id
                                        WHERE pi.prescription_id = ?
                                        ORDER BY pi.id ASC');
                $stmIt->execute([(int)$currentRx['id']]);
                $rxItems = $stmIt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (\Throwable $e) { $rxItems = []; }
        if (!empty($currentRx) && !empty($rxItems) && !$isPackageSummary):
        ?>
        <div class="mt-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-md font-semibold text-gray-800">Đơn thuốc đã <?= htmlspecialchars($currentRx['status']) ?></h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Thuốc</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Liều dùng</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tần suất</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bắt đầu</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kết thúc</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Đường dùng</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dặn dò</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($rxItems as $it): 
                            $label = ($it['med_name'] ?? 'Thuốc')
                                . (!empty($it['med_strength']) ? (' ' . $it['med_strength']) : '')
                                . (!empty($it['med_form']) ? (' (' . $it['med_form'] . ')') : '');
                        ?>
                        <tr>
                            <td class="px-3 py-2 text-sm text-gray-700"><?= htmlspecialchars($label) ?></td>
                            <td class="px-3 py-2 text-sm text-gray-700"><?= (int)$it['quantity'] ?></td>
                            <td class="px-3 py-2 text-sm text-gray-700"><?= htmlspecialchars($it['dosage'] ?? '') ?></td>
                            <td class="px-3 py-2 text-sm text-gray-700"><?= htmlspecialchars($it['frequency'] ?? '') ?></td>
                            <td class="px-3 py-2 text-sm text-gray-700"><?= htmlspecialchars($it['duration'] ?? '') ?></td>
                            <td class="px-3 py-2 text-sm text-gray-700"><?= !empty($it['start_date']) ? htmlspecialchars(date('d/m/Y', strtotime($it['start_date']))) : '' ?></td>
                            <td class="px-3 py-2 text-sm text-gray-700"><?= !empty($it['end_date']) ? htmlspecialchars(date('d/m/Y', strtotime($it['end_date']))) : '' ?></td>
                            <td class="px-3 py-2 text-sm text-gray-700"><?= htmlspecialchars($it['route'] ?? '') ?></td>
                            <td class="px-3 py-2 text-sm text-gray-700"><?= htmlspecialchars($it['instructions'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!isset($rxLocked)) { $rxLocked = false; } ?>
        <form method="post" action="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/prescriptions/save">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="rx-table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Thuốc</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Liều dùng</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tần suất</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Đường dùng</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dặn dò</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bắt đầu</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kết thúc</th>
                            <th class="px-2"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-3 py-2">
                                <input type="hidden" name="medicine_id[]" class="rx-med-id">
                                <div class="relative">
                                    <input type="text" class="w-96 md:w-[28rem] lg:w-[32rem] border rounded px-2 py-1 rx-med-search" placeholder="Tìm theo tên/generic...">
                                    <div class="absolute z-10 bg-white border rounded mt-1 shadow max-h-64 overflow-auto hidden rx-med-list"></div>
                                </div>
                            </td>
                            <td class="px-3 py-2"><input <?= $rxLocked ? 'readonly' : '' ?> type="number" name="quantity[]" class="w-24 border rounded px-2 py-1" min="1" value="1"></td>
                            <td class="px-3 py-2"><input <?= $rxLocked ? 'readonly' : '' ?> type="text" name="dosage[]" class="w-48 border rounded px-2 py-1" placeholder="1 viên"></td>
                            <td class="px-3 py-2"><input <?= $rxLocked ? 'readonly' : '' ?> type="text" name="frequency[]" class="w-40 border rounded px-2 py-1" placeholder="2 lần/ngày"></td>
                            <td class="px-3 py-2"><input <?= $rxLocked ? 'readonly' : '' ?> type="text" name="duration[]" class="w-40 border rounded px-2 py-1" placeholder="5 ngày"></td>
                            <td class="px-3 py-2"><input <?= $rxLocked ? 'readonly' : '' ?> type="text" name="route[]" class="w-32 border rounded px-2 py-1" placeholder="uống"></td>
                            <td class="px-3 py-2"><input <?= $rxLocked ? 'readonly' : '' ?> type="text" name="instructions[]" class="w-64 border rounded px-2 py-1" placeholder="sau ăn"></td>
                            <td class="px-3 py-2"><input <?= $rxLocked ? 'readonly' : '' ?> type="date" name="start_date[]" class="w-36 border rounded px-2 py-1"></td>
                            <td class="px-3 py-2"><input <?= $rxLocked ? 'readonly' : '' ?> type="date" name="end_date[]" class="w-36 border rounded px-2 py-1"></td>
                            <td class="px-2 text-center"><button type="button" class="text-red-600" onclick="removeRxRow(this)"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if (!$rxLocked): ?>
            <div class="flex items-center justify-between mt-4">
                <button type="button" onclick="addRxRow()" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50"><i class="fas fa-plus mr-1"></i>Thêm dòng</button>
                <div class="space-x-2">
                    <label class="text-sm text-gray-600"><input type="checkbox" name="submit_after" value="1" class="mr-1">Lưu xong nộp luôn</label>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Lưu</button>
                </div>
            </div>
            <?php else: ?>
            <div class="mt-3 text-sm text-gray-500">Đơn thuốc đã được nộp/duyệt. Form bị khóa.</div>
            <?php endif; ?>
        </form>

        <script>
        function addRxRow(){
            const tbody = document.querySelector('#rx-table tbody');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-3 py-2">
                    <input type="hidden" name="medicine_id[]" class="rx-med-id">
                    <div class="relative"> 
                        <input type="text" class="w-96 md:w-[28rem] lg:w-[32rem] border rounded px-2 py-1 rx-med-search" placeholder="Tìm theo tên/generic...">
                        <div class="absolute z-10 bg-white border rounded mt-1 shadow max-h-64 overflow-auto hidden rx-med-list"></div>
                    </div>
                </td>
                <td class="px-3 py-2"><input type="number" name="quantity[]" class="w-24 border rounded px-2 py-1" min="1" value="1"></td>
                <td class="px-3 py-2"><input type="text" name="dosage[]" class="w-48 border rounded px-2 py-1"></td>
                <td class="px-3 py-2"><input type="text" name="frequency[]" class="w-40 border rounded px-2 py-1"></td>
                <td class="px-3 py-2"><input type="text" name="duration[]" class="w-40 border rounded px-2 py-1"></td>
                <td class="px-3 py-2"><input type="text" name="route[]" class="w-32 border rounded px-2 py-1"></td>
                <td class="px-3 py-2"><input type="text" name="instructions[]" class="w-64 border rounded px-2 py-1"></td>
                <td class="px-3 py-2"><input type="date" name="start_date[]" class="w-36 border rounded px-2 py-1"></td>
                <td class="px-3 py-2"><input type="date" name="end_date[]" class="w-36 border rounded px-2 py-1"></td>
                <td class="px-2 text-center"><button type="button" class="text-red-600" onclick="removeRxRow(this)"><i class="fas fa-trash"></i></button></td>
            `;
            tbody.appendChild(tr);
            bindMedicineSearch(tr.querySelector('.rx-med-search'));
        }
        function removeRxRow(btn){ const tr = btn.closest('tr'); tr.remove(); }

        // Autocomplete medicines
        function bindMedicineSearch(input){
            const list = input.parentElement.querySelector('.rx-med-list');
            const hidden = input.closest('td').querySelector('.rx-med-id');
            let timer=null;
            input.addEventListener('input', ()=>{
                hidden.value='';
                const q = input.value.trim();
                clearTimeout(timer);
                if(q.length<1){ list.classList.add('hidden'); list.innerHTML=''; return; }
                timer=setTimeout(async()=>{
                    try{
                        const res = await fetch('<?= APP_URL ?>/api/medicines?q='+encodeURIComponent(q));
                        const data = await res.json();
                        const items = data.items||[];
                        list.innerHTML = items.map(it=>`
                            <div class="px-2 py-1 hover:bg-purple-50 cursor-pointer text-sm border-b" data-id="${it.id}">
                                <div class="font-medium text-gray-800">${it.name}${it.strength?(' '+it.strength):''}${it.form?(' ('+it.form+')'):''}</div>
                                <div class="text-xs text-gray-600">Generic: ${it.generic_name||'-'} · Hãng: ${it.manufacturer||'-'} · Nhóm: ${it.category||'-'}</div>
                                ${it.requires_prescription?'<span class="inline-block mt-1 px-2 py-0.5 text-[10px] bg-red-100 text-red-700 rounded">Thuốc kê đơn</span>':''}
                            </div>`).join('');
                        list.classList.remove('hidden');
                        Array.from(list.children).forEach(el=>{
                            el.addEventListener('click', ()=>{
                                hidden.value = el.getAttribute('data-id');
                                input.value = el.querySelector('.font-medium').textContent;
                                list.classList.add('hidden');
                            });
                        });
                    }catch(e){ list.classList.add('hidden'); }
                }, 250);
            });
            input.addEventListener('blur', ()=> setTimeout(()=> list.classList.add('hidden'), 200));
        }
        document.querySelectorAll('.rx-med-search').forEach(bindMedicineSearch);

        // Client-side validation: require selecting from the list (medicine_id must be set if name not empty)
        const rxForm = document.querySelector('form[action$="/prescriptions/save"]');
        if (rxForm) {
            rxForm.addEventListener('submit', function(e){
                const rows = Array.from(document.querySelectorAll('#rx-table tbody tr'));
                for (const tr of rows) {
                    const nameInput = tr.querySelector('.rx-med-search');
                    const idInput = tr.querySelector('.rx-med-id');
                    if (nameInput && nameInput.value.trim() !== '' && (!idInput || !idInput.value)) {
                        e.preventDefault();
                        alert('Vui lòng chọn thuốc từ danh sách gợi ý để tránh nhầm lẫn.');
                        nameInput.focus();
                        return false;
                    }
                }
            });
        }

        // Default: show 4 rows initially if the form is not locked
        (function(){
            const rxLockedFlag = <?= $rxLocked ? 'true' : 'false' ?>;
            if (rxLockedFlag) return;
            const tbody = document.querySelector('#rx-table tbody');
            if (!tbody) return;
            let current = tbody.querySelectorAll('tr').length;
            while (current < 4) { addRxRow(); current++; }
        })();
        </script>
    </div>
    <?php endif; ?>

    

<?php endif // end only show when completed ?>
<?php endif // end empty package_appointment_id ?>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
