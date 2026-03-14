<?php
/**
 * =============================================================================
 * ADMIN USER TRAIT - QUẢN LÝ NGƯỜI DÙNG
 * =============================================================================
 * 
 * Trait này chứa các method xử lý quản lý người dùng cho AdminController
 * 
 * Routes:
 * - GET /admin/users → Danh sách người dùng (tìm kiếm, lọc, phân trang)
 */

trait AdminUserTrait {
    
    /**
     * Trang quản lý người dùng
     * URL: /admin/users
     * Method: GET
     * 
     * Query params:
     * - q: Từ khóa tìm kiếm (tên, email, SĐT, username)
     * - role: Lọc theo vai trò (KH, QUAN_LY_KHO, ADMIN)
     * - status: Lọc theo trạng thái (active, locked)
     * - page: Trang hiện tại (mặc định = 1)
     */
    public function users() {
        // =====================================================================
        // 1. NHẬN THAM SỐ TỪ REQUEST
        // =====================================================================
        
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : null;
        $role = isset($_GET['role']) ? trim($_GET['role']) : null;
        $status = isset($_GET['status']) ? trim($_GET['status']) : null;
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        
        // Validate role
        $validRoles = ['KH', 'QUAN_LY_KHO', 'ADMIN'];
        if ($role && !in_array($role, $validRoles)) {
            $role = null;
        }
        
        // Validate status
        $validStatuses = ['active', 'locked'];
        if ($status && !in_array($status, $validStatuses)) {
            $status = null;
        }
        
        // =====================================================================
        // 2. PHÂN TRANG
        // =====================================================================
        
        $limit = 10; // Số bản ghi trên 1 trang
        $offset = ($page - 1) * $limit;
        
        // =====================================================================
        // 3. LẤY DỮ LIỆU TỪ MODEL
        // =====================================================================
        
        // Lấy danh sách users
        $users = $this->userModel->getUsers($keyword, $role, $status, $limit, $offset);
        
        // Đếm tổng số users (để tính pagination)
        $totalUsers = $this->userModel->countUsers($keyword, $role, $status);
        
        // Lấy thống kê (cho stats cards)
        $stats = $this->userModel->getUserStats();
        
        // =====================================================================
        // 4. TÍNH TOÁN PAGINATION
        // =====================================================================
        
        $totalPages = ceil($totalUsers / $limit);
        
        $pagination = [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $totalUsers,
            'limit' => $limit,
            'offset' => $offset,
            'from' => $totalUsers > 0 ? $offset + 1 : 0,
            'to' => min($offset + $limit, $totalUsers),
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
            'prev_page' => $page - 1,
            'next_page' => $page + 1
        ];
        
        // =====================================================================
        // 5. CÁC FILTERS HIỆN TẠI (để giữ lại trong form)
        // =====================================================================
        
        $filters = [
            'keyword' => $keyword,
            'role' => $role,
            'status' => $status
        ];
        
        // =====================================================================
        // 6. CHUẨN BỊ DỮ LIỆU CHO VIEW
        // =====================================================================
        
        $data = [
            'page_title' => 'Quản lý Người dùng',
            'users' => $users,
            'stats' => $stats,
            'filters' => $filters,
            'pagination' => $pagination,
            'csrf_token' => Session::getCsrfToken()
        ];
        
        // =====================================================================
        // 7. RENDER VIEW
        // =====================================================================
        
        $this->view('admin/users', $data);
    }
    
}
