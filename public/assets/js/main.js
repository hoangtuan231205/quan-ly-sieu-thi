/**
 * =============================================================================
 * FRESHMART - MAIN JAVASCRIPT
 * =============================================================================
 */

// =============================================================================
// 1. KHỞI TẠO - Chạy khi trang load xong
// =============================================================================

document.addEventListener('DOMContentLoaded', function () {

    // Khởi tạo các components (CHỈ DESKTOP)
    initScrollToTop();
    initDropdownMenus();
    initSearchFocus();
    initLazyLoading();
    initUserDropdown();

    console.log('✅ FreshMart khởi tạo thành công!');

});

// =============================================================================
// 2. NÚT LÊN ĐẦU TRANG - Nút cuộn lên đầu
// =============================================================================

function initScrollToTop() {
    const scrollBtn = document.getElementById('scrollToTop');

    if (!scrollBtn) return;

    // Hiện/ẩn button khi cuộn
    window.addEventListener('scroll', function () {
        if (window.pageYOffset > 300) {
            scrollBtn.classList.add('show');
        } else {
            scrollBtn.classList.remove('show');
        }
    });

    // Cuộn lên đầu khi click
    scrollBtn.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// =============================================================================
// 4. MENU DROPDOWN - Xử lý menu thả xuống (Desktop)
// =============================================================================

function initDropdownMenus() {
    const dropdownItems = document.querySelectorAll('.has-dropdown');

    dropdownItems.forEach(item => {
        const dropdownMenu = item.querySelector('.dropdown-menu');

        if (!dropdownMenu) return;

        // Thêm animation khi hover
        item.addEventListener('mouseenter', function () {
            dropdownMenu.style.display = 'block';

            // Kích hoạt animation
            setTimeout(() => {
                dropdownMenu.style.opacity = '1';
                dropdownMenu.style.visibility = 'visible';
                dropdownMenu.style.transform = 'translateY(0)';
            }, 10);
        });

        item.addEventListener('mouseleave', function () {
            dropdownMenu.style.opacity = '0';
            dropdownMenu.style.visibility = 'hidden';
            dropdownMenu.style.transform = 'translateY(-10px)';

            setTimeout(() => {
                if (dropdownMenu.style.opacity === '0') {
                    dropdownMenu.style.display = 'none';
                }
            }, 300);
        });
    });
}

// =============================================================================
// 5. FOCUS TÌM KIẾM - Animation cho ô tìm kiếm
// =============================================================================

function initSearchFocus() {
    const searchInput = document.querySelector('.search-input');

    if (!searchInput) return;

    searchInput.addEventListener('focus', function () {
        this.parentElement.style.transform = 'scale(1.02)';
    });

    searchInput.addEventListener('blur', function () {
        this.parentElement.style.transform = 'scale(1)';
    });
}

// =============================================================================
// 6. DROPDOWN NGƯỜI DÙNG - Click để toggle menu profile
// =============================================================================

function initUserDropdown() {
    // Hỗ trợ cả .user-dropdown và .user-menu.dropdown
    const userDropdown = document.querySelector('.user-dropdown, .user-menu.dropdown');

    if (!userDropdown) return;

    const actionBtn = userDropdown.querySelector('.action-btn');
    const dropdownMenu = userDropdown.querySelector('.dropdown-menu');

    if (!actionBtn || !dropdownMenu) return;

    // Toggle dropdown khi click
    actionBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const isVisible = dropdownMenu.style.display === 'block';

        if (isVisible) {
            dropdownMenu.style.display = 'none';
        } else {
            dropdownMenu.style.display = 'block';
        }
    });

    // Đóng dropdown khi click bên ngoài
    document.addEventListener('click', function (e) {
        if (!userDropdown.contains(e.target)) {
            dropdownMenu.style.display = 'none';
        }
    });

    // Ngăn dropdown đóng khi click bên trong
    dropdownMenu.addEventListener('click', function (e) {
        e.stopPropagation();
    });
}

// =============================================================================
// 7. THÊM VÀO GIỎ HÀNG - Xử lý thêm sản phẩm vào giỏ
// =============================================================================

