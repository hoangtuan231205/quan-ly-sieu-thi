# 📋 FreshMart - Kiến Trúc Ứng Dụng & Luồng Hoạt Động

## 📁 Cấu Trúc Thư Mục

```
sieu_thi/
├── public/
│   ├── index.php              ← ĐIỂM VÀO DUY NHẤT
│   ├── assets/
│   │   ├── css/style.css      ← CSS chính
│   │   ├── js/main.js         ← JavaScript chính
│   │   └── img/               ← Hình ảnh
│   └── uploads/               ← Upload file từ user
├── app/
│   ├── controllers/           ← Xử lý logic (Controller)
│   ├── models/                ← Tương tác database (Model)
│   ├── views/                 ← Giao diện HTML (View)
│   ├── core/                  ← Core classes
│   │   ├── App.php            ← Router (phân tích URL)
│   │   ├── Database.php       ← Kết nối database
│   │   ├── Session.php        ← Quản lý session & lưu trữ user
│   │   ├── Middleware.php     ← Bảo mật & kiểm soát
│   │   └── Logger.php         ← Ghi log
│   └── helpers.php            ← Helper functions
├── config/
│   └── config.php             ← Cấu hình & autoloader
└── database/
    └── freshmart.sql          ← Database schema
```

---

## 🔄 Luồng Chạy Ứng Dụng (Request Lifecycle)

### 1️⃣ **KHỞI ĐỘNG (Entry Point)**

```
User truy cập: http://localhost/sieu_thi/public/
                     ↓
                public/index.php
                     ↓
    ┌─────────────────┼─────────────────┐
    ↓                 ↓                 ↓
 config.php      Session::start()    App Router
    ↓                 ↓                 ↓
Autoload        Khởi động session   Phân tích URL
```

### 2️⃣ **LUỒNG CHÍNH**

```
public/index.php
    │
    ├─ Require config.php
    │  ├─ Autoloader (tự động load classes)
    │  └─ Helper functions
    │
    ├─ Session::start()
    │  └─ Khởi tạo session, check user đã đăng nhập
    │
    └─ $app = new App()
       │
       ├─ parseUrl() → Lấy URL từ query string
       │  VD: /auth/login → ['auth', 'login']
       │
       └─ handleRouting($url)
          │
          ├─ Phân loại URL
          │  ├─ /                    → HomeController->index()
          │  ├─ /auth/login          → AuthController->login()
          │  ├─ /auth/register       → AuthController->register()
          │  ├─ /auth/logout         → AuthController->logout()
          │  ├─ /products            → ProductController->index()
          │  ├─ /cart                → CartController->index()
          │  ├─ /checkout            → CheckoutController->index()
          │  ├─ /admin               → AdminController->index()
          │  └─ /warehouse           → WarehouseController->index()
          │
          └─ callController($controller, $method, $params)
             │
             ├─ Khởi tạo Controller (VD: new HomeController())
             ├─ Gọi method (VD: ->index())
             ├─ Controller gọi Model để lấy data
             ├─ Controller gọi View để hiển thị
             └─ Trả về HTML cho user
```

---

## 🔐 LỰA CHỌN NÚT USER - Luồng Chính

### **Tình Huống 1: Chưa Đăng Nhập**

```
User chưa login
    ↓
Click nút USER (icon 👤) trong header
    ↓
Hiển thị Modal Auth (form đăng nhập/đăng ký)
    ├─ Tab "Đăng nhập"
    │  ├─ Input: Tài khoản hoặc Email
    │  ├─ Input: Mật khẩu
    │  └─ Button: Đăng nhập
    │
    └─ Tab "Đăng ký"
       ├─ Input: Tài khoản
       ├─ Input: Email
       ├─ Input: Mật khẩu
       └─ Button: Đăng ký
```

