<?php
/**
 * =============================================================================
 * DISPOSAL MODEL - QUẢN LÝ PHIẾU HỦY
 * =============================================================================
 * 
 * Bảng: phieu_huy, chi_tiet_phieu_huy
 * 
 * Chức năng:
 * - CRUD phiếu hủy
 * - Approval workflow (Admin duyệt)
 * - Thống kê hàng hủy
 */

class Disposal extends Model {
    
    protected $table = 'phieu_huy';
    protected $primaryKey = 'ID_phieu_huy';
    
    // ==========================================================================
    // CRUD PHIẾU HỦY
    // ==========================================================================
    
    /**
     * Tạo phiếu hủy mới
     * 
     * @param int $userId ID người tạo
     * @param string $date Ngày hủy
     * @param string $type Loại phiếu (huy, hong, het_han, dieu_chinh)
     * @param string $reason Lý do
     * @param array $items Chi tiết sản phẩm hủy
     * @return int|false ID phiếu hủy
     */
    public function createDisposal($userId, $date, $type, $reason, $items) {
        try {
            $this->beginTransaction();
            
            // Tạo phiếu hủy (trigger sẽ tự generate mã)
            $disposalId = $this->create([
                'Nguoi_tao' => $userId,
                'Ngay_huy' => $date,
                'Loai_phieu' => $type,
                'Ly_do' => $reason,
                'Trang_thai' => 'cho_duyet'
            ]);
            
            if (!$disposalId) {
                throw new Exception('Không thể tạo phiếu hủy');
            }
            
            // Thêm chi tiết
            foreach ($items as $item) {
                $this->addDetail($disposalId, $item);
            }
            
            $this->commit();
            return $disposalId;
            
        } catch (Exception $e) {
            $this->rollBack();
            error_log("Disposal::createDisposal Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Thêm chi tiết phiếu hủy
     */
    private function addDetail($disposalId, $item) {
        $sql = "INSERT INTO chi_tiet_phieu_huy 
                (ID_phieu_huy, ID_sp, ID_lo_nhap, Ten_sp, So_luong, Gia_nhap, Thanh_tien, Ghi_chu)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql, [
            $disposalId,
            $item['ID_sp'],
            $item['ID_lo_nhap'] ?? null,
            $item['Ten_sp'],
            $item['So_luong'],
            $item['Gia_nhap'],
            $item['So_luong'] * $item['Gia_nhap'],
            $item['Ghi_chu'] ?? null
        ]);
    }
    
    /**
     * Lấy danh sách phiếu hủy với filter
     * 
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getDisposals($filters = [], $limit = 20, $offset = 0) {
        $sql = "SELECT ph.*, tk.Ho_ten AS Ten_nguoi_tao,
                       tk2.Ho_ten AS Ten_nguoi_duyet,
                       (SELECT COUNT(*) FROM chi_tiet_phieu_huy WHERE ID_phieu_huy = ph.ID_phieu_huy) AS So_san_pham
                FROM phieu_huy ph
                INNER JOIN tai_khoan tk ON ph.Nguoi_tao = tk.ID
                LEFT JOIN tai_khoan tk2 ON ph.Nguoi_duyet = tk2.ID
                WHERE 1=1";
        
        $params = [];
        
        // Filter by keyword (search Ma_hien_thi or Ly_do)
        if (!empty($filters['keyword'])) {
            $sql .= " AND (ph.Ma_hien_thi LIKE ? OR ph.Ly_do LIKE ?)";
            $keyword = '%' . $filters['keyword'] . '%';
            $params[] = $keyword;
            $params[] = $keyword;
        }
        
        // Filter by status
        if (!empty($filters['trang_thai'])) {
            $sql .= " AND ph.Trang_thai = ?";
            $params[] = $filters['trang_thai'];
        }
        
        // Filter by type
        if (!empty($filters['loai_phieu'])) {
            $sql .= " AND ph.Loai_phieu = ?";
            $params[] = $filters['loai_phieu'];
        }
        
        // Filter by date range
        if (!empty($filters['tu_ngay'])) {
            $sql .= " AND ph.Ngay_huy >= ?";
            $params[] = $filters['tu_ngay'];
        }
        
        if (!empty($filters['den_ngay'])) {
            $sql .= " AND ph.Ngay_huy <= ?";
            $params[] = $filters['den_ngay'];
        }
        
        $sql .= " ORDER BY ph.Ngay_tao DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Đếm phiếu hủy với filter
     */
    public function countDisposals($filters = []) {
        $sql = "SELECT COUNT(*) AS total FROM phieu_huy WHERE 1=1";
        $params = [];
        
        // Filter by keyword
        if (!empty($filters['keyword'])) {
            $sql .= " AND (Ma_hien_thi LIKE ? OR Ly_do LIKE ?)";
            $keyword = '%' . $filters['keyword'] . '%';
            $params[] = $keyword;
            $params[] = $keyword;
        }
        
        if (!empty($filters['trang_thai'])) {
            $sql .= " AND Trang_thai = ?";
            $params[] = $filters['trang_thai'];
        }
        
        if (!empty($filters['loai_phieu'])) {
            $sql .= " AND Loai_phieu = ?";
            $params[] = $filters['loai_phieu'];
        }
        
        if (!empty($filters['tu_ngay'])) {
            $sql .= " AND Ngay_huy >= ?";
            $params[] = $filters['tu_ngay'];
        }
        
        if (!empty($filters['den_ngay'])) {
            $sql .= " AND Ngay_huy <= ?";
            $params[] = $filters['den_ngay'];
        }
        
        $result = $this->queryOne($sql, $params);
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Lấy chi tiết phiếu hủy
     */
    public function getDisposalById($id) {
        $sql = "SELECT ph.*, tk.Ho_ten AS Ten_nguoi_tao,
                       tk2.Ho_ten AS Ten_nguoi_duyet
                FROM phieu_huy ph
                INNER JOIN tai_khoan tk ON ph.Nguoi_tao = tk.ID
                LEFT JOIN tai_khoan tk2 ON ph.Nguoi_duyet = tk2.ID
                WHERE ph.ID_phieu_huy = ?";
        
        return $this->queryOne($sql, [$id]);
    }
    
    /**
     * Lấy chi tiết sản phẩm trong phiếu hủy
     */
    public function getDisposalDetails($disposalId) {
        $sql = "SELECT ct.*, sp.Ma_hien_thi AS Ma_SP, sp.Hinh_anh,
                       pn.Ma_hien_thi AS Ma_phieu_nhap, pn.Ngay_nhap,
                       ct_pn.Ngay_het_han
                FROM chi_tiet_phieu_huy ct
                INNER JOIN san_pham sp ON ct.ID_sp = sp.ID_sp
                LEFT JOIN chi_tiet_phieu_nhap ct_pn ON ct.ID_lo_nhap = ct_pn.ID_chi_tiet_nhap
                LEFT JOIN phieu_nhap_kho pn ON ct_pn.ID_phieu_nhap = pn.ID_phieu_nhap
                WHERE ct.ID_phieu_huy = ?
                ORDER BY ct.ID_chi_tiet";
        
        return $this->query($sql, [$disposalId]);
    }
    
    // ==========================================================================
    // APPROVAL WORKFLOW
    // ==========================================================================
    
    /**
     * Duyệt phiếu hủy (chỉ Admin)
     * Trigger sẽ tự động trừ kho
     */
    public function approve($disposalId, $approverId) {
        $sql = "UPDATE phieu_huy 
                SET Trang_thai = 'da_duyet', 
                    Nguoi_duyet = ?, 
                    Ngay_duyet = NOW()
                WHERE ID_phieu_huy = ? AND Trang_thai = 'cho_duyet'";
        
        $this->db->query($sql, [$approverId, $disposalId]);
        return $this->db->rowCount() > 0;
    }
    
    /**
     * Từ chối phiếu hủy
     */
    public function reject($disposalId, $approverId, $reason) {
        $sql = "UPDATE phieu_huy 
                SET Trang_thai = 'tu_choi', 
                    Nguoi_duyet = ?, 
                    Ngay_duyet = NOW(),
                    Ly_do_tu_choi = ?
                WHERE ID_phieu_huy = ? AND Trang_thai = 'cho_duyet'";
        
        $this->db->query($sql, [$approverId, $reason, $disposalId]);
        return $this->db->rowCount() > 0;
    }
    
    /**
     * Đếm phiếu hủy chờ duyệt
     */
    public function countPending() {
        $result = $this->queryOne("SELECT COUNT(*) AS total FROM phieu_huy WHERE Trang_thai = 'cho_duyet'");
        return (int)($result['total'] ?? 0);
    }
    
    /**
     * Đếm phiếu hủy theo từng trạng thái
     * @return array ['all' => x, 'cho_duyet' => y, 'da_duyet' => z, 'tu_choi' => w]
     */
    public function countByStatus() {
        $all = $this->queryOne("SELECT COUNT(*) AS total FROM phieu_huy");
        $pending = $this->queryOne("SELECT COUNT(*) AS total FROM phieu_huy WHERE Trang_thai = 'cho_duyet'");
        $approved = $this->queryOne("SELECT COUNT(*) AS total FROM phieu_huy WHERE Trang_thai = 'da_duyet'");
        $rejected = $this->queryOne("SELECT COUNT(*) AS total FROM phieu_huy WHERE Trang_thai = 'tu_choi'");
        
        return [
            'all' => (int)($all['total'] ?? 0),
            'cho_duyet' => (int)($pending['total'] ?? 0),
            'da_duyet' => (int)($approved['total'] ?? 0),
            'tu_choi' => (int)($rejected['total'] ?? 0)
        ];
    }
    
    /**
     * Lấy phiếu hủy chờ duyệt (cho dashboard)
     */
    public function getPendingDisposals($limit = 5) {
        return $this->query("SELECT * FROM v_phieu_huy_cho_duyet LIMIT ?", [$limit]);
    }
    
    // ==========================================================================
    // THỐNG KÊ
    // ==========================================================================
    
    /**
     * Thống kê hàng hủy theo loại
     */
    public function getDisposalStats() {
        return $this->query("SELECT * FROM v_thong_ke_hang_huy");
    }
    
    /**
     * Tổng giá trị hàng hủy trong khoảng thời gian
     */
    public function getTotalDisposalValue($dateFrom, $dateTo) {
        $sql = "SELECT COALESCE(SUM(Tong_tien_huy), 0) AS total
                FROM phieu_huy 
                WHERE Trang_thai = 'da_duyet'
                  AND Ngay_huy BETWEEN ? AND ?";
        
        $result = $this->queryOne($sql, [$dateFrom, $dateTo]);
        return (float)($result['total'] ?? 0);
    }
    
    // ==========================================================================
    // EXPORT EXCEL
    // ==========================================================================
    
    /**
     * Lấy dữ liệu để xuất Excel
     */
    public function getDisposalsForExport($filters = []) {
        $sql = "SELECT ph.Ma_hien_thi, ph.Loai_phieu, ph.Ngay_huy, ph.Ly_do,
                       ph.Tong_tien_huy, ph.Trang_thai, ph.Ngay_tao,
                       tk.Ho_ten AS Nguoi_tao, tk2.Ho_ten AS Nguoi_duyet, ph.Ngay_duyet
                FROM phieu_huy ph
                INNER JOIN tai_khoan tk ON ph.Nguoi_tao = tk.ID
                LEFT JOIN tai_khoan tk2 ON ph.Nguoi_duyet = tk2.ID
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['trang_thai'])) {
            $sql .= " AND ph.Trang_thai = ?";
            $params[] = $filters['trang_thai'];
        }
        
        if (!empty($filters['tu_ngay'])) {
            $sql .= " AND ph.Ngay_huy >= ?";
            $params[] = $filters['tu_ngay'];
        }
        
        if (!empty($filters['den_ngay'])) {
            $sql .= " AND ph.Ngay_huy <= ?";
            $params[] = $filters['den_ngay'];
        }
        
        $sql .= " ORDER BY ph.Ngay_huy DESC";
        
        return $this->query($sql, $params);
    }
}
