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

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
