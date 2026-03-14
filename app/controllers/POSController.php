<?php
/**
 * =============================================================================
 * POS CONTROLLER - Bán hàng tại quầy
 * =============================================================================
 * 
 * Xử lý logic bán hàng trực tiếp cho khách vãng lai
 * - Tìm sản phẩm (AJAX)
 * - Quản lý giỏ hàng (Session)
 * - Thanh toán và tạo đơn hàng
 * - In hóa đơn PDF
 */

class POSController extends Controller {
    
    // ID tài khoản hệ thống cho khách vãng lai
    const POS_CUSTOMER_ID = 999999;
    
    private $productModel;
    private $orderModel;
    private $categoryModel;
    
    public function __construct() {
        // Chỉ Admin mới được truy cập POS
        Middleware::admin();
        
        $this->productModel = $this->model('Product');
        $this->orderModel = $this->model('Order');
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * Trang chính POS
     */
    public function index() {
        // Khởi tạo giỏ POS nếu chưa có
        if (!isset($_SESSION['pos_cart'])) {
            $_SESSION['pos_cart'] = [];
        }
        
        $cart = $_SESSION['pos_cart'];
        $total = $this->calculateTotal($cart);
        
        // Nạp danh mục cho các tab lọc
        $categories = $this->categoryModel->getAllActive();
        
        // Nạp sản phẩm ban đầu (tất cả còn hàng)
        $products = $this->productModel->getActiveProductsForPOS(20);
        
        $data = [
            'page_title' => 'Bán hàng tại quầy - FreshMart',
            'cart' => $cart,
            'total' => $total,
            'categories' => $categories,
            'products' => $products,
            'cashier_name' => Session::getUserName(),
            'csrf_token' => Session::getCsrfToken(),
            'current_date' => date('d/m/Y')
        ];
        
        $this->view('pos/index', $data);
    }
    
    /**
     * Tìm kiếm sản phẩm (AJAX)
     */
    public function search() {
        if (!$this->isAjax()) {
            return $this->json(['error' => 'Invalid request'], 400);
        }
        
        $keyword = trim(get('q', ''));
        
        if (strlen($keyword) < 1) {
            return $this->json(['products' => []]);
        }
        
        // Tìm theo tên hoặc mã sản phẩm
        $products = $this->productModel->searchForPOS($keyword, 20);
        
        return $this->json([
            'success' => true,
            'products' => $products
        ]);
    }
    
    /**
     * Lọc sản phẩm theo danh mục (AJAX)
     */
    public function filterByCategory() {
        if (!$this->isAjax()) {
            return $this->json(['error' => 'Invalid request'], 400);
        }
        
        $categoryId = (int)get('category_id', 0);
        
        if ($categoryId === 0) {
            // Nạp tất cả sản phẩm
            $products = $this->productModel->getActiveProductsForPOS(30);
        } else {
            // Nạp theo danh mục
            $products = $this->productModel->getProductsByCategory($categoryId, 30);
        }
        
        return $this->json([
            'success' => true,
            'products' => $products
        ]);
    }
    
    /**
     * Thêm sản phẩm vào giỏ POS (AJAX)
     */
    public function addToCart() {
        if (!$this->isAjax() || !$this->isMethod('POST')) {
            return $this->json(['error' => 'Invalid request'], 400);
        }
        
        $productId = (int)post('product_id');
        $quantity = (int)post('quantity', 1);
        
        if ($quantity < 1) $quantity = 1;
        
        // Lấy thông tin sản phẩm
        $product = $this->productModel->findById($productId);
        
        if (!$product || $product['Trang_thai'] !== 'active') {
            return $this->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại hoặc không khả dụng'
            ]);
        }
        
        // Kiểm tra tồn kho
        if ($product['So_luong_ton'] < $quantity) {
            return $this->json([
                'success' => false,
                'message' => "Chỉ còn {$product['So_luong_ton']} sản phẩm trong kho"
            ]);
        }
        
        // Khởi tạo giỏ nếu chưa có
        if (!isset($_SESSION['pos_cart'])) {
            $_SESSION['pos_cart'] = [];
        }
        
