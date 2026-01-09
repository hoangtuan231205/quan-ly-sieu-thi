<?php
/**
 * PRODUCT CONTROLLER - FIXED VERSION
 * Các method được sửa lỗi xung đột
 */

class ProductController extends Controller {
    
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * METHOD: index() - DANH SÁCH TẤT CẢ SẢN PHẨM - FIXED
     */
    public function index() {
        $filters = [
            'category_id' => get('category', ''),
            'min_price' => get('min_price', ''),
            'max_price' => get('max_price', ''),
            'sort' => get('sort', 'newest'),
            'page' => max(1, (int)get('page', 1)),
            'keyword' => get('keyword', '')
        ];
        
        $orderBy = 'sp.Ngay_tao DESC';
        
        switch ($filters['sort']) {
            case 'price_asc':
                $orderBy = 'sp.Gia_tien ASC';
                break;
            case 'price_desc':
                $orderBy = 'sp.Gia_tien DESC';
                break;
            case 'bestseller':
                $orderBy = '(SELECT IFNULL(SUM(So_luong), 0) FROM chi_tiet_don_hang WHERE ID_sp = sp.ID_sp) DESC';
                break;
            case 'name_asc':
                $orderBy = 'sp.Ten ASC';
                break;
        }
        
        $totalProducts = $this->productModel->countProducts($filters);
        
        $perPage = 12;
        $pagination = $this->paginate($totalProducts, $perPage, $filters['page']);
        
        $products = $this->productModel->getProducts(
            $filters,
            $orderBy,
            $pagination['per_page'],
            $pagination['offset']
        );
        
        $categories = $this->categoryModel->getCategoriesTree();
        
        $priceRange = $this->productModel->getPriceRange();
        
        $breadcrumb = [
            ['name' => 'Trang chủ', 'url' => BASE_URL],
            ['name' => 'Sản phẩm', 'url' => '']
        ];
        
        if (!empty($filters['category_id'])) {
            $category = $this->categoryModel->findById($filters['category_id']);
            if ($category) {
                $breadcrumb[] = ['name' => $category['Ten_danh_muc'], 'url' => ''];
            }
        }
        
        $data = [
            'page_title' => 'Danh sách sản phẩm - FreshMart',
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'pagination' => $pagination,
            'price_range' => $priceRange,
            'breadcrumb' => $breadcrumb,
            'total_products' => $totalProducts,
            'cart_count' => Session::getCartCount(),  // ✅ FIXED
            'is_logged_in' => Session::isLoggedIn()  // ✅ FIXED
        ];
        
        $this->view('customer/products', $data);
    }
    
    /**
     * METHOD: detail() - CHI TIẾT SẢN PHẨM - FIXED
     */
    public function detail($productId = null) {
        if (!$productId || !is_numeric($productId)) {
            Session::flash('error', 'Sản phẩm không tồn tại');
            redirect('/products');
        }
        
        $product = $this->productModel->findById($productId);
        
        if (!$product) {
            Session::flash('error', 'Sản phẩm không tồn tại');
            redirect('/products');
        }
        
        if ($product['Trang_thai'] !== 'active') {
            Session::flash('error', 'Sản phẩm tạm thời không khả dụng');
            redirect('/products');
        }
        
        $category = $this->categoryModel->findById($product['ID_danh_muc']);
        
        $relatedProducts = $this->productModel->getRelatedProducts(
            $productId,
            $product['ID_danh_muc'],
            8
        );
        
        $breadcrumb = [
            ['name' => 'Trang chủ', 'url' => BASE_URL],
            ['name' => 'Sản phẩm', 'url' => BASE_URL . '/products']
        ];
        
        if ($category && $category['Danh_muc_cha']) {
            $parentCategory = $this->categoryModel->findById($category['Danh_muc_cha']);
            if ($parentCategory) {
                $breadcrumb[] = [
                    'name' => $parentCategory['Ten_danh_muc'],
                    'url' => BASE_URL . '/products?category=' . $parentCategory['ID_danh_muc']
                ];
            }
        }
        
        if ($category) {
            $breadcrumb[] = [
                'name' => $category['Ten_danh_muc'],
                'url' => BASE_URL . '/products?category=' . $category['ID_danh_muc']
            ];
        }
        
        $breadcrumb[] = ['name' => $product['Ten'], 'url' => ''];
        
        $inCart = false;
        $cartQuantity = 0;
        
        if (Session::isLoggedIn()) {
            $cartModel = $this->model('Cart');
            $cartItem = $cartModel->getCartItem(Session::getUserId(), $productId);
            if ($cartItem) {
                $inCart = true;
                $cartQuantity = $cartItem['So_luong'];
            }
        }
        
        $data = [
            'page_title' => $product['Ten'] . ' - FreshMart',
            'meta_description' => mb_substr(strip_tags($product['Mo_ta_sp'] ?? ''), 0, 160),
            'product' => $product,
            'category' => $category,
            'related_products' => $relatedProducts,
            'breadcrumb' => $breadcrumb,
            'in_cart' => $inCart,
            'cart_quantity' => $cartQuantity,
            'cart_count' => Session::getCartCount(),  // ✅ FIXED
            'is_logged_in' => Session::isLoggedIn(),  // ✅ FIXED
            'categories' => $this->categoryModel->getCategoriesTree()
        ];
        
        $this->view('customer/product_detail', $data);
    }
    