**Quy trình Đăng nhập:**
```
User click "Đăng nhập"
    ↓
POST → /auth/login
    ↓
AuthController->login() [POST]
    │
    ├─ Validate input (required, length)
    ├─ Sanitize input (xóa ký tự đặc biệt)
    ├─ Rate limiting (chặn brute force, 5 lần/5 phút)
    ├─ Query DB: SELECT * FROM tai_khoan WHERE username OR email
    ├─ password_verify() → Kiểm tra mật khẩu
    ├─ Kiểm tra trạng thái tài khoản (active/banned)
    ├─ Session::login($user)
    │  └─ Lưu user info vào $_SESSION
    ├─ Redirect theo role:
    │  ├─ ADMIN → /admin
    │  ├─ QUAN_LY_KHO → /warehouse
    │  └─ KH → / (trang chủ)
    └─ ✅ Thành công → Hiện thông báo success
```

**Quy trình Đăng ký:**
```
User click "Đăng ký"
    ↓
POST → /auth/register
    ↓
AuthController->register() [POST]
    │
    ├─ Validate input (required, length, email, password match)
    ├─ Sanitize input
    ├─ Kiểm tra username đã tồn tại?
    ├─ Kiểm tra email đã tồn tại?
    ├─ Hash password: password_hash($password, PASSWORD_DEFAULT)
    ├─ INSERT INTO tai_khoan VALUES (...)
    │  └─ Set role = 'KH' (khách hàng)
    ├─ Session::login($newUser)
    │  └─ Đăng nhập luôn sau khi đăng ký
    ├─ Log hoạt động
    ├─ Redirect → / (trang chủ)
    └─ ✅ Thành công → Chào mừng user
```

---

### **Tình Huống 2: Đã Đăng Nhập**

```
User đã login (Session::isLoggedIn() = true)
    ↓
Click nút USER (icon 👤) trong header
    ↓
Hiển thị User Info Card
    ├─ Tên user: "Xin chào, [username]"
    ├─ Email: user@example.com
    ├─ Button: "Xem thông tin"
    │  └─ Link → /auth/profile (Trang thông tin user)
    └─ Button: "Đăng xuất"
       └─ POST → /auth/logout
          ├─ Session::logout()
          │  └─ Xóa toàn bộ $_SESSION
          ├─ session_destroy()
          └─ Redirect → / (trang chủ)
```

---

## 📚 Chi Tiết Từng File & Hàm

### **1. public/index.php** - Điểm Vào
**Chức năng**: Entry point duy nhất
```php
1. Load config.php
2. Khởi động Session::start()
3. Khởi tạo $app = new App() (Router)
4. Router tự động gọi Controller và method phù hợp
```

---

### **2. config/config.php** - Cấu Hình & Autoloader
**Chức năng**: Cấu hình app + tự động load classes
```php
- Định nghĩa constants:
  * BASE_URL, DB_HOST, DB_NAME, DB_CHARSET
  * SESSION_NAME, SESSION_LIFETIME
  * ASSETS_DIR, UPLOADS_DIR
  
- Autoloader (spl_autoload_register):
  * Tự động load classes từ /app/core, /app/controllers, /app/models
  * Không cần require thủ công từng file
  * VD: new HomeController() → tự load HomeController.php
```

---

### **3. app/core/App.php** - Router
**Chức năng**: Phân tích URL & gọi Controller

**Các method chính:**
- `parseUrl()` → Lấy URL từ query string, chia thành mảng
- `handleRouting($url)` → Xác định Controller & method
- `callController()` → Khởi tạo & gọi Controller

**Ví dụ routing:**
```
URL: /auth/login        → AuthController->login()
URL: /products/detail/5 → ProductController->detail(5)
URL: /admin/orders      → AdminController->orders()
URL: /                  → HomeController->index()
```

---

### **4. app/core/Session.php** - Quản Lý Session

**Các method chính:**
```php
Session::start()           ← Khởi động session, bảo mật
Session::set($key, $value) ← Lưu data vào session
Session::get($key, $default) ← Lấy data từ session
Session::has($key)         ← Kiểm tra key có tồn tại?
Session::delete($key)      ← Xóa key khỏi session
Session::destroy()         ← Xóa toàn bộ session

Session::login($user)      ← Lưu user info khi đăng nhập
Session::logout()          ← Xóa user info khi đăng xuất
Session::isLoggedIn()      ← Kiểm tra đã đăng nhập?
Session::user()            ← Lấy info user hiện tại

Session::flash($key, $msg) ← Lưu thông báo 1 lần
Session::getFlash($key)    ← Lấy thông báo
```

