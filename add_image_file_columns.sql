-- Thêm cột ảnh và file vào bảng package_test_results
ALTER TABLE `package_test_results` 
ADD COLUMN `image_path` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn file ảnh X-quang, siêu âm...',
ADD COLUMN `file_path` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn file xét nghiệm, PDF...';