    /**
     * METHOD: category() - SẢN PHẨM THEO DANH MỤC - FIXED
     */
    public function category($categoryId = null) {
        if ($categoryId) {
            redirect('/products?category=' . $categoryId);
        } else {
            redirect('/products');
        }
    }
    
    /**
     * METHOD: quickView() - XEM NHANH SẢN PHẨM (AJAX) - FIXED
     */
    public function quickView($productId = null) {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        if (!$productId || !is_numeric($productId)) {
            $this->json(['error' => 'Invalid product ID'], 400);
        }
        
        $product = $this->productModel->findById($productId);
        
        if (!$product || $product['Trang_thai'] !== 'active') {
            $this->json(['error' => 'Product not found'], 404);
        }
        
        $inCart = false;
        if (Session::isLoggedIn()) {
            $cartModel = $this->model('Cart');
            $cartItem = $cartModel->getCartItem(Session::getUserId(), $productId);
            $inCart = ($cartItem !== null);
        }
        
        $this->json([
            'success' => true,
            'product' => [
                'id' => $product['ID_sp'],
                'name' => $product['Ten'],
                'price' => $product['Gia_tien'],
                'price_formatted' => formatPrice($product['Gia_tien']),
                'image' => $product['Hinh_anh'] 
                    ? UPLOAD_PRODUCT_URL . '/' . $product['Hinh_anh']
                    : asset('img/no-image.png'),
                'description' => $product['Mo_ta_sp'] ?? '',
                'stock' => $product['So_luong_ton'],
                'unit' => $product['Don_vi_tinh'] ?? 'Sản phẩm',
                'in_stock' => $product['So_luong_ton'] > 0,
                'in_cart' => $inCart,
                'detail_url' => BASE_URL . '/products/detail/' . $product['ID_sp']
            ]
        ]);
    }
    
    /**
     * METHOD: filterAjax() - LỌC SẢN PHẨM (AJAX) - FIXED
     */
    public function filterAjax() {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
        }
        
        $filters = [
            'category_id' => post('category', ''),
            'min_price' => post('min_price', ''),
            'max_price' => post('max_price', ''),
            'sort' => post('sort', 'newest'),
            'page' => max(1, (int)post('page', 1)),
            'keyword' => post('keyword', '')
        ];
        
        $orderBy = 'sp.Ngay_tao DESC';
        switch ($filters['sort']) {
            case 'price_asc':
                $orderBy = 'sp.Gia_tien ASC';
                break;
            case 'price_desc':
                $orderBy = 'sp.Gia_tien DESC';
                break;
            case 'bestseller':
                $orderBy = '(SELECT IFNULL(SUM(So_luong), 0) FROM chi_tiet_don_hang WHERE ID_sp = sp.ID_sp) DESC';
                break;
        }
        
        $totalProducts = $this->productModel->countProducts($filters);
        
        $perPage = 12;
        $pagination = $this->paginate($totalProducts, $perPage, $filters['page']);
        
        $products = $this->productModel->getProducts(
            $filters,
            $orderBy,
            $pagination['per_page'],
            $pagination['offset']
        );
        
        $formattedProducts = [];
        foreach ($products as $product) {
            $formattedProducts[] = [
                'id' => $product['ID_sp'],
                'name' => $product['Ten'],
                'price' => $product['Gia_tien'],
                'price_formatted' => formatPrice($product['Gia_tien']),  // ✅ FIXED
                'image' => $product['Hinh_anh'] 
                    ? UPLOAD_PRODUCT_URL . '/' . $product['Hinh_anh']
                    : asset('img/no-image.png'),
                'stock' => $product['So_luong_ton'],
                'in_stock' => $product['So_luong_ton'] > 0,
                'category_name' => $product['Ten_danh_muc'] ?? '',
                'url' => BASE_URL . '/products/detail/' . $product['ID_sp']
            ];
        }
        
        $this->json([
            'success' => true,
            'products' => $formattedProducts,
            'pagination' => $pagination,
            'total' => $totalProducts
        ]);
    }
}