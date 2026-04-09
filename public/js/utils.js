// ========== UTILITIES MODULE ==========
const Utils = (function() {
    'use strict';
    
    let toastTimeout = null;
    
    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        const toastIcon = document.getElementById('toast-icon');
        
        if (!toast || !toastMessage || !toastIcon) return;
        
        if (toastTimeout) {
            clearTimeout(toastTimeout);
            toast.classList.remove('show');
        }
        
        let iconClass = 'fas fa-info-circle';
        let bgColor = '#3b82f6';
        
        switch(type) {
            case 'success':
                iconClass = 'fas fa-check-circle';
                bgColor = '#10b981';
                break;
            case 'warning':
                iconClass = 'fas fa-exclamation-triangle';
                bgColor = '#f59e0b';
                break;
            case 'error':
                iconClass = 'fas fa-times-circle';
                bgColor = '#ef4444';
                break;
            case 'info':
                iconClass = 'fas fa-info-circle';
                bgColor = '#3b82f6';
                break;
        }
        
        toastIcon.className = iconClass;
        toast.style.backgroundColor = bgColor;
        toastMessage.textContent = message;
        toast.classList.add('show');
        
        toastTimeout = setTimeout(() => {
            toast.classList.remove('show');
            toastTimeout = null;
        }, 4000);
    }
    
    function formatDateShort(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return 'Invalid Date';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        } catch (error) {
            return 'Invalid Date';
        }
    }
    
    function getInitials(name) {
        if (!name) return 'U';
        return name.split(' ').map(part => part.charAt(0)).join('').toUpperCase().substring(0, 2);
    }
    
    function truncateText(text, maxLength = 100) {
        if (!text) return '';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }
    
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
    
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    return {
        showToast,
        formatDateShort,
        getInitials,
        truncateText,
        debounce,
        isValidEmail
    };
})();