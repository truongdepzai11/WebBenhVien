<?php 
$page_title = 'Tạo Hóa đơn từ Lịch khám';
ob_start(); 
?>

<div class="mb-6">
    <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>" class="text-purple-600 hover:text-purple-700">
        <i class="fas fa-arrow-left mr-2"></i>Quay lại lịch hẹn
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-file-invoice-dollar mr-2"></i>Tạo Hóa đơn cho Lịch khám #<?= $appointment['appointment_code'] ?>
    </h2>

    <!-- Thông tin lịch khám -->
    <div class="bg-purple-50 p-6 rounded-lg mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Bệnh nhân</p>
                <p class="font-bold text-gray-900"><?= htmlspecialchars($appointment['patient_name']) ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Ngày khám</p>
                <p class="font-bold text-gray-900"><?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Bác sĩ</p>
                <p class="font-bold text-gray-900"><?= htmlspecialchars($appointment['doctor_name']) ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Chuyên khoa</p>
                <p class="font-bold text-gray-900"><?= htmlspecialchars($appointment['specialization']) ?></p>
            </div>
        </div>
    </div>

    <form method="POST" action="<?= APP_URL ?>/invoices/store" id="invoiceForm">
        <input type="hidden" name="patient_id" value="<?= $appointment['patient_id'] ?>">
        <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">

        <!-- Items (Tự động thêm Phí khám) -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Chi tiết dịch vụ</h3>
                <button type="button" onclick="addItem()" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-2"></i>Thêm dịch vụ
                </button>
            </div>

            <div id="itemsContainer" class="space-y-4">
                <!-- Item mặc định: Phí khám -->
                <div class="item-row bg-gray-50 p-4 rounded-lg" id="item-0">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-3">
                            <select name="items[0][type]" required class="w-full px-3 py-2 border rounded-lg">
                                <option value="consultation" selected>Khám bệnh</option>
                                <option value="medicine">Thuốc</option>
                                <option value="test">Xét nghiệm</option>
                                <option value="procedure">Thủ thuật</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        <div class="col-span-4">
                            <input type="text" name="items[0][description]" value="Phí khám <?= htmlspecialchars($appointment['specialization']) ?>" required
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="col-span-1">
                            <input type="number" name="items[0][quantity]" value="1" min="1" required
                                   onchange="calculateItemTotal(0)"
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[0][unit_price]" value="<?= $appointment['consultation_fee'] ?>" min="0" step="1000" required
                                   onchange="calculateItemTotal(0)"
                                   class="w-full px-3 py-2 border rounded-lg">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[0][total_price]" value="<?= $appointment['consultation_fee'] ?>" readonly
                                   class="w-full px-3 py-2 border rounded-lg bg-gray-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tổng tiền -->
        <div class="border-t pt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Giảm giá (VNĐ)</label>
                        <input type="number" name="discount_amount" id="discount" value="0" min="0" step="1000"
                               onchange="calculateTotal()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thuế VAT (VNĐ)</label>
                        <input type="number" name="tax_amount" id="tax" value="0" min="0" step="1000"
                               onchange="calculateTotal()"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                        <textarea name="notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-700">
                            <span>Tổng tiền:</span>
                            <span class="font-semibold" id="totalDisplay"><?= number_format($appointment['consultation_fee']) ?> VNĐ</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Giảm giá:</span>
                            <span class="font-semibold text-red-600" id="discountDisplay">0 VNĐ</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Thuế VAT:</span>
                            <span class="font-semibold" id="taxDisplay">0 VNĐ</span>
                        </div>
                        <div class="flex justify-between text-2xl font-bold text-gray-900 pt-3 border-t">
                            <span>Tổng thanh toán:</span>
                            <span class="text-purple-600" id="finalDisplay"><?= number_format($appointment['consultation_fee']) ?> VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="total_amount" id="total_amount" value="<?= $appointment['consultation_fee'] ?>">
        <input type="hidden" name="final_amount" id="final_amount" value="<?= $appointment['consultation_fee'] ?>">

        <!-- Actions -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="<?= APP_URL ?>/appointments/<?= $appointment['id'] ?>" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Hủy
            </a>
            <button type="submit" 
                    class="px-6 py-2 gradient-bg text-white rounded-lg hover:opacity-90 transition">
                <i class="fas fa-save mr-2"></i>Tạo hóa đơn
            </button>
        </div>
    </form>
</div>

<script>
let itemCount = 1; // Bắt đầu từ 1 vì đã có item 0

function addItem() {
    const container = document.getElementById('itemsContainer');
    const itemHtml = `
        <div class="item-row bg-gray-50 p-4 rounded-lg" id="item-${itemCount}">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-3">
                    <select name="items[${itemCount}][type]" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="consultation">Khám bệnh</option>
                        <option value="medicine" selected>Thuốc</option>
                        <option value="test">Xét nghiệm</option>
                        <option value="procedure">Thủ thuật</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="col-span-4">
                    <input type="text" name="items[${itemCount}][description]" placeholder="Mô tả dịch vụ" required
                           class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="col-span-1">
                    <input type="number" name="items[${itemCount}][quantity]" value="1" min="1" required
                           onchange="calculateItemTotal(${itemCount})"
                           class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="col-span-2">
                    <input type="number" name="items[${itemCount}][unit_price]" placeholder="Đơn giá" min="0" step="1000" required
                           onchange="calculateItemTotal(${itemCount})"
                           class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div class="col-span-2 flex items-center justify-between">
                    <input type="number" name="items[${itemCount}][total_price]" readonly
                           class="w-3/4 px-3 py-2 border rounded-lg bg-gray-100">
                    <button type="button" onclick="removeItem(${itemCount})" 
                            class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
    itemCount++;
}

function removeItem(id) {
    document.getElementById(`item-${id}`).remove();
    calculateTotal();
}

function calculateItemTotal(id) {
    const row = document.getElementById(`item-${id}`);
    const quantity = parseFloat(row.querySelector('[name*="[quantity]"]').value) || 0;
    const unitPrice = parseFloat(row.querySelector('[name*="[unit_price]"]').value) || 0;
    const total = quantity * unitPrice;
    row.querySelector('[name*="[total_price]"]').value = total;
    calculateTotal();
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('[name*="[total_price]"]').forEach(input => {
        total += parseFloat(input.value) || 0;
    });

    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    const final = total - discount + tax;

    document.getElementById('total_amount').value = total;
    document.getElementById('final_amount').value = final;
    
    document.getElementById('totalDisplay').textContent = total.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('discountDisplay').textContent = discount.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('taxDisplay').textContent = tax.toLocaleString('vi-VN') + ' VNĐ';
    document.getElementById('finalDisplay').textContent = final.toLocaleString('vi-VN') + ' VNĐ';
}

// Tính tổng ban đầu
window.onload = function() {
    calculateTotal();
};
</script>

<?php 
$content = ob_get_clean();
require_once APP_PATH . '/Views/layouts/main.php';
?>