        // Thêm hoặc cập nhật số lượng
        $found = false;
        foreach ($_SESSION['pos_cart'] as &$item) {
            if ($item['ID_sp'] == $productId) {
                $newQty = $item['So_luong'] + $quantity;
                if ($newQty > $product['So_luong_ton']) {
                    return $this->json([
                        'success' => false,
                        'message' => "Chỉ còn {$product['So_luong_ton']} sản phẩm trong kho"
                    ]);
                }
                $item['So_luong'] = $newQty;
                $item['Thanh_tien'] = $item['So_luong'] * $item['Gia_tien'];
                $found = true;
                break;
            }
        }
        unset($item);
        
        if (!$found) {
            $_SESSION['pos_cart'][] = [
                'ID_sp' => $product['ID_sp'],
                'Ma_hien_thi' => $product['Ma_hien_thi'],
                'Ten' => $product['Ten'],
                'Gia_tien' => $product['Gia_tien'],
                'So_luong' => $quantity,
                'Thanh_tien' => $product['Gia_tien'] * $quantity,
                'Don_vi_tinh' => $product['Don_vi_tinh'],
                'Hinh_anh' => $product['Hinh_anh'],
                'So_luong_ton' => $product['So_luong_ton']
            ];
        }
        
        $cart = $_SESSION['pos_cart'];
        $total = $this->calculateTotal($cart);
        
