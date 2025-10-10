<?php

class Validator {
    private $errors = [];
    private $data = [];

    public function __construct($data) {
        $this->data = $data;
    }

    // Kiểm tra required
    public function required($fields, $message = null) {
        // Nếu là array, kiểm tra từng field
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $this->required($field, $message);
            }
            return $this;
        }
        
        // Kiểm tra single field
        // Chấp nhận 0 là giá trị hợp lệ (cho min_age)
        if (!isset($this->data[$fields]) || (trim($this->data[$fields]) === '' && $this->data[$fields] !== '0')) {
            $this->errors[$fields] = $message ?? "Trường này là bắt buộc";
        }
        return $this;
    }

    // Kiểm tra email
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? "Email không hợp lệ";
        }
        return $this;
    }

    // Kiểm tra độ dài tối thiểu
    public function min($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? "Phải có ít nhất {$length} ký tự";
        }
        return $this;
    }

    // Kiểm tra độ dài tối đa
    public function max($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = $message ?? "Không được vượt quá {$length} ký tự";
        }
        return $this;
    }

    // Kiểm tra khớp
    public function match($field, $matchField, $message = null) {
        if (isset($this->data[$field]) && isset($this->data[$matchField]) && 
            $this->data[$field] !== $this->data[$matchField]) {
            $this->errors[$field] = $message ?? "Không khớp";
        }
        return $this;
    }

    // Kiểm tra unique trong database
    public function unique($field, $model, $column, $message = null) {
        if (isset($this->data[$field])) {
            $result = $model->{'findBy' . ucfirst($column)}($this->data[$field]);
            if ($result) {
                $this->errors[$field] = $message ?? "Đã tồn tại";
            }
        }
        return $this;
    }

    // Kiểm tra số điện thoại
    public function phone($field, $message = null) {
        if (isset($this->data[$field]) && !preg_match('/^[0-9]{10,11}$/', $this->data[$field])) {
            $this->errors[$field] = $message ?? "Số điện thoại không hợp lệ";
        }
        return $this;
    }

    // Kiểm tra ngày
    public function date($field, $message = null) {
        if (isset($this->data[$field])) {
            $d = DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            if (!$d || $d->format('Y-m-d') !== $this->data[$field]) {
                $this->errors[$field] = $message ?? "Ngày không hợp lệ";
            }
        }
        return $this;
    }

    // Kiểm tra có lỗi
    public function fails() {
        return !empty($this->errors);
    }

    // Lấy lỗi
    public function errors() {
        return $this->errors;
    }

    // Lấy lỗi đầu tiên
    public function firstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}