function addToCart(productId, quantity = 1) {
    // Lấy CSRF token từ meta tag
    const csrfToken = document.querySelector('meta[name="csrf_token"]')?.content || '';
    let baseUrl = document.querySelector('meta[name="base_url"]')?.content || '';

    // Fallback: nếu baseUrl rỗng, tự tạo từ URL hiện tại
    if (!baseUrl) {
        const pathname = window.location.pathname;
        if (pathname.includes('/sieu_thi')) {
            baseUrl = '/sieu_thi';
        } else {
            baseUrl = '';
        }
    }

    // Tạo FormData
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('csrf_token', csrfToken);

    // Gửi AJAX request
    fetch(baseUrl + '/public/cart/add', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật badge giỏ hàng
                updateCartBadge(data.cart_count);

                // Hiển thị thông báo
                showNotification('Đã thêm vào giỏ hàng!', 'success');

                // Animation cho nút giỏ hàng
                animateCartButton();
            } else {
                showNotification(data.message || 'Có lỗi xảy ra!', 'error');
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            showNotification('Không thể thêm vào giỏ hàng!', 'error');
        });
}

// =============================================================================
// 8. CẬP NHẬT BADGE GIỎ HÀNG - Cập nhật số lượng hiển thị
// =============================================================================

function updateCartBadge(count) {
    const cartBadge = document.querySelector('.cart-badge');

    if (cartBadge) {
        cartBadge.textContent = count;

        // Animation bằng CSS class
        cartBadge.classList.add('cart-updated');
        setTimeout(() => {
            cartBadge.classList.remove('cart-updated');
        }, 500);
    }
}

// =============================================================================
// 9. ANIMATION GIỎ HÀNG - Hiệu ứng khi thêm vào giỏ
// =============================================================================

function animateCartButton() {
    const cartBtn = document.querySelector('.cart-btn');

    if (cartBtn) {
        cartBtn.style.animation = 'pulse 0.5s ease';
        setTimeout(() => {
            cartBtn.style.animation = '';
        }, 500);
    }
}

// =============================================================================
// 10. CÁC HÀM TIỆN ÍCH
// =============================================================================

// Định dạng giá tiền
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// Debounce function (cho tìm kiếm)
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function (cho cuộn trang)
function throttle(func, limit) {
    let inThrottle;
    return function () {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
}

// =============================================================================
// 11. LAZY LOADING ẢNH - Tối ưu hóa hiệu suất tải ảnh
// =============================================================================

function initLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');

    if (lazyImages.length === 0) return;

    // Theo dõi ảnh đã tải để tránh tải lại
    const loadedImages = new Set();

    // Theo dõi element đã xử lý để tránh xử lý lại
    const processedElements = new WeakSet();

    // Giới hạn số ảnh tải cùng lúc để tránh quá tải RAM
    let currentlyLoading = 0;
    const MAX_CONCURRENT_LOADS = 6;

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;

                // QUAN TRỌNG: Ngừng theo dõi ngay để tránh trigger lại
                observer.unobserve(img);

                // Bỏ qua nếu element đã xử lý
                if (processedElements.has(img)) {
                    return;
                }

                // Đánh dấu element đã xử lý
                processedElements.add(img);

                const imgSrc = img.dataset.src;

                // Bỏ qua nếu đã tải hoặc không có source
                if (loadedImages.has(imgSrc) || !imgSrc) {
                    img.classList.remove('skeleton');
                    return;
                }

                // Bỏ qua nếu đạt giới hạn đồng thời
                if (currentlyLoading >= MAX_CONCURRENT_LOADS) {
                    loadedImages.add(imgSrc);
                    img.classList.remove('skeleton');
                    return;
                }

                currentlyLoading++;
                loadedImages.add(imgSrc);

                const tempImg = new Image();

                // Timeout để tránh treo
                const loadTimeout = setTimeout(() => {
                    tempImg.onload = null;
                    tempImg.onerror = null;
                    tempImg.src = '';
                    currentlyLoading--;

                    const fallback = img.getAttribute('onerror')?.match(/'([^']+)'/)?.[1];
                    if (fallback && img.src !== fallback) {
                        img.src = fallback;
                    }
                    img.classList.remove('skeleton');
                }, 10000);

                tempImg.onload = function () {
                    clearTimeout(loadTimeout);
                    img.src = imgSrc;
                    img.classList.remove('skeleton');
                    img.classList.add('loaded');
                    img.removeAttribute('data-src');
                    currentlyLoading--;

                    tempImg.onload = null;
                    tempImg.onerror = null;
                };

                tempImg.onerror = function () {
                    clearTimeout(loadTimeout);
                    currentlyLoading--;

                    const fallback = img.getAttribute('onerror')?.match(/'([^']+)'/)?.[1];
                    if (fallback && img.src !== fallback) {
                        img.src = fallback;
                    }
                    img.classList.remove('skeleton');
                    tempImg.onload = null;
                    tempImg.onerror = null;
                };

                tempImg.src = imgSrc;
            }
        });
    }, { rootMargin: '50px 0px', threshold: 0.01 });

    lazyImages.forEach(img => imageObserver.observe(img));
}

