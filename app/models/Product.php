<?php
/**
 * =============================================================================
 * PRODUCT MODEL - QUẢN LÝ SẢN PHẨM
 * =============================================================================
 * 
 * Bảng: san_pham
 * 
 * Chức năng:
 * - CRUD sản phẩm
 * - Filter, search, sort
 * - Quản lý tồn kho
 * - Thống kê bán chạy
 */

class Product extends Model {
    
    protected $table = 'san_pham';
    protected $primaryKey = 'ID_sp';
    
    /**
     * User ID hiện tại (cho trigger ghi log)
     */
    private $currentUserId = null;
    
    /**
     * Set current user ID (cho trigger)
     */
    public function setCurrentUserId($userId) {
        $this->currentUserId = $userId;
        
        // Set biến session MySQL để trigger sử dụng
        if ($userId) {
            $this->db->query("SET @current_user_id = ?", [$userId]);
        }
    }
    private $allowedOrderBy = [
        'newest' => 'sp.Ngay_tao DESC',
        'oldest' => 'sp.Ngay_tao ASC',
        'price_asc' => 'sp.Gia_tien ASC',
        'price_desc' => 'sp.Gia_tien DESC',
        'name_asc' => 'sp.Ten ASC',
        'name_desc' => 'sp.Ten DESC',
        'bestseller' => '(SELECT IFNULL(SUM(So_luong), 0) FROM chi_tiet_don_hang WHERE ID_sp = sp.ID_sp) DESC',
        'stock_asc' => 'sp.So_luong_ton ASC',
        'stock_desc' => 'sp.So_luong_ton DESC'
    ];
    
    /**
     * Kiểm tra keyword có thực sự match trong text hay không
     * Phân biệt dấu tiếng Việt chính xác (cá != cải != cà)
     * 
     * @param string $text
     * @param string $keyword
     * @return bool
     */
    private function vietnameseMatch($text, $keyword) {
        $text = mb_strtolower($text, 'UTF-8');
        $keyword = mb_strtolower($keyword, 'UTF-8');
        return mb_strpos($text, $keyword, 0, 'UTF-8') !== false;
    }
    
    /**
     * ==========================================================================
     * DANH SÁCH SẢN PHẨM (CUSTOMER)
     * ==========================================================================
     */
    
