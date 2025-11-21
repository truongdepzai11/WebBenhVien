<?php /* Doctor - inbox consultations */ ?>
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Hộp thư tư vấn</h1>
  </div>

  <div class="bg-white rounded shadow">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left">Mã</th>
          <th class="px-4 py-2 text-left">Bệnh nhân</th>
          <th class="px-4 py-2 text-left">Chủ đề</th>
          <th class="px-4 py-2 text-left">Trạng thái</th>
          <th class="px-4 py-2 text-left">Cập nhật</th>
          <th class="px-4 py-2"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($tickets ?? []) as $t): ?>
          <tr class="border-t">
            <td class="px-4 py-2 font-medium"><?= htmlspecialchars($t['code']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($t['patient_name'] ?? '') ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($t['subject']) ?></td>
            <td class="px-4 py-2">
              <span class="px-2 py-1 rounded text-xs <?= $t['status']==='closed'?'bg-gray-200':($t['status']==='answered'?'bg-green-100 text-green-700':'bg-blue-100 text-blue-700') ?>"><?= htmlspecialchars($t['status']) ?></span>
            </td>
            <td class="px-4 py-2 text-gray-500"><?= !empty($t['last_message_at'])? date('d/m/Y H:i', strtotime($t['last_message_at'])) : '' ?></td>
            <td class="px-4 py-2 text-right"><a class="text-purple-700 hover:underline" href="<?= APP_URL.'/consultations/'.$t['id'] ?>">Mở</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tickets)): ?>
          <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">Chưa có tư vấn nào.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
