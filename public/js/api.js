// ========== API MODULE ==========
const API = (function() {
    'use strict';
    
    const BASE_URL = '/api';
    
    async function request(endpoint, options = {}) {
        try {
            const response = await fetch(BASE_URL + endpoint, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    ...options.headers
                },
                credentials: 'same-origin'
            });
            
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }
            return data;
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }
    
    return {
        // Auth
        async login(username, password) {
            return request('/login', { method: 'POST', body: JSON.stringify({ username, password }) });
        },
        async logout() {
            return request('/logout', { method: 'POST' });
        },
        async getUser() {
            return request('/user');
        },
        
        // Student
        async getStudentProfile() {
            return request('/student/profile');
        },
        async updateStudentProfile(data) {
            return request('/student/profile', { method: 'PUT', body: JSON.stringify(data) });
        },
        async getStudentSkills() {
            return request('/student/skills');
        },
        async addStudentSkill(skillData) {
            return request('/student/skills', { method: 'POST', body: JSON.stringify(skillData) });
        },
        async getStudentRecommendations() {
            return request('/student/recommendations');
        },
        async getStudentOjtOfferings() {
            return request('/student/ojt-offerings');
        },
        async getStudentAnnouncements() {
            return request('/student/announcements');
        },
        
        // Jobs
        async getJobs(filters = {}) {
            const queryString = new URLSearchParams(filters).toString();
            return request(`/jobs${queryString ? '?' + queryString : ''}`);
        },
        async getJobById(id) {
            return request(`/jobs/${id}`);
        },
        
        // Announcements
        async getAnnouncements() {
            return request('/announcements');
        },
        async getEventQR(eventId) {
            return request(`/announcements/${eventId}/qr`);
        },
        
        // Admin
        async getDashboardStats() {
            return request('/admin/stats');
        },
        async getActivityFeed() {
            return request('/admin/activity');
        },
        async getUsers(filters = {}) {
            const queryString = new URLSearchParams(filters).toString();
            return request(`/admin/users${queryString ? '?' + queryString : ''}`);
        },
        async createUser(userData) {
            return request('/admin/users', { method: 'POST', body: JSON.stringify(userData) });
        },
        async updateUser(userId, userData) {
            return request(`/admin/users/${userId}`, { method: 'PUT', body: JSON.stringify(userData) });
        },
        async deleteUser(userId) {
            return request(`/admin/users/${userId}`, { method: 'DELETE' });
        },
        async getJobPosts(filters = {}) {
            const queryString = new URLSearchParams(filters).toString();
            return request(`/admin/jobs${queryString ? '?' + queryString : ''}`);
        },
        async createJobPost(jobData) {
            return request('/admin/jobs', { method: 'POST', body: JSON.stringify(jobData) });
        },
        async updateJobPost(jobId, jobData) {
            return request(`/admin/jobs/${jobId}`, { method: 'PUT', body: JSON.stringify(jobData) });
        },
        async deleteJobPost(jobId) {
            return request(`/admin/jobs/${jobId}`, { method: 'DELETE' });
        },
        async approveJob(jobId) {
            return request(`/admin/jobs/${jobId}/approve`, { method: 'POST' });
        },
        async rejectJob(jobId, reason = '') {
            return request(`/admin/jobs/${jobId}/reject`, { method: 'POST', body: JSON.stringify({ reason }) });
        },
        async getMonitoringStats() {
            return request('/admin/monitoring/stats');
        },
        async getStudentData(queryString = '') {
            return request(`/admin/monitoring/students${queryString}`);
        },
        async createAnnouncement(announcementData) {
            return request('/admin/announcements', { method: 'POST', body: JSON.stringify(announcementData) });
        },
        async updateAnnouncement(id, announcementData) {
            return request(`/admin/announcements/${id}`, { method: 'PUT', body: JSON.stringify(announcementData) });
        },
        async deleteAnnouncement(id) {
            return request(`/admin/announcements/${id}`, { method: 'DELETE' });
        },
        async publishAnnouncement(id) {
            return request(`/admin/announcements/${id}/publish`, { method: 'POST' });
        },
        async generateRecommendations() {
            return request('/admin/recommendations/generate', { method: 'POST' });
        },
        async getAllRecommendations() {
            return request('/admin/recommendations');
        },
        async markRecommendationAsSent(recommendationId) {
            return request(`/admin/recommendations/${recommendationId}/sent`, { method: 'POST' });
        },
        async getOjtOfferings() {
            return request('/admin/ojt');
        },
        async createOjtOffering(offeringData) {
            return request('/admin/ojt', { method: 'POST', body: JSON.stringify(offeringData) });
        },
        async updateOjtOffering(id, offeringData) {
            return request(`/admin/ojt/${id}`, { method: 'PUT', body: JSON.stringify(offeringData) });
        },
        async deleteOjtOffering(id) {
            return request(`/admin/ojt/${id}`, { method: 'DELETE' });
        },
        async recordAttendance(data) {
            return request('/admin/attendance', { method: 'POST', body: JSON.stringify(data) });
        },
        async getEventAttendance(eventId) {
            return request(`/admin/attendance/${eventId}`);
        }
    };
})();