    /**
     * Lấy danh sách sản phẩm (có filter)
     * 
     * @param array $filters
     * @param string $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getProducts($filters = [], $orderBy = 'newest', $limit = 12, $offset = 0) {
        // ===== BƯỚC 1: VALIDATE ORDER BY ✅ FIX =====
        // Nếu orderBy không hợp lệ → dùng default
        if (!isset($this->allowedOrderBy[$orderBy])) {
            $orderBy = 'newest';
        }
        
        // Lấy SQL ORDER BY an toàn từ whitelist
        $orderBySql = $this->allowedOrderBy[$orderBy];
        
        // ===== BƯỚC 2: XÂY DỰNG QUERY =====
        $sql = "SELECT 
                    sp.*,
                    dm.Ten_danh_muc
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                WHERE sp.Trang_thai = 'active'";
        
        $params = [];
        
        // ===== BƯỚC 3: THÊM FILTERS ✅ IMPROVED =====
        
        // Filter theo danh mục (bao gồm cả danh mục con)
        if (!empty($filters['category_id']) && is_numeric($filters['category_id'])) {
            $catId = (int)$filters['category_id'];
            $sql .= " AND (sp.ID_danh_muc = ? OR sp.ID_danh_muc IN (SELECT ID_danh_muc FROM danh_muc WHERE Danh_muc_cha = ?))";
            $params[] = $catId;
            $params[] = $catId;
        }
        
        // Filter theo khoảng giá
        if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
            $sql .= " AND sp.Gia_tien >= ?";
            $params[] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
            $sql .= " AND sp.Gia_tien <= ?";
            $params[] = (float)$filters['max_price'];
        }
        
        // Search theo tên SẢN PHẨM HOẶC tên DANH MỤC
        if (!empty($filters['keyword'])) {
            // Sanitize keyword
            $keyword = trim($filters['keyword']);
            if (strlen($keyword) > 0) {
                // Search both product name AND category name
                // This allows "mì" to find all products in "Mì" category
                $sql .= " AND (sp.Ten LIKE ? OR dm.Ten_danh_muc LIKE ?)";
                $params[] = '%' . $keyword . '%';
                $params[] = '%' . $keyword . '%';
            }
        }
        
        // ===== BƯỚC 4: THÊM ORDER BY (AN TOÀN) ✅ FIX =====
        $sql .= " ORDER BY {$orderBySql}";
        
        // ===== BƯỚC 5: THÊM LIMIT & OFFSET ✅ FIX =====
        // Validate limit & offset
        // Lấy nhiều hơn để bù cho việc filter bỏ false positives
        $fetchLimit = !empty($filters['keyword']) ? max(1, min((int)$limit * 3, 300)) : max(1, min((int)$limit, 100));
        $offset = max(0, (int)$offset);
        
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $fetchLimit;
        $params[] = $offset;
        
        // ===== BƯỚC 6: EXECUTE QUERY =====
        $results = $this->db->query($sql, $params)->fetchAll();
        
        // ===== BƯỚC 7: FILTER VIETNAMESE DIACRITICS ✅ NEW =====
        // Lọc lại kết quả bằng PHP để phân biệt dấu tiếng Việt chính xác
        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);
            $filteredResults = [];
            
            foreach ($results as $product) {
                $matchInName = $this->vietnameseMatch($product['Ten'], $keyword);
                $matchInCategory = $this->vietnameseMatch($product['Ten_danh_muc'] ?? '', $keyword);
                
                if ($matchInName || $matchInCategory) {
                    $filteredResults[] = $product;
                }
            }
            
            // Giới hạn lại theo limit gốc
            return array_slice($filteredResults, 0, (int)$limit);
        }
        
        return $results;
    }
    
    /**
     * Đếm số lượng sản phẩm
     * 
     * @param array $filters
     * @return int
     */
    public function countProducts($filters = []) {
        // Nếu có keyword, cần đếm chính xác với Vietnamese filter
        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);
            if (strlen($keyword) > 0) {
                // Query tất cả sản phẩm matching và đếm bằng PHP
                $sql = "SELECT sp.Ten, dm.Ten_danh_muc
                        FROM {$this->table} sp
                        LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                        WHERE sp.Trang_thai = 'active'
                        AND (sp.Ten LIKE ? OR dm.Ten_danh_muc LIKE ?)";
                
                $params = ['%' . $keyword . '%', '%' . $keyword . '%'];
                
                // Add other filters
                if (!empty($filters['category_id']) && is_numeric($filters['category_id'])) {
                    $catId = (int)$filters['category_id'];
                    $sql .= " AND (sp.ID_danh_muc = ? OR sp.ID_danh_muc IN (SELECT ID_danh_muc FROM danh_muc WHERE Danh_muc_cha = ?))";
                    $params[] = $catId;
                    $params[] = $catId;
                }
                
                if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
                    $sql .= " AND sp.Gia_tien >= ?";
                    $params[] = (float)$filters['min_price'];
                }
                
