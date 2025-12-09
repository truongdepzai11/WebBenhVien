<?php 
$page_title = 'Kết quả của tôi';
ob_start();
?>
<div class="mb-6">
  <h3 class="text-2xl font-bold text-gray-800"><i class="fas fa-vial mr-2"></i>Kết quả của tôi</h3>
  <p class="text-gray-600 mt-1">Xem kết quả các gói khám bạn đã đăng ký</p>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
  <?php if (empty($packages)): ?>
    <div class="p-12 text-center">
      <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
      <p class="text-gray-500">Chưa có gói khám nào.</p>
    </div>
  <?php else: ?>
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã ĐK</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gói khám</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày đăng ký</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái kết quả</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <?php foreach ($packages as $pa): ?>
        <?php 
          $fs = $pa['final_status'] ?? 'in_progress';
          $fsColor = $fs==='approved' ? 'bg-green-100 text-green-800' : ($fs==='awaiting_review' ? 'bg-blue-100 text-blue-800' : ($fs==='returned' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800'));
        ?>
        <tr class="hover:bg-gray-50">
          <td class="px-6 py-4">#<?= $pa['id'] ?></td>
          <td class="px-6 py-4"><?= htmlspecialchars($pa['package_name']) ?></td>
          <td class="px-6 py-4"><?= date('d/m/Y H:i', strtotime($pa['created_at'])) ?></td>
          <td class="px-6 py-4">
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $fsColor ?>">
              <?= htmlspecialchars($fs) ?> (<?= (int)($pa['approved_count'] ?? 0) ?>/<?= (int)($pa['total_services'] ?? 0) ?>)
            </span>
          </td>
          <td class="px-6 py-4 text-center">
            <a class="text-purple-600 hover:text-purple-800" href="<?= APP_URL ?>/my-results/package/<?= $pa['id'] ?>"><i class="fas fa-eye mr-1"></i>Xem</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<div class="mt-10">
  <h4 class="text-xl font-semibold text-gray-800 mb-3 flex items-center gap-2">
    <i class="fas fa-stethoscope text-purple-500"></i>
    Kết quả khám thường
  </h4>

  <div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($regularResults)): ?>
      <div class="p-10 text-center text-gray-500">
        <p>Bạn chưa có kết quả cho lịch khám thường nào.</p>
        <p class="text-sm mt-1">Sau khi bác sĩ hoàn tất và lưu chẩn đoán/đơn thuốc, dữ liệu sẽ hiển thị tại đây.</p>
      </div>
    <?php else: ?>
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã lịch</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bác sĩ</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thời gian khám</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chẩn đoán chính</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái chẩn đoán</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái đơn thuốc</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kết quả khám</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php foreach ($regularResults as $item): ?>
          <?php
            $dxStatus = $item['diagnosis_status'] ?? null;
            $rxStatus = $item['prescription_status'] ?? null;
            $dxBadge = 'bg-gray-100 text-gray-700';
            if ($dxStatus === 'approved') { $dxBadge = 'bg-green-100 text-green-700'; }
            $rxBadge = 'bg-gray-100 text-gray-700';
            if ($rxStatus === 'approved') { $rxBadge = 'bg-green-100 text-green-700'; }
            elseif ($rxStatus === 'dispensed') { $rxBadge = 'bg-purple-100 text-purple-700'; }
            elseif ($rxStatus === 'submitted') { $rxBadge = 'bg-blue-100 text-blue-700'; }
          ?>
          <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-800">#<?= htmlspecialchars($item['appointment_code']) ?></td>
            <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($item['doctor_name'] ?? '—') ?></td>
            <td class="px-6 py-4 text-gray-600">
              <?php if (!empty($item['appointment_date'])): ?>
                <?= date('d/m/Y', strtotime($item['appointment_date'])) ?>
                <?php if (!empty($item['appointment_time'])): ?>
                  <span class="block text-sm text-gray-400"><?= date('H:i', strtotime($item['appointment_time'])) ?></span>
                <?php endif; ?>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($item['diagnosis_primary'] ?? '—') ?></td>
            <td class="px-6 py-4">
              <?php if ($dxStatus): ?>
              <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $dxBadge ?>"><?= strtoupper(htmlspecialchars($dxStatus)) ?></span>
              <?php else: ?>
              <span class="text-gray-400 text-sm">Chưa có</span>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4 text-gray-700">
              <?php if ($rxStatus): ?>
              <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $rxBadge ?>"><?= strtoupper(htmlspecialchars($rxStatus)) ?></span>
              <?php else: ?>
              <span class="text-gray-400 text-sm">Chưa có</span>
              <?php endif; ?>
              <?php
                $rxItems = $item['prescription_items'] ?? [];
                $rxCode = $item['prescription_code'] ?? null;
              ?>
              <?php if (!empty($rxItems)): ?>
                <div class="mt-3 border border-gray-100 rounded-lg overflow-hidden">
                  <table class="w-full text-xs">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Thuốc</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Liều dùng</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Tần suất</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Thời gian</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Số lượng</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Đường dùng</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Dặn dò</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                      <?php foreach ($rxItems as $rxRow):
                        $label = $rxRow['drug_label'] ?? '';
                        if ($label === '' && !empty($rxRow['medicine_id'])) {
                            $label = 'Mã thuốc #' . (int)$rxRow['medicine_id'];
                        }
                        if (!empty($rxRow['med_strength'])) {
                            $label .= ' ' . $rxRow['med_strength'];
                        }
                        if (!empty($rxRow['med_form'])) {
                            $label .= ' (' . $rxRow['med_form'] . ')';
                        }
                      ?>
                      <tr>
                        <td class="px-3 py-2 text-gray-700">
                          <?= htmlspecialchars($label ?: 'Thuốc') ?>
                          <?php if (!empty($rxCode)): ?>
                            <span class="block text-[11px] text-gray-400">Đơn: <?= htmlspecialchars($rxCode) ?></span>
                          <?php endif; ?>
                        </td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($rxRow['dosage'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($rxRow['frequency'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($rxRow['duration'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= (int)($rxRow['quantity'] ?? 0) ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($rxRow['route'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($rxRow['instructions'] ?? '') ?></td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </td>
            <?php
              $resultStatus = $item['result_status'] ?? null;
              $resultItems = $item['result_items'] ?? [];
              $resultNote = $item['result_note'] ?? null;
              $resultBadge = 'bg-gray-100 text-gray-700';
              if ($resultStatus === 'approved') { $resultBadge = 'bg-green-100 text-green-700'; }
              elseif ($resultStatus === 'submitted') { $resultBadge = 'bg-blue-100 text-blue-700'; }
              elseif ($resultStatus === 'returned') { $resultBadge = 'bg-orange-100 text-orange-700'; }
            ?>
            <td class="px-6 py-4 text-gray-700">
              <?php if ($resultStatus): ?>
                <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $resultBadge ?>"><?= strtoupper(htmlspecialchars($resultStatus)) ?></span>
              <?php else: ?>
                <span class="text-gray-400 text-sm">Chưa có</span>
              <?php endif; ?>
              <?php if (!empty($resultNote)): ?>
                <div class="mt-2 text-xs text-gray-500">Ghi chú: <?= nl2br(htmlspecialchars($resultNote)) ?></div>
              <?php endif; ?>
              <?php if (!empty($resultItems)): ?>
                <div class="mt-3 border border-gray-100 rounded-lg overflow-hidden">
                  <table class="w-full text-xs">
                    <thead class="bg-gray-50">
                      <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Chỉ số</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Kết quả</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Khoảng tham chiếu</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Tình trạng</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500 uppercase">Ghi chú</th>
                      </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                      <?php foreach ($resultItems as $resRow): ?>
                      <tr>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($resRow['metric_name'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($resRow['result_value'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($resRow['reference_range'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700 uppercase"><?= htmlspecialchars($resRow['result_status'] ?? '') ?></td>
                        <td class="px-3 py-2 text-gray-700"><?= htmlspecialchars($resRow['notes'] ?? '') ?></td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </td>
            <td class="px-6 py-4 text-center">
              <a class="text-purple-600 hover:text-purple-800" href="<?= APP_URL ?>/appointments/<?= (int)$item['appointment_id'] ?>"><i class="fas fa-eye mr-1"></i>Xem</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