**Cơ chế Flash Messages:**
```
Lần 1: Session::flash('success', 'Đăng nhập thành công!')
       → $_SESSION['flash_success'] = 'Đăng nhập thành công!'

Lần 2: View hiển thị message
       → echo Session::getFlash('success')

Lần 3: getFlash() xóa message
       → unset($_SESSION['flash_success'])
       
→ Message chỉ hiển thị 1 lần, sau đó tự động xóa
```

---

### **5. app/core/Database.php** - Kết Nối DB

**Singleton Pattern** - Chỉ tạo 1 kết nối duy nhất
```php
$db = Database::getInstance() ← Lấy instance duy nhất

// Methods chính:
$db->query($sql, $params)    ← Thực thi query với Prepared Statements
$result->fetch()             ← Lấy 1 row
$result->fetchAll()          ← Lấy tất cả rows
$result->rowCount()          ← Số lượng rows bị ảnh hưởng
```

**Ví dụ:**
```php
// Prevent SQL Injection
$user = $db->query(
    "SELECT * FROM tai_khoan WHERE Tai_khoan = ?", 
    [$username]
)->fetch();
```

---

### **6. app/controllers/Controller.php** - Base Controller

**Các method chính:**
```php
$this->view($viewName, $data)    ← Load view & hiển thị
$this->model($modelName)         ← Load model
$this->json($data, $statusCode)  ← Trả về JSON (AJAX)
$this->validate($data, $rules)   ← Validate input
$this->sanitize($data)           ← Xóa ký tự đặc biệt
$this->isMethod($method)         ← Kiểm tra HTTP method
```

---

### **7. app/controllers/AuthController.php** - Xử Lý Auth

**Methods chính:**

#### **login() - Đăng nhập**
```
GET  → Hiển thị form đăng nhập
POST → Xử lý đăng nhập

Quy trình:
1. Validate: username, password required
2. Rate limiting: 5 lần/5 phút/IP
3. Query DB tìm user
4. password_verify() kiểm tra password
5. Kiểm tra trạng thái (active/banned)
6. Session::login() lưu user
7. Redirect theo role (admin/warehouse/customer)
```

#### **register() - Đăng ký**
```
GET  → Hiển thị form đăng ký
POST → Xử lý đăng ký

Quy trình:
1. Validate: username, email, password, fullname, phone, address
2. Kiểm tra username/email đã tồn tại
3. Hash password: password_hash($password, PASSWORD_DEFAULT)
4. INSERT vào DB
5. Session::login() tự động đăng nhập
6. Redirect → trang chủ
```

#### **logout() - Đăng xuất**
```
POST → Xóa session & redirect

Quy trình:
1. Session::logout() xóa user info
2. session_destroy() xóa toàn bộ session
3. Redirect → trang chủ
```

---

### **8. app/controllers/HomeController.php** - Trang Chủ

#### **index() - Trang Chủ**
```
GET  → Hiển thị trang chủ

Quy trình:
1. Lấy danh sách categories: getCategoriesTree()
2. Lấy 8 sản phẩm bán chạy: getBestSellers(8)
3. Lấy 12 sản phẩm mới: getLatestProducts(12)
4. Truyền data vào view
5. Hiển thị customer/home.php
```

---

### **9. app/models/User.php** - Quản Lý User

**Methods chính:**
```php
login($username, $password)     ← Kiểm tra đăng nhập
findByUsername($username)       ← Tìm user theo username
findByEmail($email)             ← Tìm user theo email
findById($id)                   ← Tìm user theo ID
create($data)                   ← Tạo user mới
update($id, $data)              ← Cập nhật thông tin
```

---

### **10. app/models/Model.php** - Base Model

