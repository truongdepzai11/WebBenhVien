<?php /* Ticket thread */ ?>
<div class="px-6 py-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold"><?= htmlspecialchars($ticket['subject']) ?></h1>
    <span class="px-2 py-1 rounded text-xs <?= $ticket['status']==='closed'?'bg-gray-200':'bg-blue-100 text-blue-700' ?>"><?= htmlspecialchars($ticket['status']) ?></span>
  </div>

  <div class="bg-white rounded shadow divide-y">
    <?php foreach (($messages ?? []) as $m): ?>
      <div class="p-4">
        <div class="flex items-center justify-between">
          <div class="font-medium"><?= htmlspecialchars($m['sender_name'] ?? 'Người dùng') ?></div>
          <div class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></div>
        </div>
        <div class="mt-2 whitespace-pre-line text-gray-800"><?= nl2br(htmlspecialchars($m['message_text'])) ?></div>
        <?php $atts = $attachmentsByMsg[$m['id']] ?? []; if (!empty($atts)): ?>
          <div class="mt-2 text-sm">
            <div class="text-gray-500 mb-1">Tệp đính kèm:</div>
            <ul class="list-disc pl-5">
              <?php foreach ($atts as $a): ?>
                <li><a class="text-purple-700 hover:underline" href="<?= APP_URL . $a['file_path'] ?>" target="_blank"><?= htmlspecialchars($a['file_name']) ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <form action="<?= APP_URL.'/consultations/'.$ticket['id'].'/reply' ?>" method="post" enctype="multipart/form-data" class="mt-4 bg-white rounded shadow p-4 space-y-3">
    <div>
      <label class="block text-sm font-medium mb-1">Nội dung</label>
      <textarea name="message" rows="4" class="w-full border rounded px-3 py-2" required></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">File đính kèm</label>
      <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
    </div>
    <div class="flex justify-end">
      <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Gửi</button>
    </div>
  </form>
</div>
