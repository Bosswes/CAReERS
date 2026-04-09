// ========== MAIN APP MODULE ==========
const App = (function() {
    'use strict';
    
    function init() {
        console.log('CAReERS - Job Recommendation System Starting...');
        
        // Check for saved user session
        const savedUser = sessionStorage.getItem('currentUser');
        if (savedUser) {
            try {
                const user = JSON.parse(savedUser);
                API.getUser().then(response => {
                    if (response && response.user) {
                        document.getElementById('auth-container').style.display = 'none';
                        document.getElementById('app-container').style.display = 'block';
                        UI.updateSidebarUserInfo(user);
                        UI.setupSidebarNavigation(user);
                        UI.showDashboard();
                    } else {
                        sessionStorage.removeItem('currentUser');
                    }
                }).catch(() => {
                    sessionStorage.removeItem('currentUser');
                });
            } catch (error) {
                console.error('Error loading saved session:', error);
                sessionStorage.removeItem('currentUser');
            }
        }
    }
    
    return { init };
})();

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});