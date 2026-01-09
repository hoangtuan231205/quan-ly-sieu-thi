<?php /**
 * Auth modal partial: shows login/register forms or user info when logged in.
 */
?>
<div id="authModal" class="auth-modal" aria-hidden="true">
    <div class="auth-overlay" data-close="true"></div>
    <div class="auth-card">
        <button class="auth-close" aria-label="Close">&times;</button>

        <div class="auth-inner">
            <?php if (!empty($_SESSION['user'])): ?>
                <div class="user-info">
                    <h2>Xin chào, <?= htmlspecialchars($_SESSION['user']['username'] ?? $_SESSION['user']['email'] ?? 'Người dùng') ?></h2>
                    <p>Email: <?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?></p>
                    <div class="user-actions">
                        <a class="btn" href="<?= BASE_URL ?>/auth/profile">Xem thông tin</a>
                        <form action="<?= BASE_URL ?>/auth/logout" method="POST" style="display:inline;">
                            <button type="submit" class="btn btn-logout">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-tabs">
                    <button class="tab-btn active" data-tab="login">Đăng nhập</button>
                    <button class="tab-btn" data-tab="register">Đăng ký</button>
                </div>

                <div class="auth-contents">
                    <div id="login" class="auth-pane active">
                        <h2>Đăng nhập</h2>
                        <form action="<?= BASE_URL ?>/auth/login" method="POST" class="auth-form">
                            <input type="text" name="identifier" placeholder="Tài khoản hoặc Email" required>
                            <input type="password" name="password" placeholder="Mật khẩu" required>
                            <button type="submit" class="btn btn-primary">Đăng nhập</button>
                        </form>
                        <p class="switch">Chưa có tài khoản? <a href="#" class="switch-to" data-to="register">Đăng ký</a></p>
                    </div>

                    <div id="register" class="auth-pane">
                        <h2>Đăng ký</h2>
                        <form action="<?= BASE_URL ?>/auth/register" method="POST" class="auth-form">
                            <input type="text" name="username" placeholder="Tài khoản" required>
                            <input type="email" name="email" placeholder="Email" required>
                            <input type="password" name="password" placeholder="Mật khẩu" required>
                            <button type="submit" class="btn btn-primary">Đăng ký</button>
                        </form>
                        <p class="switch">Đã có tài khoản? <a href="#" class="switch-to" data-to="login">Đăng nhập</a></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const authModal = document.getElementById('authModal');
    // Bind only to elements explicitly marked to open the auth modal.
    // This prevents intercepting the header's account link so it can navigate.
    const openBtns = document.querySelectorAll('.open-auth-modal');
    const overlay = authModal.querySelector('.auth-overlay');
    const closeBtn = authModal.querySelector('.auth-close');
    const tabBtns = authModal.querySelectorAll('.tab-btn');
    const panes = authModal.querySelectorAll('.auth-pane');

    function openModal(tab) {
        authModal.setAttribute('aria-hidden', 'false');
        authModal.classList.add('open');
        if (tab) switchTo(tab);
    }

    function closeModal() {
        authModal.setAttribute('aria-hidden', 'true');
        authModal.classList.remove('open');
    }

    function switchTo(name) {
        tabBtns.forEach(b => b.classList.toggle('active', b.dataset.tab === name));
        panes.forEach(p => p.classList.toggle('active', p.id === name));
    }

    openBtns.forEach(btn => btn.addEventListener('click', function (e) {
        e.preventDefault();
        openModal('login');
    }));

    overlay.addEventListener('click', closeModal);
    closeBtn.addEventListener('click', closeModal);

    tabBtns.forEach(b => b.addEventListener('click', () => switchTo(b.dataset.tab)));
    authModal.querySelectorAll('.switch-to').forEach(a => a.addEventListener('click', function (e) {
        e.preventDefault();
        switchTo(this.dataset.to);
    }));
});
</script>