        return $this->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ',
            'cart' => $cart,
            'total' => $total,
            'total_formatted' => formatPrice($total)
        ]);
    }
    
    /**
     * Cập nhật số lượng trong giỏ (AJAX)
     */
    public function updateQuantity() {
        if (!$this->isAjax() || !$this->isMethod('POST')) {
            return $this->json(['error' => 'Invalid request'], 400);
        }
        
        $productId = (int)post('product_id');
        $quantity = (int)post('quantity');
        
        if ($quantity < 1) {
            // Xóa sản phẩm nếu số lượng < 1
            return $this->removeFromCart();
        }
        
        if (!isset($_SESSION['pos_cart'])) {
            return $this->json(['success' => false, 'message' => 'Giỏ hàng trống']);
        }
        
        foreach ($_SESSION['pos_cart'] as &$item) {
            if ($item['ID_sp'] == $productId) {
                // Kiểm tra tồn kho
                if ($quantity > $item['So_luong_ton']) {
                    return $this->json([
                        'success' => false,
                        'message' => "Chỉ còn {$item['So_luong_ton']} sản phẩm trong kho"
                    ]);
                }
                $item['So_luong'] = $quantity;
                $item['Thanh_tien'] = $item['So_luong'] * $item['Gia_tien'];
                break;
            }
        }
        unset($item);
        
        $cart = $_SESSION['pos_cart'];
        $total = $this->calculateTotal($cart);
        
        return $this->json([
            'success' => true,
            'cart' => $cart,
            'total' => $total,
            'total_formatted' => formatPrice($total)
        ]);
    }
    
    /**
     * Xóa sản phẩm khỏi giỏ (AJAX)
     */
    public function removeFromCart() {
        if (!$this->isAjax() || !$this->isMethod('POST')) {
            return $this->json(['error' => 'Invalid request'], 400);
        }
        
        $productId = (int)post('product_id');
        
        if (!isset($_SESSION['pos_cart'])) {
            return $this->json(['success' => false, 'message' => 'Giỏ hàng trống']);
        }
        
        $_SESSION['pos_cart'] = array_filter($_SESSION['pos_cart'], function($item) use ($productId) {
            return $item['ID_sp'] != $productId;
        });
        $_SESSION['pos_cart'] = array_values($_SESSION['pos_cart']); // Re-index
        
        $cart = $_SESSION['pos_cart'];
        $total = $this->calculateTotal($cart);
        
        return $this->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm',
            'cart' => $cart,
            'total' => $total,
            'total_formatted' => formatPrice($total)
        ]);
    }
    
    /**
     * Xóa toàn bộ giỏ (AJAX)
     */
    public function clearCart() {
        if (!$this->isAjax() || !$this->isMethod('POST')) {
            return $this->json(['error' => 'Invalid request'], 400);
        }
        
        $_SESSION['pos_cart'] = [];
        
        return $this->json([
            'success' => true,
            'message' => 'Đã xóa giỏ hàng',
            'cart' => [],
            'total' => 0,
            'total_formatted' => formatPrice(0)
        ]);
    }
    
    /**
     * Thanh toán (AJAX)
     */
    public function checkout() {
        // Bắt đầu output buffering để bắt các cảnh báo/thông báo PHP không mong muốn
        ob_start();
        
        try {
            if (!$this->isAjax() || !$this->isMethod('POST')) {
                ob_end_clean();
                return $this->json(['error' => 'Invalid request'], 400);
            }
            
            if (!Middleware::verifyCsrf(post('csrf_token', ''))) {
                ob_end_clean();
                return $this->json(['success' => false, 'message' => 'CSRF token không hợp lệ']);
            }
            
            $cart = $_SESSION['pos_cart'] ?? [];
            
            if (empty($cart)) {
                ob_end_clean();
                return $this->json(['success' => false, 'message' => 'Giỏ hàng trống']);
            }
            
            $total = $this->calculateTotal($cart);
            $cashReceived = (float)post('cash_received', 0);
            
            if ($cashReceived < $total) {
                ob_end_clean();
                return $this->json([
                    'success' => false,
                    'message' => 'Số tiền khách đưa không đủ'
                ]);
            }
            
            // Tạo đơn hàng POS
            $orderId = $this->createPOSOrder($cart, $total);
            
            if (!$orderId) {
                throw new Exception("Không thể tạo đơn hàng");
            }
            
            // Lưu lại cart trước khi xóa để trả về cho receipt
            $cartItems = $cart;
            
            // Xóa giỏ sau khi thanh toán thành công
            $_SESSION['pos_cart'] = [];
            
            // Xóa bộ đệm output (cảnh báo, v.v.) trước khi gửi JSON
            $output = ob_get_clean();
            if (!empty($output)) {
                error_log("POS Checkout Buffer Output: " . $output);
            }
            
            return $this->json([
                'success' => true,
                'message' => 'Thanh toán thành công!',
                'order_id' => $orderId,
                'items' => $cartItems,
                'total' => $total,
                'cash_received' => $cashReceived,
                'change' => $cashReceived - $total,
                'change_formatted' => formatPrice($cashReceived - $total),
                'cashier_name' => Session::getUserName(),
                'date' => date('d/m/Y H:i')
            ]);
            
        } catch (Exception $e) {
            // Ghi log lỗi
            error_log("POS Checkout Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            // Xóa bộ đệm
            $output = ob_get_clean();
            if (!empty($output)) {
                error_log("POS Checkout Buffer Output (Error): " . $output);
            }
            
            // Trả về JSON lỗi
            return $this->json(['success' => false, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Lấy dữ liệu hóa đơn (AJAX)
     */
    public function getReceipt() {
        $orderId = (int)get('order_id');
        
        if (!$orderId) {
            return $this->json(['error' => 'Order ID required'], 400);
        }
        
        $order = $this->orderModel->findById($orderId);
        
        if (!$order || $order['ID_tk'] != self::POS_CUSTOMER_ID) {
            return $this->json(['error' => 'Order not found'], 404);
        }
        
        $orderDetails = $this->orderModel->getOrderDetails($orderId);
        
        return $this->json([
            'success' => true,
            'order' => $order,
            'items' => $orderDetails,
            'cashier' => Session::getUserName()
        ]);
    }
    
    /**
     * Tạo đơn hàng POS
     */
    private function createPOSOrder($cart, $total) {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Tạo đơn hàng
            $sql = "INSERT INTO don_hang 
                    (ID_tk, Ten_nguoi_nhan, Sdt_nguoi_nhan, Dia_chi_giao_hang, 
                     Ghi_chu, Tong_tien, Phi_van_chuyen, Thanh_tien, Trang_thai) 
                    VALUES 
                    (:id_tk, :ten, :sdt, :diachi, :ghichu, :tongtien, 0, :thanhtien, 'da_giao')";
            
            $orderId = $db->insert($sql, [
                'id_tk' => self::POS_CUSTOMER_ID,
                'ten' => 'Khách vãng lai',
                'sdt' => '0000000000',
                'diachi' => 'Mua tại quầy',
                'ghichu' => 'Mua tại quầy - Thu ngân: ' . Session::getUserName(),
                'tongtien' => $total,
                'thanhtien' => $total
            ]);
            
            if (!$orderId) {
                throw new Exception("Không thể tạo đơn hàng");
            }
            
            // Thêm chi tiết đơn hàng với FEFO Multi-Batch allocation
            foreach ($cart as $item) {
                // FEFO Multi-Batch: Phân bổ từ nhiều lô nếu 1 lô không đủ
                $allocations = $this->findBatchesFEFO($db, $item['ID_sp'], $item['So_luong']);
                
                foreach ($allocations as $batch) {
                    $batchQuantity = $batch['So_luong_xuat'];
                    $batchTotal = $item['Gia_tien'] * $batchQuantity;
                    
                    $sql = "INSERT INTO chi_tiet_don_hang 
                            (ID_dh, ID_sp, Ten_sp, So_luong, Gia_tien, Thanh_tien, Hinh_anh, Don_gia_von, ID_chi_tiet_nhap) 
                            VALUES 
                            (:id_dh, :id_sp, :ten_sp, :so_luong, :gia_tien, :thanh_tien, :hinh_anh, :don_gia_von, :id_lo)";
                    
                    $db->insert($sql, [
                        'id_dh' => $orderId,
                        'id_sp' => $item['ID_sp'],
                        'ten_sp' => $item['Ten'],
                        'so_luong' => $batchQuantity,
                        'gia_tien' => $item['Gia_tien'],
                        'thanh_tien' => $batchTotal,
                        'hinh_anh' => $item['Hinh_anh'] ?? null,
                        'don_gia_von' => $batch['Don_gia_nhap'],
                        'id_lo' => $batch['ID_chi_tiet_nhap']
                    ]);
                }
            }
            
            $db->commit();
            
            return $orderId;
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
    
    /**
     * FEFO Multi-Batch Allocation - Phân bổ từ nhiều lô theo FEFO
     */
    private function findBatchesFEFO($db, $productId, $quantityNeeded) {
        $sql = "SELECT ID_chi_tiet_nhap, Don_gia_nhap, So_luong_con
                FROM chi_tiet_phieu_nhap
                WHERE ID_sp = ? 
                  AND So_luong_con > 0
                  AND (Ngay_het_han IS NULL OR Ngay_het_han > CURDATE())
                ORDER BY 
                    CASE WHEN Ngay_het_han IS NULL THEN 1 ELSE 0 END,
                    Ngay_het_han ASC,
                    ID_chi_tiet_nhap ASC";
        
        $batches = $db->query($sql, [$productId])->fetchAll();
        
        $allocations = [];
        $remaining = $quantityNeeded;
        
        foreach ($batches as $batch) {
            if ($remaining <= 0) break;
            
            $takeFromBatch = min($remaining, $batch['So_luong_con']);
            
            $allocations[] = [
                'ID_chi_tiet_nhap' => $batch['ID_chi_tiet_nhap'],
                'Don_gia_nhap' => $batch['Don_gia_nhap'],
                'So_luong_xuat' => $takeFromBatch
            ];
            
            $remaining -= $takeFromBatch;
        }
        
        // Fallback nếu không đủ hàng
        if ($remaining > 0) {
            $sql = "SELECT Gia_nhap FROM san_pham WHERE ID_sp = ?";
            $product = $db->query($sql, [$productId])->fetch();
            $allocations[] = [
                'ID_chi_tiet_nhap' => null,
                'Don_gia_nhap' => $product ? $product['Gia_nhap'] : 0,
                'So_luong_xuat' => $remaining
            ];
        }
        
        return $allocations;
    }
    
    /**
     * Tính tổng tiền giỏ hàng
     */
    private function calculateTotal($cart) {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['Thanh_tien'];
        }
        return $total;
    }
}
