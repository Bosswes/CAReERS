// ========== AUTHENTICATION MODULE ==========
const Auth = (function() {
    'use strict';
    
    let currentUser = null;
    
    function init() {
        setupEventListeners();
        checkSavedSession();
    }
    
    async function checkSavedSession() {
        try {
            const response = await API.getUser();
            if (response.user) {
                currentUser = response.user;
                sessionStorage.setItem('currentUser', JSON.stringify(currentUser));
                loginSuccess(currentUser);
            } else {
                window.location.href = '/login';
            }
        } catch (error) {
            window.location.href = '/login';
        }
    }
    
    function setupEventListeners() {
        const togglePassword = document.getElementById('toggle-password');
        if (togglePassword) {
            togglePassword.addEventListener('click', togglePasswordVisibility);
        }
        
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', handleLogin);
        }
        
        const forgotLink = document.getElementById('forgot-password-link');
        if (forgotLink) {
            forgotLink.addEventListener('click', (e) => {
                e.preventDefault();
                openForgotPasswordModal();
            });
        }
        
        const logoutBtn = document.getElementById('sidebar-logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', handleLogout);
        }
        
        // Forgot password modal
        const closeForgot = document.getElementById('close-forgot-modal');
        if (closeForgot) closeForgot.addEventListener('click', closeForgotPasswordModal);
        
        const cancelForgot = document.getElementById('cancel-forgot');
        if (cancelForgot) cancelForgot.addEventListener('click', closeForgotPasswordModal);
        
        const forgotForm = document.getElementById('forgot-password-form');
        if (forgotForm) forgotForm.addEventListener('submit', handleForgotPassword);
    }
    
    function togglePasswordVisibility(e) {
        const button = e.currentTarget;
        const input = button.parentElement.querySelector('input');
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    function openForgotPasswordModal() {
        const modal = document.getElementById('forgot-password-modal');
        if (modal) {
            modal.style.display = 'flex';
            document.getElementById('forgot-email').value = '';
        }
    }
    
    function closeForgotPasswordModal() {
        const modal = document.getElementById('forgot-password-modal');
        if (modal) modal.style.display = 'none';
    }
    
    function handleForgotPassword(e) {
        e.preventDefault();
        const email = document.getElementById('forgot-email').value.trim();
        
        if (!email) {
            Utils.showToast('Please enter your email address.', 'warning');
            return;
        }
        
        if (!Utils.isValidEmail(email)) {
            Utils.showToast('Please enter a valid email address.', 'warning');
            return;
        }
        
        Utils.showToast('Password reset link sent! (Demo mode)', 'success');
        closeForgotPasswordModal();
    }
    
    async function handleLogin(e) {
        e.preventDefault();
        
        const username = document.getElementById('login-student-id').value.trim();
        const password = document.getElementById('login-password').value;
        
        if (!username || !password) {
            Utils.showToast('Please enter both username/email and password.', 'warning');
            return;
        }
        
        try {
            Utils.showToast('Logging in...', 'info');
            const response = await API.login(username, password);
            
            if (response.success) {
                currentUser = response.user;
                sessionStorage.setItem('currentUser', JSON.stringify(currentUser));
                if (response.registrationData) {
                    sessionStorage.setItem('registrationData', JSON.stringify(response.registrationData));
                } else {
                    // Clear old data if none returned
                    sessionStorage.removeItem('registrationData');
                }
                loginSuccess(currentUser);
            } else {
                Utils.showToast(response.message || 'Invalid credentials', 'error');
            }
        } catch (error) {
            Utils.showToast(error.message || 'Login failed', 'error');
        }
    }
    
    function loginSuccess(user) {
        Utils.showToast(`Welcome, ${user.name}!`, 'success');
        
        document.getElementById('auth-container').style.display = 'none';
        document.getElementById('app-container').style.display = 'block';
        
        UI.updateSidebarUserInfo(user);
        UI.setupSidebarNavigation(user);
        UI.showDashboard();
    }
    
    async function handleLogout() {
        try {
            await API.logout();
        } catch (error) {
            console.error('Logout error:', error);
        }
        
        sessionStorage.removeItem('currentUser');
        currentUser = null;
        
        window.location.href = '/login';
    }
    
    return {
        init,
        handleLogout
    };
})();

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    UI.init();
    Auth.init();
});