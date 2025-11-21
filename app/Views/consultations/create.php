<?php /* Patient - create consultation */ ?>
<div class="px-6 py-6">
  <h1 class="text-2xl font-bold mb-4">Gửi câu hỏi tư vấn</h1>

  <?php if (!empty($_SESSION['error'])): ?>
    <div class="mb-4 p-3 rounded bg-red-50 text-red-700"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <form action="<?= APP_URL ?>/consultations" method="post" enctype="multipart/form-data" class="bg-white rounded shadow p-4 space-y-4">
    <div>
      <label class="block text-sm font-medium mb-1">Bác sĩ (tuỳ chọn)</label>
      <select name="doctor_id" class="w-full border rounded px-3 py-2">
        <option value="">-- Chưa chọn --</option>
        <?php foreach (($doctors ?? []) as $d): ?>
          <option value="<?= (int)$d['id'] ?>"><?= htmlspecialchars($d['full_name'] ?? ('BS#'.$d['id'])) ?><?= !empty($d['specialization']) ? ' - '.$d['specialization'] : '' ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Chủ đề</label>
      <input type="text" name="subject" class="w-full border rounded px-3 py-2" required />
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Nội dung</label>
      <textarea name="message" rows="6" class="w-full border rounded px-3 py-2" required></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">File đính kèm (tối đa 5, mỗi file ≤ 10MB)</label>
      <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
    </div>
    <div class="flex justify-end gap-3">
      <a href="<?= APP_URL ?>/consultations" class="px-4 py-2 rounded border">Huỷ</a>
      <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">Gửi</button>
    </div>
  </form>
</div>
