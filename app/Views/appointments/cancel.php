<?php 
$page_title = 'Hủy Lịch hẹn';
ob_start(); 
?>

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>" class="text-purple-600 hover:text-purple-700">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
            <i class="fas fa-times-circle text-red-600 mr-2"></i>Xác nhận Hủy Lịch hẹn
        </h2>

        <!-- Thông tin lịch hẹn -->
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="font-bold text-gray-800 mb-4">Thông tin lịch hẹn</h3>
            <div class="space-y-2 text-gray-700">
                <p><strong>Mã lịch:</strong> <?= htmlspecialchars($appointment['appointment_code']) ?></p>
                <p><strong>Bác sĩ:</strong> <?= htmlspecialchars($appointment['doctor_name']) ?></p>
                <p><strong>Chuyên khoa:</strong> <?= htmlspecialchars($appointment['specialization']) ?></p>
                <p><strong>Ngày khám:</strong> <?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?></p>
                <p><strong>Giờ khám:</strong> <?= date('H:i', strtotime($appointment['appointment_time'])) ?></p>
            </div>
        </div>

        <!-- Cảnh báo phí hủy -->
        <?php if ($cancellationFee > 0): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3 mt-1"></i>
                <div>
                    <h3 class="font-bold text-red-800 mb-2">Cảnh báo: Phí hủy lịch muộn!</h3>
                    <p class="text-red-700 mb-3">
                        Bạn đang hủy lịch trong vòng <strong>1 giờ</strong> trước giờ khám. 
                        Theo chính sách của bệnh viện, bạn sẽ bị tính phí hủy lịch.
                    </p>
                    <div class="bg-white p-4 rounded border border-red-200">
                        <p class="text-sm text-gray-600 mb-1">Phí khám gốc:</p>
                        <p class="text-lg font-semibold text-gray-900 mb-2"><?= number_format($appointment['consultation_fee']) ?> VNĐ</p>
                        <p class="text-sm text-gray-600 mb-1">Phí hủy (30%):</p>
                        <p class="text-2xl font-bold text-red-600"><?= number_format($cancellationFee) ?> VNĐ</p>
                    </div>
                    <p class="text-sm text-red-600 mt-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Hóa đơn phí hủy sẽ được tạo tự động và bạn cần thanh toán trong vòng 7 ngày.
                    </p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-green-50 border-l-4 border-green-500 p-6 mb-6">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 text-2xl mr-3 mt-1"></i>
                <div>
                    <h3 class="font-bold text-green-800 mb-2">Hủy miễn phí</h3>
                    <p class="text-green-700">
                        Bạn đang hủy lịch trước <strong>1 giờ</strong> so với giờ khám. 
                        Không có phí hủy lịch.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form hủy lịch -->
        <form method="POST" action="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>/cancel">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lý do hủy lịch <span class="text-red-500">*</span>
                </label>
                <textarea name="cancellation_reason" rows="4" required
                          placeholder="Vui lòng cho chúng tôi biết lý do bạn hủy lịch..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <div class="flex space-x-4">
                <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>" 
                   class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                    <i class="fas fa-times mr-2"></i>Không, giữ lịch
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-check mr-2"></i>Xác nhận hủy lịch
                </button>
            </div>
        </form>

        <!-- Chính sách hủy lịch -->
        <div class="mt-8 pt-6 border-t">
            <h3 class="font-bold text-gray-800 mb-3">
                <i class="fas fa-info-circle mr-2"></i>Chính sách hủy lịch
            </h3>
            <ul class="space-y-2 text-sm text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                    <span>Hủy trước <strong>1 giờ</strong>: Miễn phí, không mất chi phí</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2 mt-1"></i>
                    <span>Hủy trong vòng <strong>1 giờ</strong>: Phí hủy <strong>30%</strong> phí khám</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-times text-red-500 mr-2 mt-1"></i>
                    <span>Không đến khám (No-show): Phí phạt <strong>100%</strong> phí khám</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
