// ========== ADMIN MODULE ==========
const Admin = (function() {
    'use strict';
    
    async function loadDashboard() {
        try {
            const stats = await API.getDashboardStats();
            if (stats.success && stats.stats) {
                document.getElementById('total-students').textContent = stats.stats.total_students || 0;
                document.getElementById('total-jobs').textContent = stats.stats.total_jobs || 0;
                document.getElementById('pending-jobs-count').textContent = stats.stats.pending_jobs || 0;
                document.getElementById('total-recommendations').textContent = stats.stats.total_recommendations || 0;
            }
            await loadActivityFeed();
        } catch (error) {
            console.error('Error loading admin dashboard:', error);
            Utils.showToast('Error loading dashboard', 'error');
        }
    }
    
    async function loadActivityFeed() {
        const container = document.getElementById('activity-feed');
        if (!container) return;
        
        try {
            const response = await API.getActivityFeed();
            let activities = response.success && response.activities ? response.activities : [];
            
            container.innerHTML = '';
            
            if (activities.length === 0) {
                container.innerHTML = '<p class="text-muted text-center">No recent activities</p>';
                return;
            }
            
            activities.forEach(act => {
                const div = document.createElement('div');
                div.className = 'activity-item';
                div.innerHTML = `
                    <div class="activity-icon"><i class="fas ${act.icon || 'fa-bell'}"></i></div>
                    <div class="activity-content">
                        <h4>${act.text}</h4>
                        <span class="activity-time">${act.time}</span>
                    </div>
                `;
                container.appendChild(div);
            });
        } catch (error) {
            console.error('Error loading activity feed:', error);
            container.innerHTML = '<p class="text-muted text-center">No recent activities</p>';
        }
    }
    
    function showUserManagement() {
        loadUsersTable();
        setupUserFilters();
        
        const addBtn = document.getElementById('add-student-btn');
        if (addBtn) addBtn.addEventListener('click', () => openUserModal('add', 'student'));
    }
    
    async function loadUsersTable() {
        const tbody = document.getElementById('users-table-body');
        if (!tbody) return;
        
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Loading users...</td></tr>';
        
        try {
            const roleFilter = document.getElementById('user-role-filter')?.value;
            const searchTerm = document.getElementById('user-search')?.value;
            
            const params = {};
            if (roleFilter) params.role = roleFilter;
            if (searchTerm) params.search = searchTerm;
            
            const response = await API.getUsers(params);
            let users = response.success ? response.users : [];
            
            tbody.innerHTML = '';
            
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No users found</td></tr>';
                return;
            }
            
            users.forEach(user => {
                const row = document.createElement('tr');
                const roleBadgeClass = user.role === 'student' ? 'student' : 'admin';
                const statusClass = user.status === 'active' ? 'active' : 'pending';
                
                row.innerHTML = `
                    <td>${user.name || 'N/A'}</td>
                    <td>${user.email || 'N/A'}</td>
                    <td><span class="role-badge ${roleBadgeClass}">${user.role}</span></td>
                    <td><span class="status-badge ${statusClass}">${user.status || 'active'}</span></td>
                    <td>${user.profile_completion || 0}%</td>
                    <td>${user.created_at ? Utils.formatDateShort(user.created_at) : 'N/A'}</td>
                    <td class="table-actions">
                        <button class="btn-primary btn-sm edit-user" data-id="${user.id}" data-role="${user.role}">Edit</button>
                        ${user.role === 'student' ? `<button class="btn-danger btn-sm delete-user" data-id="${user.id}">Delete</button>` : ''}
                    </td>
                `;
                tbody.appendChild(row);
            });
            
            attachUserListeners(tbody);
        } catch (error) {
            console.error('Error loading users:', error);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Error loading users</td></tr>';
        }
    }
    
    function attachUserListeners(tbody) {
        tbody.querySelectorAll('.edit-user').forEach(btn => {
            btn.addEventListener('click', () => {
                const userId = btn.dataset.id;
                const role = btn.dataset.role;
                openUserModal('edit', role, userId);
            });
        });
        
        tbody.querySelectorAll('.delete-user').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (confirm('Are you sure you want to delete this user?')) {
                    try {
                        const response = await API.deleteUser(btn.dataset.id);
                        if (response.success) {
                            Utils.showToast('User deleted successfully', 'success');
                            loadUsersTable();
                        }
                    } catch (error) {
                        Utils.showToast('Error deleting user', 'error');
                    }
                }
            });
        });
    }
    
    function setupUserFilters() {
        const applyBtn = document.getElementById('apply-user-filters');
        const searchInput = document.getElementById('user-search');
        const roleFilter = document.getElementById('user-role-filter');
        
        if (applyBtn) applyBtn.addEventListener('click', loadUsersTable);
        if (searchInput) searchInput.addEventListener('input', Utils.debounce(loadUsersTable, 300));
        if (roleFilter) roleFilter.addEventListener('change', loadUsersTable);
    }
    
    function openUserModal(mode, role, userId = null) {
        const modal = document.getElementById('user-modal');
        const title = document.getElementById('modal-title');
        const studentFields = document.getElementById('student-fields');
        const deleteBtn = document.getElementById('delete-user');
        
        if (!modal) return;
        
        studentFields.style.display = 'block';
        deleteBtn.style.display = 'none';
        
        if (mode === 'add') {
            title.textContent = 'Add Student';
            document.getElementById('modal-student-id').value = '';
            document.getElementById('modal-student-name').value = '';
            document.getElementById('modal-student-email').value = '';
            document.getElementById('modal-student-degree').value = '';
            document.getElementById('modal-student-password').value = '';
            document.getElementById('modal-student-password-confirm').value = '';
            document.getElementById('modal-user-id').value = '';
        } else if (mode === 'edit' && userId) {
            title.textContent = 'Edit Student';
            document.getElementById('modal-user-id').value = userId;
            loadUserDataForEdit(userId);
            deleteBtn.style.display = 'block';
        }
        
        modal.style.display = 'flex';
        
        const saveBtn = document.getElementById('save-user');
        const cancelBtn = document.getElementById('cancel-user');
        const closeBtn = document.getElementById('close-user-modal');
        
        saveBtn.onclick = () => handleSaveUser(mode);
        deleteBtn.onclick = () => handleDeleteUser(userId);
        cancelBtn.onclick = () => modal.style.display = 'none';
        closeBtn.onclick = () => modal.style.display = 'none';
    }
    
    async function loadUserDataForEdit(userId) {
        try {
            const response = await API.getUsers({ search: userId });
            if (response.success && response.users && response.users.length > 0) {
                const user = response.users[0];
                document.getElementById('modal-student-id').value = user.id || '';
                document.getElementById('modal-student-name').value = user.name || '';
                document.getElementById('modal-student-email').value = user.email || '';
                document.getElementById('modal-student-degree').value = user.program || '';
                document.getElementById('modal-student-password').value = '';
                document.getElementById('modal-student-password-confirm').value = '';
            }
        } catch (error) {
            console.error('Error loading user data:', error);
        }
    }
    
    async function handleSaveUser(mode) {
        const id = document.getElementById('modal-user-id').value;
        const studentId = document.getElementById('modal-student-id').value;
        const name = document.getElementById('modal-student-name').value;
        const email = document.getElementById('modal-student-email').value;
        const degree = document.getElementById('modal-student-degree').value;
        const password = document.getElementById('modal-student-password').value;
        const passwordConfirm = document.getElementById('modal-student-password-confirm').value;
        
        if (mode === 'add') {
            if (!password) {
                Utils.showToast('Please enter a password', 'error');
                return;
            }
            if (password !== passwordConfirm) {
                Utils.showToast('Passwords do not match', 'error');
                return;
            }
        }

        const nameParts = name.split(' ');
        const firstName = nameParts[0] || '';
        const lastName = nameParts.slice(1).join(' ') || '';
        
       try {
            if (mode === 'add') {
                await API.createUser({
                    role: 'student',
                    student_number: studentId,
                    first_name: firstName,
                    last_name: lastName,
                    email: email,
                    password: password,
                    program: degree,
                });
                Utils.showToast('Student added successfully', 'success');
            } else {
                await API.updateUser(id, {
                    role: 'student',
                    first_name: firstName,
                    last_name: lastName,
                    email: email,
                    program: degree,
                });
                Utils.showToast('Student updated successfully', 'success');
            }
            
            document.getElementById('user-modal').style.display = 'none';
            loadUsersTable();
        } catch (error) {
            Utils.showToast('Error saving user', 'error');
        }
    }
    
    async function handleDeleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            try {
                await API.deleteUser(userId);
                Utils.showToast('User deleted successfully', 'success');
                document.getElementById('user-modal').style.display = 'none';
                loadUsersTable();
            } catch (error) {
                Utils.showToast('Error deleting user', 'error');
            }
        }
    }
    
    function showJobManagement() {
        loadJobCards();
        setupJobFilters();
        
        const createBtn = document.getElementById('create-job-post-btn');
        if (createBtn) createBtn.addEventListener('click', () => showCreateJobForm());
    }
    
    async function loadJobCards() {
        const container = document.getElementById('admin-job-cards');
        if (!container) return;
        
        container.innerHTML = '<div class="loading">Loading jobs...</div>';
        
        try {
            const status = document.getElementById('job-status-filter')?.value;
            const industry = document.getElementById('job-industry-filter')?.value;
            const search = document.getElementById('job-search-admin')?.value;
            
            const params = {};
            if (status) params.status = status;
            if (industry) params.industry = industry;
            if (search) params.search = search;
            
            const response = await API.getJobPosts(params);
            let jobs = response.success ? response.jobs : [];
            
            const totalJobs = document.getElementById('total-jobs-count');
            const pendingJobs = document.getElementById('pending-jobs-count2');
            const approvedJobs = document.getElementById('approved-jobs-count');
            const rejectedJobs = document.getElementById('rejected-jobs-count');
            const pendingBadge = document.getElementById('pending-jobs-count-badge');
            
            const pendingCount = jobs.filter(j => j.status === 'pending').length;
            const approvedCount = jobs.filter(j => j.status === 'approved').length;
            const rejectedCount = jobs.filter(j => j.status === 'rejected').length;
            
            if (totalJobs) totalJobs.textContent = jobs.length;
            if (pendingJobs) pendingJobs.textContent = pendingCount;
            if (approvedJobs) approvedJobs.textContent = approvedCount;
            if (rejectedJobs) rejectedJobs.textContent = rejectedCount;
            if (pendingBadge) pendingBadge.textContent = `${pendingCount} pending`;
            
            container.innerHTML = '';
            
            if (jobs.length === 0) {
                container.innerHTML = '<div class="text-center text-muted">No jobs found</div>';
                return;
            }
            
            jobs.forEach(job => {
                const card = document.createElement('div');
                card.className = 'job-approval-card';
                const statusColor = job.status === 'pending' ? '#f59e0b' : job.status === 'approved' ? '#10b981' : '#ef4444';
                card.innerHTML = `
                    <div class="job-info">
                        <h4 class="view-job-details" data-id="${job.job_id}" style="cursor:pointer; color:#2E7D32; text-decoration:underline dotted; text-underline-offset:3px;" title="Click to view full details">${job.title}</h4>
                        <div class="job-company"><i class="fas fa-building"></i> ${job.employer_name || 'N/A'}</div>
                        <div class="job-meta">
                            <span><i class="fas fa-calendar"></i> Posted: ${Utils.formatDateShort(job.posted_date)}</span>
                            <span><i class="fas fa-map-marker-alt"></i> ${job.location || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="job-status">
                        <span style="background: ${statusColor}20; color: ${statusColor}; padding: 6px 14px; border-radius: 20px;">${job.status}</span>
                        <div class="job-actions">
                            <button class="btn-sm view-applicants" data-id="${job.job_id}" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd; border-radius:8px; padding:5px 12px; font-size:12px; font-weight:600; cursor:pointer;">
                                <i class="fas fa-users"></i> Applicants
                            </button>
                            ${job.status === 'pending' ? `
                                <button class="btn-success btn-sm approve-job" data-id="${job.job_id}">Approve</button>
                                <button class="btn-danger btn-sm reject-job" data-id="${job.job_id}">Reject</button>
                            ` : ''}
                            <button class="btn-secondary btn-sm delete-job" data-id="${job.job_id}">Delete</button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
            
            attachJobListeners(container);
        } catch (error) {
            console.error('Error loading jobs:', error);
            container.innerHTML = '<div class="text-center text-muted">Error loading jobs</div>';
        }
    }
    
    function attachJobListeners(container) {
        // Clickable job title → full job details modal
        container.querySelectorAll(".view-job-details").forEach(el => {
            el.addEventListener("click", () => openJobDetailsModal(el.dataset.id));
        });

        // View Applicants button
        container.querySelectorAll(".view-applicants").forEach(btn => {
            btn.addEventListener("click", () => openApplicantsModal(btn.dataset.id));
        });

        container.querySelectorAll('.approve-job').forEach(btn => {
            btn.addEventListener('click', async () => {
                try {
                    await API.approveJob(btn.dataset.id);
                    Utils.showToast('Job approved', 'success');
                    loadJobCards();
                } catch (error) {
                    Utils.showToast('Error approving job', 'error');
                }
            });
        });
        
        container.querySelectorAll('.reject-job').forEach(btn => {
            btn.addEventListener('click', async () => {
                const reason = prompt('Enter rejection reason:');
                try {
                    await API.rejectJob(btn.dataset.id, reason);
                    Utils.showToast('Job rejected', 'info');
                    loadJobCards();
                } catch (error) {
                    Utils.showToast('Error rejecting job', 'error');
                }
            });
        });
        
        container.querySelectorAll('.delete-job').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (confirm('Are you sure you want to delete this job?')) {
                    try {
                        await API.deleteJobPost(btn.dataset.id);
                        Utils.showToast('Job deleted', 'success');
                        loadJobCards();
                    } catch (error) {
                        Utils.showToast('Error deleting job', 'error');
                    }
                }
            });
        });
    }
    
    function setupJobFilters() {
        const search = document.getElementById('job-search-admin');
        const status = document.getElementById('job-status-filter');
        const industry = document.getElementById('job-industry-filter');
        
        if (search) search.addEventListener('input', Utils.debounce(loadJobCards, 300));
        if (status) status.addEventListener('change', loadJobCards);
        if (industry) industry.addEventListener('change', loadJobCards);
    }
    
    function showCreateJobForm() {
        UI.navigateTo('create-job');
        setupCreateJobForm();
    }
    
    function setupCreateJobForm() {
        const submitBtn = document.getElementById('submit-job');
        const cancelBtn = document.getElementById('cancel-job');
        
        if (submitBtn) submitBtn.onclick = submitJobPost;
        if (cancelBtn) cancelBtn.onclick = () => UI.navigateTo('job-management');
    }
    
    async function submitJobPost() {
        const title = document.getElementById('job-title')?.value;
        const industry = document.getElementById('job-industry')?.value;
        const jobType = document.getElementById('job-type')?.value;
        const location = document.getElementById('job-location')?.value;
        const employerName = document.getElementById('employer-name')?.value;
        const employerContact = document.getElementById('employer-contact')?.value;
        const placementAdminEmail = document.getElementById('placement-admin-email')?.value;
        const description = document.getElementById('job-description')?.value;
        const minGwa = document.getElementById('job-gwa')?.value;
        const minYearLevel = document.getElementById('job-year-level')?.value;
        const skills = document.getElementById('job-skills')?.value;
        const deadline = document.getElementById('job-deadline')?.value;
        
        if (!title || !industry || !jobType || !location || !employerName || !description) {
            Utils.showToast('Please fill all required fields', 'warning');
            return;
        }
        
        try {
            await API.createJobPost({
                title,
                industry,
                job_type: jobType,
                location,
                employer_name: employerName,
                employer_contact: employerContact,
                placement_admin_email: placementAdminEmail,
                description,
                min_gwa: minGwa,
                min_year_level: minYearLevel,
                skills: skills,
                deadline_date: deadline
            });
            Utils.showToast('Job posted successfully', 'success');
            UI.navigateTo('job-management');
        } catch (error) {
            Utils.showToast('Error creating job', 'error');
        }
    }
    
    function showDataMonitoring() {
        loadMonitoringStats();
    }

    function showStudentFiltering() {
        setupStudentFilters();
    }

    async function loadMonitoringStats() {
        try {
            const response = await API.getMonitoringStats();
            if (response.success && response.stats) {
                const stats = response.stats;
                document.getElementById('total-students-monitor').textContent = stats.total_students || 0;
                document.getElementById('active-jobs-monitor').textContent = stats.active_jobs || 0;
                document.getElementById('avg-completion-percentage').textContent = `${stats.avg_completion || 0}%`;
                document.getElementById('avg-completion-progress').style.width = `${stats.avg_completion || 0}%`;
                document.getElementById('incomplete-profiles-count').textContent = stats.incomplete_profiles || 0;
                document.getElementById('missing-skills-count').textContent = stats.missing_skills || 0;
                document.getElementById('missing-gwa-count').textContent = stats.missing_gwa || 0;
                document.getElementById('missing-degree-count').textContent = stats.missing_degree || 0;
            }
        } catch (error) {
            console.error('Error loading monitoring stats:', error);
        }
    }

    function setupStudentFilters() {
        const search      = document.getElementById('student-search');
        const course      = document.getElementById('filter-course');
        const gwaMin      = document.getElementById('filter-gwa-min');
        const gwaMax      = document.getElementById('filter-gwa-max');
        const yearLevel   = document.getElementById('filter-year-level');
        const skill       = document.getElementById('filter-skill');
        const profileStat = document.getElementById('filter-profile-status');
        const applyBtn    = document.getElementById('apply-student-filters');
        const resetBtn    = document.getElementById('reset-student-filters');

        if (search) search.addEventListener('input', Utils.debounce(loadStudentData, 300));
        if (applyBtn) applyBtn.addEventListener('click', loadStudentData);
        if (resetBtn) resetBtn.addEventListener('click', () => {
            if (search)      search.value      = '';
            if (course)      course.value      = '';
            if (gwaMin)      gwaMin.value      = '';
            if (gwaMax)      gwaMax.value      = '';
            if (yearLevel)   yearLevel.value   = '';
            if (skill)       skill.value       = '';
            if (profileStat) profileStat.value = 'all';
            loadStudentData();
        });

        loadStudentData();
    }

    async function loadStudentData() {
        const tbody = document.getElementById('student-data-table-body');
        if (!tbody) return;

        const params = new URLSearchParams();
        const search      = document.getElementById('student-search')?.value || '';
        const course      = document.getElementById('filter-course')?.value || '';
        const gwaMin      = document.getElementById('filter-gwa-min')?.value || '';
        const gwaMax      = document.getElementById('filter-gwa-max')?.value || '';
        const yearLevel   = document.getElementById('filter-year-level')?.value || '';
        const skill       = document.getElementById('filter-skill')?.value || '';
        const profileStat = document.getElementById('filter-profile-status')?.value || 'all';

        if (search)      params.append('search', search);
        if (course)      params.append('course', course);
        if (gwaMin)      params.append('gwa_min', gwaMin);
        if (gwaMax)      params.append('gwa_max', gwaMax);
        if (yearLevel)   params.append('year_level', yearLevel);
        if (skill)       params.append('skill', skill);
        if (profileStat && profileStat !== 'all') params.append('profile_status', profileStat);

        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Loading...</td></tr>';

        try {
            const response = await API.getStudentData(params.toString() ? '?' + params.toString() : '');
            let students = response.success ? response.students : [];

            // update counter
            const counter = document.getElementById('student-filter-count');
            if (counter) counter.textContent = `${students.length} student(s) found`;

            tbody.innerHTML = '';

            if (students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No students match the filters</td></tr>';
                return;
            }

            students.forEach(s => {
                const completion      = s.completion || 0;
                const completionClass = completion === 100 ? 'high' : completion >= 50 ? 'medium' : 'low';
                const statusColor     = s.profile_status === 'complete' ? '#16a34a' : '#dc2626';
                const statusLabel     = s.profile_status === 'complete' ? '✅ Complete' : '⚠️ Incomplete';

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${s.name || 'N/A'}</td>
                    <td>${s.student_number || 'N/A'}</td>
                    <td>${s.program || 'N/A'}</td>
                    <td>${s.year_level || 'N/A'}</td>
                    <td>${s.gwa || 'N/A'}</td>
                    <td style="max-width:160px; font-size:12px;">${s.skills || 'None'}</td>
                    <td><span class="completion-badge ${completionClass}">${completion}%</span></td>
                    <td><span style="color:${statusColor}; font-weight:600;">${statusLabel}</span></td>
                `;
                tbody.appendChild(row);
            });
        } catch (error) {
            console.error('Error loading student data:', error);
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Error loading data</td></tr>';
        }
    }
    
    function showReportsAnnouncements() {
        loadAnnouncements();
        setupAnnouncementTabs();
        
        const createBtn = document.getElementById('create-announcement-btn');
        if (createBtn) createBtn.addEventListener('click', () => openAnnouncementModal('add'));
        
        const generateBtns = document.querySelectorAll('.generate-report');
        generateBtns.forEach(btn => {
            btn.addEventListener('click', () => Utils.showToast('Report generation feature coming soon', 'info'));
        });
    }
    
    async function loadAnnouncements() {
        const container = document.getElementById('admin-announcements-list');
        if (!container) return;
        
        container.innerHTML = '<div class="loading">Loading announcements...</div>';
        
        try {
            const response = await API.getAnnouncements();
            let announcements = response.success ? response.announcements : [];
            
            document.getElementById('total-announcements').textContent = announcements.length;
            document.getElementById('upcoming-events').textContent = announcements.filter(a => a.announcement_type === 'event').length;
            
            container.innerHTML = '';
            
            if (announcements.length === 0) {
                container.innerHTML = '<div class="text-center text-muted">No announcements</div>';
                return;
            }
            
            announcements.forEach(ann => {
                const div = document.createElement('div');
                div.className = 'announcement-item';
                div.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                                <h4 style="margin:0;">${ann.title}</h4>
                                ${ann.announcement_type === 'event' ? (() => {
    const today = new Date(); today.setHours(0,0,0,0);
    const start = ann.start_date ? new Date(ann.start_date) : null;
    const end   = ann.end_date   ? new Date(ann.end_date)   : (start ? new Date(ann.start_date) : null);
    if (start) { start.setHours(0,0,0,0); }
    if (end)   { end.setHours(23,59,59,999); }
    const now = new Date();

    let badge = '';
    if (start && now >= start && now <= end) {
        badge = `<span style="background:#f59e0b;color:white;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">🔴 ONGOING EVENT</span>`;
    } else if (ann.registration_status === 'closed' || (end && now > end)) {
        badge = `<span style="background:#dc2626;color:white;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">🔒 CLOSED REGISTRATION</span>`;
    } else {
        badge = `<span style="background:#16a34a;color:white;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">✅ OPEN FOR REGISTRATION</span>`;
    }
    return badge;
})() : ''}
                            </div>
                            <p class="text-muted" style="margin-top:4px;">${Utils.formatDateShort(ann.start_date)}${ann.end_date ? ' → ' + Utils.formatDateShort(ann.end_date) : ''}${ann.location ? ' · ' + ann.location : ''}</p>
                            <p>${Utils.truncateText(ann.content, 150)}</p>
                            
                        </div>
                        <div style="display:flex; flex-direction:column; gap:6px; min-width:120px;">
                            ${ann.announcement_type === 'event' ? `
                                <a href="/attendance/scan/${ann.announcement_id}" target="_blank" class="btn-primary btn-sm" style="text-align:center; text-decoration:none; padding:5px 10px; border-radius:6px; background:#1565c0; color:white;">📷 Scan QR</a>
                                <a href="/attendance/print/${ann.announcement_id}" target="_blank" class="btn-secondary btn-sm" style="text-align:center; text-decoration:none; padding:5px 10px; border-radius:6px; background:#555; color:white;">🖨️ Print</a>
                            ` : ''}
                            <button class="btn-primary btn-sm edit-announcement" data-id="${ann.announcement_id}">Edit</button>
                            <button class="btn-danger btn-sm delete-announcement" data-id="${ann.announcement_id}">Delete</button>
                        </div>
                    </div>
                `;
                container.appendChild(div);
            });
            
            attachAnnouncementListeners(container);
        } catch (error) {
            console.error('Error loading announcements:', error);
            container.innerHTML = '<div class="text-center text-muted">Error loading announcements</div>';
        }
    }
    
    function attachAnnouncementListeners(container) {
        container.querySelectorAll('.edit-announcement').forEach(btn => {
            btn.addEventListener('click', () => openAnnouncementModal('edit', btn.dataset.id));
        });
        
        container.querySelectorAll('.delete-announcement').forEach(btn => {
            btn.addEventListener('click', async () => {
                if (confirm('Are you sure you want to delete this announcement?')) {
                    try {
                        await API.deleteAnnouncement(btn.dataset.id);
                        Utils.showToast('Announcement deleted', 'success');
                        loadAnnouncements();
                    } catch (error) {
                        Utils.showToast('Error deleting announcement', 'error');
                    }
                }
            });
        });
    }
    
    function setupAnnouncementTabs() {
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.style.display = 'none');
                this.classList.add('active');
                const target = document.getElementById(tabId);
                if (target) target.style.display = 'block';
            });
        });
    }
    
    function openAnnouncementModal(mode, id = null) {
        const modal = document.getElementById('announcement-modal');
        const title = document.getElementById('announcement-modal-title');
        const idField = document.getElementById('announcement-id');
        
        if (!modal) return;
        
        if (mode === 'add') {
            title.textContent = 'Create Announcement';
            idField.value = '';
            document.getElementById('announcement-title').value = '';
            document.getElementById('announcement-content').value = '';
            document.getElementById('announcement-type').value = 'general';
            document.getElementById('announcement-date').value = new Date().toISOString().split('T')[0];
            document.getElementById('announcement-location').value = '';
            document.getElementById('announcement-registration-status').value = 'open';
            document.getElementById('announcement-publish').checked = true;
        } else if (mode === 'edit' && id) {
            title.textContent = 'Edit Announcement';
            idField.value = id;
            loadAnnouncementData(id);
        }
        
        modal.style.display = 'flex';
        
        const form = document.getElementById('announcement-form');
        const cancelBtn = document.getElementById('cancel-announcement');
        const closeBtn = document.getElementById('close-announcement-modal');
        
        form.onsubmit = (e) => {
            e.preventDefault();
            saveAnnouncement(mode);
        };
        cancelBtn.onclick = () => modal.style.display = 'none';
        closeBtn.onclick = () => modal.style.display = 'none';
    }
    
    async function loadAnnouncementData(id) {
        try {
            const response = await API.getAnnouncements();
            if (response.success) {
                const ann = response.announcements.find(a => a.announcement_id == id);
                if (ann) {
                    document.getElementById('announcement-title').value = ann.title;
                    document.getElementById('announcement-content').value = ann.content;
                    document.getElementById('announcement-type').value = ann.announcement_type;
                    document.getElementById('announcement-date').value = ann.start_date;
                    document.getElementById('announcement-location').value = ann.location || '';
                    document.getElementById('announcement-registration-status').value = ann.registration_status || 'open';
                    document.getElementById('announcement-publish').checked = ann.is_published;
                }
            }
        } catch (error) {
            console.error('Error loading announcement:', error);
        }
    }
    
    async function saveAnnouncement(mode) {
        const id = document.getElementById('announcement-id').value;
        const title = document.getElementById('announcement-title').value;
        const content = document.getElementById('announcement-content').value;
        const type = document.getElementById('announcement-type').value;
        const date = document.getElementById('announcement-date').value;
        const location = document.getElementById('announcement-location').value;
        const registrationStatus = document.getElementById('announcement-registration-status').value;
        const published = document.getElementById('announcement-publish').checked;
        
        if (!title || !content) {
            Utils.showToast('Please fill all required fields', 'warning');
            return;
        }

        // Validate: hindi pwede past date ang start_date
        if (date) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(date);
            if (selectedDate < today) {
                Utils.showToast('Event date cannot be a past date.', 'warning');
                return;
            }
        }
        
        try {
           if (mode === 'add') {
                await API.createAnnouncement({
                    title,
                    content,
                    announcement_type: type,
                    start_date: date,
                    location,
                    registration_status: registrationStatus,
                    is_published: published,
                    target_audience: 'all'
                });
                Utils.showToast('Announcement created', 'success');
            } else {
                await API.updateAnnouncement(id, {
                    title,
                    content,
                    announcement_type: type,
                    start_date: date,
                    location,
                    registration_status: registrationStatus,
                    is_published: published
                });
                Utils.showToast('Announcement updated', 'success');
            }
            
            document.getElementById('announcement-modal').style.display = 'none';
            loadAnnouncements();
        } catch (error) {
            Utils.showToast('Error saving announcement', 'error');
        }
    }
    
    return {
        loadDashboard,
        showUserManagement,
        showJobManagement,
        showDataMonitoring,
        showStudentFiltering,
        showReportsAnnouncements
    };
})();
// =============================================
// JOB DETAILS MODAL
// =============================================
// Inject the two modal functions into the Admin IIFE scope by appending
// helper functions that are called via window scope from injected listeners.
// Since admin.js uses an IIFE, we expose the functions globally here.

window.openJobDetailsModal = async function(jobId) {
    let modal = document.getElementById('job-details-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'job-details-modal';
        modal.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;padding:20px;';
        modal.innerHTML = `
            <div style="background:white;border-radius:20px;max-width:680px;width:100%;max-height:88vh;overflow-y:auto;box-shadow:0 25px 60px rgba(0,0,0,0.25);">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:22px 28px;border-bottom:1px solid #e2e8f0;position:sticky;top:0;background:white;border-radius:20px 20px 0 0;z-index:1;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:42px;height:42px;background:#d1fae5;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-briefcase" style="color:#2E7D32;font-size:18px;"></i></div>
                        <h3 id="jd-modal-title" style="margin:0;color:#1e293b;font-size:18px;font-weight:700;">Job Details</h3>
                    </div>
                    <button onclick="document.getElementById('job-details-modal').style.display='none';" style="background:#f1f5f9;border:none;width:36px;height:36px;border-radius:10px;cursor:pointer;font-size:16px;color:#64748b;display:flex;align-items:center;justify-content:center;">&times;</button>
                </div>
                <div id="jd-modal-body" style="padding:28px;">
                    <div style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-spinner fa-spin" style="font-size:28px;"></i><p style="margin-top:12px;">Loading...</p></div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
    }
    modal.style.display = 'flex';

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch(`/api/admin/jobs?search=`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            credentials: 'same-origin'
        });
        const data = await res.json();
        const job = data.jobs?.find(j => j.job_id == jobId);

        if (!job) {
            document.getElementById('jd-modal-body').innerHTML = '<p style="text-align:center;color:#ef4444;">Job not found.</p>';
            return;
        }

        document.getElementById('jd-modal-title').textContent = job.title;
        const statusColor = job.status === 'pending' ? '#f59e0b' : job.status === 'approved' ? '#10b981' : '#ef4444';
        const skills = Array.isArray(job.required_skills) ? job.required_skills : [];

        document.getElementById('jd-modal-body').innerHTML = `
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
                <span style="background:${statusColor}18;color:${statusColor};padding:5px 14px;border-radius:20px;font-size:13px;font-weight:700;text-transform:uppercase;">${job.status}</span>
                <span style="background:#f0fdf4;color:#2E7D32;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:600;"><i class="fas fa-tag"></i> ${job.job_type || 'N/A'}</span>
                ${job.industry ? `<span style="background:#eff6ff;color:#2563eb;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:600;">${job.industry}</span>` : ''}
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                <div style="background:#f8fafc;border-radius:12px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Company</div>
                    <div style="font-weight:600;color:#1e293b;"><i class="fas fa-building" style="color:#2E7D32;margin-right:6px;"></i>${job.employer_name || 'N/A'}</div>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Location</div>
                    <div style="font-weight:600;color:#1e293b;"><i class="fas fa-map-marker-alt" style="color:#2E7D32;margin-right:6px;"></i>${job.location || 'N/A'}</div>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Contact</div>
                    <div style="font-weight:600;color:#1e293b;">${job.employer_contact || 'N/A'}</div>
                </div>
                <div style="background:#f8fafc;border-radius:12px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Deadline</div>
                    <div style="font-weight:600;color:#1e293b;">${job.deadline_date ? new Date(job.deadline_date).toLocaleDateString('en-PH') : 'No deadline'}</div>
                </div>
                ${job.min_gwa ? `<div style="background:#f8fafc;border-radius:12px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Min GWA</div>
                    <div style="font-weight:600;color:#1e293b;">${job.min_gwa}</div>
                </div>` : ''}
                ${job.min_year_level ? `<div style="background:#f8fafc;border-radius:12px;padding:14px;">
                    <div style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Min Year Level</div>
                    <div style="font-weight:600;color:#1e293b;">Year ${job.min_year_level}</div>
                </div>` : ''}
            </div>

            ${job.description ? `
            <div style="margin-bottom:20px;">
                <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:10px;"><i class="fas fa-align-left" style="color:#2E7D32;margin-right:6px;"></i>Description</div>
                <div style="background:#f8fafc;border-radius:12px;padding:16px;color:#374151;font-size:14px;line-height:1.7;">${job.description}</div>
            </div>` : ''}

            ${skills.length > 0 ? `
            <div style="margin-bottom:20px;">
                <div style="font-size:13px;font-weight:700;color:#1e293b;margin-bottom:10px;"><i class="fas fa-tools" style="color:#2E7D32;margin-right:6px;"></i>Required Skills</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    ${skills.map(s => `<span style="background:#d1fae5;color:#065f46;padding:5px 14px;border-radius:20px;font-size:13px;font-weight:600;">${s}</span>`).join('')}
                </div>
            </div>` : ''}

            <div style="margin-top:20px;padding-top:16px;border-top:1px solid #e2e8f0;display:flex;gap:10px;">
                <button onclick="openApplicantsModal(${job.job_id}); document.getElementById('job-details-modal').style.display='none';"
                    style="background:#0369a1;color:white;border:none;padding:10px 20px;border-radius:10px;cursor:pointer;font-weight:600;font-size:14px;">
                    <i class="fas fa-users"></i> View Applicants
                </button>
                <button onclick="document.getElementById('job-details-modal').style.display='none';"
                    style="background:#f1f5f9;color:#475569;border:none;padding:10px 20px;border-radius:10px;cursor:pointer;font-weight:600;font-size:14px;">
                    Close
                </button>
            </div>
        `;
    } catch(e) {
        console.error('Error loading job details:', e);
        document.getElementById('jd-modal-body').innerHTML = '<p style="text-align:center;color:#ef4444;">Error loading job details.</p>';
    }
};

