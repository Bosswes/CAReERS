// ========== UI MODULE ==========
const UI = (function() {
    'use strict';
    
    let currentUser = null;
    
    function init() {
        setupEventListeners();
    }
    
    function setupEventListeners() {
        const mobileToggle = document.getElementById('mobile-menu-toggle');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                document.querySelector('.sidebar').classList.add('active');
            });
        }
        
        const sidebarToggle = document.getElementById('sidebar-toggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                document.querySelector('.sidebar').classList.toggle('active');
            });
        }
        
        // Close sidebar on outside click
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const mobileToggle = document.getElementById('mobile-menu-toggle');
            if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('active') &&
                !sidebar.contains(event.target) && event.target !== mobileToggle && !mobileToggle?.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
        
        // Close modals on backdrop click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.style.display = 'none';
                }
            });
        });
    }
    
    function updateSidebarUserInfo(user) {
        if (!user) return;
        
        const avatarInitials = document.getElementById('sidebar-avatar-initials');
        const userName = document.getElementById('sidebar-user-name');
        const userRole = document.getElementById('sidebar-user-role');
        
        if (avatarInitials) avatarInitials.textContent = Utils.getInitials(user.name);
        if (userName) userName.textContent = user.name;
        if (userRole) userRole.textContent = user.role === 'admin' ? 'Administrator' : 'Student';
    }
    
    function setupSidebarNavigation(user) {
        const menu = document.getElementById('sidebar-menu');
        if (!menu) return;
        
        let navItems = [];
        
        if (user.role === 'student') {
            navItems = [
                { href: '#student-dashboard', text: 'Dashboard', icon: 'fas fa-home', section: 'student-dashboard' },
                { href: '#student-profile', text: 'My Profile', icon: 'fas fa-user', section: 'student-profile' },
                { href: '#job-recommendations', text: 'Job Recommendations', icon: 'fas fa-briefcase', section: 'job-recommendations' },
                { href: '#ojt-offerings', text: 'OJT Offerings', icon: 'fas fa-graduation-cap', section: 'ojt-offerings' },
                { href: '#announcements', text: 'Announcements', icon: 'fas fa-bullhorn', section: 'announcements' }
            ];
        } else if (user.role === 'admin') {
            navItems = [
                { href: '#admin-dashboard', text: 'Dashboard', icon: 'fas fa-home', section: 'admin-dashboard' },
                { href: '#user-management', text: 'User Management', icon: 'fas fa-users-cog', section: 'user-management' },
                { href: '#job-management', text: 'Job Management', icon: 'fas fa-clipboard-check', section: 'job-management' },
                { href: '#data-monitoring', text: 'Data Monitoring', icon: 'fas fa-chart-pie', section: 'data-monitoring' },
                { href: '#reports-announcements', text: 'Reports & Announcements', icon: 'fas fa-bullhorn', section: 'reports-announcements' }
            ];
        }
        
        menu.innerHTML = '';
        
        navItems.forEach(item => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.href = item.href;
            a.innerHTML = `<i class="${item.icon}"></i> ${item.text}`;
            a.dataset.section = item.section;
            
            a.addEventListener('click', (e) => {
                e.preventDefault();
                navigateTo(item.section);
                updateActiveNav(a);
                
                if (window.innerWidth <= 768) {
                    document.querySelector('.sidebar').classList.remove('active');
                }
            });
            
            li.appendChild(a);
            menu.appendChild(li);
        });
        
        // Set dashboard active by default
        const firstLink = menu.querySelector('a');
        if (firstLink) firstLink.classList.add('active');
    }
    
    function updateActiveNav(activeLink) {
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.classList.remove('active');
        });
        if (activeLink) activeLink.classList.add('active');
    }
    
    function navigateTo(section) {
        // Hide all dashboard sections
        document.querySelectorAll('.dashboard-section').forEach(el => {
            el.style.display = 'none';
        });
        
        // Show the selected section
        const targetSection = document.getElementById(section);
        if (targetSection) {
            targetSection.style.display = 'block';
            
            // Load section-specific data
            if (section === 'student-dashboard') {
                Student.loadDashboard();
            } else if (section === 'student-profile') {
                Student.showProfile();
            } else if (section === 'job-recommendations') {
                Student.showRecommendations();
            } else if (section === 'ojt-offerings') {
                Student.showOjtOfferings();
            } else if (section === 'announcements') {
                Student.showAnnouncements();
            } else if (section === 'admin-dashboard') {
                Admin.loadDashboard();
            } else if (section === 'user-management') {
                Admin.showUserManagement();
            } else if (section === 'job-management') {
                Admin.showJobManagement();
            } else if (section === 'data-monitoring') {
                Admin.showDataMonitoring();
            } else if (section === 'reports-announcements') {
                Admin.showReportsAnnouncements();
            }
        }
    }
    
    function showDashboard() {
        const user = JSON.parse(sessionStorage.getItem('currentUser'));
        if (user && user.role === 'student') {
            navigateTo('student-dashboard');
        } else if (user && user.role === 'admin') {
            navigateTo('admin-dashboard');
        }
    }
    
    function generateEventQRCode(eventName, eventDate, eventLocation) {
        const modal = document.getElementById('qr-modal');
        const container = document.getElementById('qr-code');
        const eventEl = document.getElementById('qr-event-name');
        
        if (!modal || !container) return;
        
        container.innerHTML = '';
        
        const data = JSON.stringify({
            event: eventName,
            date: eventDate,
            location: eventLocation,
            timestamp: new Date().toISOString(),
            type: 'event_checkin'
        });
        
        if (typeof QRCode !== 'undefined') {
            new QRCode(container, {
                text: data,
                width: 250,
                height: 250,
                colorDark: "#2E7D32",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            
            if (eventEl) {
                eventEl.textContent = `${eventName} - ${eventDate}`;
            }
            
            modal.style.display = 'flex';
            Utils.showToast('QR Code generated successfully!', 'success');
        } else {
            container.innerHTML = '<p class="text-muted">QR library not loaded</p>';
            Utils.showToast('QR library not available', 'error');
        }
    }
    
    return {
        init,
        updateSidebarUserInfo,
        setupSidebarNavigation,
        navigateTo,
        showDashboard,
        generateEventQRCode
    };
})();