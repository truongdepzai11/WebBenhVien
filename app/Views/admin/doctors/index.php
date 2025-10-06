<?php 
$page_title = 'Quản lý Bác sĩ';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-user-md mr-2"></i>Quản lý Bác sĩ
    </h3>
    <a href="<?= APP_URL ?>/admin/doctors/create" 
       class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
        <i class="fas fa-plus mr-2"></i>Thêm bác sĩ
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($doctors)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-user-md text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-4">Chưa có bác sĩ nào</p>
            <a href="<?= APP_URL ?>/admin/doctors/create" 
               class="inline-flex items-center px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                <i class="fas fa-plus mr-2"></i>Thêm bác sĩ đầu tiên
            </a>
        </div>
    <?php else: ?>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã BS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ tên</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chuyên khoa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kinh nghiệm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phí khám</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($doctors as $doctor): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($doctor['doctor_code']) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-md text-green-600"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($doctor['full_name']) ?></div>
                                <div class="text-xs text-gray-500"><?= htmlspecialchars($doctor['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                            <?= htmlspecialchars($doctor['specialization']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <?= $doctor['experience_years'] ?> năm
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <?= number_format($doctor['consultation_fee']) ?> VNĐ
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="<?= APP_URL ?>/admin/doctors/<?= $doctor['id'] ?>/edit" 
                           class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?= APP_URL ?>/admin/doctors/<?= $doctor['id'] ?>/delete" method="POST" 
                              class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa bác sĩ này?')">
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
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