**Các method CRUD cơ bản:**
```php
$this->getAll($conditions, $orderBy, $limit)  ← Lấy tất cả
$this->findById($id)                          ← Tìm 1 record
$this->create($data)                          ← Tạo record
$this->update($id, $data)                     ← Cập nhật
$this->delete($id)                            ← Xóa
```

---

### **11. app/views/layouts/header.php** - Header (Thanh trên cùng)

**Cấu trúc:**
```html
┌─────────────────────────────────────────┐
│ Top Bar: Giao hàng nhanh | Hotline      │
├─────────────────────────────────────────┤
│ Logo | Search Bar | [👤] [🛒] [☰]      │
├─────────────────────────────────────────┤
│ Categories Menu: Sữa | Rau | Thịt...   │
└─────────────────────────────────────────┘
```

**Nút USER (👤):**
```php
<?php if (Session::isLoggedIn()): ?>
    <!-- Đã login: Link đến /admin/users -->
    <a href="<?= BASE_URL ?>/admin/users" class="action-btn">
        <i class="fas fa-user"></i>
    </a>
<?php else: ?>
    <!-- Chưa login: Link đến /auth/login -->
    <a href="<?= BASE_URL ?>/auth/login" class="action-btn">
        <i class="fas fa-user"></i>
    </a>
<?php endif; ?>
```

---

### **12. app/views/auth/auth_modal.php** - Modal Auth

**Cấu trúc:**
```html
IF SESSION['user'] exists:
    ┌─────────────────┐
    │ Xin chào, [name]│
    │ Email: ...      │
    │ [Xem thông tin] │
    │ [Đăng xuất]     │
    └─────────────────┘

ELSE:
    ┌──────────────────────┐
    │ [Đăng nhập] [Đăng ký]│
    ├──────────────────────┤
    │ Đăng nhập:           │
    │ [Tài khoản/Email]    │
    │ [Mật khẩu]           │
    │ [Đăng nhập]          │
    │ + [Đăng ký]          │
    ├──────────────────────┤
    │ Đăng ký:             │
    │ [Tài khoản]          │
    │ [Email]              │
    │ [Mật khẩu]           │
    │ [Đăng ký]            │
    │ + [Đăng nhập]        │
    └──────────────────────┘
```

**JavaScript xử lý:**
```javascript
- Click nút user → Mở modal
- Click overlay/close → Đóng modal
- Click tab "Đăng nhập"/"Đăng ký" → Chuyển tab
- Form POST tới AuthController
```

---

### **13. app/views/customer/home.php** - Trang Chủ

**Cấu trúc:**
```html
1. Hero Slider (Banner chính)
2. Danh mục sản phẩm
3. Sản phẩm bán chạy
4. Sản phẩm mới
5. Footer
```

---

### **14. public/assets/js/main.js** - JavaScript Chính

**Các function:**
```javascript
initMobileMenu()      ← Menu mobile responsive
initScrollToTop()     ← Nút cuộn lên đầu
initDropdownMenus()   ← Menu dropdown categories
initSearchFocus()     ← Focus search bar
```

---

### **15. app/helpers.php** - Helper Functions

```php
asset($path)          ← Tạo URL đến assets
                         VD: asset('img/logo.png') 
                         → /public/assets/img/logo.png

get($key, $default)   ← Lấy từ $_GET
post($key, $default)  ← Lấy từ $_POST
redirect($path)       ← Chuyển hướng (header Location)
formatPrice($amount)  ← Format giá tiền: 100000 → 100.000đ
```

---

## 🔐 Quy Trình Bảo Mật

### **1. SQL Injection Prevention**
```php
❌ Sai:
$user = $db->query("SELECT * FROM users WHERE id = " . $id);

✅ Đúng (Prepared Statements):
$user = $db->query("SELECT * FROM users WHERE id = ?", [$id]);
```

### **2. Password Security**
```php
// Đăng ký: Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);
INSERT INTO tai_khoan (Mat_khau) VALUES ($hashed);

// Đăng nhập: Verify password
if (password_verify($inputPassword, $storedHash)) {
    // Password đúng
}
```

