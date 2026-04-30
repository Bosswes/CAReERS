// ========== STUDENT MODULE ==========
const Student = (function() {
    'use strict';
    
    async function loadDashboard() {
        try {
            const profile = await API.getStudentProfile();
            if (profile.success && profile.profile) {
                let completion = 0;
                if (profile.profile.first_name) completion += 25;
                if (profile.profile.last_name) completion += 25;
                if (profile.profile.program) completion += 25;
                if (profile.profile.skills && profile.profile.skills.length > 0) completion += 25;
                
                const pct = document.getElementById('completion-percentage');
                const bar = document.getElementById('profile-progress');
                if (pct) pct.textContent = completion + '%';
                if (bar) bar.style.width = completion + '%';
            }
            
            const jobs = await API.getJobs();
            if (jobs.success) {
                const el = document.getElementById('available-jobs-count');
                if (el) el.textContent = jobs.jobs.length;
            }
            
            const recommendations = await API.getStudentRecommendations();
            if (recommendations.success && recommendations.recommendations) {
                const el = document.getElementById('recommendations-count');
                if (el) el.textContent = recommendations.recommendations.length;
                loadRecommendedJobs(recommendations.recommendations);
            }
            
            const announcements = await API.getStudentAnnouncements();
            if (announcements.success) {
                const el = document.getElementById('announcements-count');
                if (el) el.textContent = announcements.announcements.length;
            }
            
            const ojtOfferings = await API.getStudentOjtOfferings();
            if (ojtOfferings.success) {
                const el = document.getElementById('ojt-count');
                if (el) el.textContent = ojtOfferings.offerings.length;
            }
            
        } catch (error) {
            console.error('Error loading student dashboard:', error);
            Utils.showToast('Error loading dashboard', 'error');
        }
    }
    
    function loadRecommendedJobs(recommendations) {
        const container = document.getElementById('recommended-jobs-scroll');
        if (!container) return;
        
        container.innerHTML = '';
        
        if (!recommendations || recommendations.length === 0) {
            container.innerHTML = '<p class="text-muted">No recommendations yet. Complete your profile for better matches.</p>';
            return;
        }
        
        recommendations.slice(0, 5).forEach(job => {
            const card = document.createElement('div');
            card.className = 'job-card';
            card.innerHTML = `
                <div class="job-header">
                    <div class="job-title">
                        <h4>${Utils.truncateText(job.title, 40)}</h4>
                        <p>${job.employer_name || 'Company'}</p>
                    </div>
                    <span class="match-score high">${job.match_score}%</span>
                </div>
                <div class="job-details">
                    <span><i class="fas fa-map-marker-alt"></i> ${job.location || 'N/A'}</span>
                    <span><i class="fas fa-briefcase"></i> ${job.job_type || 'N/A'}</span>
                </div>
                <div class="job-description">${Utils.truncateText(job.description || 'No description available', 100)}</div>
            `;
            container.appendChild(card);
        });
    }
    
    function showProfile() {
        loadProfileForm();
        setupProfileListeners();
    }
    
    const courseToSection = {
        'BS Computer Science': 'BSCS',
        'BS Information Technology': 'BSIT',
        'BS Computer Engineering': 'BSCpE',
        'Bachelor of Secondary Education': 'BSED',
        'BS Business Management': 'BSBM',
        'BS Hospitality Management': 'BSHM',
        'BS Industrial Technology': 'BSIT-Ind',
        'BS Nursing': 'BSN',
        'BS Medical Technology': 'BSMT',
    };

    async function loadProfileForm() {
        try {
            const profile = await API.getStudentProfile();
            if (profile.success && profile.profile) {
                const p = profile.profile;
                document.getElementById('profile-name').value = `${p.first_name || ''} ${p.last_name || ''}`.trim();
                document.getElementById('profile-email').value = p.cvsu_email || '';
                document.getElementById('profile-student-id').value = p.student_number || '';
                document.getElementById('profile-contact').value = p.contact_number || '';
                document.getElementById('profile-degree').value = p.program || '';
                document.getElementById('profile-year-level').value = p.year_level ? String(p.year_level) : '';
                document.getElementById('profile-gwa').value = p.general_weighted_average || '';
                document.getElementById('profile-skills').value = (p.skills || []).join(', ');

                // Load profile photo from DATABASE (not sessionStorage)
                if (p.profile_photo) {
                    const preview = document.getElementById('profile-photo-preview');
                    const icon = document.getElementById('profile-photo-icon');
                    if (icon) icon.style.display = 'none';
                    if (preview) preview.innerHTML = `<img src="${p.profile_photo}" style="width:100%;height:100%;object-fit:cover;">`;
                    const removeBtn = document.getElementById('remove-photo-btn');
                    if (removeBtn) removeBtn.style.display = 'inline-block';
                    // Update sidebar avatar
                    const avatar = document.getElementById('sidebar-avatar-initials');
                    if (avatar) {
                        avatar.style.backgroundImage = `url(${p.profile_photo})`;
                        avatar.style.backgroundSize = 'cover';
                        avatar.style.backgroundPosition = 'center';
                        avatar.style.color = 'transparent';
                        avatar.textContent = '';
                    }
                    // Save to sessionStorage keyed by student number
                    sessionStorage.setItem('profilePhoto_' + p.student_number, p.profile_photo);
                }

                // Load educational background into sessionStorage for resume
                const regData = JSON.parse(sessionStorage.getItem('registrationData') || '{}');
                const updatedRegData = {
                    ...regData,
                    shsSchool:   p.shs_school    || regData.shsSchool   || '',
                    shsYearGrad: p.shs_year_grad || regData.shsYearGrad || '',
                    shsType:     p.shs_type      || regData.shsType     || '',
                    hsSchool:    p.hs_school     || regData.hsSchool    || '',
                    hsYearGrad:  p.hs_year_grad  || regData.hsYearGrad  || '',
                    hsType:      p.hs_type       || regData.hsType      || '',
                    elemSchool:  p.elem_school   || regData.elemSchool  || '',
                    elemYearGrad:p.elem_year_grad|| regData.elemYearGrad|| '',
                    elemType:    p.elem_type     || regData.elemType    || '',
                };
                sessionStorage.setItem('registrationData', JSON.stringify(updatedRegData));

                // Save personal details from DB to currentUser so resume always shows them
                const currentUser = JSON.parse(sessionStorage.getItem('currentUser') || '{}');
                currentUser.birth_date     = p.birth_date     || currentUser.birth_date     || '';
                currentUser.birth_place    = p.birth_place    || currentUser.birth_place    || '';
                currentUser.full_address   = p.full_address   || currentUser.full_address   || '';
                currentUser.shs_school     = p.shs_school     || currentUser.shs_school     || '';
                currentUser.shs_year_grad  = p.shs_year_grad  || currentUser.shs_year_grad  || '';
                currentUser.hs_school      = p.hs_school      || currentUser.hs_school      || '';
                currentUser.hs_year_grad   = p.hs_year_grad   || currentUser.hs_year_grad   || '';
                currentUser.elem_school    = p.elem_school    || currentUser.elem_school    || '';
                currentUser.elem_year_grad = p.elem_year_grad || currentUser.elem_year_grad || '';
                sessionStorage.setItem('currentUser', JSON.stringify(currentUser));

                // Load character references from DB
                const refMap = {
                    'ref1_name': 'r-ref1-name', 'ref1_position': 'r-ref1-position',
                    'ref1_company': 'r-ref1-company', 'ref1_contact': 'r-ref1-contact',
                    'ref2_name': 'r-ref2-name', 'ref2_position': 'r-ref2-position',
                    'ref2_company': 'r-ref2-company', 'ref2_contact': 'r-ref2-contact',
                };
                Object.entries(refMap).forEach(([dbField, elId]) => {
                    const el = document.getElementById(elId);
                    if (el && p[dbField]) el.value = p[dbField];
                });

                // Auto-set section based on course
                const sectionField = document.getElementById('profile-section');
                if (sectionField) {
                    if (p.section) {
                        sectionField.value = p.section;
                    } else if (p.program) {
                        sectionField.value = courseToSection[p.program] || '';
                    }
                    sectionField.readOnly = false;
                    sectionField.style.background = '';
                    sectionField.style.cursor = '';
                    sectionField.placeholder = 'e.g. BSED-4A';
                }
            }
        } catch (error) {
            console.error('Error loading profile:', error);
            Utils.showToast('Error loading profile', 'error');
        }
    }
    
    function setupProfileListeners() {
        const saveBtn = document.getElementById('save-profile');
        if (saveBtn) saveBtn.addEventListener('click', saveProfile);
        
        const cancelBtn = document.getElementById('cancel-profile');
        if (cancelBtn) cancelBtn.addEventListener('click', () => UI.navigateTo('student-dashboard'));
    }
    
    async function saveProfile() {
        const name = document.getElementById('profile-name').value.trim();
        const contact = document.getElementById('profile-contact').value;
        const degree = document.getElementById('profile-degree').value;
        const yearLevelRaw = document.getElementById('profile-year-level').value;
        const yearLevel = yearLevelRaw ? yearLevelRaw.replace(/\D/g, '') : null;
        const gwa = parseFloat(document.getElementById('profile-gwa').value);
        const skills = document.getElementById('profile-skills').value;
        const section = document.getElementById('profile-section').value.trim();

        const nameParts = name.split(' ');
        const firstName = nameParts[0] || '';
        const lastName = nameParts.slice(1).join(' ') || '';
        const yearLevelNum = yearLevel ? parseInt(yearLevel) : null;

        // Get photo from preview (base64)
        const photoImg = document.querySelector('#profile-photo-preview img');
        const profilePhoto = photoImg ? photoImg.src : null;

        // Get character references
        const ref1Name     = document.getElementById('r-ref1-name')?.value.trim()     || null;
        const ref1Position = document.getElementById('r-ref1-position')?.value.trim() || null;
        const ref1Company  = document.getElementById('r-ref1-company')?.value.trim()  || null;
        const ref1Contact  = document.getElementById('r-ref1-contact')?.value.trim()  || null;
        const ref2Name     = document.getElementById('r-ref2-name')?.value.trim()     || null;
        const ref2Position = document.getElementById('r-ref2-position')?.value.trim() || null;
        const ref2Company  = document.getElementById('r-ref2-company')?.value.trim()  || null;
        const ref2Contact  = document.getElementById('r-ref2-contact')?.value.trim()  || null;

        try {
            await API.updateStudentProfile({
                first_name: firstName,
                last_name: lastName,
                contact_number: contact,
                program: degree,
                year_level: yearLevelNum,
                general_weighted_average: gwa,
                skills: skills,
                section: section,
                profile_photo: profilePhoto,
                ref1_name: ref1Name,
                ref1_position: ref1Position,
                ref1_company: ref1Company,
                ref1_contact: ref1Contact,
                ref2_name: ref2Name,
                ref2_position: ref2Position,
                ref2_company: ref2Company,
                ref2_contact: ref2Contact
            });
            
            // Save photo to sessionStorage per student after successful save
            const photoImgAfterSave = document.querySelector('#profile-photo-preview img');
            if (photoImgAfterSave) {
                const user = JSON.parse(sessionStorage.getItem('currentUser') || '{}');
                const studentNo = user.student_number || user.id || 'guest';
                sessionStorage.setItem('profilePhoto_' + studentNo, photoImgAfterSave.src);
            }
            // Reset upload button to normal
            const uploadBtn = document.querySelector('button[onclick*="profile-photo-input"]');
            if (uploadBtn) {
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Photo';
                uploadBtn.style.background = '';
            }
            Utils.showToast('Profile saved successfully!', 'success');
            UI.navigateTo('student-dashboard');
            
        } catch (error) {
            console.error('Error saving profile:', error);
            Utils.showToast('Error saving profile', 'error');
        }
    }
    
    function showRecommendations() {
        loadRecommendations();
        setupFilters();
    }
    
    async function loadRecommendations() {
        const container = document.getElementById('jobs-grid');
        if (!container) return;
        
        container.innerHTML = '<div class="loading">Loading recommendations...</div>';
        
        try {
            const response = await API.getStudentRecommendations();
            
            if (response.success && response.recommendations && response.recommendations.length > 0) {
                document.getElementById('profile-warning').style.display = 'none';
                container.innerHTML = '';
                
                // Filter out internship jobs — sila nasa OJT Offerings na
                const nonInternshipJobs = response.recommendations.filter(
                    job => job.job_type !== 'internship'
                );
                
                if (nonInternshipJobs.length === 0) {
                    container.innerHTML = '<p class="text-muted">No job recommendations yet. Complete your profile to get matches.</p>';
                    return;
                }
                
                nonInternshipJobs.forEach(job => {
                    const card = createJobCard(job);
                    container.appendChild(card);
                });
            } else {
                document.getElementById('profile-warning').style.display = 'block';
                container.innerHTML = '<p class="text-muted">No recommendations yet. Complete your profile to get matches.</p>';
            }
        } catch (error) {
            console.error('Error loading recommendations:', error);
            container.innerHTML = '<p class="text-muted">Error loading recommendations</p>';
        }
    }
    
    function showJobModal(job) {
        // Remove existing modal if any
        const existing = document.getElementById('job-detail-modal');
        if (existing) existing.remove();

        const modal = document.createElement('div');
        modal.id = 'job-detail-modal';
        modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;';
        modal.innerHTML = `
            <div style="background:white;border-radius:16px;padding:32px;max-width:600px;width:100%;max-height:85vh;overflow-y:auto;position:relative;">
                <button id="close-job-modal" style="position:absolute;top:16px;right:16px;background:none;border:none;font-size:22px;cursor:pointer;color:#6b7280;">&times;</button>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                    <div>
                        <h2 style="font-size:20px;font-weight:700;color:#1a1a1a;margin:0 0 4px;">${job.title}</h2>
                        <p style="color:#6b7280;margin:0;">${job.employer_name || 'Company'}</p>
                    </div>
                    <span style="background:#dcfce7;color:#16a34a;padding:6px 14px;border-radius:20px;font-weight:700;font-size:15px;">${job.match_score}%</span>
                </div>
                <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;">
                    <span style="color:#555;"><i class="fas fa-map-marker-alt" style="color:#2E7D32;"></i> ${job.location || 'N/A'}</span>
                    <span style="color:#555;"><i class="fas fa-briefcase" style="color:#2E7D32;"></i> ${job.job_type || 'N/A'}</span>
                    <span style="color:#555;"><i class="fas fa-industry" style="color:#2E7D32;"></i> ${job.industry || 'N/A'}</span>
                    ${job.employer_contact ? `<span style="color:#555;"><i class="fas fa-envelope" style="color:#2E7D32;"></i> ${job.employer_contact}</span>` : ''}
                </div>
                <hr style="margin-bottom:16px;border-color:#e5e7eb;">
                <h4 style="font-weight:600;margin-bottom:8px;">Job Description</h4>
                <p style="color:#374151;line-height:1.7;margin-bottom:16px;">${job.description || 'No description available.'}</p>
                ${job.requirements ? `<h4 style="font-weight:600;margin-bottom:8px;">Requirements</h4><p style="color:#374151;line-height:1.7;margin-bottom:16px;">${job.requirements}</p>` : ''}
                ${job.required_skills && job.required_skills.length > 0 ? `
                    <h4 style="font-weight:600;margin-bottom:8px;">Required Skills</h4>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
                        ${job.required_skills.map(s => `<span style="background:#f0fdf4;border:1px solid #86efac;color:#16a34a;padding:4px 10px;border-radius:20px;font-size:13px;">${s}</span>`).join('')}
                    </div>
                ` : ''}
                <button class="btn-apply-modal" data-job-id="${job.job_id}" style="background:#2E7D32;color:white;border:none;padding:12px 24px;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;width:100%;margin-top:8px;">
                    <i class="fas fa-paper-plane"></i> Apply Now
                </button>
            </div>
        `;

        document.body.appendChild(modal);

        document.getElementById('close-job-modal').addEventListener('click', () => modal.remove());
        modal.addEventListener('click', (e) => { if (e.target === modal) modal.remove(); });

        modal.querySelector('.btn-apply-modal').addEventListener('click', async function() {
            const jobId = this.dataset.jobId;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing resume...';

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.content;

                // Generate resume PDF from the printable resume div
                const resumeElement = document.getElementById('resume-content');
                let resumeBase64 = null;

                if (resumeElement && typeof html2pdf !== 'undefined') {
                    // Temporarily open resume modal to render it properly
                    if (typeof openResumeModal === 'function') openResumeModal();

                    // Hide ref inputs before PDF capture so they don't appear
                    document.querySelectorAll('.ref-inputs').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('.ref-fill-note').forEach(el => el.style.display = 'none');
                    const refsDisplay = document.getElementById('r-refs-display');
                    if (refsDisplay) {
                        refsDisplay.innerHTML = buildRefsHTML ? buildRefsHTML() : refsDisplay.innerHTML;
                        refsDisplay.style.display = 'block';
                    }

                   const pdfBlob = await html2pdf().set({
                        margin: [2, 10, 8, 10],
                        filename: 'resume.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { 
                            scale: 2, 
                            useCORS: true, 
                            logging: false,
                            scrollY: 0,
                            windowHeight: resumeElement.scrollHeight
                        },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                        pagebreak: { mode: 'avoid-all' }
                    }).from(resumeElement).outputPdf('blob');

                    // Restore ref inputs after PDF capture
                    document.querySelectorAll('.ref-inputs').forEach(el => el.style.display = 'grid');
                    document.querySelectorAll('.ref-fill-note').forEach(el => el.style.display = 'block');

                    // Close resume modal after generating
                    if (typeof closeResumeModal === 'function') closeResumeModal();

                    // Convert blob to base64
                    resumeBase64 = await new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onloadend = () => resolve(reader.result.split(',')[1]);
                        reader.readAsDataURL(pdfBlob);
                    });
                }

                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';

                const res = await fetch('/api/student/apply', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    credentials: 'same-origin',
                    body: JSON.stringify({ job_id: jobId, resume_base64: resumeBase64 })
                });
                const data = await res.json();
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check"></i> Applied!';
                    btn.style.background = '#16a34a';
                    Utils.showToast(data.message, 'success');
                } else {
                    btn.innerHTML = '<i class="fas fa-check"></i> Already Applied';
                    btn.style.background = '#6b7280';
                    Utils.showToast(data.message, 'warning');
                }
            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Apply Now';
                Utils.showToast('Error submitting application', 'error');
            }
        });
    }

    function createJobCard(job) {
        const card = document.createElement('div');
        card.className = 'job-card';
        card.style.cursor = 'pointer';
        card.innerHTML = `
            <div class="job-header">
                <div>
                    <div class="job-title">${Utils.truncateText(job.title, 40)}</div>
                    <div class="job-company">${job.employer_name || 'Company'}</div>
                </div>
                <span class="match-score high">${job.match_score}%</span>
            </div>
            <div class="job-details">
                <span><i class="fas fa-map-marker-alt"></i> ${job.location || 'N/A'}</span>
                <span><i class="fas fa-briefcase"></i> ${job.job_type || 'N/A'}</span>
            </div>
            <div class="job-description">${Utils.truncateText(job.description || 'No description available', 120)}</div>
            <div style="margin-top:12px;">
                <button class="btn-apply" data-job-id="${job.job_id}" style="background:#2E7D32;color:white;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;width:100%;transition:all 0.2s;">
                    <i class="fas fa-paper-plane"></i> View Details & Apply
                </button>
            </div>
        `;

        // Open modal on card or button click
        card.addEventListener('click', () => showJobModal(job));
        const applyBtn = card.querySelector('.btn-apply');
        if (applyBtn) {
            applyBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                showJobModal(job);
            });
        }

        return card;
    }
    
    function setupFilters() {
        const debounced = Utils.debounce(filterJobs, 300);
        const industry = document.getElementById('filter-industry');
        const jobType = document.getElementById('filter-job-type');
        const location = document.getElementById('filter-location');
        const match = document.getElementById('filter-match');
        
        if (industry) industry.addEventListener('change', filterJobs);
        if (jobType) jobType.addEventListener('change', filterJobs);
        if (location) location.addEventListener('input', debounced);
        if (match) match.addEventListener('change', filterJobs);
    }
    
    async function filterJobs() {
        const industry = document.getElementById('filter-industry')?.value;
        const jobType = document.getElementById('filter-job-type')?.value;
        const location = document.getElementById('filter-location')?.value?.toLowerCase();
        const minMatch = parseInt(document.getElementById('filter-match')?.value) || 0;
        
        try {
            const response = await API.getStudentRecommendations();
            let jobs = response.success ? response.recommendations : [];
            
            if (industry && industry !== '') {
                jobs = jobs.filter(job => job.industry === industry);
            }
            if (jobType && jobType !== '') {
                jobs = jobs.filter(job => job.job_type === jobType);
            }
            if (location) {
                jobs = jobs.filter(job => (job.location || '').toLowerCase().includes(location));
            }
            if (minMatch > 0) {
                jobs = jobs.filter(job => (job.match_score || 0) >= minMatch);
            }
            
            const container = document.getElementById('jobs-grid');
            if (!container) return;
            
            container.innerHTML = '';
            
            if (jobs.length === 0) {
                container.innerHTML = '<p class="text-muted">No jobs match your filters.</p>';
                return;
            }
            
            jobs.forEach(job => {
                const card = createJobCard(job);
                container.appendChild(card);
            });
            
        } catch (error) {
            console.error('Error filtering jobs:', error);
        }
    }
    
    function showOjtOfferings() {
        loadOjtOfferings();
    }
    
    async function loadOjtOfferings() {
        const container = document.getElementById('ojt-grid');
        if (!container) return;
        
        container.innerHTML = '<div class="loading">Loading OJT offerings...</div>';
        
        try {
            // Load both: dedicated OJT offerings + internship jobs from recommendations
            const [ojtResponse, recoResponse] = await Promise.all([
                API.getStudentOjtOfferings(),
                API.getStudentRecommendations()
            ]);

            const allCards = [];

            // Dedicated OJT offerings (existing)
            if (ojtResponse.success && ojtResponse.offerings && ojtResponse.offerings.length > 0) {
                ojtResponse.offerings.forEach(ojt => {
                    allCards.push({ type: 'ojt', data: ojt });
                });
            }

            // Internship jobs from recommendations
            if (recoResponse.success && recoResponse.recommendations) {
                recoResponse.recommendations
                    .filter(job => job.job_type === 'internship')
                    .forEach(job => {
                        allCards.push({ type: 'job', data: job });
                    });
            }

            container.innerHTML = '';

            if (allCards.length === 0) {
                container.innerHTML = '<p class="text-muted">No OJT offerings available at the moment.</p>';
                return;
            }

            allCards.forEach(item => {
                const card = document.createElement('div');
                card.className = 'job-card';

                if (item.type === 'ojt') {
                    const ojt = item.data;
                    card.innerHTML = `
                        <div class="job-header">
                            <div>
                                <div class="job-title">${Utils.truncateText(ojt.title, 40)}</div>
                                <div class="job-company">${ojt.company_name}</div>
                            </div>
                            <span class="match-score">${ojt.slots} slots</span>
                        </div>
                        <div class="job-details">
                            <span><i class="fas fa-map-marker-alt"></i> ${ojt.location || 'N/A'}</span>
                            <span><i class="fas fa-clock"></i> ${ojt.duration || 'N/A'}</span>
                        </div>
                        <div class="job-description">${Utils.truncateText(ojt.description || 'No description', 100)}</div>
                    `;
                } else {
                    const job = item.data;
                    card.innerHTML = `
                        <div class="job-header">
                            <div>
                                <div class="job-title">${Utils.truncateText(job.title, 40)}</div>
                                <div class="job-company">${job.employer_name || 'Company'}</div>
                            </div>
                            <span class="match-score high">${job.match_score}%</span>
                        </div>
                        <div class="job-details">
                            <span><i class="fas fa-map-marker-alt"></i> ${job.location || 'N/A'}</span>
                            <span><i class="fas fa-clock"></i> ${job.ojt_hours ? job.ojt_hours + ' hours' : 'Internship'}</span>
                        </div>
                        <div class="job-description">${Utils.truncateText(job.description || 'No description', 100)}</div>
                        <div style="margin-top:12px;">
                            <button class="btn-apply" data-job-id="${job.job_id}" style="background:#2E7D32;color:white;border:none;padding:8px 18px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;width:100%;">
                                <i class="fas fa-paper-plane"></i> View Details & Apply
                            </button>
                        </div>
                    `;
                    card.addEventListener('click', () => showJobModal(job));
                    const btn = card.querySelector('.btn-apply');
                    if (btn) btn.addEventListener('click', (e) => { e.stopPropagation(); showJobModal(job); });
                }

                container.appendChild(card);
            });

        } catch (error) {
            console.error('Error loading OJT offerings:', error);
            container.innerHTML = '<p class="text-muted">Error loading OJT offerings</p>';
        }
    }
    
    function showAnnouncements() {
        loadAnnouncements();
    }
    
    async function loadAnnouncements() {
        const container = document.getElementById('student-announcements-grid');
        if (!container) return;

        container.innerHTML = '<div class="loading">Loading announcements...</div>';

        try {
            const response = await API.getStudentAnnouncements();

            if (response.success && response.announcements && response.announcements.length > 0) {
                container.innerHTML = '';

                // Check registration status for all events from DB
                const registrationMap = {};
                const studentNumberMap = {};
                await Promise.all(
                    response.announcements
                        .filter(ann => ann.announcement_type === 'event')
                        .map(async ann => {
                            try {
                                const status = await API.getMyEventRegistration(ann.announcement_id);
                                registrationMap[ann.announcement_id] = status.registered;
                                if (status.student_number) {
                                    studentNumberMap[ann.announcement_id] = status.student_number;
                                }
                            } catch (e) {
                                registrationMap[ann.announcement_id] = false;
                            }
                        })
                );

                response.announcements.forEach(ann => {
                    const isRegistered = registrationMap[ann.announcement_id] || false;
                    const card = document.createElement('div');
                    card.className = 'student-announcement-card';
                    card.innerHTML = `
                        <div class="announcement-header">
                            <h3>${ann.title}</h3>
                            <span class="announcement-type">${ann.announcement_type}</span>
                        </div>
                        <div class="announcement-date">
                            <i class="fas fa-calendar-alt"></i> ${Utils.formatDateShort(ann.start_date)}
                        </div>
                        <div class="announcement-body">${ann.content}</div>
                        ${ann.announcement_type === 'event' ? `
                            <div class="announcement-footer" style="margin-top:12px;">
                                <button class="btn-primary btn-sm register-event-link"
                                    data-id="${ann.announcement_id}"
                                    data-title="${ann.title}"
                                    style="padding:8px 14px; border-radius:6px; background:#16a34a; color:white; border:none; cursor:pointer; ${isRegistered ? 'display:none;' : ''}">
                                    <i class="fas fa-user-plus"></i> Register for this Event
                                </button>
                                <button class="btn-secondary btn-sm show-my-qr"
                                    data-event="${ann.title}"
                                    data-id="${ann.announcement_id}"
                                    style="margin-left:8px; padding:8px 14px; border-radius:6px; ${isRegistered ? '' : 'display:none;'}">
                                    <i class="fas fa-qrcode"></i> Get My QR Code
                                </button>
                                <p style="font-size:12px; color:#888; margin-top:6px;">📌 Register first, then click "Get My QR Code" to get your attendance QR.</p>
                            </div>
                        ` : ''}
                    `;
                    container.appendChild(card);
                });

                container.querySelectorAll('.register-event-link').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const eventId = this.dataset.id;
                        const registerBtn = this;
                        registerBtn.disabled = true;
                        registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';

                        try {
                            const data = await API.registerForEvent(eventId);
                            if (!data.success) {
                                Utils.showToast(data.message || 'Already registered or error occurred.', 'warning');
                                registerBtn.disabled = false;
                                registerBtn.innerHTML = '<i class="fas fa-user-plus"></i> Register for this Event';
                                return;
                            }

                            // Store student number for QR
                            if (data.student_number) {
                                studentNumberMap[eventId] = data.student_number;
                            }

                            registerBtn.style.display = 'none';
                            const qrBtn = container.querySelector(`.show-my-qr[data-id="${eventId}"]`);
                            if (qrBtn) qrBtn.style.display = 'inline-block';
                            Utils.showToast('Registered successfully! You can now get your QR code.', 'success');
                        } catch(e) {
                            Utils.showToast('Registration failed. Please try again.', 'error');
                            registerBtn.disabled = false;
                            registerBtn.innerHTML = '<i class="fas fa-user-plus"></i> Register for this Event';
                        }
                    });
                });

                container.querySelectorAll('.show-my-qr').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        const eventId = this.dataset.id;
                        const eventName = this.dataset.event;

                        // Get student number: from map, or fetch from API
                        let studentNumber = studentNumberMap[eventId];
                        if (!studentNumber) {
                            try {
                                const status = await API.getMyEventRegistration(eventId);
                                studentNumber = status.student_number;
                            } catch(e) {}
                        }
                        if (!studentNumber) {
                            // Fallback to sessionStorage
                            const user = JSON.parse(sessionStorage.getItem('currentUser') || '{}');
                            studentNumber = user.student_number || user.id || '';
                        }
                        if (!studentNumber) {
                            Utils.showToast('Student number not found. Please re-login.', 'error');
                            return;
                        }

                        const modal = document.getElementById('qr-modal');
                        const qrContainer = document.getElementById('qr-code');
                        const eventEl = document.getElementById('qr-event-name');
                        if (!modal || !qrContainer) return;
                        qrContainer.innerHTML = '';
                        if (typeof QRCode !== 'undefined') {
                            new QRCode(qrContainer, {
                                text: studentNumber,
                                width: 250,
                                height: 250,
                                colorDark: "#2E7D32",
                                colorLight: "#ffffff",
                                correctLevel: QRCode.CorrectLevel.H
                            });
                            if (eventEl) eventEl.textContent = `${eventName} — ${studentNumber}`;
                            modal.style.display = 'flex';
                        } else {
                            Utils.showToast('QR library not available', 'error');
                        }
                    });
                });

            } else {
                container.innerHTML = '<p class="text-muted">No announcements available.</p>';
            }
        } catch (error) {
            console.error('Error loading announcements:', error);
            container.innerHTML = '<p class="text-muted">Error loading announcements</p>';
        }
    }
    
    async function loadNotifications() {
        try {
            const res = await API.getNotifications();
            if (!res.success) return;

            const badge = document.getElementById('bell-badge');
            const bellWrapper = document.getElementById('bell-wrapper');
            const list = document.getElementById('notif-list');

            // Show bell only for students
            if (bellWrapper) bellWrapper.classList.add('visible');

            // Desktop bell
            const bellWrapperDesktop = document.getElementById('bell-wrapper-desktop');
            const bellBadgeDesktop = document.getElementById('bell-badge-desktop');
            const listDesktop = document.getElementById('notif-list-desktop');
            const bellBtnDesktop = document.getElementById('bell-btn-desktop');
            const dropdownDesktop = document.getElementById('notif-dropdown-desktop');

            if (bellWrapperDesktop) bellWrapperDesktop.style.display = 'block';

            if (bellBadgeDesktop) {
                if (res.unread_count > 0) {
                    bellBadgeDesktop.textContent = res.unread_count > 99 ? '99+' : res.unread_count;
                    bellBadgeDesktop.style.display = 'block';
                } else {
                    bellBadgeDesktop.style.display = 'none';
                }
            }

            if (listDesktop) {
                if (res.notifications.length === 0) {
                    listDesktop.innerHTML = '<p style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;">No notifications yet</p>';
                } else {
                    listDesktop.innerHTML = res.notifications.map(n => `
                        <div class="notif-item ${n.is_read ? '' : 'unread'}" onclick="Student.readNotif(${n.id})" style="padding:12px 16px;border-bottom:1px solid #f0f0f0;cursor:pointer;">
                            <div class="notif-title" style="font-weight:600;font-size:13px;">
                                <i class="fas ${n.type === 'job' ? 'fa-briefcase' : 'fa-bullhorn'}" style="color:#2E7D32;margin-right:6px;"></i>
                                ${n.title}
                            </div>
                            <div class="notif-msg" style="font-size:12px;color:#64748b;margin-top:2px;">${n.message}</div>
                            <div class="notif-time" style="font-size:11px;color:#94a3b8;margin-top:4px;">${new Date(n.created_at).toLocaleDateString('en-PH', {month:'short', day:'numeric', hour:'2-digit', minute:'2-digit'})}</div>
                        </div>
                    `).join('');
                }
            }

            if (bellBtnDesktop && dropdownDesktop) {
                bellBtnDesktop.onclick = (e) => {
                    e.stopPropagation();
                    dropdownDesktop.style.display = dropdownDesktop.style.display === 'none' ? 'block' : 'none';
                };
                document.addEventListener('click', () => { dropdownDesktop.style.display = 'none'; }, { once: true });
            }

            // Update badge
            if (badge) {
                if (res.unread_count > 0) {
                    badge.textContent = res.unread_count > 99 ? '99+' : res.unread_count;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }

            // Populate dropdown
            if (list) {
                if (res.notifications.length === 0) {
                    list.innerHTML = '<p style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;">No notifications yet</p>';
                } else {
                    list.innerHTML = res.notifications.map(n => `
                        <div class="notif-item ${n.is_read ? '' : 'unread'}" onclick="Student.readNotif(${n.id})">
                            <div class="notif-title">
                                <i class="fas ${n.type === 'job' ? 'fa-briefcase' : 'fa-bullhorn'}" style="color:#2E7D32;margin-right:6px;"></i>
                                ${n.title}
                            </div>
                            <div class="notif-msg">${n.message}</div>
                            <div class="notif-time">${new Date(n.created_at).toLocaleDateString('en-PH', {month:'short', day:'numeric', hour:'2-digit', minute:'2-digit'})}</div>
                        </div>
                    `).join('');
                }
            }

            // Bell toggle
            const bellBtn = document.getElementById('bell-btn');
            const dropdown = document.getElementById('notif-dropdown');
            if (bellBtn && dropdown) {
                bellBtn.onclick = (e) => {
                    e.stopPropagation();
                    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
                };
                document.addEventListener('click', () => { dropdown.style.display = 'none'; }, { once: true });
            }

        } catch (err) {
            console.error('Notification load error:', err);
        }
    }

    async function markAllRead() {
        await API.markAllNotificationsRead();
        loadNotifications();
    }

    async function readNotif(id) {
        await API.markNotificationRead(id);
        loadNotifications();
    }

    return {
        loadDashboard,
        showProfile,
        showRecommendations,
        showOjtOfferings,
        showAnnouncements,
        loadNotifications,
        markAllRead,
        readNotif
    };
})();