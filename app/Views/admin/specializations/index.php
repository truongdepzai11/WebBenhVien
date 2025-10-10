<?php 
$page_title = 'Quản lý Chuyên khoa';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-stethoscope mr-2"></i>Quản lý Chuyên khoa
    </h3>
    <a href="<?= APP_URL ?>/admin/specializations/create" 
       class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
        <i class="fas fa-plus mr-2"></i>Thêm chuyên khoa
    </a>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <?php if (empty($specializations)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-stethoscope text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg mb-4">Chưa có chuyên khoa nào</p>
            <a href="<?= APP_URL ?>/admin/specializations/create" 
               class="inline-flex items-center px-6 py-3 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                <i class="fas fa-plus mr-2"></i>Thêm chuyên khoa đầu tiên
            </a>
        </div>
    <?php else: ?>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên chuyên khoa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Độ tuổi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giới tính</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($specializations as $spec): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900"><?= $spec['id'] ?></td>
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-900"><?= htmlspecialchars($spec['name']) ?></span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate">
                        <?= htmlspecialchars($spec['description']) ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        <?= $spec['min_age'] ?> - <?= $spec['max_age'] ?> tuổi
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <?php
                        $genderLabels = [
                            'both' => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Cả hai</span>',
                            'male' => '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Nam</span>',
                            'female' => '<span class="px-2 py-1 bg-pink-100 text-pink-800 rounded-full text-xs">Nữ</span>'
                        ];
                        echo $genderLabels[$spec['gender_requirement']];
                        ?>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center space-x-3">
                            <a href="<?= APP_URL ?>/admin/specializations/<?= $spec['id'] ?>/edit" 
                               class="text-blue-600 hover:text-blue-900" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= APP_URL ?>/admin/specializations/<?= $spec['id'] ?>/delete" method="POST" 
                                  class="inline" onsubmit="return confirm('Bạn có chắc muốn xóa chuyên khoa này?')">
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
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
