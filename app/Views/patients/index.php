<?php 
$page_title = 'Danh sách Bệnh nhân';
ob_start(); 
?>

<div class="mb-6 flex items-center justify-between">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-hospital-user mr-2"></i>Danh sách Bệnh nhân
    </h3>
</div>

<!-- Search -->
<div class="bg-white rounded-lg shadow-md p-4 mb-6">
    <form action="<?= APP_URL ?>/patients/search" method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="keyword" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Tìm kiếm bệnh nhân theo tên, mã bệnh nhân...">
        </div>
        <button type="submit" class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
            <i class="fas fa-search mr-2"></i>Tìm kiếm
        </button>
    </form>
</div>

<!-- Patients Table -->
<?php if (empty($patients)): ?>
    <div class="bg-white rounded-lg shadow-md p-12 text-center">
        <i class="fas fa-hospital-user text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Không tìm thấy bệnh nhân nào</p>
    </div>
<?php else: ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã BN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ tên</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày sinh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giới tính</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số điện thoại</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhóm máu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($patients as $patient): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($patient['patient_code']) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-purple-600"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($patient['full_name']) ?></div>
                                <div class="text-xs text-gray-500"><?= htmlspecialchars($patient['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <?= $patient['date_of_birth'] ? date('d/m/Y', strtotime($patient['date_of_birth'])) : 'N/A' ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php
                        $genderLabels = [
                            'male' => '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Nam</span>',
                            'female' => '<span class="px-2 py-1 bg-pink-100 text-pink-800 rounded-full text-xs">Nữ</span>',
                            'other' => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Khác</span>'
                        ];
                        echo $genderLabels[$patient['gender']] ?? 'N/A';
                        ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <?= htmlspecialchars($patient['phone']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <?php if ($patient['blood_type']): ?>
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                <?= htmlspecialchars($patient['blood_type']) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-gray-400">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="<?= APP_URL ?>/patients/<?= $patient['id'] ?>" 
                           class="text-purple-600 hover:text-purple-900 mr-3">
                            <i class="fas fa-eye"></i> Chi tiết
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
