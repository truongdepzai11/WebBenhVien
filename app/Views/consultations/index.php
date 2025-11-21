<?php /* Patient - list consultations */ ?>
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Tư vấn sức khỏe</h1>
    <a href="<?= APP_URL ?>/consultations/create" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Gửi câu hỏi</a>
  </div>

  <?php if (!empty($_SESSION['success'])): ?>
    <div class="mb-4 p-3 rounded bg-green-50 text-green-700"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['error'])): ?>
    <div class="mb-4 p-3 rounded bg-red-50 text-red-700"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <div class="bg-white rounded shadow">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left">Mã</th>
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
            <td class="px-4 py-2"><?= htmlspecialchars($t['subject']) ?></td>
            <td class="px-4 py-2"><span class="px-2 py-1 rounded text-xs <?= $t['status']==='closed'?'bg-gray-200':'bg-blue-100 text-blue-700' ?>"><?= htmlspecialchars($t['status']) ?></span></td>
            <td class="px-4 py-2 text-gray-500"><?= !empty($t['last_message_at'])? date('d/m/Y H:i', strtotime($t['last_message_at'])) : '' ?></td>
            <td class="px-4 py-2 text-right"><a class="text-purple-700 hover:underline" href="<?= APP_URL.'/consultations/'.$t['id'] ?>">Xem</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($tickets)): ?>
          <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Chưa có câu hỏi nào.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
