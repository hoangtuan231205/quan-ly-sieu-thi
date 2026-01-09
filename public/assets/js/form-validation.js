
/**
 * Lớp xác thực form - FreshMart
 * Kiểm tra form real-time với thông báo lỗi thân thiện
 */
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (!this.form) {
            console.error(`Không tìm thấy form với id "${formId}"`);
            return;
        }
        this.init();
    }

    init() {
        const inputs = this.form.querySelectorAll('input, textarea, select');

        // Thêm event listener blur để xác thực
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearError(input));
        });

        // Thêm event listener submit
        this.form.addEventListener('submit', (e) => this.validateForm(e));
    }

    validateField(field) {
        const rules = field.dataset.validate?.split('|') || [];
        let error = null;

        for (let rule of rules) {
            // Kiểm tra bắt buộc
            if (rule === 'required' && !field.value.trim()) {
                error = 'Trường này là bắt buộc';
                break;
            }

            // Kiểm tra độ dài tối thiểu
            if (rule.startsWith('min:')) {
                const min = parseInt(rule.split(':')[1]);
                if (field.value.length < min) {
                    error = `Tối thiểu ${min} ký tự`;
                    break;
                }
            }

            // Kiểm tra độ dài tối đa
            if (rule.startsWith('max:')) {
                const max = parseInt(rule.split(':')[1]);
                if (field.value.length > max) {
                    error = `Tối đa ${max} ký tự`;
                    break;
                }
            }

            // Kiểm tra email
            if (rule === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value)) {
                    error = 'Email không hợp lệ';
                    break;
                }
            }

            // Kiểm tra số điện thoại
            if (rule === 'phone') {
                const phoneRegex = /^(0|\+84)(3|5|7|8|9)[0-9]{8}$/;
                if (!phoneRegex.test(field.value.replace(/\s/g, ''))) {
                    error = 'Số điện thoại không hợp lệ';
                    break;
                }
            }

            // Kiểm tra số
            if (rule === 'numeric') {
                if (isNaN(field.value)) {
                    error = 'Chỉ được nhập số';
                    break;
                }
            }

            // Kiểm tra phạm vi số
            if (rule.startsWith('numeric_range:')) {
                const [min, max] = rule.split(':')[1].split(',').map(Number);
                const value = Number(field.value);
                if (isNaN(value) || value < min || value > max) {
                    error = `Giá trị phải từ ${min} đến ${max}`;
                    break;
                }
            }

            // Kiểm tra danh sách cho phép
            if (rule.startsWith('in:')) {
                const allowedValues = rule.split(':')[1].split(',');
                if (!allowedValues.includes(field.value)) {
                    error = 'Giá trị không hợp lệ';
                    break;
                }
            }

            // Kiểm tra regex
            if (rule.startsWith('regex:')) {
                const pattern = new RegExp(rule.split(':')[1]);
                if (!pattern.test(field.value)) {
                    error = 'Định dạng không hợp lệ';
                    break;
                }
            }

            // Kiểm tra trùng khớp (xác nhận mật khẩu)
            if (rule.startsWith('match:')) {
                const matchFieldId = rule.split(':')[1];
                const matchField = document.getElementById(matchFieldId);
                if (matchField && field.value !== matchField.value) {
                    error = 'Giá trị không khớp';
                    break;
                }
            }
        }

        if (error) {
            this.showError(field, error);
            return false;
        } else {
            this.showSuccess(field);
            return true;
        }
    }

    showError(field, message) {
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');

        let errorDiv = field.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    showSuccess(field) {
        field.classList.add('is-valid');
        field.classList.remove('is-invalid');

        const errorDiv = field.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.style.display = 'none';
        }
    }

    clearError(field) {
        field.classList.remove('is-invalid', 'is-valid');

        const errorDiv = field.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.style.display = 'none';
        }
    }

    validateForm(e) {
        const inputs = this.form.querySelectorAll('input[data-validate], textarea[data-validate], select[data-validate]');
        let isValid = true;
        let firstInvalidField = null;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
                if (!firstInvalidField) {
                    firstInvalidField = input;
                }
            }
        });

        if (!isValid) {
            e.preventDefault();

            // Cuộn tới trường lỗi đầu tiên
            if (firstInvalidField) {
                firstInvalidField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstInvalidField.focus();
            }

            // Hiển thị tóm tắt lỗi
            this.showErrorSummary();
        }

        return isValid;
    }

    showErrorSummary() {
        const invalidFields = this.form.querySelectorAll('.is-invalid');
        if (invalidFields.length === 0) return;

        // Xóa tóm tắt lỗi cũ
        const existingSummary = this.form.querySelector('.error-summary');
        if (existingSummary) {
            existingSummary.remove();
        }

        // Tạo tóm tắt lỗi
        const summary = document.createElement('div');
        summary.className = 'error-summary alert alert-danger';
        summary.innerHTML = `
            <strong><i class="fas fa-exclamation-triangle me-2"></i>Vui lòng kiểm tra lại:</strong>
            <ul class="mb-0 mt-2">
                ${Array.from(invalidFields).map(field => {
            const label = field.previousElementSibling?.textContent || field.placeholder || 'Trường';
            const error = field.nextElementSibling?.textContent || 'Không hợp lệ';
            return `<li>${label}: ${error}</li>`;
        }).join('')}
            </ul>
        `;

        // Chèn vào đầu form
        this.form.insertBefore(summary, this.form.firstChild);

        // Tự động xóa sau 5 giây
        setTimeout(() => {
            summary.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => summary.remove(), 300);
        }, 5000);
    }

    reset() {
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            this.clearError(input);
        });

        const errorSummary = this.form.querySelector('.error-summary');
        if (errorSummary) {
            errorSummary.remove();
        }
    }
}

// Tự động khởi tạo form có thuộc tính data-validate-form
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('[data-validate-form]');
    forms.forEach(form => {
        new FormValidator(form.id);
    });
});

// Xuất để dùng ở các script khác
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FormValidator;
}
