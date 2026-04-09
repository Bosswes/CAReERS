    // ========== DATA MODULE ==========
const Data = (function() {
    'use strict';
        
        let currentUser = null;
        
        // Safe array getter - protects against non-array values
        function safeArray(data, defaultValue = []) {
            if (Array.isArray(data)) return data;
            if (data && typeof data === 'object') {
                if (data.data && Array.isArray(data.data)) return data.data;
                if (data.applications && Array.isArray(data.applications)) return data.applications;
                if (data.jobs && Array.isArray(data.jobs)) return data.jobs;
                if (data.users && Array.isArray(data.users)) return data.users;
                if (data.students && Array.isArray(data.students)) return data.students;
                if (data.announcements && Array.isArray(data.announcements)) return data.announcements;
            }
            return defaultValue;
        }
        
        // In-memory storage for demo/fallback data
        let dummyAccounts = {
            student: [
                { email: 'juan.delacruz@cvsu.edu.ph', password: 'student123', name: 'Juan Dela Cruz', role: 'student', student_number: '202400001' }
            ],
            employer: [
                { email: 'employer@company.com', password: 'demo123', name: 'Tech Solutions Inc.', role: 'employer' }
            ],
            admin: [
                { email: 'admin@cvsu.edu.ph', password: 'admin123', name: 'Admin User', role: 'admin' }
            ]
        };

        let sampleApplicants = [
            { id: 1, name: 'John Doe', degree: 'BS Computer Science', gwa: '1.5', skills: ['Python', 'JavaScript', 'React'], matchScore: 95 },
            { id: 2, name: 'Jane Smith', degree: 'BS Information Technology', gwa: '1.8', skills: ['Java', 'SQL', 'Spring'], matchScore: 87 },
            { id: 3, name: 'Mike Johnson', degree: 'BS Computer Engineering', gwa: '2.0', skills: ['C++', 'Python', 'Embedded'], matchScore: 82 },
            { id: 4, name: 'Anna Santos', degree: 'BS Computer Science', gwa: '1.6', skills: ['JavaScript', 'React', 'Node.js'], matchScore: 90 },
            { id: 5, name: 'Pedro Reyes', degree: 'BS Information Technology', gwa: '1.9', skills: ['PHP', 'Laravel', 'MySQL'], matchScore: 78 }
        ];

        let jobPosts = [
            { 
                id: 1, 
                title: 'Junior Software Developer', 
                company: 'Tech Solutions Inc.',
                location: 'Makati City',
                industry: 'IT',
                type: 'full-time',
                salary: '₱25,000 - ₱35,000',
                description: 'We are looking for a passionate junior developer...',
                requirements: { skills: ['PHP', 'JavaScript', 'MySQL'] },
                date: '2024-03-01',
                status: 'approved',
                matchScore: 85
            },
            { 
                id: 2, 
                title: 'Web Developer Intern', 
                company: 'Digital Agency Co.',
                location: 'Quezon City',
                industry: 'IT',
                type: 'internship',
                salary: '₱8,000 - ₱10,000',
                description: 'Internship opportunity for IT students...',
                requirements: { skills: ['HTML', 'CSS', 'JavaScript'] },
                date: '2024-03-05',
                status: 'pending',
                matchScore: 75
            },
            { 
                id: 3, 
                title: 'Network Engineer', 
                company: 'Telecom Corp',
                location: 'Pasig City',
                industry: 'Telecommunications',
                type: 'full-time',
                salary: '₱30,000 - ₱45,000',
                description: 'Looking for network engineer with CCNA certification...',
                requirements: { skills: ['Networking', 'CCNA', 'TCP/IP'] },
                date: '2024-02-28',
                status: 'approved',
                matchScore: 65
            }
        ];

        let applications = [
            { id: 1, student_number: '202400001', jobId: 1, status: 'pending', applied_at: '2024-03-02T10:30:00' },
            { id: 2, student_number: '202400001', jobId: 2, status: 'reviewed', applied_at: '2024-03-03T14:20:00' }
        ];

        let announcements = [
            {
                id: 1,
                title: 'Career Fair 2024',
                content: 'Join us for the annual career fair on March 15, 2024 at the CvSU Gymnasium. Over 50 companies will be participating!',
                type: 'event',
                date: '2024-03-15',
                published: true,
                createdAt: '2024-03-01T09:00:00'
            },
            {
                id: 2,
                title: 'Resume Writing Workshop',
                content: 'Learn how to create an effective resume that stands out to employers. Workshop will be held on March 10, 2024.',
                type: 'event',
                date: '2024-03-10',
                published: true,
                createdAt: '2024-03-02T11:30:00'
            },
            {
                id: 3,
                title: 'Job Application Deadline Extended',
                content: 'The deadline for summer internship applications has been extended to March 30, 2024.',
                type: 'deadline',
                date: '2024-03-30',
                published: true,
                createdAt: '2024-03-03T15:45:00'
            }
        ];

        let users = [
            {
                email: 'juan.delacruz@cvsu.edu.ph',
                fullName: 'Juan Dela Cruz',
                role: 'student',
                status: 'active',
                profileCompletion: 75,
                profileComplete: false,
                profileData: {
                    degree: 'BS Computer Science',
                    gwa: '1.75',
                    skills: 'PHP, JavaScript, MySQL'
                },
                lastActive: '2024-03-05T08:30:00'
            }
        ];

        function initialize() {
            // Load user from session if exists
            const savedUser = sessionStorage.getItem('currentUser');
            if (savedUser) {
                try {
                    currentUser = JSON.parse(savedUser);
                } catch (error) {
                    console.error('Error loading saved user:', error);
                }
            }
        }

        function getCurrentUser() {
            return currentUser;
        }

        function setCurrentUser(user) {
            currentUser = user;
            if (user) {
                sessionStorage.setItem('currentUser', JSON.stringify(user));
            } else {
                sessionStorage.removeItem('currentUser');
            }
        }

        function loadSavedUser() {
            return currentUser;
        }

        // API wrapper functions with fallback to local data
        async function getJobs() {
            try {
                const response = await API.getJobs();
                return safeArray(response.jobs, []);
            } catch (error) {
                console.error('Error fetching jobs from API, using local data:', error);
                return safeArray(jobPosts, []);
            }
        }

        async function getAnnouncements() {
            try {
                const response = await API.getAnnouncements();
                return safeArray(response.announcements, []);
            } catch (error) {
                console.error('Error fetching announcements from API, using local data:', error);
                return safeArray(announcements, []);
            }
        }

        async function getRecommendations() {
            try {
                const response = await API.getJobRecommendations();
                return safeArray(response.recommendations, []);
            } catch (error) {
                console.error('Error fetching recommendations from API, using local data:', error);
                // Calculate recommendations based on user profile
                return calculateLocalRecommendations();
            }
        }

        async function getApplications() {
            try {
                const response = await API.getUserApplications();
                return safeArray(response.applications, []);
            } catch (error) {
                console.error('Error fetching applications from API, using local data:', error);
                return safeArray(applications, []);
            }
        }

        async function applyForJob(jobId) {
            try {
                const response = await API.applyForJob(jobId);
                return response.success === true;
            } catch (error) {
                console.error('Error applying for job via API, using local:', error);
                // Local fallback
                const newApp = {
                    id: applications.length + 1,
                    student_number: currentUser?.student_number || '202400001',
                    jobId: parseInt(jobId),
                    status: 'pending',
                    applied_at: new Date().toISOString()
                };
                applications.push(newApp);
                return true;
            }
        }

        // Local helper functions
        function calculateLocalRecommendations() {
            const user = currentUser;
            if (!user) return [];
            
            return jobPosts
                .filter(job => job.status === 'approved')
                .map(job => ({
                    ...job,
                    match_score: job.matchScore || Math.floor(Math.random() * 30) + 70
                }))
                .sort((a, b) => b.match_score - a.match_score)
                .slice(0, 5);
        }

        // Getter functions for local data
        function getDummyAccounts() {
            return dummyAccounts;
        }

        function getSampleApplicants() {
            return safeArray(sampleApplicants, []);
        }

        function getJobPosts() {
            return safeArray(jobPosts, []);
        }

        function getApplicationsLocal() {
            return safeArray(applications, []);
        }

        function getAnnouncementsLocal() {
            return safeArray(announcements, []);
        }

        function getUsers() {
            return safeArray(users, []);
        }

        // Setter/Updater functions
        function updateJobStatus(jobId, status) {
            const jobs = safeArray(jobPosts, []);
            const index = jobs.findIndex(j => j.id === parseInt(jobId));
            if (index !== -1) {
                jobPosts[index].status = status;
                return true;
            }
            return false;
        }

        function addJobPost(job) {
            if (job && typeof job === 'object') {
                jobPosts.push(job);
                return true;
            }
            return false;
        }

        function addAnnouncement(announcement) {
            if (announcement && typeof announcement === 'object') {
                announcements.push(announcement);
                return true;
            }
            return false;
        }

        function updateAnnouncement(id, updatedData) {
            const index = announcements.findIndex(a => a.id === parseInt(id));
            if (index !== -1) {
                announcements[index] = { ...announcements[index], ...updatedData };
                return true;
            }
            return false;
        }

        function deleteAnnouncement(id) {
            const index = announcements.findIndex(a => a.id === parseInt(id));
            if (index !== -1) {
                announcements.splice(index, 1);
                return true;
            }
            return false;
        }

        function updateUser(email, userData) {
            const index = users.findIndex(u => u.email === email);
            if (index !== -1) {
                users[index] = { ...users[index], ...userData };
                return true;
            }
            return false;
        }

        // Save functions (for local storage persistence)
        function saveAnnouncements() {
            // In a real app, this would save to localStorage or API
            console.log('Announcements saved locally');
        }

        // Return public API
        return {
            initialize,
            getCurrentUser,
            setCurrentUser,
            loadSavedUser,
            
            // API-wrapped functions
            getJobs,
            getAnnouncements,
            getRecommendations,
            getApplications,
            applyForJob,
            
            // Local data getters
            getDummyAccounts,
            getSampleApplicants,
            getJobPosts,
            getApplicationsLocal,
            getAnnouncementsLocal,
            getUsers,
            
            // Data manipulation
            updateJobStatus,
            addJobPost,
            addAnnouncement,
            updateAnnouncement,
            deleteAnnouncement,
            updateUser,
            saveAnnouncements
        };
})();