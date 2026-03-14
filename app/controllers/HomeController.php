<?php
/**
 * HOME CONTROLLER - FIXED VERSION
 * Các method được sửa lỗi xung đột
 */

class HomeController extends Controller {
    
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
    }
    
    /**
     * METHOD: index() - TRANG CHỦ - FIXED
     */
    public function index() {
        $categories = $this->categoryModel->getCategoriesTree();
        
        $bestSellers = $this->productModel->getBestSellers(8);
        
        $newProducts = $this->productModel->getLatestProducts(12);
        
        $discountProducts = [];
        
        $slides = [
            [
                'image' => asset('img/banners/slide1.jpg'),
                'title' => 'Rau củ tươi ngon mỗi ngày',
                'subtitle' => 'Fresh, Local, and Delicious',
                'button_text' => 'Mua ngay',
                'button_link' => BASE_URL . '/products?category=5'
            ],
            [
                'image' => asset('img/banners/slide2.jpg'),
                'title' => 'Sữa tươi 100% organic',
                'subtitle' => 'Nguồn dinh dưỡng cho cả nhà',
                'button_text' => 'Khám phá',
                'button_link' => BASE_URL . '/products?category=1'
            ],
            [
                'image' => asset('img/banners/slide3.jpg'),
                'title' => 'Thịt hải sản tươi sống',
                'subtitle' => 'Đảm bảo vệ sinh an toàn thực phẩm',
                'button_text' => 'Xem ngay',
                'button_link' => BASE_URL . '/products?category=17'
            ]
        ];
        
        $data = [
            'page_title' => 'Trang chủ - FreshMart',
            'meta_description' => 'Siêu thị thực phẩm tươi sống FreshMart - Giao hàng nhanh 2h, Miễn phí từ 150.000đ',
            'categories' => $categories,
            'slides' => $slides,
            'best_sellers' => $bestSellers,
            'new_products' => $newProducts,
            'discount_products' => $discountProducts,
            'cart_count' => Session::getCartCount(),  // ✅ FIXED
            'is_logged_in' => Session::isLoggedIn(),
            'user_name' => Session::get('user_name', 'Khách'),
            'flash_success' => Session::getFlash('success'),
            'flash_error' => Session::getFlash('error'),
            'flash_warning' => Session::getFlash('warning'),
            'flash_info' => Session::getFlash('info')
        ];
        
        $this->view('customer/home', $data);
    }
    
    /**
     * METHOD: search() - TÌM KIẾM SẢN PHẨM (AJAX) - FIXED
     */
    public function search() {
        $keyword = get('q', '');
        
        if (empty($keyword) || strlen($keyword) < 2) {
            $this->json([
                'success' => false,
                'message' => 'Vui lòng nhập ít nhất 2 ký tự'
            ]);
        }
        
        $keyword = $this->sanitize($keyword);
        
        $results = $this->productModel->search($keyword, 10);
        
        $formattedResults = [];
        foreach ($results as $product) {
            $formattedResults[] = [
                'id' => $product['ID_sp'],
                'name' => $product['Ten'],
                'price' => $product['Gia_tien'],
                'price_formatted' => formatPrice($product['Gia_tien']),  // ✅ FIXED
                'image' => $product['Hinh_anh'] 
                    ? UPLOAD_PRODUCT_URL . '/' . $product['Hinh_anh'] 
                    : asset('img/no-image.png'),
                'url' => BASE_URL . '/products/detail/' . $product['ID_sp'],
                'in_stock' => $product['So_luong_ton'] > 0
            ];
        }
        
        $this->json([
            'success' => true,
            'count' => count($formattedResults),
            'results' => $formattedResults
        ]);
    }
    
    /**
     * METHOD: about() - GIỚI THIỆU - FIXED
     */
    public function about() {
        $data = [
            'page_title' => 'Giới thiệu - FreshMart',
            'categories' => $this->categoryModel->getCategoriesTree(),
            'cart_count' => Session::getCartCount(),  // ✅ FIXED
            'is_logged_in' => Session::isLoggedIn()
        ];
        
        $this->view('customer/about', $data);
    }
    
    /**
     * METHOD: contact() - LIÊN HỆ - FIXED
     */
    public function contact() {
        if ($this->isMethod('POST')) {
            $validation = $this->validate($_POST, [
                'name' => 'required|max:100',
                'email' => 'required|email',
                'phone' => 'required|numeric',
                'message' => 'required|max:1000'
            ]);
            
            if (!$validation['valid']) {
                Session::flash('error', 'Vui lòng kiểm tra lại thông tin');
                redirect('/contact');
            }
            
            // TODO: Lưu vào database hoặc gửi email
            
            Session::flash('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.');
            redirect('/contact');
        }
        
        $data = [
            'page_title' => 'Liên hệ - FreshMart',
            'categories' => $this->categoryModel->getCategoriesTree(),
            'cart_count' => Session::getCartCount(),  // ✅ FIXED
            'is_logged_in' => Session::isLoggedIn()
        ];
        
        $this->view('customer/contact', $data);
    }
}