                if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
                    $sql .= " AND sp.Gia_tien <= ?";
                    $params[] = (float)$filters['max_price'];
                }
                
                $results = $this->db->query($sql, $params)->fetchAll();
                
                // Filter với Vietnamese match
                $count = 0;
                foreach ($results as $product) {
                    $matchInName = $this->vietnameseMatch($product['Ten'], $keyword);
                    $matchInCategory = $this->vietnameseMatch($product['Ten_danh_muc'] ?? '', $keyword);
                    
                    if ($matchInName || $matchInCategory) {
                        $count++;
                    }
                }
                
                return $count;
            }
        }
        
        // Không có keyword - đếm bình thường
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} sp
                WHERE sp.Trang_thai = 'active'";
        
        $params = [];
        
        if (!empty($filters['category_id']) && is_numeric($filters['category_id'])) {
            $catId = (int)$filters['category_id'];
            $sql .= " AND (sp.ID_danh_muc = ? OR sp.ID_danh_muc IN (SELECT ID_danh_muc FROM danh_muc WHERE Danh_muc_cha = ?))";
            $params[] = $catId;
            $params[] = $catId;
        }
        
        if (!empty($filters['min_price']) && is_numeric($filters['min_price'])) {
            $sql .= " AND sp.Gia_tien >= ?";
            $params[] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price']) && is_numeric($filters['max_price'])) {
            $sql .= " AND sp.Gia_tien <= ?";
            $params[] = (float)$filters['max_price'];
        }
        
        $result = $this->db->query($sql, $params)->fetch();
        return (int) $result['total'];
    }
    
    /**
     * Lấy khoảng giá (min-max)
     * 
     * @return array
     */
    public function getPriceRange() {
        $sql = "SELECT 
                    MIN(Gia_tien) as min_price,
                    MAX(Gia_tien) as max_price
                FROM {$this->table}
                WHERE Trang_thai = 'active'";
        
        return $this->db->query($sql)->fetch();
    }
    
    /**
     * Tìm kiếm sản phẩm (LIKE search - more compatible)
     * 
     * @param string $keyword
     * @param int $limit
     * @return array
     */
        public function search($keyword, $limit = 10) {
        // ===== VALIDATE INPUT ✅ FIX =====
        $keyword = trim($keyword);
        
        if (strlen($keyword) < 2) {
            return []; // Keyword quá ngắn
        }
        
        if (strlen($keyword) > 200) {
            $keyword = substr($keyword, 0, 200); // Giới hạn độ dài
        }
        
        // Validate limit
        $limit = max(1, min((int)$limit, 50)); // Max 50 results
        
        // ===== EXECUTE QUERY =====
        $searchTerm = '%' . $keyword . '%';
        
        $sql = "SELECT 
                    sp.*,
                    dm.Ten_danh_muc
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                WHERE sp.Trang_thai = 'active'
                    AND (sp.Ten LIKE ? OR sp.Mo_ta_sp LIKE ? OR sp.Ma_hien_thi LIKE ?)
                ORDER BY 
                    CASE 
                        WHEN sp.Ten LIKE ? THEN 1
                        WHEN sp.Ma_hien_thi LIKE ? THEN 2
                        ELSE 3
                    END,
                    sp.Ngay_tao DESC
                LIMIT ?";
        
        return $this->db->query($sql, [
            $searchTerm, 
            $searchTerm, 
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $limit
        ])->fetchAll();
    }
    
    /**
     * Lấy sản phẩm liên quan (cùng danh mục)
     * 
     * @param int $productId
     * @param int $categoryId
     * @param int $limit
     * @return array
     */
    public function getRelatedProducts($productId, $categoryId, $limit = 8) {
        $sql = "SELECT 
                    sp.*,
                    dm.Ten_danh_muc
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                WHERE sp.ID_danh_muc = ?
                    AND sp.ID_sp != ?
                    AND sp.Trang_thai = 'active'
                ORDER BY RAND()
                LIMIT {$limit}";
        
        return $this->db->query($sql, [$categoryId, $productId])->fetchAll();
    }
    
    /**
     * Lấy sản phẩm mới nhất
     * 
     * @param int $limit
     * @return array
     */
    public function getLatestProducts($limit = 12) {
        $sql = "SELECT 
                    sp.*,
                    dm.Ten_danh_muc
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                WHERE sp.Trang_thai = 'active'
                ORDER BY sp.Ngay_tao DESC
                LIMIT {$limit}";
        
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Lấy sản phẩm bán chạy (từ VIEW: v_san_pham_ban_chay)
     * Fallback to latest products if no orders exist yet
     * 
     * @param int $limit
     * @return array
     */
    public function getBestSellers($limit = 8) {
        $sql = "SELECT 
                    sp.*,
                    SUM(ct.So_luong) as Da_ban
                FROM {$this->table} sp
                JOIN chi_tiet_don_hang ct ON sp.ID_sp = ct.ID_sp
                JOIN don_hang dh ON ct.ID_dh = dh.ID_dh
                WHERE dh.Trang_thai = 'da_giao' AND sp.Trang_thai = 'active'
                GROUP BY sp.ID_sp
                ORDER BY Da_ban DESC
                LIMIT {$limit}";
                
        $bestsellers = $this->db->query($sql)->fetchAll();
        
        // If no bestsellers (no orders yet), fallback to latest products
        if (empty($bestsellers)) {
            return $this->getLatestProducts($limit);
        }
        
        return $bestsellers;
    }
    
    /**
     * ==========================================================================
     * ADMIN - QUẢN LÝ SẢN PHẨM
     * ==========================================================================
     */
    
    /**
     * Lấy danh sách sản phẩm cho admin (bao gồm inactive)
     * 
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getProductsForAdmin($filters = [], $limit = 20, $offset = 0) {
        $sql = "SELECT 
                    sp.*,
                    dm.Ten_danh_muc
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                WHERE 1=1";
        
        $params = [];
        
        // ===== FILTERS ✅ IMPROVED =====
        if (!empty($filters['category_id']) && is_numeric($filters['category_id'])) {
            $sql .= " AND sp.ID_danh_muc = ?";
            $params[] = (int)$filters['category_id'];
        }
        
        if (!empty($filters['status'])) {
            // Whitelist status
            $allowedStatus = ['active', 'inactive'];
            if (in_array($filters['status'], $allowedStatus)) {
                $sql .= " AND sp.Trang_thai = ?";
                $params[] = $filters['status'];
            }
        }
        
        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);
            if (strlen($keyword) > 0 && strlen($keyword) <= 200) {
                $sql .= " AND (sp.Ten LIKE ? OR sp.Ma_hien_thi LIKE ?)";
                $searchTerm = '%' . $keyword . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
        }
        
        // ===== ORDER BY & LIMIT ✅ FIX =====
        $limit = max(1, min((int)$limit, 100));
        $offset = max(0, (int)$offset);
        
        $sql .= " ORDER BY sp.Ngay_tao ASC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    /**
     * Kiểm tra sản phẩm có trong đơn hàng chưa
     * 
     * @param int $productId
     * @return bool
     */
    public function hasOrders($productId) {
        $sql = "SELECT COUNT(*) as total FROM chi_tiet_don_hang WHERE ID_sp = ?";
        $result = $this->db->query($sql, [$productId])->fetch();
        
        return $result['total'] > 0;
    }
    
    /**
     * Lấy tất cả sản phẩm (cho dropdown select)
     * 
     * @return array
     */
    public function getAllProducts() {
        $sql = "SELECT ID_sp, Ma_hien_thi, Ten, Don_vi_tinh, So_luong_ton
                FROM {$this->table}
                WHERE Trang_thai = 'active'
                ORDER BY Ten ASC";
        
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Xuất Excel sản phẩm
     * 
     * @return array
     */
    public function getAllProductsForExport() {
        $sql = "SELECT 
                    sp.Ma_hien_thi,
                    sp.Ten,
                    dm.Ten_danh_muc,
                    sp.Gia_tien,
                    sp.So_luong_ton,
                    sp.Don_vi_tinh,
                    sp.Xuat_xu,
                    sp.Trang_thai,
                    sp.Ngay_tao
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                ORDER BY sp.Ngay_tao DESC";
        
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * ==========================================================================
     * WAREHOUSE - QUẢN LÝ TỒN KHO
     * ==========================================================================
     */
    
    /**
     * Lấy sản phẩm sắp hết hàng (từ VIEW: v_san_pham_sap_het)
     * 
     * @param int $limit
     * @return array
     */
    public function getLowStockProducts($limit = 20) {
        $sql = "SELECT * FROM v_san_pham_sap_het LIMIT {$limit}";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Đếm số sản phẩm sắp hết hàng (tồn <= 10)
     * 
     * @return int
     */
    public function getLowStockCount() {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE So_luong_ton <= 10 AND Trang_thai = 'active'";
        
        $result = $this->db->query($sql)->fetch();
        return (int) $result['total'];
    }
    
    /**
     * Đếm số sản phẩm hết hàng
     * 
     * @return int
     */
    public function getOutOfStockCount() {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} 
                WHERE So_luong_ton = 0 AND Trang_thai = 'active'";
        
        $result = $this->db->query($sql)->fetch();
        return (int) $result['total'];
    }
    
    /**
     * Tổng giá trị tồn kho
     * 
     * @return float
     */
    public function getTotalInventoryValue() {
        $sql = "SELECT SUM(So_luong_ton * Gia_tien) as total 
                FROM {$this->table} 
                WHERE Trang_thai = 'active'";
        
        $result = $this->db->query($sql)->fetch();
        return (float) ($result['total'] ?? 0);
    }
    
    /**
     * Lấy tồn kho (có filter)
     * 
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getInventory($filters = [], $limit = 20, $offset = 0) {
        $sql = "SELECT 
                    sp.*,
                    dm.Ten_danh_muc
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND sp.ID_danh_muc = ?";
            $params[] = $filters['category_id'];
        }
        
        // Filter theo trạng thái tồn kho
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'low') {
                $sql .= " AND sp.So_luong_ton > 0 AND sp.So_luong_ton <= 10";
            } elseif ($filters['status'] === 'out') {
                $sql .= " AND sp.So_luong_ton = 0";
            }
        }
        
        if (!empty($filters['keyword'])) {
            $sql .= " AND (sp.Ten LIKE ? OR sp.Ma_hien_thi LIKE ?)";
            $keyword = '%' . $filters['keyword'] . '%';
            $params[] = $keyword;
            $params[] = $keyword;
        }
        
        $sql .= " ORDER BY sp.So_luong_ton ASC, sp.Ten ASC LIMIT {$limit} OFFSET {$offset}";
        
        return $this->db->query($sql, $params)->fetchAll();
    }
    
    /**
     * Đếm tồn kho
     * 
     * @param array $filters
     * @return int
     */
    public function countInventory($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND ID_danh_muc = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'low') {
                $sql .= " AND So_luong_ton > 0 AND So_luong_ton <= 10";
            } elseif ($filters['status'] === 'out') {
                $sql .= " AND So_luong_ton = 0";
            }
        }
        
        if (!empty($filters['keyword'])) {
            $sql .= " AND (Ten LIKE ? OR Ma_hien_thi LIKE ?)";
            $keyword = '%' . $filters['keyword'] . '%';
            $params[] = $keyword;
            $params[] = $keyword;
        }
        
        $result = $this->db->query($sql, $params)->fetch();
        return (int) $result['total'];
    }
    
    /**
     * Cập nhật tồn kho
     * 
     * @param int $productId
     * @param int $newStock
     * @return bool
     */
    public function updateStock($productId, $newStock) {
        return $this->update($productId, ['So_luong_ton' => $newStock]);
    }
    
    /**
     * Xuất Excel tồn kho
     * 
     * @return array
     */
    public function getAllInventoryForExport() {
        $sql = "SELECT 
                    sp.Ma_hien_thi,
                    sp.Ten,
                    dm.Ten_danh_muc,
                    sp.So_luong_ton,
                    sp.Don_vi_tinh,
                    sp.Gia_tien,
                    sp.Trang_thai
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                ORDER BY sp.So_luong_ton ASC";
        
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Tìm kiếm sản phẩm (cho warehouse)
     * 
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function searchForWarehouse($keyword, $limit = 20) {
        $sql = "SELECT 
                    sp.ID_sp,
                    sp.Ma_hien_thi,
                    sp.Ten,
                    sp.Don_vi_tinh,
                    sp.So_luong_ton,
                    sp.Gia_tien AS gia,
                    dm.Ten_danh_muc
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                WHERE (sp.Ten LIKE ? OR sp.Ma_hien_thi LIKE ?)
                    AND sp.Trang_thai = 'active'
                ORDER BY sp.Ten ASC
                LIMIT {$limit}";
        
        $keyword = '%' . $keyword . '%';
        return $this->db->query($sql, [$keyword, $keyword])->fetchAll();
    }
    
    /**
     * ==========================================================================
     * STATISTICS
     * ==========================================================================
     */
    
    /**
     * Tổng số sản phẩm
     * 
     * @return int
     */
    public function getTotalProducts() {
        return $this->count(['Trang_thai' => 'active']);
    }
    
    /**
     * Báo cáo sản phẩm
     * 
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function getProductReport($dateFrom, $dateTo) {
        $sql = "SELECT 
                    sp.ID_sp,
                    sp.Ma_hien_thi,
                    sp.Ten,
                    dm.Ten_danh_muc,
                    sp.So_luong_ton,
                    IFNULL(SUM(ct.So_luong), 0) as So_luong_ban,
                    IFNULL(SUM(ct.Thanh_tien), 0) as Doanh_thu
                FROM {$this->table} sp
                LEFT JOIN danh_muc dm ON sp.ID_danh_muc = dm.ID_danh_muc
                LEFT JOIN chi_tiet_don_hang ct ON sp.ID_sp = ct.ID_sp
                LEFT JOIN don_hang dh ON ct.ID_dh = dh.ID_dh 
                    AND dh.Trang_thai = 'da_giao'
                    AND DATE(dh.Ngay_dat) BETWEEN ? AND ?
                WHERE sp.Trang_thai = 'active'
                GROUP BY sp.ID_sp
                ORDER BY Doanh_thu DESC";
        
        return $this->db->query($sql, [$dateFrom, $dateTo])->fetchAll();
    }
    
    /**
     * Tìm kiếm sản phẩm cho POS (theo tên hoặc mã)
     * 
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function searchForPOS($keyword, $limit = 10) {
        $keyword = trim($keyword);
        
        if (strlen($keyword) < 1) {
            return [];
        }
        
        $searchTerm = '%' . $keyword . '%';
        $limit = max(1, min((int)$limit, 30));
        
        $sql = "SELECT 
                    ID_sp,
                    Ma_hien_thi,
                    Ten,
                    Gia_tien,
                    So_luong_ton,
                    Don_vi_tinh,
                    Hinh_anh
                FROM {$this->table}
                WHERE Trang_thai = 'active'
                    AND So_luong_ton > 0
                    AND (Ten LIKE ? OR Ma_hien_thi LIKE ?)
                ORDER BY 
                    CASE 
                        WHEN Ma_hien_thi LIKE ? THEN 1
                        ELSE 2
                    END,
                    Ten ASC
                LIMIT ?";
        
        return $this->db->query($sql, [
            $searchTerm, 
            $searchTerm,
            $searchTerm,
            $limit
        ])->fetchAll();
    }
    
    /**
     * Get active products for POS grid display
     * 
     * @param int $limit
     * @return array
     */
    public function getActiveProductsForPOS($limit = 20) {
        $sql = "SELECT 
                    ID_sp,
                    Ma_hien_thi,
                    Ten,
                    Gia_tien,
                    So_luong_ton,
                    Don_vi_tinh,
                    Hinh_anh,
                    ID_danh_muc
                FROM {$this->table}
                WHERE Trang_thai = 'active'
                    AND So_luong_ton > 0
                ORDER BY Ngay_tao DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$limit])->fetchAll();
    }
    
    /**
     * Get products by category for POS
     * 
     * @param int $categoryId
     * @param int $limit
     * @return array
     */
    public function getProductsByCategory($categoryId, $limit = 30) {
        $sql = "SELECT 
                    ID_sp,
                    Ma_hien_thi,
                    Ten,
                    Gia_tien,
                    So_luong_ton,
                    Don_vi_tinh,
                    Hinh_anh,
                    ID_danh_muc
                FROM {$this->table}
                WHERE Trang_thai = 'active'
                    AND So_luong_ton > 0
                    AND ID_danh_muc = ?
                ORDER BY Ten ASC
                LIMIT ?";
        
        return $this->db->query($sql, [$categoryId, $limit])->fetchAll();
    }

    /**
     * Tìm kiếm sản phẩm cho phiếu hủy
     * 
     * @param string $keyword
     * @param int $limit
     * @return array
     */
    public function searchForDisposal($keyword, $limit = 20) {
        $keyword = trim($keyword);
        $searchTerm = '%' . $keyword . '%';
        
        $sql = "SELECT 
                    ID_sp,
                    Ma_hien_thi,
                    Ten,
                    Gia_nhap,
                    So_luong_ton,
                    Don_vi_tinh
                FROM {$this->table}
                WHERE Trang_thai = 'active'
                    AND So_luong_ton > 0
                    AND (Ten LIKE ? OR Ma_hien_thi LIKE ?)
                ORDER BY Ten ASC
                LIMIT ?";
                
        return $this->db->query($sql, [$searchTerm, $searchTerm, $limit])->fetchAll();
    }
    
    /**
     * Lấy danh sách lô hàng còn tồn kho của sản phẩm
     * 
     * @param int $productId
     * @return array
     */
    public function getBatches($productId) {
        // Query joins with phieu_nhap to get Ma_phieu
        $sql = "SELECT 
                    ct.ID_chi_tiet_nhap,
                    pn.Ma_hien_thi as Ma_phieu_nhap,
                    ct.So_luong_con,
                    ct.Don_gia_nhap,
                    ct.Ngay_het_han
                FROM chi_tiet_phieu_nhap ct
                JOIN phieu_nhap_kho pn ON ct.ID_phieu_nhap = pn.ID_phieu_nhap
                WHERE ct.ID_sp = ? 
                AND (ct.So_luong_con > 0 OR ct.So_luong_con IS NULL)
                ORDER BY ct.Ngay_het_han ASC, ct.ID_chi_tiet_nhap ASC";
                
        // Note: Logic 'OR ct.So_luong_con IS NULL' might be needed if old data didn't track So_luong_con correctly 
        // but assuming new system uses So_luong_con. 
        // For safety, assuming So_luong_con IS NULL means it's an old record or full stock, 
        // but typically we initialized So_luong_con = So_luong via migration.
        // Let's stick to So_luong_con > 0.
        
        $sql = "SELECT 
                    ct.ID_chi_tiet_nhap,
                    pn.Ma_hien_thi as Ma_phieu_nhap,
                    ct.So_luong_con,
                    ct.Don_gia_nhap,
                    ct.Ngay_het_han
                FROM chi_tiet_phieu_nhap ct
                JOIN phieu_nhap_kho pn ON ct.ID_phieu_nhap = pn.ID_phieu_nhap
                WHERE ct.ID_sp = ? 
                AND ct.So_luong_con > 0
                ORDER BY ct.Ngay_het_han ASC";

        return $this->db->query($sql, [$productId])->fetchAll();
    }
}