### **3. Session Security**
```php
ini_set('session.cookie_httponly', 1);  ← JS không đọc cookie
ini_set('session.use_only_cookies', 1); ← Chỉ dùng cookies
session_regenerate_id(true);            ← Tránh session fixation
```

### **4. Rate Limiting (Brute Force Protection)**
```php
// 5 lần đăng nhập sai trong 5 phút → block
Middleware::rateLimit('login_' . $ip, 5, 300)
```

### **5. Input Validation & Sanitization**
```php
// Validate: required, type, length
$this->validate($_POST, [
    'username' => 'required|min:4|max:100',
    'email' => 'required|email',
    'password' => 'required|min:6'
]);

// Sanitize: xóa ký tự đặc biệt
$this->sanitize($_POST)
→ filter_var($value, FILTER_SANITIZE_STRING)
```

---

## 📊 Database Schema (Table: tai_khoan)

```sql
CREATE TABLE tai_khoan (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Tai_khoan VARCHAR(100) UNIQUE NOT NULL,
    Email VARCHAR(150) UNIQUE NOT NULL,
    Mat_khau VARCHAR(255) NOT NULL,          ← Hashed password
    Ho_ten VARCHAR(200),
    Sdt VARCHAR(20),
    Dia_chi TEXT,
    Phan_quyen ENUM('KH', 'ADMIN', 'QUAN_LY_KHO'),  ← Role
    Trang_thai ENUM('active', 'banned', 'inactive'), ← Status
    Ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Cap_nhat_cuoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 🎯 Tóm Tắt Luồng Đăng Nhập/Đăng Ký

```
┌─────────────────────────────────────────────────────────────┐
│                      CHƯA ĐĂNG NHẬP                         │
├─────────────────────────────────────────────────────────────┤
│ Click nút USER (👤)                                         │
│         ↓                                                   │
│ Hiển thị Modal Auth (auth_modal.php)                        │
│         ↓                                                   │
│ ┌─────────────────────────────────────────────────────────┐│
│ │ Tab Đăng nhập:      │ Tab Đăng ký:                      ││
│ │ [Username/Email]    │ [Username]                        ││
│ │ [Password]          │ [Email]                           ││
│ │ [Đăng nhập]         │ [Password]                        ││
│ │                     │ [Fullname]                        ││
│ │                     │ [Phone]                           ││
│ │                     │ [Address]                         ││
│ │                     │ [Đăng ký]                         ││
│ └─────────────────────────────────────────────────────────┘│
│         ↓ (POST)                    ↓ (POST)                │
│    /auth/login              /auth/register                  │
│    AuthController           AuthController                  │
│    ->login()                ->register()                    │
│         ↓                        ↓                          │
│    Validate + Hash        Validate + Hash                  │
│    Query DB + Verify      INSERT + Auto Login              │
│    Session::login()       Session::login()                 │
│         ↓                        ↓                          │
│    Redirect theo role    Redirect → /                      │
│    (ADMIN/KHO/home)                                        │
│         ↓                                                   │
└─────────────────────────────────────────────────────────────┘
         ↓
┌─────────────────────────────────────────────────────────────┐
│                      ĐÃ ĐĂNG NHẬP                           │
├─────────────────────────────────────────────────────────────┤
│ Click nút USER (👤)                                         │
│         ↓                                                   │
│ Hiển thị User Info Card (auth_modal.php)                    │
│         ↓                                                   │
│ ┌─────────────────────────────────────────────────────────┐│
│ │ Xin chào, [Username]                                  ││
│ │ Email: user@example.com                               ││
│ │ [Xem thông tin] [Đăng xuất]                          ││
│ └─────────────────────────────────────────────────────────┘│
│         ↓ (Đăng xuất POST)                                  │
│    /auth/logout                                            │
│    AuthController->logout()                                │
│         ↓                                                   │
│    Session::logout()                                       │
│    session_destroy()                                       │
│    Redirect → /                                            │
│         ↓                                                   │
└─────────────────────────────────────────────────────────────┘
         ↓
    Quay lại CHƯA ĐĂNG NHẬP
