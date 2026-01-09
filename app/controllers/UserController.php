    
<?php
/**
 * USER CONTROLLER - Trang thông tin tài khoản cá nhân
 */
class UserController extends Controller {
    public function profile() {
        // Kiểm tra session
        if (!isset($_SESSION['user']['ID'])) {
            redirect(BASE_URL . '/auth/login');
            exit;
        }
        
        // Lấy ID từ session
        $userId = $_SESSION['user']['ID'];
        $userModel = $this->model('User');
        
        // Tìm user theo ID
        $userDb = $userModel->findById($userId);
        
        if (!$userDb) {
            redirect(BASE_URL . '/auth/login');
            exit;
        }
        
        $user = [
            'fullname' => $userDb['Ho_ten'] ?? '',
            'email' => $userDb['Email'] ?? '',
            'phone' => $userDb['Sdt'] ?? '',
            'address' => $userDb['Dia_chi'] ?? '',
            'avatar' => $userDb['Avatar'] ?? '',
            'created_at' => $userDb['Ngay_tao'] ?? date('Y-m-d'),
        ];
        $this->view('customer/profile', ['user' => $user]);
    }
    
    public function updateProfile() {
        if (!isset($_SESSION['user']['ID'])) {
            redirect(BASE_URL . '/auth/login');
            exit;
        }
        $userId = $_SESSION['user']['ID'];
        $userModel = $this->model('User');
        $data = [
            'Ho_ten' => $_POST['fullname'] ?? '',
            'Email' => $_POST['email'] ?? '',
            'Sdt' => $_POST['phone'] ?? '',
            'Dia_chi' => $_POST['address'] ?? '',
        ];
        $userModel->updateProfile($userId, $data);
        // Cập nhật lại session user với thông tin mới
        $_SESSION['user']['Ho_ten'] = $data['Ho_ten'];
        $_SESSION['user']['Email'] = $data['Email'];
        $_SESSION['user']['Sdt'] = $data['Sdt'];
        $_SESSION['user']['Dia_chi'] = $data['Dia_chi'];
        redirect(BASE_URL . '/user/profile');
    }
    
    public function updateAvatar() {
        if (!isset($_SESSION['user']['ID'])) {
            redirect(BASE_URL . '/auth/login');
            exit;
        }
        
        $userId = $_SESSION['user']['ID'];
        
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
            $file = $_FILES['avatar'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                $_SESSION['error'] = 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF)';
                redirect(BASE_URL . '/user/profile');
                exit;
            }
            
            if ($file['size'] > 2 * 1024 * 1024) { // 2MB
                $_SESSION['error'] = 'Kích thước ảnh tối đa 2MB';
                redirect(BASE_URL . '/user/profile');
                exit;
            }
            
            // Create avatars directory if not exists
            $uploadDir = PUBLIC_PATH . '/img/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Update database
                $userModel = $this->model('User');
                $userModel->update($userId, ['Avatar' => $filename]);
                
                // Update session
                $_SESSION['user']['Avatar'] = $filename;
                $_SESSION['success'] = 'Cập nhật ảnh đại diện thành công!';
            } else {
                $_SESSION['error'] = 'Lỗi khi upload ảnh';
            }
        }
        
        redirect(BASE_URL . '/user/profile');
    }
}
