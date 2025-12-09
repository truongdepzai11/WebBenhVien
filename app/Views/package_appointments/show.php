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
        // Hiển thị trạng thái theo tiến độ phân công dịch vụ trong gói
        $as = isset($assignedCount) ? (int)$assignedCount : 0;
        $ts = isset($packageServices) ? (int)count($packageServices) : 0;

        if ($ts > 0) {
            if ($as === 0) {
                $colorClass = 'bg-yellow-100 text-yellow-800';
                $label = 'Chưa phân công';
            } elseif ($as < $ts) {
                $colorClass = 'bg-orange-100 text-orange-800';
                $label = "Đã phân công (còn thiếu) {$as}/{$ts} dịch vụ";
            } else {
                $colorClass = 'bg-green-100 text-green-800';
                $label = "Đã phân công đầy đủ {$as}/{$ts} dịch vụ";
            }
        } else {
            $colorClass = 'bg-gray-100 text-gray-800';
            $label = 'Không có dịch vụ';
        }
        ?>
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full <?= $colorClass ?>">
            <?= $label ?>
        </span>
        <?php if (!empty($packageAppointment['final_status'])): ?>
            <?php 
              $fs = $packageAppointment['final_status'];
              $fsColor = $fs==='approved' ? 'bg-green-100 text-green-800' : ($fs==='awaiting_review' ? 'bg-blue-100 text-blue-800' : ($fs==='returned' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800'));
            ?>
            <span class="ml-2 px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full <?= $fsColor ?>">
                Trạng thái kết quả: <?= htmlspecialchars($fs) ?>
            </span>
        <?php endif; ?>
        <?php if (($packageAppointment['final_status'] ?? '') === 'approved'): ?>
            <a href="<?= APP_URL ?>/package-appointments/<?= $packageAppointment['id'] ?>/export-pdf" 
               class="ml-3 px-4 py-2 inline-flex items-center text-sm font-semibold rounded-lg border border-purple-600 text-purple-700 hover:bg-purple-50">
                <i class="fas fa-file-pdf mr-2"></i>Xuất PDF
            </a>
            <?php if (!empty($packageAppointment['final_pdf_path'])): ?>
            <a href="<?= str_replace(APP_PATH, APP_URL, $packageAppointment['final_pdf_path']) ?>" target="_blank" 
               class="ml-2 px-4 py-2 inline-flex items-center text-sm font-semibold rounded-lg bg-purple-600 text-white hover:bg-purple-700">
                <i class="fas fa-download mr-2"></i>Tải PDF
            </a>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ((Auth::isAdmin() || Auth::isReceptionist()) && isset($summaryAppointment) && $summaryAppointment && ($summaryAppointment['status'] ?? '') === 'completed'): ?>
            <?php if (isset($summaryInvoice) && $summaryInvoice): ?>
                <a href="<?= APP_URL ?>/invoices/<?= $summaryInvoice['id'] ?>" 
                   class="ml-3 px-4 py-2 inline-flex items-center text-sm font-semibold rounded-lg gradient-bg text-white hover:opacity-90">
                    <i class="fas fa-file-invoice mr-2"></i>Xem hóa đơn
                </a>
            <?php else: ?>
                <a href="<?= APP_URL ?>/invoices/create-from-appointment/<?= $summaryAppointment['id'] ?>" 
                   class="ml-3 px-4 py-2 inline-flex items-center text-sm font-semibold rounded-lg gradient-bg text-white hover:opacity-90">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Tạo hóa đơn
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php 
$summaryStatus = $summaryAppointment['status'] ?? ($packageAppointment['status'] ?? 'scheduled');
$summaryStatusMap = [
    'pending' => ['label' => 'Chờ xác nhận', 'icon' => 'fas fa-clock', 'bg' => 'bg-yellow-50 border-yellow-200 text-yellow-800'],
    'confirmed' => ['label' => 'Đã xác nhận', 'icon' => 'fas fa-check-circle', 'bg' => 'bg-blue-50 border-blue-200 text-blue-800'],
    'completed' => ['label' => 'Đã hoàn thành', 'icon' => 'fas fa-check-double', 'bg' => 'bg-green-50 border-green-200 text-green-700'],
    'cancelled' => ['label' => 'Đã hủy', 'icon' => 'fas fa-ban', 'bg' => 'bg-red-50 border-red-200 text-red-700'],
];
$summaryStatusMeta = $summaryStatusMap[$summaryStatus] ?? ['label' => ucfirst($summaryStatus), 'icon' => 'fas fa-info-circle', 'bg' => 'bg-gray-50 border-gray-200 text-gray-700'];

$childDone = (int)($completionStats['completed'] ?? 0);
$childTotal = (int)($completionStats['total'] ?? 0);
if ($childTotal === 0) {
    $progressMeta = ['label' => 'Chưa có lịch con nào được ghi nhận', 'detail' => '0/0 dịch vụ', 'icon' => 'fas fa-calendar-minus', 'bg' => 'bg-amber-50 border-amber-200 text-amber-700'];
} elseif ($childDone === 0) {
    $progressMeta = ['label' => 'Chưa khám dịch vụ nào', 'detail' => '0/' . $childTotal . ' dịch vụ', 'icon' => 'fas fa-hourglass-start', 'bg' => 'bg-amber-50 border-amber-200 text-amber-700'];
} elseif ($childDone < $childTotal) {
    $progressMeta = ['label' => 'Đang khám', 'detail' => $childDone . '/' . $childTotal . ' dịch vụ đã hoàn thành', 'icon' => 'fas fa-stethoscope', 'bg' => 'bg-blue-50 border-blue-200 text-blue-800'];
} else {
    $progressMeta = ['label' => 'Đã hoàn thành tất cả dịch vụ', 'detail' => $childTotal . '/' . $childTotal . ' dịch vụ', 'icon' => 'fas fa-trophy', 'bg' => 'bg-green-50 border-green-200 text-green-700'];
}
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
    <div class="rounded-xl border <?= $summaryStatusMeta['bg'] ?> p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-white/70 flex items-center justify-center text-xl">
                <i class="<?= $summaryStatusMeta['icon'] ?>"></i>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold opacity-80">Tình trạng lịch tổng</p>
                <p class="text-lg font-bold leading-tight"><?= $summaryStatusMeta['label'] ?></p>
                <?php if (!empty($summaryAppointment['appointment_date'])): ?>
                <p class="text-sm font-medium opacity-80 mt-1">
                    Ngày khám: <?= date('d/m/Y', strtotime($summaryAppointment['appointment_date'])) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="rounded-xl border <?= $progressMeta['bg'] ?> p-5 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-white/70 flex items-center justify-center text-xl">
                <i class="<?= $progressMeta['icon'] ?>"></i>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wider font-semibold opacity-80">Trạng thái dịch vụ</p>
                <p class="text-lg font-bold leading-tight"><?= $progressMeta['label'] ?></p>
                <p class="text-sm font-medium opacity-80 mt-1"><?= $progressMeta['detail'] ?></p>
            </div>
        </div>
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
<?php 
  $as = isset($assignedCount) ? (int)$assignedCount : 0; 
  $ts = isset($packageServices) ? (int)count($packageServices) : 0; 
?>
<?php if ((Auth::isAdmin() || Auth::isReceptionist()) && $ts > 0 && $as < $ts): ?>
<div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="text-white">
            <h4 class="text-xl font-bold mb-2">
                <i class="fas fa-magic mr-2"></i>Phân công bác sĩ tự động
            </h4>
            <p class="text-purple-100">
                Hệ thống sẽ tự động phân công <?= count($packageServices) ?> dịch vụ (còn thiếu: <?= max(0, $ts - $as) ?>)
            </p>
        </div>
        <form action="<?= APP_URL ?>/package-appointments/<?= $packageAppointment['id'] ?>/auto-assign" 
              method="POST"
              onsubmit="return confirm('Bạn có chắc muốn phân công tự động?\n\nHệ thống sẽ tạo lịch khám còn thiếu cho gói này.')">
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
                (<?= isset($assignedCount) ? (int)$assignedCount : 0 ?>/<?= count($packageServices) ?> đã phân công)
            </span>
            <?php 
                // Tổng thời lượng toàn gói
                $__totalDuration = 0; 
                foreach ($packageServices as $__svc) { $__totalDuration += (int)($__svc['duration_minutes'] ?? 30); }
                $___fmt = function($mins){ $mins=(int)$mins; if($mins<60) return $mins.' phút'; $h=intdiv($mins,60); $m=$mins%60; return $h.' giờ'.($m>0?(' '.$m.' phút'):''); };
            ?>
            <span class="ml-3 px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-semibold">
                ⏱ Tổng thời lượng gói: <?= $___fmt($__totalDuration) ?>
            </span>
            <div class="ml-auto flex items-center gap-2">
                <?php if ((Auth::isAdmin() || Auth::isReceptionist()) && $ts > 0 && $as < $ts): ?>
                <form action="<?= APP_URL ?>/package-appointments/<?= $packageAppointment['id'] ?>/auto-assign" method="POST">
                    <button type="submit" class="px-3 py-1.5 text-xs bg-purple-600 text-white rounded hover:bg-purple-700">
                        <i class="fas fa-robot mr-1"></i>Tự động phân công
                    </button>
                </form>
                <?php endif; ?>

                <?php if (isset($summaryAppointment) && $summaryAppointment && ($summaryAppointment['status'] ?? 'pending') === 'pending' && (Auth::isDoctor() || Auth::isAdmin())): ?>
                <form action="<?= APP_URL ?>/appointments/<?= $summaryAppointment['id'] ?>/update-status" method="POST"
                      onsubmit="return confirm('Xác nhận lịch tổng của gói này?');">
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                        <i class="fas fa-check-circle mr-1"></i>Xác nhận lịch
                    </button>
                </form>
                <?php endif; ?>
            </div>
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
            $sourceAppointments = array_values(array_filter($appointments, function($a){
                // Bỏ qua lịch tổng hợp gói (reason có ":") và chỉ tính lịch có bác sĩ
                if (empty($a['doctor_id'])) return false;
                $rs = strtolower(trim($a['reason'] ?? ''));
                return ($rs === '' || strpos($rs, ':') === false);
            }));
            $uniqueReasons = [];
            foreach ($sourceAppointments as $apt) {
                // Lưu theo reason chính xác
                $appointmentMap[$apt['reason']] = $apt;
                
                // Cũng lưu theo reason đã trim và lowercase để match dễ hơn
                $key = strtolower(trim($apt['reason'] ?? ''));
                if ($key !== '' && !in_array($key, $uniqueReasons, true)) {
                    $uniqueReasons[] = $key;
                }
            }
            $assignedCount = count($uniqueReasons);
            // Tính tổng thời lượng theo danh mục và chuẩn bị formatter
            $categoryTotals = [];
            foreach ($packageServices as $svc) {
                $cat = $svc['service_category'] ?? 'other';
                if (!isset($categoryTotals[$cat])) { $categoryTotals[$cat] = 0; }
                $categoryTotals[$cat] += (int)($svc['duration_minutes'] ?? 30);
            }
            $formatDuration = function($mins) {
                $mins = (int)$mins; if ($mins < 60) return $mins . ' phút';
                $h = intdiv($mins, 60); $m = $mins % 60;
                return $h . ' giờ' . ($m > 0 ? (' ' . $m . ' phút') : '');
            };
            $categoryNames = [
                'general' => 'Khám tổng quát',
                'blood_test' => 'Xét nghiệm máu',
                'urine_test' => 'Xét nghiệm nước tiểu',
                'imaging' => 'Chẩn đoán hình ảnh',
                'specialist' => 'Khám chuyên khoa',
                'other' => 'Khác'
            ];
            // Bản đồ thời lượng theo tên dịch vụ (để suy ra thời lượng của các lịch đã phân công)
            $durationByService = [];
            foreach ($packageServices as $svc) {
                $durationByService[strtolower(trim($svc['service_name']))] = (int)($svc['duration_minutes'] ?? 30);
            }
            $svcStatusMeta = [
                'pending' => [
                    'label' => 'Chờ xác nhận',
                    'note' => 'Chưa khám',
                    'icon' => 'fas fa-clock',
                    'badgeClass' => 'bg-yellow-100 text-yellow-800',
                    'panelBorder' => 'border-yellow-200',
                    'panelBg' => 'bg-yellow-50',
                    'textClass' => 'text-yellow-800'
                ],
                'confirmed' => [
                    'label' => 'Đã xác nhận',
                    'note' => 'Đang chờ khám',
                    'icon' => 'fas fa-user-check',
                    'badgeClass' => 'bg-blue-100 text-blue-800',
                    'panelBorder' => 'border-blue-200',
                    'panelBg' => 'bg-blue-50',
                    'textClass' => 'text-blue-800'
                ],
                'completed' => [
                    'label' => 'Hoàn thành',
                    'note' => 'Đã hoàn thành dịch vụ',
                    'icon' => 'fas fa-check-double',
                    'badgeClass' => 'bg-green-100 text-green-800',
                    'panelBorder' => 'border-green-200',
                    'panelBg' => 'bg-green-50',
                    'textClass' => 'text-green-800'
                ],
                'cancelled' => [
                    'label' => 'Đã hủy',
                    'note' => 'Dịch vụ đã bị hủy',
                    'icon' => 'fas fa-ban',
                    'badgeClass' => 'bg-gray-200 text-gray-700',
                    'panelBorder' => 'border-gray-200',
                    'panelBg' => 'bg-gray-50',
                    'textClass' => 'text-gray-700'
                ],
                'late_cancelled' => [
                    'label' => 'Hủy muộn',
                    'note' => 'Bệnh nhân hủy muộn',
                    'icon' => 'fas fa-exclamation-triangle',
                    'badgeClass' => 'bg-orange-100 text-orange-800',
                    'panelBorder' => 'border-orange-200',
                    'panelBg' => 'bg-orange-50',
                    'textClass' => 'text-orange-800'
                ],
                'no_show' => [
                    'label' => 'Vắng mặt',
                    'note' => 'Bệnh nhân không đến khám',
                    'icon' => 'fas fa-user-times',
                    'badgeClass' => 'bg-red-100 text-red-800',
                    'panelBorder' => 'border-red-200',
                    'panelBg' => 'bg-red-50',
                    'textClass' => 'text-red-800'
                ],
                'default' => [
                    'label' => 'Trạng thái khác',
                    'note' => 'Đang cập nhật',
                    'icon' => 'fas fa-info-circle',
                    'badgeClass' => 'bg-gray-100 text-gray-700',
                    'panelBorder' => 'border-gray-200',
                    'panelBg' => 'bg-gray-50',
                    'textClass' => 'text-gray-700'
                ],
            ];
            // Tập khoảng thời gian đã chiếm theo ngày trong gói
            $usedIntervalsByDate = [];
            foreach ($serviceAppointments as $apt) {
                $date = $apt['appointment_date'];
                $reasonKey = strtolower(trim($apt['reason'] ?? ''));
                $dur = $durationByService[$reasonKey] ?? 30;
                $startTs = strtotime($apt['appointment_date'] . ' ' . $apt['appointment_time']);
                $endTs = $startTs + $dur * 60;
                if (!isset($usedIntervalsByDate[$date])) $usedIntervalsByDate[$date] = [];
                $usedIntervalsByDate[$date][] = [$startTs, $endTs];
            }
            // Hàm kiểm tra trống theo khoảng
            $isFreeSlot = function($date, $startTs, $endTs) use ($usedIntervalsByDate) {
                if (!isset($usedIntervalsByDate[$date])) return true;
                foreach ($usedIntervalsByDate[$date] as [$s,$e]) {
                    if ($startTs < $e && $endTs > $s) return false; // overlap
                }
                return true;
            };
            $lastCategory = null;
            
            foreach ($packageServices as $index => $service): 
                // Hiển thị tiêu đề danh mục và tổng thời lượng khi chuyển nhóm
                $currentCat = $service['service_category'] ?? 'other';
                if ($currentCat !== $lastCategory):
            ?>
            <div class="flex items-center gap-2 mt-2 mb-1">
                <h5 class="text-base font-bold text-gray-800">
                    <i class="fas fa-tag mr-2 text-gray-400"></i><?= htmlspecialchars($categoryNames[$currentCat] ?? $currentCat) ?>
                </h5>
                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold">
                    ⏱ Tổng thời lượng: <?= $formatDuration($categoryTotals[$currentCat] ?? 0) ?>
                </span>
            </div>
            <?php $lastCategory = $currentCat; endif; ?>
            <?php 
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
                    foreach ($sourceAppointments as $apt) {
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
                                <h5 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                    <?= htmlspecialchars($service['service_name']) ?>
                                    <?php 
                                        $__dur = (int)($service['duration_minutes'] ?? 30);
                                        if (!isset($formatDuration)) {
                                            $formatDuration = function($mins){ $mins=(int)$mins; if($mins<60) return $mins.' phút'; $h=intdiv($mins,60); $m=$mins%60; return $h.' giờ'.($m>0?(' '.$m.' phút'):'' ); };
                                        }
                                    ?>
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">
                                        ⏱ <?= $formatDuration($__dur) ?>
                                    </span>
                                </h5>
                                <?php if ($service['service_category']): ?>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-tag mr-1"></i><?= htmlspecialchars($service['service_category']) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($hasAppointment): ?>
                        <?php 
                            $apptStatus = $appointment['status'] ?? 'pending';
                            $meta = $svcStatusMeta[$apptStatus] ?? $svcStatusMeta['default'];
                        ?>
                        <!-- Đã phân công -->
                        <div class="ml-11 mt-3 p-3 <?= $meta['panelBg'] ?> border <?= $meta['panelBorder'] ?> rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $meta['badgeClass'] ?> flex items-center gap-1">
                                            <i class="<?= $meta['icon'] ?>"></i>
                                            <?= $meta['label'] ?>
                                        </span>
                                        <span class="text-xs font-medium <?= $meta['textClass'] ?>"><?= $meta['note'] ?></span>
                                    </div>
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
                                <div class="ml-4 flex items-center space-x-3">
                                    <?php if ($apptStatus === 'pending' && (Auth::isDoctor() || Auth::isAdmin())): ?>
                                    <form action="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/update-status" method="POST" class="inline"
                                          onsubmit="return confirm('Xác nhận lịch hẹn này?');">
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="text-blue-600 hover:text-blue-800" title="Xác nhận">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>" 
                                       class="text-green-600 hover:text-green-800"
                                       title="Xem chi tiết">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Khối Kết quả & Duyệt -->
                        <?php 
                           $svcId = (int)$service['id'];
                           $aps = $apsByServiceId[$svcId] ?? null;
                           $state = $aps['result_state'] ?? 'draft';
                           $stateColor = $state==='approved' ? 'bg-green-100 text-green-800' : ($state==='submitted' ? 'bg-blue-100 text-blue-800' : ($state==='returned' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800'));
                           $metrics = $metricsByServiceId[$svcId] ?? [];
                        ?>
                        <div class="ml-11 mt-3 p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h6 class="font-semibold text-gray-800">Kết quả bác sĩ</h6>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $stateColor ?>">Trạng thái: <?= htmlspecialchars($state) ?></span>
                            </div>
                            <?php if (!empty($aps['review_note']) && $state==='returned'): ?>
                                <div class="mb-3 p-2 rounded bg-orange-50 text-orange-800 text-sm">Lý do trả về: <?= nl2br(htmlspecialchars($aps['review_note'])) ?></div>
                            <?php endif; ?>
                            <?php if (empty($metrics)): ?>
                                <p class="text-sm text-gray-500 italic">Chưa có chỉ số nào.</p>
                            <?php else: ?>
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
                                  <?php foreach ($metrics as $m): ?>
                                  <tr>
                                    <td class="px-3 py-2"><?= htmlspecialchars($m['metric_name']) ?></td>
                                    <td class="px-3 py-2"><?= htmlspecialchars($m['result_value'] ?? '') ?></td>
                                    <td class="px-3 py-2"><?= htmlspecialchars($m['reference_range'] ?? '') ?></td>
                                    <td class="px-3 py-2"><?= htmlspecialchars($m['result_status'] ?? '') ?></td>
                                    <td class="px-3 py-2"><?= htmlspecialchars($m['notes'] ?? '') ?></td>
                                  </tr>
                                  <?php endforeach; ?>
                                </tbody>
                              </table>
                            </div>
                            <?php endif; ?>

                            <?php if ((Auth::isAdmin() || Auth::isDoctor()) && in_array($state, ['submitted','returned'])): ?>
                            <form action="<?= APP_URL ?>/package-appointments/<?= $packageAppointment['id'] ?>/review-service" method="POST" class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                                <input type="hidden" name="service_id" value="<?= $svcId ?>">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Ghi chú/ Lý do trả về</label>
                                    <input type="text" name="review_note" value="" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md" placeholder="Nhập ghi chú cho bác sĩ...">
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" name="action" value="return" class="px-3 py-2 text-sm bg-orange-600 text-white rounded hover:bg-orange-700"><i class="fas fa-undo mr-1"></i>Trả về</button>
                                    <button type="submit" name="action" value="approve" class="px-3 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700"><i class="fas fa-check mr-1"></i>Duyệt</button>
                                </div>
                            </form>
                            <?php endif; ?>
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
                                        <?php $allowed = $doctorsByService[$service['id']] ?? []; ?>
                                        <select name="doctor_id" required 
                                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                            <option value="">-- Chọn bác sĩ --</option>
                                            <?php if (!empty($allowed)): ?>
                                                <?php foreach ($allowed as $doctor): ?>
                                                <option value="<?= (int)$doctor['id'] ?>">
                                                    <?= htmlspecialchars($doctor['full_name']) ?> - <?= htmlspecialchars($doctor['specialization']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>Chưa cấu hình bác sĩ cho dịch vụ này</option>
                                            <?php endif; ?>
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
                                        <?php $durMinutes = (int)($service['duration_minutes'] ?? 30); ?>
                                        <select name="appointment_time" required
                                                class="pkg-time-select w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                                data-duration="<?= $durMinutes ?>">
                                            <option value="">-- Chọn giờ --</option>
                                            <?php
                                                $selectedDate = $packageAppointment['appointment_date'];
                                                $durMinutes = (int)($service['duration_minutes'] ?? 30);
                                                $step = 5; // phút
                                                $dayStart = strtotime($selectedDate . ' 08:00:00');
                                                $dayEnd   = strtotime($selectedDate . ' 17:00:00');
                                                for ($start = $dayStart; $start + $durMinutes * 60 <= $dayEnd; $start += $step * 60) {
                                                    $end = $start + $durMinutes * 60;
                                                    if ($isFreeSlot($selectedDate, $start, $end)) {
                                                        $label = date('H:i', $start);
                                                        echo "<option value='{$label}' data-ts='{$start}'>{$label}</option>";
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
            <?php if (($packageAppointment['final_status'] ?? '') === 'approved'): ?>
            <div class="flex items-center justify-end mt-4">
                <a href="<?= APP_URL ?>/package-appointments/<?= $packageAppointment['id'] ?>/export-pdf" 
                   class="px-4 py-2 inline-flex items-center text-sm font-semibold rounded-lg border border-purple-600 text-purple-700 hover:bg-purple-50">
                    <i class="fas fa-file-pdf mr-2"></i>Xuất PDF
                </a>
                <?php if (!empty($packageAppointment['final_pdf_path'])): ?>
                <a href="<?= str_replace(APP_PATH, APP_URL, $packageAppointment['final_pdf_path']) ?>" target="_blank" 
                   class="ml-2 px-4 py-2 inline-flex items-center text-sm font-semibold rounded-lg bg-purple-600 text-white hover:bg-purple-700">
                    <i class="fas fa-download mr-2"></i>Tải PDF
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  function getFormDate(selectEl){
    const form = selectEl.closest('form');
    if(!form) return null;
    const dateInput = form.querySelector('input[name="appointment_date"]');
    return dateInput ? dateInput.value : null;
  }
  function collectChosenIntervals(){
    const map = {}; // date => [ [s,e], ... ]
    document.querySelectorAll('.pkg-time-select').forEach(sel => {
      const timeOpt = sel.options[sel.selectedIndex];
      if(!timeOpt || !timeOpt.dataset.ts) return;
      const date = getFormDate(sel);
      if(!date) return;
      const start = parseInt(timeOpt.dataset.ts,10);
      const dur = parseInt(sel.dataset.duration||'30',10)*60; // seconds
      const end = start + dur;
      if(!map[date]) map[date] = [];
      map[date].push([start,end]);
    });
    return map;
  }
  function overlaps(aStart,aEnd,bStart,bEnd){ return aStart < bEnd && aEnd > bStart; }
  function refreshOptions(){
    const chosen = collectChosenIntervals();
    document.querySelectorAll('.pkg-time-select').forEach(sel => {
      const date = getFormDate(sel);
      const myDur = parseInt(sel.dataset.duration||'30',10)*60;
      const currentValue = sel.value;
      const currentTs = sel.selectedIndex>0 && sel.options[sel.selectedIndex].dataset.ts ? parseInt(sel.options[sel.selectedIndex].dataset.ts,10) : null;
      Array.from(sel.options).forEach(opt => {
        if(!opt.dataset.ts){ opt.disabled = false; return; }
        const ts = parseInt(opt.dataset.ts,10);
        const end = ts + myDur;
        let ok = true;
        const intervals = chosen[date] || [];
        for(const [s,e] of intervals){
          // allow my currently selected interval
          if(currentTs !== null && ts === currentTs) { ok = true; continue; }
          if(overlaps(ts,end,s,e)) { ok = false; break; }
        }
        opt.disabled = !ok;
      });
    });
  }
  document.querySelectorAll('.pkg-time-select').forEach(sel => {
    sel.addEventListener('change', refreshOptions);
  });
  document.querySelectorAll('input[name="appointment_date"]').forEach(inp => {
    inp.addEventListener('change', function(){
      // Simple approach: no regeneration; just re-run disabling (server pre-filtered for default date)
      refreshOptions();
    });
  });
  refreshOptions();
});
</script>
