<?php 
$page_title = 'Quản lý Users';
ob_start(); 
?>

<div class="mb-6">
    <h3 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-users-cog mr-2"></i>Quản lý Users
    </h3>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Họ tên</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số ĐT</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày tạo</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php foreach ($users as $user): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <?= $user['id'] ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($user['username']) ?></span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <?= htmlspecialchars($user['full_name']) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    <?= htmlspecialchars($user['email']) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    <?= htmlspecialchars($user['phone']) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <?php
                    $roleColors = [
                        'admin' => 'bg-red-100 text-red-800',
                        'doctor' => 'bg-green-100 text-green-800',
                        'patient' => 'bg-blue-100 text-blue-800',
                        'staff' => 'bg-gray-100 text-gray-800'
                    ];
                    $roleLabels = [
                        'admin' => 'Admin',
                        'doctor' => 'Bác sĩ',
                        'patient' => 'Bệnh nhân',
                        'staff' => 'Nhân viên'
                    ];
                    ?>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $roleColors[$user['role']] ?>">
                        <?= $roleLabels[$user['role']] ?>
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
