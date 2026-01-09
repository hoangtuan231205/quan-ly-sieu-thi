<?php

class Controller {

    protected function view($view, $data = []) {
        // Extract mảng $data thành các biến riêng
        // VD: ['name' => 'John'] → biến $name = 'John'
        extract($data);
        
        // Đường dẫn đến file view
        $viewFile = '../app/views/' . $view . '.php';
        
        // Kiểm tra file có tồn tại không
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View không tồn tại: {$viewFile}");
        }
    }

    protected function model($model) {
        // Đường dẫn file model
        $modelFile = '../app/models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        } else {
            die("Model không tồn tại: {$modelFile}");
        }
    }

    protected function json($data, $statusCode = 200) {
        // Ngăn chặn các lỗi/cảnh báo PHP làm hỏng JSON
        ini_set('display_errors', 0);
        
        // Xóa TẤT CẢ các bộ đệm output để đảm bảo không có output trước đó rò rỉ
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
    

    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $ruleList = explode('|', $ruleString);
            $value = $data[$field] ?? '';
            
            foreach ($ruleList as $rule) {
                // Rule: required
                if ($rule === 'required' && empty($value)) {
                    $errors[$field][] = ucfirst($field) . ' không được để trống';
                }
                
                // Rule: email
                if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = 'Email không hợp lệ';
                }
                
                // Rule: min:6
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        $errors[$field][] = ucfirst($field) . " phải có ít nhất {$min} ký tự";
                    }
                }
                
                // Rule: max:255
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        $errors[$field][] = ucfirst($field) . " không được quá {$max} ký tự";
                    }
                }
                
                // Rule: numeric
                if ($rule === 'numeric' && !empty($value) && !is_numeric($value)) {
                    $errors[$field][] = ucfirst($field) . ' phải là số';
                }
                
                // Rule: match:password (confirm password)
                if (strpos($rule, 'match:') === 0) {
                    $matchField = substr($rule, 6);
                    if ($value !== ($data[$matchField] ?? '')) {
                        $errors[$field][] = ucfirst($field) . ' không khớp';
                    }
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Sanitize input (loại bỏ HTML tags, XSS)
     * 
     * @param mixed $data
     * @return mixed
     * 
     * VÍ DỤ:
     * $cleanData = $this->sanitize($_POST);
     */
    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        
        // Loại bỏ khoảng trắng thừa
        $data = trim($data);
        
        // Loại bỏ backslashes
        $data = stripslashes($data);
        
        // Chuyển special characters thành HTML entities (tránh XSS)
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        return $data;
    }
    
    /**
     * Kiểm tra request method
     * 
     * @param string $method GET, POST, PUT, DELETE
     * @return bool
     */
    protected function isMethod($method) {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    }
    
    /**
     * Kiểm tra AJAX request
     * 
     * @return bool
     */
    protected function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Upload file
     * 
     * @param array $file $_FILES['field_name']
     * @param string $targetDir Thư mục đích
     * @return array ['success' => bool, 'filename' => string, 'message' => string]
     */
    protected function uploadFile($file, $targetDir = UPLOAD_PRODUCT_PATH) {
        // Sử dụng function uploadImage() đã có trong config.php
        return uploadImage($file, $targetDir);
    }
    
   protected function paginate($total, $perPage = ITEMS_PER_PAGE, $currentPage = 1) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    // ✅ THÊM PREV_URL VÀ NEXT_URL
    $currentUrl = $_SERVER['REQUEST_URI'];
    $baseUrl = strtok($currentUrl, '?');
    parse_str(parse_url($currentUrl, PHP_URL_QUERY) ?? '', $params);
    
    // Tạo URL trang trước
    if ($currentPage > 1) {
        $params['page'] = $currentPage - 1;
        $prevUrl = $baseUrl . '?' . http_build_query($params);
    } else {
        $prevUrl = '#';
    }
    
    // Tạo URL trang sau
    if ($currentPage < $totalPages) {
        $params['page'] = $currentPage + 1;
        $nextUrl = $baseUrl . '?' . http_build_query($params);
    } else {
        $nextUrl = '#';
    }
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'prev_url' => $prevUrl,  // ✅ THÊM
        'next_url' => $nextUrl   // ✅ THÊM
    ];
}
    
    /**
     * Set flash message và redirect
     * 
     * @param string $type success, error, warning, info
     * @param string $message
     * @param string $redirectUrl
     */
    protected function flashAndRedirect($type, $message, $redirectUrl) {
        Session::flash($type, $message);
        redirect($redirectUrl);
    }
    
    /**
     * Load layout với content
     * 
     * @param string $view View chính
     * @param array $data Dữ liệu
     * @param string $layout Layout file (mặc định: layouts/main)
     */
    protected function layout($view, $data = [], $layout = 'layouts/main') {
        // Bắt đầu output buffering
        ob_start();
        
        // Nạp nội dung view
        extract($data);
        require_once '../app/views/' . $view . '.php';
        
        // Lấy content
        $content = ob_get_clean();
        
        // Nạp layout với content
        require_once '../app/views/' . $layout . '.php';
    }
}