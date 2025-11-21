<?php
$page_title = 'Thông báo';
?>
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-xl font-bold mb-4"><i class="fas fa-bell mr-2"></i>Thông báo của bạn</h3>

    <?php if (empty($notifications)): ?>
        <div class="text-gray-500 italic">Hiện chưa có thông báo nào.</div>
    <?php else: ?>
        <ul class="divide-y divide-gray-200">
            <?php foreach ($notifications as $n): ?>
                <li class="py-3 flex items-start justify-between <?= $n['is_read'] ? '' : 'bg-purple-50' ?>">
                    <div>
                        <div class="font-semibold text-gray-800">
                            <?= htmlspecialchars($n['title']) ?>
                            <?php if (!$n['is_read']): ?>
                                <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-purple-600 text-white">Mới</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-gray-600 text-sm mt-1"><?= nl2br(htmlspecialchars($n['message'])) ?></div>
                        <div class="text-xs text-gray-400 mt-1"><?= date('d/m/Y H:i', strtotime($n['created_at'])) ?></div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <?php if (!empty($n['link'])): ?>
                        <a class="px-3 py-1 text-sm rounded bg-blue-600 text-white" href="<?= APP_URL . $n['link'] ?>">Mở</a>
                        <?php endif; ?>
                        <?php if (!$n['is_read']): ?>
                        <form method="post" action="<?= APP_URL ?>/notifications/<?= (int)$n['id'] ?>/read">
                            <button class="px-3 py-1 text-sm rounded bg-gray-200 hover:bg-gray-300">Đánh dấu đã đọc</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