// =============================================
// APPLICANTS MODAL
// =============================================
window.openApplicantsModal = async function(jobId) {
    let modal = document.getElementById('job-applicants-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'job-applicants-modal';
        modal.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;padding:20px;';
        modal.innerHTML = `
            <div style="background:white;border-radius:20px;max-width:860px;width:100%;max-height:88vh;overflow-y:auto;box-shadow:0 25px 60px rgba(0,0,0,0.25);">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:22px 28px;border-bottom:1px solid #e2e8f0;position:sticky;top:0;background:white;border-radius:20px 20px 0 0;z-index:1;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:42px;height:42px;background:#dbeafe;border-radius:12px;display:flex;align-items:center;justify-content:center;"><i class="fas fa-users" style="color:#0369a1;font-size:18px;"></i></div>
                        <div>
                            <h3 id="appl-modal-title" style="margin:0;color:#1e293b;font-size:18px;font-weight:700;">Applicants</h3>
                            <p id="appl-modal-subtitle" style="margin:0;font-size:13px;color:#64748b;"></p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('job-applicants-modal').style.display='none';" style="background:#f1f5f9;border:none;width:36px;height:36px;border-radius:10px;cursor:pointer;font-size:16px;color:#64748b;display:flex;align-items:center;justify-content:center;">&times;</button>
                </div>
                <div id="appl-modal-body" style="padding:28px;">
                    <div style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-spinner fa-spin" style="font-size:28px;"></i><p style="margin-top:12px;">Loading applicants...</p></div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
    }
    modal.style.display = 'flex';
    document.getElementById('appl-modal-title').textContent = 'Applicants';
    document.getElementById('appl-modal-subtitle').textContent = '';
    document.getElementById('appl-modal-body').innerHTML = '<div style="text-align:center;padding:40px;color:#94a3b8;"><i class="fas fa-spinner fa-spin" style="font-size:28px;"></i><p style="margin-top:12px;">Loading applicants...</p></div>';

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch(`/api/admin/jobs/${jobId}/applicants`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            credentials: 'same-origin'
        });
        const data = await res.json();

        if (!data.success) throw new Error(data.message);

        const job = data.job;
        const applicants = data.applicants;

        document.getElementById('appl-modal-title').textContent = `Applicants – ${job.title}`;
        document.getElementById('appl-modal-subtitle').textContent = `${job.employer_name} · ${data.total} applicant${data.total !== 1 ? 's' : ''}`;

        if (applicants.length === 0) {
            document.getElementById('appl-modal-body').innerHTML = `
                <div style="text-align:center;padding:50px;color:#94a3b8;">
                    <i class="fas fa-user-slash" style="font-size:42px;margin-bottom:14px;display:block;"></i>
                    <p style="font-size:15px;font-weight:600;">No applicants yet</p>
                    <p style="font-size:13px;">No students have applied to this job posting.</p>
                </div>`;
            return;
        }

        const statusColors = { pending:'#f59e0b', accepted:'#10b981', rejected:'#ef4444', sent:'#3b82f6' };

        const rows = applicants.map(a => {
            const color = statusColors[a.status] || '#6b7280';
            const date = a.applied_at ? new Date(a.applied_at).toLocaleDateString('en-PH') : '-';
            const skills = Array.isArray(a.skills) ? a.skills.slice(0,4) : [];
            const skillTags = skills.map(s => `<span style="background:#d1fae5;color:#065f46;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600;">${s}</span>`).join(' ');
            return `<tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:12px 10px;">
                    <div style="font-weight:700;color:#1e293b;">${a.first_name} ${a.last_name}</div>
                    <div style="font-size:12px;color:#64748b;">${a.cvsu_email}</div>
                </td>
                <td style="padding:12px 10px;">
                    <div style="font-size:13px;color:#374151;">${a.program || 'N/A'}</div>
                    <div style="font-size:12px;color:#94a3b8;">Year ${a.year_level || '?'} · GWA: ${a.general_weighted_average || 'N/A'}</div>
                </td>
                <td style="padding:12px 10px;">${skillTags || '<span style="color:#94a3b8;font-size:12px;">None listed</span>'}</td>
                <td style="padding:12px 10px;">${date}</td>
                <td style="padding:12px 10px;">
                    <span style="background:${color}20;color:${color};padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:capitalize;">${a.status}</span>
                </td>
                <td style="padding:12px 10px;">
                    <select onchange="window.updateAppStatus(${a.application_id}, this.value)"
                        style="padding:5px 8px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;cursor:pointer;">
                        <option value="pending" ${a.status==='pending'?'selected':''}>Pending</option>
                        <option value="sent" ${a.status==='sent'?'selected':''}>Sent to Employer</option>
                        <option value="accepted" ${a.status==='accepted'?'selected':''}>Accepted</option>
                        <option value="rejected" ${a.status==='rejected'?'selected':''}>Rejected</option>
                    </select>
                </td>
            </tr>`;
        }).join('');

        document.getElementById('appl-modal-body').innerHTML = `
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f0fdf4;">
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Student</th>
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Program</th>
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Skills</th>
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Applied</th>
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Status</th>
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Action</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    } catch(e) {
        console.error('Error loading applicants:', e);
        document.getElementById('appl-modal-body').innerHTML = '<p style="text-align:center;color:#ef4444;padding:30px;">Error loading applicants.</p>';
    }
    // =============================================
// REGISTRANTS MODAL (Events/Announcements)
// =============================================
window.openRegistrantsModal = async function(eventId, eventTitle) {
    let modal = document.getElementById('event-registrants-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'event-registrants-modal';
        modal.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;padding:20px;';
        modal.innerHTML = `
            <div style="background:white;border-radius:20px;max-width:900px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 25px 60px rgba(0,0,0,0.25);">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:22px 28px;border-bottom:1px solid #e2e8f0;position:sticky;top:0;background:white;border-radius:20px 20px 0 0;z-index:1;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:42px;height:42px;background:#d1fae5;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-users" style="color:#2E7D32;font-size:18px;"></i>
                        </div>
                        <div>
                            <h3 id="reg-modal-title" style="margin:0;color:#1e293b;font-size:18px;font-weight:700;">Registrants</h3>
                            <p id="reg-modal-subtitle" style="margin:0;font-size:13px;color:#64748b;"></p>
                        </div>
                    </div>
                    <button onclick="document.getElementById('event-registrants-modal').style.display='none';"
                        style="background:#f1f5f9;border:none;width:36px;height:36px;border-radius:10px;cursor:pointer;font-size:18px;color:#64748b;">&times;</button>
                </div>
                <div id="reg-modal-body" style="padding:28px;">
                    <div style="text-align:center;padding:40px;color:#94a3b8;">
                        <i class="fas fa-spinner fa-spin" style="font-size:28px;"></i>
                        <p style="margin-top:12px;">Loading registrants...</p>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
    }

    modal.style.display = 'flex';
    document.getElementById('reg-modal-title').textContent = eventTitle || 'Registrants';
    document.getElementById('reg-modal-subtitle').textContent = '';
    document.getElementById('reg-modal-body').innerHTML = `
        <div style="text-align:center;padding:40px;color:#94a3b8;">
            <i class="fas fa-spinner fa-spin" style="font-size:28px;"></i>
            <p style="margin-top:12px;">Loading registrants...</p>
        </div>`;

    try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        const res = await fetch(`/api/admin/announcements/${eventId}/registrants`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            credentials: 'same-origin'
        });
        const data = await res.json();

        if (!data.success) throw new Error(data.message || 'Failed to load registrants');

        const registrants = data.registrants || [];
        const attended = data.attended || [];

        document.getElementById('reg-modal-subtitle').textContent =
            `${data.registrant_count} registrant(s) · ${data.attendance_count} attended`;

        if (registrants.length === 0) {
            document.getElementById('reg-modal-body').innerHTML = `
                <div style="text-align:center;padding:50px;color:#94a3b8;">
                    <i class="fas fa-user-slash" style="font-size:42px;margin-bottom:14px;display:block;"></i>
                    <p style="font-size:15px;font-weight:600;">No registrants yet</p>
                    <p style="font-size:13px;">No students have registered for this event.</p>
                </div>`;
            return;
        }

        const rows = registrants.map(r => {
            const hasAttended = attended.includes(r.student_number);
            return `<tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:12px 10px;">
                    <div style="font-weight:700;color:#1e293b;">${r.first_name} ${r.last_name}</div>
                    <div style="font-size:12px;color:#64748b;">${r.cvsu_email || ''}</div>
                </td>
                <td style="padding:12px 10px;font-size:13px;color:#374151;">${r.student_number}</td>
                <td style="padding:12px 10px;font-size:13px;color:#374151;">${r.course || r.program || 'N/A'}</td>
                <td style="padding:12px 10px;font-size:13px;color:#374151;">${r.section || 'N/A'}</td>
                <td style="padding:12px 10px;">
                    ${r.attendance_status === 'present'
                        ? `<span style="background:#d1fae5;color:#065f46;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">✅ Present</span>`
                        : r.attendance_status === 'absent'
                            ? `<span style="background:#fee2e2;color:#991b1b;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">❌ Absent</span>`
                            : `<span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">⏳ Pending</span>`
                    }
                </td>
            </tr>`;
        }).join('');

        document.getElementById('reg-modal-body').innerHTML = `
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f0fdf4;">
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Student</th>
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Student No.</th>
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Course</th>
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Section</th>
                            <th style="padding:10px;text-align:left;color:#1a4731;border-bottom:2px solid #d1fae5;">Attendance</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    } catch(e) {
        console.error('Error loading registrants:', e);
        document.getElementById('reg-modal-body').innerHTML =
            '<p style="text-align:center;color:#ef4444;padding:30px;">Error loading registrants.</p>';
    }
};
};