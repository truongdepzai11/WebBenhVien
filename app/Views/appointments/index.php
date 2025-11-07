<?php 
$page_title = isset($pageTitle) ? $pageTitle : 'Quản lý Lịch hẹn';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-calendar-check mr-2"></i><?= $page_title ?>
    </h3>
    <?php if (Auth::isPatient()): ?>
    <a href="<?= APP_URL ?>/appointments/create" 
       class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
        <i class="fas fa-plus mr-2"></i>Đặt lịch mới
    </a>
    <?php endif; ?>
</div>

<!-- Appointments Table -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php 
    // Kiểm tra có appointments không (bao gồm cả regular và package)
    $hasAppointments = !empty($appointments) || !empty($regularAppointments) || !empty($packageAppointments);
    ?>
    <?php if (!$hasAppointments): ?>
        <div class="p-12 text-center">
            <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-4">Chưa có lịch hẹn nào</p>
            <?php if (Auth::isPatient()): ?>
            <a href="<?= APP_URL ?>/appointments/create" 
               class="inline-flex items-center px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                <i class="fas fa-plus mr-2"></i>Đặt lịch khám ngay
            </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã lịch</th>
                        <?php if (!Auth::isPatient()): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bệnh nhân</th>
                        <?php endif; ?>
                        <?php if (!Auth::isDoctor()): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bác sĩ</th>
                        <?php endif; ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày khám</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giờ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại khám</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lý do</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Hiển thị gói khám trước -->
                    <?php if (isset($packageAppointments) && !empty($packageAppointments)): ?>
                        <?php foreach ($packageAppointments as $pkgIndex => $pkg): ?>
                        <!-- Dòng tổng hợp gói khám (màu vàng) - Click để xem lịch hẹn -->
                        <tr class="hover:bg-yellow-100 bg-yellow-50" 
                            onclick="window.location.href='<?= APP_URL ?>/package-appointments/<?= $pkg['id'] ?>/appointments'"
                            style="cursor: pointer;">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-purple-600 font-bold">#PKG<?= $pkg['id'] ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-purple-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($pkg['patient_name']) ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <?= htmlspecialchars($pkg['patient_code']) ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-box-open text-purple-600 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-900">Khám theo gói</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    <?= htmlspecialchars($pkg['package_name']) ?>
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d/m/Y', strtotime($pkg['appointment_date'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Nhiều dịch vụ
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-box-open mr-1"></i>Khám theo gói
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500"><?= htmlspecialchars($pkg['package_name']) ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php
                                $statusColors = [
                                    'scheduled' => 'bg-yellow-100 text-yellow-800',
                                    'in_progress' => 'bg-purple-100 text-purple-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800'
                                ];
                                $statusLabels = [
                                    'scheduled' => 'Chờ khám',
                                    'in_progress' => 'Đang khám',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy'
                                ];
                                $colorClass = $statusColors[$pkg['status']] ?? 'bg-gray-100 text-gray-800';
                                $label = $statusLabels[$pkg['status']] ?? $pkg['status'];
                                ?>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $colorClass ?>">
                                    <?= $label ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <i class="fas fa-arrow-right text-purple-600"></i>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Hiển thị appointments thường hoặc appointments của gói -->
                    <?php 
                    // Nếu đang xem appointments theo package, dùng $appointments
                    // Nếu đang xem trang thường, dùng $regularAppointments
                    $displayAppointments = !empty($appointments) ? $appointments : $regularAppointments;
                    ?>
                    <?php foreach ($displayAppointments as $apt): ?>
                    <?php 
                    // Nếu appointment thuộc gói khám → màu vàng
                    $isPackageAppointment = !empty($apt['package_appointment_id']);
                    $rowClass = $isPackageAppointment ? 'bg-yellow-50 hover:bg-yellow-100' : 'hover:bg-gray-50';
                    ?>
                    <tr class="<?= $rowClass ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($isPackageAppointment): ?>
                                <a href="<?= APP_URL ?>/package-appointments/<?= $apt['package_appointment_id'] ?>" 
                                   class="text-sm font-medium text-purple-600 hover:text-purple-900">
                                    <?= htmlspecialchars($apt['appointment_code']) ?>
                                </a>
                            <?php else: ?>
                                <a href="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>" 
                                   class="text-sm font-medium text-purple-600 hover:text-purple-900">
                                    <?= htmlspecialchars($apt['appointment_code']) ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <?php if (!Auth::isPatient()): ?>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= htmlspecialchars($apt['patient_name']) ?></div>
                            <div class="text-xs text-gray-500"><?= htmlspecialchars($apt['patient_code']) ?></div>
                        </td>
                        <?php endif; ?>
                        <?php if (!Auth::isDoctor()): ?>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($isPackageAppointment && !empty($apt['assigned_doctors'])): ?>
                                <?php 
                                $as = (int)($apt['assigned_count'] ?? 0);
                                $ts = (int)($apt['total_services'] ?? 0);
                                if ($ts > 0) {
                                    if ($as === 0) {
                                        $assignLabel = '<span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">Chưa phân công</span>';
                                    } elseif ($as < $ts) {
                                        $assignLabel = '<span class="px-2 py-0.5 text-xs rounded-full bg-orange-100 text-orange-800">Đã phân công (còn thiếu) '. $as .'/'. $ts .' dịch vụ</span>';
                                    } else {
                                        $assignLabel = '<span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">Đã phân công đầy đủ '. $as .'/'. $ts .' dịch vụ</span>';
                                    }
                                    echo $assignLabel;
                                }
                                ?>
                            <?php else: ?>
                                <?php if (!empty($apt['doctor_name'])): ?>
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($apt['doctor_name']) ?></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($apt['specialization']) ?></div>
                                <?php else: ?>
                                    <?php 
                                    if ($isPackageAppointment) {
                                        $as = (int)($apt['assigned_count'] ?? 0);
                                        $ts = (int)($apt['total_services'] ?? 0);
                                        if ($ts > 0) {
                                            if ($as === 0) {
                                                echo '<div class="text-sm text-gray-400 italic">Chưa phân công</div>';
                                            } elseif ($as < $ts) {
                                                echo '<div class="text-sm text-gray-700">Đã phân công (còn thiếu) '. $as .'/'. $ts .'</div>';
                                            } else {
                                                echo '<div class="text-sm text-gray-700">Đã phân công đầy đủ '. $as .'/'. $ts .'</div>';
                                            }
                                        } else {
                                            echo '<div class="text-sm text-gray-400 italic">Chưa phân công</div>';
                                        }
                                    } else {
                                        echo '<div class="text-sm text-gray-400 italic">Chưa phân công</div>';
                                    }
                                    ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <?php if ($isPackageAppointment && !empty($apt['appointment_dates'])): ?>
                                <?php 
                                $formattedDates = array_map(function($date) {
                                    return date('d/m/Y', strtotime($date));
                                }, $apt['appointment_dates']);
                                echo implode(', ', $formattedDates);
                                ?>
                            <?php else: ?>
                                <?= date('d/m/Y', strtotime($apt['appointment_date'])) ?>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <?php if ($isPackageAppointment): ?>
                                <span class="text-gray-500 italic">Nhiều giờ</span>
                            <?php elseif (!empty($apt['appointment_time'])): ?>
                                <?= date('H:i', strtotime($apt['appointment_time'])) ?>
                            <?php else: ?>
                                <span class="text-gray-400 italic">Chưa xác định</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($isPackageAppointment): ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-box-open mr-1"></i>Khám theo gói
                                </span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-user-md mr-1"></i>Khám thường
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700 max-w-xs truncate"><?= htmlspecialchars($apt['reason']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColors[$apt['status']] ?>">
                                <?= $statusLabels[$apt['status']] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <?php if ($apt['status'] === 'pending' && (Auth::isDoctor() || Auth::isAdmin())): ?>
                                <form action="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>/update-status" method="POST" class="inline">
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="text-blue-600 hover:text-blue-900" title="Xác nhận">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if ($apt['status'] === 'confirmed' && (Auth::isDoctor() || Auth::isAdmin())): ?>
                                <form action="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>/update-status" method="POST" class="inline">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Hoàn thành">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                </form>
                                <a href="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>/no-show" 
                                   onclick="return confirm('Xác nhận bệnh nhân không đến khám? Sẽ tạo hóa đơn phạt 100% phí khám.')"
                                   class="text-red-600 hover:text-red-900" title="Vắng mặt">
                                    <i class="fas fa-user-times"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if (in_array($apt['status'], ['pending', 'confirmed'])): ?>
                                <a href="<?= APP_URL ?>/appointments/<?= $apt['id'] ?>/cancel" 
                                   class="text-orange-600 hover:text-orange-900" title="Hủy lịch">
                                    <i class="fas fa-times-circle"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
