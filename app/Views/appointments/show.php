<?php 
$page_title = 'Chi tiết Lịch hẹn';
ob_start(); 
?>

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
            <?php if (Auth::isDoctor() && $appointment['status'] === 'confirmed'): ?>
            <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/complete" 
               class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-check mr-2"></i>Hoàn thành khám
            </a>
            <?php endif; ?>

            <?php if ($appointment['status'] === 'pending' && (Auth::isDoctor() || Auth::isAdmin())): ?>
            <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/confirm" 
               class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-check-circle mr-2"></i>Xác nhận lịch
            </a>
            <?php endif; ?>

            <?php if (in_array($appointment['status'], ['pending', 'confirmed'])): ?>
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

    <form method="post" action="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/results/save">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="metrics-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chỉ số</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kết quả</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Khoảng tham chiếu</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tình trạng</th>
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
                                <option value="pending" <?= (isset($row['result_status']) && $row['result_status']==='pending') ? 'selected' : '' ?>>Chờ đánh giá</option>
                                <option value="normal" <?= (isset($row['result_status']) && $row['result_status']==='normal') ? 'selected' : '' ?>>Bình thường</option>
                                <option value="abnormal" <?= (isset($row['result_status']) && $row['result_status']==='abnormal') ? 'selected' : '' ?>>Bất thường</option>
                            </select>
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
                <button type="submit" formaction="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/results/submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Nộp kết quả</button>
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
                    <option value="pending">Chờ đánh giá</option>
                    <option value="normal">Bình thường</option>
                    <option value="abnormal">Bất thường</option>
                </select>
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
    </script>
</div>
<?php endif; ?>

<?php endif; // end only show when completed ?>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
