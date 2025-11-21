<?php 
$page_title = 'Tạo Hồ sơ bệnh án';
ob_start(); 
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="<?= APP_URL ?>/medical-records" class="text-purple-600 hover:text-purple-700">
            <i class="fas fa-arrow-left mr-2"></i>Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-file-medical mr-2"></i>Tạo Hồ sơ bệnh án
        </h2>

        <form method="POST" action="<?= APP_URL ?>/medical-records/store">
            <!-- Chọn bệnh nhân -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Bệnh nhân <span class="text-red-500">*</span>
                </label>
                <select name="patient_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Chọn bệnh nhân --</option>
                    <?php foreach ($patients as $patient): ?>
                    <option value="<?= $patient['id'] ?>" <?= ($selectedPatient && $selectedPatient['id'] == $patient['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($patient['full_name']) ?> (<?= htmlspecialchars($patient['patient_code']) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Chọn lịch hẹn (chỉ hiện với Bác sĩ) -->
            <?php if (Auth::isDoctor()): ?>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lịch hẹn của bệnh nhân với bạn <span class="text-red-500">*</span>
                </label>
                <select id="appointmentSelect" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                        <?= isset($appointmentId) ? 'disabled' : '' ?>
                >
                    <option value="">-- Chọn lịch hẹn --</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Chọn lịch hẹn đã xác nhận/hoàn thành. Ngày khám sẽ tự động điền theo lịch hẹn.</p>
            </div>
            <?php endif; ?>

            <!-- Ngày khám -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ngày khám <span class="text-red-500">*</span>
                </label>
                <input type="date" name="visit_date" id="visitDateInput" required value="<?= isset($appointmentVisitDate) && $appointmentVisitDate ? htmlspecialchars($appointmentVisitDate) : date('Y-m-d') ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                       <?= Auth::isDoctor() ? 'readonly' : '' ?>>
            </div>

            <!-- Triệu chứng -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Triệu chứng <span class="text-red-500">*</span>
                </label>
                <textarea name="symptoms" required rows="3" placeholder="Mô tả triệu chứng của bệnh nhân..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Chẩn đoán -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Chẩn đoán <span class="text-red-500">*</span>
                </label>
                <textarea name="diagnosis" required rows="3" placeholder="Chẩn đoán bệnh..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Điều trị -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Phương pháp điều trị <span class="text-red-500">*</span>
                </label>
                <textarea name="treatment" required rows="4" placeholder="Kê đơn thuốc, chỉ định xét nghiệm..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Đơn thuốc (văn bản hoặc sẽ bổ sung nhập chi tiết sau) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Đơn thuốc (tùy chọn)
                </label>
                <textarea name="prescription" rows="4" placeholder="Ví dụ:\n- Paracetamol 500mg: 1 viên x 3 lần/ngày x 5 ngày\n- Vitamin C 500mg: 1 viên x 2 lần/ngày x 5 ngày"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                <p class="text-xs text-gray-500 mt-1">Có thể để trống nếu sẽ nhập chi tiết theo từng thuốc ở bước sau.</p>
            </div>

            <!-- Kết quả xét nghiệm (mỗi dòng 1 chỉ số) -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kết quả xét nghiệm (tùy chọn)
                </label>
                <textarea name="test_results" rows="4" placeholder="Mỗi dòng một kết quả, ví dụ:\nCRP: 10 mg/L\nWBC: 12 K/µL\nPCR cúm A/B: âm tính"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Ghi chú -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Ghi chú
                </label>
                <textarea name="notes" rows="2" placeholder="Lời dặn, theo dõi..."
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
            </div>

            <!-- Hidden field -->
            <?php if (isset($appointmentId)): ?>
            <input type="hidden" name="appointment_id" value="<?= $appointmentId ?>">
            <?php else: ?>
            <input type="hidden" name="appointment_id" id="appointmentIdInput" value="">
            <?php endif; ?>

            <!-- Actions -->
            <div class="flex space-x-4">
                <a href="<?= APP_URL ?>/medical-records" 
                   class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                    Hủy
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                    <i class="fas fa-save mr-2"></i>Lưu hồ sơ
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (Auth::isDoctor()): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const patientSelect = document.querySelector('select[name="patient_id"]');
  const appointmentSelect = document.getElementById('appointmentSelect');
  const visitDateInput = document.getElementById('visitDateInput');
  const appointmentIdInput = document.getElementById('appointmentIdInput');

  function clearAppointments() {
    if (!appointmentSelect) return;
    appointmentSelect.innerHTML = '<option value="">-- Chọn lịch hẹn --</option>';
    if (appointmentIdInput) appointmentIdInput.value = '';
  }

  async function loadAppointments(patientId) {
    if (!appointmentSelect || !patientId) { clearAppointments(); return; }
    clearAppointments();
    try {
      const res = await fetch('<?= APP_URL ?>/api/doctor/patient-appointments/' + encodeURIComponent(patientId));
      const data = await res.json();
      if (Array.isArray(data)) {
        data.forEach(a => {
          const d = a.appointment_date;
          const t = a.appointment_time ? (' ' + a.appointment_time.substring(0,5)) : '';
          const opt = document.createElement('option');
          opt.value = a.id;
          opt.textContent = d + t + ' - ' + (a.status === 'completed' ? 'Hoàn thành' : 'Đã xác nhận');
          opt.dataset.date = d;
          appointmentSelect.appendChild(opt);
        });
      }
    } catch (e) { console.warn('Load appointments failed', e); }
  }

  if (patientSelect) {
    patientSelect.addEventListener('change', function() {
      loadAppointments(this.value);
    });
    if (patientSelect.value) {
      loadAppointments(patientSelect.value);
    }
  }

  if (appointmentSelect) {
    appointmentSelect.addEventListener('change', function() {
      const selected = this.options[this.selectedIndex];
      if (selected && selected.value) {
        if (visitDateInput) visitDateInput.value = selected.dataset.date;
        if (appointmentIdInput) appointmentIdInput.value = selected.value;
      } else {
        if (appointmentIdInput) appointmentIdInput.value = '';
      }
    });
  }
});
</script>
<?php endif; ?>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
