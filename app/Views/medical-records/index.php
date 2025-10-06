<?php 
$page_title = 'Hồ sơ bệnh án';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-file-medical mr-2"></i>Hồ sơ bệnh án
    </h3>
    
    <?php if (Auth::isDoctor() || Auth::isAdmin()): ?>
    <a href="<?= APP_URL ?>/medical-records/create" class="gradient-bg text-white px-6 py-3 rounded-lg hover:opacity-90 transition">
        <i class="fas fa-plus mr-2"></i>Tạo hồ sơ mới
    </a>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
    <i class="fas fa-check-circle mr-2"></i><?= $_SESSION['success'] ?>
</div>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
    <i class="fas fa-exclamation-circle mr-2"></i><?= $_SESSION['error'] ?>
</div>
<?php unset($_SESSION['error']); endif; ?>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($records)): ?>
    <div class="p-12 text-center">
        <i class="fas fa-file-medical text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Chưa có hồ sơ bệnh án nào</p>
        <?php if (Auth::isDoctor() || Auth::isAdmin()): ?>
        <a href="<?= APP_URL ?>/medical-records/create" class="inline-block mt-4 text-purple-600 hover:text-purple-700">
            <i class="fas fa-plus mr-2"></i>Tạo hồ sơ đầu tiên
        </a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã hồ sơ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bệnh nhân</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bác sĩ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày khám</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chẩn đoán</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($records as $record): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900">MR<?= str_pad($record['id'], 5, '0', STR_PAD_LEFT) ?></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($record['patient_name']) ?></div>
                            <div class="text-sm text-gray-500"><?= htmlspecialchars($record['patient_code']) ?></div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900"><?= htmlspecialchars($record['doctor_name']) ?></div>
                    <div class="text-sm text-gray-500"><?= htmlspecialchars($record['specialization']) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($record['visit_date'])) ?></div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900 line-clamp-2"><?= htmlspecialchars($record['diagnosis']) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="<?= APP_URL ?>/medical-records/<?= $record['id'] ?>" 
                       class="text-purple-600 hover:text-purple-900" title="Chi tiết">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