// =============================================================================
// 12. CUỘN MƯỢT - Cuộn mượt cho anchor links
// =============================================================================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');

        if (href === '#') return;

        e.preventDefault();

        const target = document.querySelector(href);

        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// =============================================================================
// 13. CONSOLE BRANDING - Thông điệp thương hiệu trong console
// =============================================================================

console.log(
    '%c🌿 FreshMart - Siêu thị thực phẩm tươi sống 🌿',
    'color: #496C2C; font-size: 20px; font-weight: bold; padding: 10px;'
);
console.log(
    '%cPhát triển bởi ❤️ FreshMart Team',
    'color: #999; font-size: 12px;'
);

// =============================================================================
// 14. SLIDER BANNER - Slider tự động cho banner
// =============================================================================

let currentSlide = 0;
let slideInterval;

function initHeroSlider() {
    const slides = document.querySelectorAll('.hero-slide-full');
    const dots = document.querySelectorAll('.dot');
    const container = document.querySelector('.slider-container');

    if (slides.length <= 1) return;

    console.log('🎡 FreshMart Slider đã khởi tạo - 3 giây/slide');

    function showSlide(index) {
        // Xóa class active ở slide hiện tại
        slides[currentSlide].classList.remove('active');
        if (dots[currentSlide]) dots[currentSlide].classList.remove('active');

        // Tính index mới
        currentSlide = (index + slides.length) % slides.length;

        // Thêm class active cho slide mới
        slides[currentSlide].classList.add('active');
        if (dots[currentSlide]) dots[currentSlide].classList.add('active');
    }

    // Các hàm global để HTML gọi được (onclick)
    window.nextSlide = function () {
        showSlide(currentSlide + 1);
    };

    window.prevSlide = function () {
        showSlide(currentSlide - 1);
    };

    window.goToSlide = function (index) {
        if (index === currentSlide) return;
        showSlide(index);
        resetTimer();
    };

    function startTimer() {
        stopTimer();
        slideInterval = setInterval(window.nextSlide, 3000);
    }

    function stopTimer() {
        if (slideInterval) clearInterval(slideInterval);
    }

    function resetTimer() {
        stopTimer();
        startTimer();
    }

    // Tạm dừng khi hover
    if (container) {
        container.addEventListener('mouseenter', stopTimer);
        container.addEventListener('mouseleave', startTimer);
    }

    // Chạy slide
    console.log('🚀 Banner auto-slide đã khởi động');
    startTimer();
}

// Khởi tạo slider
document.addEventListener('DOMContentLoaded', initHeroSlider);

// =============================================================================
// 15. MUA NGAY - Thêm vào giỏ và checkout ngay
// =============================================================================

/**
 * Mua ngay - Mua 1 sản phẩm và chuyển thẳng tới checkout
 * @param {number} productId - ID sản phẩm
 * @param {number} quantity - Số lượng (sẽ force = 1)
 */
function buyNow(productId, quantity = 1) {
    const csrfToken = document.querySelector('meta[name="csrf_token"]')?.content || '';
    const baseUrl = document.querySelector('meta[name="base_url"]')?.content || '';

    // QUAN TRỌNG: Force quantity = 1 để chỉ mua 1 sản phẩm
    quantity = 1;

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('csrf_token', csrfToken);

    // Gọi /cart/buyNow thay vì /cart/add
    fetch(baseUrl + '/public/cart/buyNow', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Chuyển thẳng tới checkout
                window.location.href = baseUrl + '/public/checkout';
            } else {
                if (typeof showNotification === 'function') {
                    showNotification(data.message || 'Có lỗi xảy ra!', 'error');
                } else {
                    alert(data.message || 'Có lỗi xảy ra!');
                }
            }
        })
        .catch(error => {
            console.error('Lỗi Mua Ngay:', error);
            if (typeof showNotification === 'function') {
                showNotification('Có lỗi xảy ra. Vui lòng thử lại.', 'error');
            }
        });
}

// Gán vào window để HTML có thể gọi
window.buyNow = buyNow;
window.addToCart = addToCart;