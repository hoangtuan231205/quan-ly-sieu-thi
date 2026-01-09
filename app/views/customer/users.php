<!-- Thông tin tài khoản cá nhân -->
<div class="account-info-container">
	<h2>Thông tin tài khoản</h2>
	<form id="userInfoForm" class="account-info-form" autocomplete="off">
		<div class="form-group">
			<label>Họ tên</label>
			<input type="text" name="fullname" value="Nguyễn Văn A" disabled>
		</div>
		<div class="form-group">
			<label>Email</label>
			<input type="email" name="email" value="nguyenvana@email.com" disabled>
		</div>
		<div class="form-group">
			<label>Số điện thoại</label>
			<input type="text" name="phone" value="0123456789" disabled>
		</div>
		<div class="form-group">
			<label>Địa chỉ</label>
			<input type="text" name="address" value="123 Đường ABC, Quận 1, TP.HCM" disabled>
		</div>
		<div class="account-info-actions">
			<button type="button" id="editInfoBtn" class="btn-account btn-update">Cập nhật thông tin</button>
			<button type="button" id="changePasswordBtn" class="btn-account btn-password">Đổi mật khẩu</button>
		</div>
	</form>

	<!-- Form cập nhật thông tin (ẩn mặc định) -->
	<form id="editInfoForm" class="account-edit-form" style="display:none;" autocomplete="off">
		<div class="form-group">
			<label>Họ tên</label>
			<input type="text" name="fullname" value="Nguyễn Văn A">
		</div>
		<div class="form-group">
			<label>Email</label>
			<input type="email" name="email" value="nguyenvana@email.com">
		</div>
		<div class="form-group">
			<label>Số điện thoại</label>
			<input type="text" name="phone" value="0123456789">
		</div>
		<div class="form-group">
			<label>Địa chỉ</label>
			<input type="text" name="address" value="123 Đường ABC, Quận 1, TP.HCM">
		</div>
		<div class="account-info-actions">
			<button type="submit" class="btn-account btn-save">Lưu thay đổi</button>
			<button type="button" id="cancelEditBtn" class="btn-account btn-cancel">Hủy</button>
		</div>
	</form>

	<!-- Form đổi mật khẩu (ẩn mặc định) -->
	<form id="changePasswordForm" class="account-password-form" style="display:none;" autocomplete="off">
		<div class="form-group">
			<label>Mật khẩu cũ</label>
			<input type="password" name="old_password" required>
		</div>
		<div class="form-group">
			<label>Mật khẩu mới</label>
			<input type="password" name="new_password" required>
		</div>
		<div class="form-group">
			<label>Xác nhận mật khẩu mới</label>
			<input type="password" name="confirm_password" required>
		</div>
		<div class="account-info-actions">
			<button type="submit" class="btn-account btn-save">Đổi mật khẩu</button>
			<button type="button" id="cancelPasswordBtn" class="btn-account btn-cancel">Hủy</button>
		</div>
		<div id="passwordError" class="form-error" style="display:none;color:var(--color-danger);margin-top:8px;"></div>
	</form>
</div>

<script>
// Hiện form cập nhật thông tin
document.getElementById('editInfoBtn').onclick = function() {
	document.getElementById('userInfoForm').style.display = 'none';
	document.getElementById('editInfoForm').style.display = 'block';
	document.getElementById('changePasswordForm').style.display = 'none';
};
// Hủy cập nhật
document.getElementById('cancelEditBtn').onclick = function() {
	document.getElementById('userInfoForm').style.display = 'block';
	document.getElementById('editInfoForm').style.display = 'none';
};
// Hiện form đổi mật khẩu
document.getElementById('changePasswordBtn').onclick = function() {
	document.getElementById('userInfoForm').style.display = 'none';
	document.getElementById('editInfoForm').style.display = 'none';
	document.getElementById('changePasswordForm').style.display = 'block';
};
// Hủy đổi mật khẩu
document.getElementById('cancelPasswordBtn').onclick = function() {
	document.getElementById('userInfoForm').style.display = 'block';
	document.getElementById('changePasswordForm').style.display = 'none';
};
// Kiểm tra xác nhận mật khẩu mới và mật khẩu cũ (demo, cần thay bằng ajax thực tế)
document.getElementById('changePasswordForm').onsubmit = function(e) {
	e.preventDefault();
	var oldPass = this.old_password.value;
	var newPass = this.new_password.value;
	var confirmPass = this.confirm_password.value;
	var errorDiv = document.getElementById('passwordError');
	errorDiv.style.display = 'none';
	// Giả lập mật khẩu cũ là '123456' (thực tế phải kiểm tra server)
	if (oldPass !== '123456') {
		errorDiv.textContent = 'Mật khẩu cũ không đúng!';
		errorDiv.style.display = 'block';
		return false;
	}
	if (newPass !== confirmPass) {
		errorDiv.textContent = 'Xác nhận mật khẩu mới không khớp!';
		errorDiv.style.display = 'block';
		return false;
	}
	alert('Đổi mật khẩu thành công!');
	document.getElementById('userInfoForm').style.display = 'block';
	document.getElementById('changePasswordForm').style.display = 'none';
	return false;
};
</script>