```

---

## 🚀 Quy Trình Request (Bước Chi Tiết)

### **Ví dụ 1: User đăng nhập**

```
1. User vào http://localhost/sieu_thi/public/
2. index.php được load
3. Khởi động Session::start()
4. $app = new App()
5. parseUrl() → URL rỗng [] (trang chủ)
6. handleRouting([]) → HomeController->index()
7. HomeController load data (categories, products)
8. $this->view('customer/home', $data)
9. home.php được load, hiển thị trang chủ
10. Trong header.php: Session::isLoggedIn() = false
11. Nút USER link tới /auth/login
12. Trang chủ hiển thị, user click nút USER
13. Browser redirect tới /auth/login
14. parseUrl() → ['auth', 'login']
15. handleRouting(['auth', 'login']) → AuthController->login()
16. login() GET → Hiển thị form đăng nhập
17. User nhập username & password, click "Đăng nhập"
18. Form POST tới /auth/login
19. AuthController->login() POST
20. Validate → Sanitize → Rate limit → Query DB
21. password_verify() thành công
22. Session::login($user) → $_SESSION['user'] = $user
23. Redirect theo role
24. User nhìn thấy trang chủ
25. Trang load lại: Session::isLoggedIn() = true
26. Nút USER link tới /admin/users hoặc hiển thị modal với user info
27. User click nút USER
28. Modal hiển thị: "Xin chào, [Username]"
```

### **Ví dụ 2: User đăng ký**

```
1-11. (Giống như trên, nhưng user click tab "Đăng ký")
12. User nhìn thấy form đăng ký
13. Nhập username, email, password, fullname, phone, address
14. Click "Đăng ký"
15. Form POST tới /auth/register
16. AuthController->register() POST
17. Validate tất cả fields
18. Kiểm tra username/email đã tồn tại
19. Hash password + INSERT vào DB
20. User ID được trả về
21. findById($userId) lấy user info mới
22. Session::login($newUser)
23. Log hoạt động: Middleware::logActivity('register', ...)
24. Redirect → / (trang chủ)
25. User được tự động đăng nhập
26. Trang chủ hiển thị, modal đóng
27. Nút USER hiện card user info
28. User có thể click "Đăng xuất"
```

---

## 📝 Tóm Tắt Các Hàm & File

| File/Class | Chức Năng | Method/Hàm Chính |
|---|---|---|
| **public/index.php** | Entry point | Load config, start session, init router |
| **config/config.php** | Cấu hình & autoload | Autoloader, constants |
| **App.php** | Router | parseUrl(), handleRouting(), callController() |
| **Session.php** | Quản lý session | start(), set(), get(), login(), logout() |
| **Database.php** | Kết nối DB | getInstance(), query(), fetch() |
| **Controller.php** | Base controller | view(), model(), json(), validate() |
| **AuthController.php** | Xử lý auth | login(), register(), logout() |
| **HomeController.php** | Trang chủ | index() |
| **User.php** | User model | login(), register(), findBy...() |
| **Model.php** | Base model | getAll(), findById(), create(), update() |
| **header.php** | Header & nav | Hiển thị nút USER, menu categories |
| **auth_modal.php** | Modal auth | Form login/register hoặc user info |
| **home.php** | Trang chủ | Hero slider, categories, products |
| **main.js** | JavaScript | initMobileMenu(), initScrollToTop(), ... |
| **helpers.php** | Helper functions | asset(), redirect(), formatPrice() |

---

## ✅ Kết Luận

**Kiến trúc MVC (Model-View-Controller):**
- **Model** (User.php, Product.php, ...): Quản lý dữ liệu & logic DB
- **View** (home.php, auth_modal.php, ...): Hiển thị giao diện HTML
- **Controller** (AuthController, HomeController, ...): Xử lý logic & liên kết Model-View

**Luồng chính:**
```
Request → App Router → Controller → Model (DB) → Controller → View → HTML Response
```

**Authentication Flow:**
```
Chưa login → Click User → Modal → Đăng nhập/Đăng ký → Session::login() → Redirect → Đã login
```

---

**Ngày tạo**: 27/12/2025
**Phiên bản**: 1.0
