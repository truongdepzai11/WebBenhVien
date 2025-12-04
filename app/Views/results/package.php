<?php 
$page_title = 'Kết quả gói #' . $pa['id'];
ob_start();
?>
<div class="mb-6">
  <a href="<?= APP_URL ?>/my-results" class="text-purple-600 hover:text-purple-700"><i class="fas fa-arrow-left mr-2"></i>Quay lại</a>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
  <div class="flex items-center justify-between">
    <div>
      <h3 class="text-2xl font-bold text-gray-800">Gói khám: <?= htmlspecialchars($pa['package_name']) ?></h3>
      <p class="text-gray-600">Mã ĐK: #<?= $pa['id'] ?></p>
    </div>
    <?php 
      $fs = $pa['final_status'] ?? 'in_progress';
      $fsColor = $fs==='approved' ? 'bg-green-100 text-green-800' : ($fs==='awaiting_review' ? 'bg-blue-100 text-blue-800' : ($fs==='returned' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800'));
    ?>
    <div class="flex items-center gap-3">
      <?php if (!empty($pa['final_pdf_path'])): ?>
        <a href="<?= APP_URL ?>/package-appointments/<?= (int)$pa['id'] ?>/download-pdf" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-purple-600 text-white hover:bg-purple-700">
          <i class="fas fa-file-pdf"></i>
          <span>Tải PDF</span>
        </a>
      <?php elseif (($pa['final_status'] ?? '') === 'approved'): ?>
        <a href="<?= APP_URL ?>/package-appointments/<?= (int)$pa['id'] ?>/export-pdf" class="inline-flex items-center gap-2 px-3 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700">
          <i class="fas fa-download"></i>
          <span>Xuất PDF</span>
        </a>
      <?php endif; ?>
      <span class="px-3 py-1 text-sm font-semibold rounded-full <?= $fsColor ?>">Trạng thái: <?= htmlspecialchars($fs) ?></span>
    </div>
  </div>
</div>

<?php if (empty($apsRows)): ?>
  <div class="p-8 text-center text-gray-500 bg-white rounded-lg shadow">Chưa có kết quả nào.</div>
<?php else: ?>
  <div class="space-y-4">
    <?php foreach ($apsRows as $row): ?>
      <?php 
        $svcName = $row['service_name'];
        $svcId = (int)$row['service_id'];
        $state = $row['result_state'];
        $stateColor = $state==='approved' ? 'bg-green-100 text-green-800' : ($state==='submitted' ? 'bg-blue-100 text-blue-800' : ($state==='returned' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800'));
        $metrics = $metricsByServiceId[$svcId] ?? [];
        $rj = json_decode($row['result_json'] ?? '', true);
      ?>
      <?php if ($state !== 'approved' && ($pa['final_status'] ?? '') !== 'approved') continue; ?>
      <div class="bg-white rounded-lg shadow p-5">
        <div class="flex items-center justify-between">
          <h4 class="text-lg font-semibold text-gray-900"><i class="fas fa-stethoscope mr-2"></i><?= htmlspecialchars($svcName) ?></h4>
          <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $stateColor ?>">Trạng thái: <?= htmlspecialchars($state) ?></span>
        </div>

        <?php if (!empty($rj['findings']) || !empty($rj['conclusion'])): ?>
          <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php if (!empty($rj['findings'])): ?>
            <div>
              <h5 class="font-semibold text-gray-800 mb-1">Mô tả</h5>
              <div class="text-gray-700 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($rj['findings'])) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($rj['conclusion'])): ?>
            <div>
              <h5 class="font-semibold text-gray-800 mb-1">Kết luận</h5>
              <div class="text-gray-700 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($rj['conclusion'])) ?></div>
            </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($metrics)): ?>
          <div class="mt-4 overflow-x-auto">
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

        <?php if (!empty($row['result_files'])): ?>
          <?php $files = json_decode($row['result_files'], true); if (is_array($files) && !empty($files)): ?>
          <div class="mt-4">
            <h5 class="font-semibold text-gray-800 mb-2">Hình ảnh/Tệp đính kèm</h5>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
              <?php foreach ($files as $f): ?>
                <a href="<?= APP_URL . $f ?>" target="_blank" class="block border rounded hover:shadow">
                  <div class="p-2 text-sm text-gray-700 truncate"><?= htmlspecialchars(basename($f)) ?></div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>
        <?php endif; ?>

        <?php 
          // Dùng lịch con (service_appointment_id) để ánh xạ chẩn đoán/đơn thuốc đúng dịch vụ
          $svcAptId = (int)($row['service_appointment_id'] ?? 0);
          $dx = $diagnosisByAppointmentId[$svcAptId] ?? null;
          $rx = $prescriptionByAppointmentId[$svcAptId] ?? null;
          $rxItems = $rx ? ($prescriptionItemsByRxId[(int)$rx['id']] ?? []) : [];
        ?>

        <?php if (!empty($dx)): ?>
        <div class="mt-4 border-t pt-4">
          <h5 class="font-semibold text-gray-800 mb-2"><i class="fas fa-notes-medical mr-2"></i>Chẩn đoán</h5>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <div><span class="font-semibold">ICD-10:</span> <?= htmlspecialchars($dx['primary_icd10'] ?? '') ?></div>
            <div><span class="font-semibold">Trạng thái:</span> <?= htmlspecialchars($dx['status'] ?? '') ?></div>
            <div class="md:col-span-2"><span class="font-semibold">Dấu hiệu & lâm sàng:</span><br><?= nl2br(htmlspecialchars($dx['clinical_findings'] ?? '')) ?></div>
            <div class="md:col-span-2"><span class="font-semibold">Đánh giá:</span><br><?= nl2br(htmlspecialchars($dx['assessment'] ?? '')) ?></div>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($rx) && ($rx['status'] ?? '') !== 'draft'): ?>
        <div class="mt-4 border-t pt-4">
          <div class="flex items-center justify-between mb-2">
            <h5 class="font-semibold text-gray-800"><i class="fas fa-prescription-bottle-alt mr-2"></i>Đơn thuốc</h5>
            <?php 
              $rxs = $rx['status'] ?? 'draft';
              $rxColor = $rxs==='approved' ? 'bg-green-100 text-green-800' : ($rxs==='submitted' ? 'bg-blue-100 text-blue-800' : ($rxs==='dispensed' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'));
            ?>
            <span class="px-2 py-0.5 text-xs font-semibold rounded-full <?= $rxColor ?>"><?= htmlspecialchars($rxs) ?></span>
          </div>
          <?php if (!empty($rxItems)): ?>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-left">Thuốc</th>
                  <th class="px-3 py-2 text-left">SL</th>
                  <th class="px-3 py-2 text-left">Liều</th>
                  <th class="px-3 py-2 text-left">Số lần</th>
                  <th class="px-3 py-2 text-left">Thời gian</th>
                  <th class="px-3 py-2 text-left">Đường dùng</th>
                  <th class="px-3 py-2 text-left">Hướng dẫn</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-100">
                <?php foreach ($rxItems as $it): ?>
                <tr>
                  <td class="px-3 py-2"><?= htmlspecialchars($it['drug_name'] ?? '') ?><?= empty($it['drug_name']) && !empty($it['medicine_id']) ? ('#'.(int)$it['medicine_id']) : '' ?></td>
                  <td class="px-3 py-2"><?= htmlspecialchars($it['quantity']) ?></td>
                  <td class="px-3 py-2"><?= htmlspecialchars($it['dosage']) ?></td>
                  <td class="px-3 py-2"><?= htmlspecialchars($it['frequency']) ?></td>
                  <td class="px-3 py-2"><?= htmlspecialchars($it['duration']) ?></td>
                  <td class="px-3 py-2"><?= htmlspecialchars($it['route'] ?? '') ?></td>
                  <td class="px-3 py-2 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($it['instructions'] ?? '')) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php else: ?>
          <div class="text-gray-500 text-sm">Đơn thuốc không có mục nào.</div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
