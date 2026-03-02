<template>
  <div class="become-agent-page">
    <!-- Header -->
    <header class="page-header">
      <div class="header-content">
        <router-link to="/dashboard" class="back-link">← Back to Dashboard</router-link>
        <h1 class="header-title">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
          <span class="header-sub">Become an Agent</span>
        </h1>
      </div>
    </header>

    <div class="page-body">
      <!-- Progress Steps -->
      <div class="steps-bar">
        <div v-for="s in steps" :key="s.num" class="step" :class="{ active: currentStep === s.num, done: currentStep > s.num }">
          <div class="step-circle">
            <span v-if="currentStep > s.num">✓</span>
            <span v-else>{{ s.num }}</span>
          </div>
          <span class="step-label">{{ s.label }}</span>
        </div>
      </div>

      <!-- Already Applied State -->
      <div v-if="existingApplication" :class="['result-card', existingApplication.status === 'approved' ? 'result-success' : existingApplication.status === 'rejected' ? 'result-rejected' : 'result-pending']">
        <div class="result-icon">{{ existingApplication.status === 'approved' ? '✅' : existingApplication.status === 'rejected' ? '🚫' : '📋' }}</div>
        <h2 v-if="existingApplication.status === 'pending'">Application Under Review</h2>
        <h2 v-else-if="existingApplication.status === 'approved'">You're Already an Agent!</h2>
        <h2 v-else-if="existingApplication.status === 'rejected'">Application Was Rejected</h2>

        <p class="result-message" v-if="existingApplication.status === 'pending'">Your application is currently under admin review. Please allow up to 12 hours for a response.</p>
        <p class="result-message" v-else-if="existingApplication.status === 'approved'">Your application has been approved! You are now an agent.</p>
        <p class="result-message" v-else-if="existingApplication.status === 'rejected'">
          Your previous application was rejected by RealtyLinkPH Buddy.
          <span v-if="existingApplication.rejection_reason"><br><br>{{ existingApplication.rejection_reason }}</span>
        </p>

        <div v-if="existingApplication.status === 'rejected' && existingApplication._canReapplyAt" class="cooldown-info">
          <span class="cooldown-icon">⏳</span>
          <span>You can reapply at <strong>{{ formatReapplyTime(existingApplication._canReapplyAt) }}</strong></span>
        </div>

        <div class="result-actions">
          <router-link v-if="existingApplication.status === 'approved'" to="/agent/dashboard" class="btn-primary-lg">Go to Agent Dashboard →</router-link>
          <router-link to="/dashboard" class="btn-secondary-lg">Back to Dashboard</router-link>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════ -->
      <!-- STEP 1: Professional Information -->
      <!-- ════════════════════════════════════════════════ -->
      <div v-else-if="currentStep === 1" class="step-content">
        <div class="step-card">
          <div class="card-header">
            <h2>Professional Information</h2>
            <p>Fill in your details to start the verification process.</p>
          </div>

          <div class="form-grid">
            <div class="form-group full">
              <label>Full Name <span class="req">*</span></label>
              <input v-model="form.applicant_name" type="text" class="form-input" placeholder="Your full legal name" />
              <span v-if="errors.applicant_name" class="field-error">{{ errors.applicant_name }}</span>
            </div>

            <div class="form-group">
              <label>PRC License Number <span class="req">*</span></label>
              <input v-model="form.license_number" type="text" class="form-input" placeholder="e.g. 0012345" />
              <span v-if="errors.license_number" class="field-error">{{ errors.license_number }}</span>
            </div>

            <div class="form-group">
              <label>PRC ID Number <span class="req">*</span></label>
              <input v-model="form.prc_id" type="text" class="form-input" placeholder="e.g. REB-0012345" />
              <span v-if="errors.prc_id" class="field-error">{{ errors.prc_id }}</span>
            </div>

            <div class="form-group">
              <label>License Expiry Date <span class="req">*</span></label>
              <input v-model="form.license_expiry_date" type="date" class="form-input" />
              <span v-if="errors.license_expiry_date" class="field-error">{{ errors.license_expiry_date }}</span>
            </div>

            <div class="form-group">
              <label>Accreditation <span class="req">*</span></label>
              <select v-model="form.accreditation" class="form-input">
                <option value="">Select accreditation...</option>
                <option value="REIN">REIN</option>
                <option value="PAMI">PAMI</option>
                <option value="OTHER">Other</option>
              </select>
              <span v-if="errors.accreditation" class="field-error">{{ errors.accreditation }}</span>
            </div>

            <div class="form-group">
              <label>Company Name <span class="req">*</span></label>
              <input v-model="form.company_name" type="text" class="form-input" placeholder="Your real estate company" />
              <span v-if="errors.company_name" class="field-error">{{ errors.company_name }}</span>
            </div>

            <div class="form-group full">
              <label>Company Address <span class="req">*</span></label>
              <input v-model="form.company_address" type="text" class="form-input" placeholder="Full office address" />
              <span v-if="errors.company_address" class="field-error">{{ errors.company_address }}</span>
            </div>

            <div class="form-group full">
              <label>Bio <span class="optional">(Optional)</span></label>
              <textarea v-model="form.bio" class="form-input form-textarea" rows="3" placeholder="Tell us about your real estate experience..."></textarea>
            </div>
          </div>

          <div class="step-footer">
            <router-link to="/dashboard" class="btn-secondary-lg">Cancel</router-link>
            <button class="btn-primary-lg" @click="goToStep2">Next: Upload PRC License →</button>
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════ -->
      <!-- STEP 2: Upload PRC License -->
      <!-- ════════════════════════════════════════════════ -->
      <div v-else-if="currentStep === 2" class="step-content">
        <div class="step-card">
          <div class="card-header">
            <h2>Upload PRC License</h2>
            <p>Upload a clear photo of your PRC license card. RealtyLinkPH Buddy will verify it using AI.</p>
          </div>

          <div class="upload-zone" @click="$refs.prcInput.click()" @dragover.prevent @drop.prevent="handleDrop">
            <input ref="prcInput" type="file" accept="image/jpeg,image/png,image/jpg" class="hidden-input" @change="onFileSelected" />

            <div v-if="!prcPreviewUrl" class="upload-empty">
              <div class="upload-icon-large">🪪</div>
              <h3>Drop your PRC license photo here</h3>
              <p>or click to browse</p>
              <p class="upload-hint">JPG or PNG, max 5MB</p>
            </div>

            <div v-else class="upload-preview" @click.stop>
              <img :src="prcPreviewUrl" alt="PRC License Preview" class="preview-image" />
              <div class="preview-overlay">
                <button class="btn-change" @click.stop="$refs.prcInput.click()">📷 Change Photo</button>
                <button class="btn-remove" @click.stop="removeFile">🗑️ Remove</button>
              </div>
            </div>
          </div>

          <span v-if="errors.prc_license_photo" class="field-error" style="margin-top: 8px; display: block;">{{ errors.prc_license_photo }}</span>

          <div class="info-box">
            <span class="info-icon">💡</span>
            <div>
              <strong>Tips for a clear photo:</strong>
              <ul>
                <li>Ensure the entire PRC card is visible</li>
                <li>Take the photo in good lighting</li>
                <li>Make sure the text on the card is readable</li>
                <li>Avoid glare or shadows on the card</li>
              </ul>
            </div>
          </div>

          <div class="step-footer">
            <button class="btn-secondary-lg" @click="currentStep = 1">← Back</button>
            <button class="btn-primary-lg" @click="submitApplication" :disabled="!prcLicenseFile">
              Submit for Verification →
            </button>
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════ -->
      <!-- STEP 3: AI Verification Loading -->
      <!-- ════════════════════════════════════════════════ -->
      <div v-else-if="currentStep === 3" class="step-content">
        <div class="step-card verify-card">
          <div class="ai-buddy">
            <div class="buddy-avatar">
              <div class="buddy-pulse"></div>
              <img src="/images/buddy-avatar.jpg" alt="RealtyLinkPH Buddy" class="buddy-img" />
            </div>
            <h2>RealtyLinkPH Buddy</h2>
            <p class="buddy-subtitle">AI Verification Assistant</p>
          </div>

          <div class="verify-status">
            <div class="verify-spinner"></div>
            <p class="verify-text">{{ verifyingText }}</p>
            <p class="verify-subtext">This usually takes 10-20 seconds...</p>
          </div>

          <div class="verify-steps-list">
            <div class="verify-step" :class="{ active: verifyPhase >= 1, done: verifyPhase > 1 }">
              <span class="vs-icon">{{ verifyPhase > 1 ? '✅' : '🔍' }}</span>
              <span>Scanning PRC license image...</span>
            </div>
            <div class="verify-step" :class="{ active: verifyPhase >= 2, done: verifyPhase > 2 }">
              <span class="vs-icon">{{ verifyPhase > 2 ? '✅' : '📋' }}</span>
              <span>Extracting license information...</span>
            </div>
            <div class="verify-step" :class="{ active: verifyPhase >= 3 }">
              <span class="vs-icon">{{ verifyPhase > 3 ? '✅' : '🔐' }}</span>
              <span>Verifying against your application...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════ -->
      <!-- STEP 4: Result -->
      <!-- ════════════════════════════════════════════════ -->
      <div v-else-if="currentStep === 4" class="step-content">
        <!-- APPROVED -->
        <div v-if="verificationResult && verificationResult.decision === 'approved'" class="result-card result-success">
          <div class="result-icon">🎉</div>
          <h2>Congratulations!</h2>
          <p class="result-message">{{ verificationResult.message }}</p>

          <!-- Dual AI Verification Badge -->
          <div v-if="verificationResult.verification_method === 'dual_ai'" class="dual-ai-badge">
            <span class="dual-ai-icon">&#x1F916;&#x1F916;</span>
            <span class="dual-ai-text">Dual AI Verified</span>
            <span class="dual-ai-sub">Gemini + DeepSeek</span>
          </div>
          <div v-else class="dual-ai-badge dual-ai-single">
            <span class="dual-ai-icon">&#x1F916;</span>
            <span class="dual-ai-text">AI Verified</span>
            <span class="dual-ai-sub">Gemini Vision</span>
          </div>

          <div class="ai-badge">
            <img src="/images/buddy-avatar.jpg" alt="Buddy" class="ai-badge-avatar" />
            <div>
              <strong>RealtyLinkPH Buddy says:</strong>
              <p>{{ verificationResult.ai_reasoning }}</p>
            </div>
          </div>

          <!-- Authenticity Details - Approved -->
          <div v-if="verificationResult.security_features_found && verificationResult.security_features_found.length" class="authenticity-details authenticity-success">
            <div class="authenticity-header">
              <span class="auth-icon">&#x2705;</span>
              <strong>Security Features Verified</strong>
              <span v-if="verificationResult.authenticity_score" class="auth-score auth-score-good">{{ Math.round(verificationResult.authenticity_score * 100) }}% authentic</span>
            </div>
            <div class="auth-tags">
              <span v-for="feature in verificationResult.security_features_found" :key="feature" class="auth-tag auth-tag-good">{{ feature }}</span>
            </div>
          </div>

          <div class="result-actions">
            <button class="btn-primary-lg" @click="goToAgentDashboard">Go to Agent Dashboard →</button>
          </div>
        </div>

        <!-- REJECTED — Not a PRC License or Fake -->
        <div v-else-if="verificationResult && verificationResult.decision === 'rejected'" class="result-card result-rejected">
          <div class="result-icon">&#x1F6AB;</div>
          <h2>Application Rejected</h2>
          <p class="result-message">{{ verificationResult.message }}</p>

          <!-- Dual AI Badge for rejected -->
          <div v-if="verificationResult.verification_method === 'dual_ai'" class="dual-ai-badge dual-ai-reject">
            <span class="dual-ai-icon">&#x1F916;&#x1F916;</span>
            <span class="dual-ai-text">Dual AI Flagged</span>
            <span class="dual-ai-sub">Gemini + DeepSeek</span>
          </div>

          <div class="ai-badge ai-badge-danger">
            <img src="/images/buddy-avatar.jpg" alt="Buddy" class="ai-badge-avatar" />
            <div>
              <strong>RealtyLinkPH Buddy says:</strong>
              <p>{{ verificationResult.ai_reasoning }}</p>
            </div>
          </div>

          <div v-if="verificationResult.document_type" class="detected-doc-type">
            <span class="ddt-icon">&#x1F4C4;</span>
            <span>Detected document type: <strong>{{ verificationResult.document_type }}</strong></span>
          </div>

          <!-- Red Flags Detected -->
          <div v-if="verificationResult.red_flags_detected && verificationResult.red_flags_detected.length" class="authenticity-details authenticity-danger">
            <div class="authenticity-header">
              <span class="auth-icon">&#x1F6A9;</span>
              <strong>Red Flags Detected</strong>
              <span v-if="verificationResult.authenticity_score !== null && verificationResult.authenticity_score !== undefined" class="auth-score auth-score-bad">{{ Math.round(verificationResult.authenticity_score * 100) }}% authentic</span>
            </div>
            <div class="auth-tags">
              <span v-for="flag in verificationResult.red_flags_detected" :key="flag" class="auth-tag auth-tag-bad">{{ flag }}</span>
            </div>
          </div>

          <!-- Missing Security Features -->
          <div v-if="verificationResult.security_features_missing && verificationResult.security_features_missing.length" class="authenticity-details authenticity-warn">
            <div class="authenticity-header">
              <span class="auth-icon">&#x26A0;&#xFE0F;</span>
              <strong>Missing Security Features</strong>
            </div>
            <div class="auth-tags">
              <span v-for="feature in verificationResult.security_features_missing" :key="feature" class="auth-tag auth-tag-warn">{{ feature }}</span>
            </div>
          </div>

          <!-- DeepSeek Concerns -->
          <div v-if="verificationResult.deepseek_concerns && verificationResult.deepseek_concerns.length" class="authenticity-details authenticity-danger">
            <div class="authenticity-header">
              <span class="auth-icon">&#x1F9E0;</span>
              <strong>DeepSeek AI Concerns</strong>
            </div>
            <div class="auth-tags">
              <span v-for="concern in verificationResult.deepseek_concerns" :key="concern" class="auth-tag auth-tag-bad">{{ concern }}</span>
            </div>
          </div>

          <div class="review-info">
            <div class="review-item">
              <span class="ri-icon">&#x23F3;</span>
              <span>You can reapply after <strong>12 hours</strong> with a valid PRC license photo</span>
            </div>
            <div class="review-item">
              <span class="ri-icon">&#x1FAAA;</span>
              <span>Make sure to upload a clear photo of your <strong>PRC License Card</strong> or <strong>PRC Certification Letter</strong></span>
            </div>
            <div class="review-item">
              <span class="ri-icon">&#x26A0;&#xFE0F;</span>
              <span>Resumes, diplomas, fake/edited documents, or other non-PRC documents will not be accepted</span>
            </div>
            <div class="review-item">
              <span class="ri-icon">&#x1F464;</span>
              <span>The name on your PRC license must match your RealtyLinkPH account name</span>
            </div>
          </div>

          <div class="result-actions">
            <button class="btn-secondary-lg" @click="goToDashboard">Back to Dashboard</button>
          </div>
        </div>

        <!-- UNCLEAR — Pending Admin Review -->
        <div v-else class="result-card result-pending">
          <div class="result-icon">📋</div>
          <h2>Application Submitted for Review</h2>
          <p class="result-message">{{ verificationResult ? verificationResult.message : 'Your application has been submitted.' }}</p>

          <div class="ai-badge ai-badge-warn" v-if="verificationResult && verificationResult.ai_reasoning">
            <img src="/images/buddy-avatar.jpg" alt="Buddy" class="ai-badge-avatar" />
            <div>
              <strong>RealtyLinkPH Buddy notes:</strong>
              <p>{{ verificationResult.ai_reasoning }}</p>
            </div>
          </div>

          <div class="review-info">
            <div class="review-item">
              <span class="ri-icon">⏰</span>
              <span>An admin will review your application within <strong>12 hours</strong></span>
            </div>
            <div class="review-item">
              <span class="ri-icon">📧</span>
              <span>You will be notified once a decision is made</span>
            </div>
            <div class="review-item">
              <span class="ri-icon">📱</span>
              <span>You can check your application status anytime from the Dashboard</span>
            </div>
          </div>

          <div class="result-actions">
            <button class="btn-secondary-lg" @click="goToDashboard">Back to Dashboard</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BecomeAgent',

  data() {
    return {
      currentStep: 1,
      steps: [
        { num: 1, label: 'Information' },
        { num: 2, label: 'Upload PRC' },
        { num: 3, label: 'AI Verification' },
        { num: 4, label: 'Result' },
      ],

      form: {
        applicant_name: '',
        license_number: '',
        prc_id: '',
        company_name: '',
        company_address: '',
        accreditation: '',
        bio: '',
        license_expiry_date: '',
      },

      prcLicenseFile: null,
      prcPreviewUrl: null,
      errors: {},

      // Verification state
      isVerifying: false,
      verifyPhase: 0,
      verifyingText: 'Initializing verification...',
      verificationResult: null,

      // Existing application
      existingApplication: null,
      checkingStatus: true,

      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      token: localStorage.getItem('auth_token') || '',
    };
  },

  mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.form.applicant_name = user.name || '';

    if (user.role !== 'buyer') {
      this.$router.push('/dashboard');
      return;
    }

    this.checkExistingApplication();
  },

  methods: {
    async checkExistingApplication() {
      try {
        const res = await fetch(`${this.apiUrl}/api/user/agent-application-status`, {
          headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success && data.hasApplication) {
          if (data.application.status === 'rejected') {
            // If rejected but can't reapply yet, show the existing application
            if (!data.canReapply) {
              this.existingApplication = data.application;
              this.existingApplication._canReapplyAt = data.canReapplyAt;
            }
            // If canReapply is true, let them fill the form again
          } else {
            this.existingApplication = data.application;
          }
        }
      } catch (e) {
        // Ignore — proceed with form
      } finally {
        this.checkingStatus = false;
      }
    },

    // ── Step 1 Validation ──────────────────────────────
    goToStep2() {
      this.errors = {};
      const f = this.form;
      if (!f.applicant_name.trim()) this.errors.applicant_name = 'Full name is required.';
      if (!f.license_number.trim()) this.errors.license_number = 'PRC license number is required.';
      if (!f.prc_id.trim()) this.errors.prc_id = 'PRC ID number is required.';
      if (!f.license_expiry_date) this.errors.license_expiry_date = 'License expiry date is required.';
      else if (new Date(f.license_expiry_date) <= new Date()) this.errors.license_expiry_date = 'Expiry date must be in the future.';
      if (!f.accreditation) this.errors.accreditation = 'Accreditation is required.';
      if (!f.company_name.trim()) this.errors.company_name = 'Company name is required.';
      if (!f.company_address.trim()) this.errors.company_address = 'Company address is required.';

      if (Object.keys(this.errors).length > 0) return;
      this.currentStep = 2;
    },

    // ── File Handling ──────────────────────────────────
    onFileSelected(e) {
      const file = e.target.files[0];
      if (!file) return;
      if (file.size > 5 * 1024 * 1024) {
        this.errors.prc_license_photo = 'File must be under 5MB.';
        return;
      }
      if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
        this.errors.prc_license_photo = 'Only JPG and PNG files are accepted.';
        return;
      }
      this.errors.prc_license_photo = '';
      this.prcLicenseFile = file;
      this.prcPreviewUrl = URL.createObjectURL(file);
    },

    handleDrop(e) {
      const file = e.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) {
        this.onFileSelected({ target: { files: [file] } });
      }
    },

    removeFile() {
      this.prcLicenseFile = null;
      this.prcPreviewUrl = null;
      if (this.$refs.prcInput) this.$refs.prcInput.value = '';
    },

    // ── Submit Application ─────────────────────────────
    async submitApplication() {
      if (!this.prcLicenseFile) {
        this.errors.prc_license_photo = 'Please upload your PRC license photo.';
        return;
      }

      this.currentStep = 3;
      this.isVerifying = true;
      this.verifyPhase = 1;
      this.verifyingText = 'Scanning your PRC license...';

      // Simulate phased progress while dual-AI verification runs
      const phaseTimer = setInterval(() => {
        if (this.verifyPhase < 4) {
          this.verifyPhase++;
          if (this.verifyPhase === 2) this.verifyingText = 'Gemini AI is analyzing document security features...';
          if (this.verifyPhase === 3) this.verifyingText = 'DeepSeek AI is cross-verifying the data...';
          if (this.verifyPhase === 4) this.verifyingText = 'Cross-referencing both AI results...';
        }
      }, 3500);

      try {
        const fd = new FormData();
        Object.keys(this.form).forEach(key => {
          if (this.form[key]) fd.append(key, this.form[key]);
        });
        fd.append('prc_license_photo', this.prcLicenseFile);

        const res = await fetch(`${this.apiUrl}/api/user/become-agent`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}` },
          body: fd,
        });

        const data = await res.json();

        clearInterval(phaseTimer);
        this.verifyPhase = 4;
        this.isVerifying = false;

        // Brief pause so user sees all checks complete
        await new Promise(r => setTimeout(r, 800));

        this.currentStep = 4;

        if (data.success) {
          this.verificationResult = {
            decision: data.decision || (data.approved ? 'approved' : 'unclear'),
            approved: data.approved,
            message: data.message,
            ai_reasoning: data.ai_reasoning || '',
            document_type: data.document_type || null,
            authenticity_score: data.authenticity_score ?? null,
            security_features_found: data.security_features_found || [],
            security_features_missing: data.security_features_missing || [],
            red_flags_detected: data.red_flags_detected || [],
            verification_method: data.verification_method || 'single_ai',
            deepseek_decision: data.deepseek_decision || null,
            deepseek_concerns: data.deepseek_concerns || [],
          };

          if (data.approved) {
            const user = JSON.parse(localStorage.getItem('user') || '{}');
            user.role = 'agent';
            localStorage.setItem('user', JSON.stringify(user));
          }
        } else {
          this.verificationResult = {
            decision: 'unclear',
            approved: false,
            message: data.message || 'Application submission failed. Please try again.',
            ai_reasoning: '',
            document_type: null,
          };
        }

      } catch (e) {
        clearInterval(phaseTimer);
        this.isVerifying = false;
        this.currentStep = 4;
        this.verificationResult = {
          decision: 'unclear',
          approved: false,
          message: 'A network error occurred. Please try again.',
          ai_reasoning: '',
          document_type: null,
        };
      }
    },

    formatReapplyTime(isoString) {
      const d = new Date(isoString);
      return d.toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' });
    },

    goToAgentDashboard() {
      this.$router.push('/agent/dashboard');
    },

    goToDashboard() {
      this.$router.push('/dashboard');
    },
  },
};
</script>

<style scoped>
.become-agent-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f9f8f5 0%, #f5f0e1 100%);
  font-family: 'Inter', sans-serif;
}

/* ── Header ───────────────────────────────────────────── */
.page-header {
  background: #fff;
  border-bottom: 1px solid #e8ecf0;
  padding: 16px 32px;
}
.header-content {
  max-width: 800px;
  margin: 0 auto;
}
.back-link {
  font-size: 13px;
  color: #666;
  text-decoration: none;
  font-weight: 500;
}
.back-link:hover { color: #333; }
.header-title {
  font-size: 22px;
  font-weight: 800;
  margin: 8px 0 0;
}
.logo-realty { color: #100c08; }
.logo-ph { color: #FFD700; }
.header-sub {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #666;
  margin-top: 2px;
}

/* ── Page Body ────────────────────────────────────────── */
.page-body {
  max-width: 800px;
  margin: 0 auto;
  padding: 32px 20px 60px;
}

/* ── Progress Steps Bar ───────────────────────────────── */
.steps-bar {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-bottom: 32px;
}
.step {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  border-radius: 24px;
  background: #fff;
  border: 2px solid #e5e7eb;
  transition: all 0.3s;
}
.step.active {
  border-color: #FFD700;
  background: #fef9e7;
}
.step.done {
  border-color: #22c55e;
  background: #f0fdf4;
}
.step-circle {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  color: #666;
  flex-shrink: 0;
}
.step.active .step-circle { background: #FFD700; color: #100c08; }
.step.done .step-circle { background: #22c55e; color: #fff; }
.step-label { font-size: 13px; font-weight: 600; color: #666; }
.step.active .step-label { color: #b8860b; }
.step.done .step-label { color: #22c55e; }

/* ── Step Card ────────────────────────────────────────── */
.step-card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  overflow: hidden;
}
.card-header {
  padding: 28px 32px 20px;
  border-bottom: 1px solid #f0f0f0;
}
.card-header h2 { font-size: 20px; font-weight: 800; color: #100c08; margin: 0 0 6px; }
.card-header p { font-size: 14px; color: #666; margin: 0; }

/* ── Form ─────────────────────────────────────────────── */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  padding: 24px 32px;
}
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 13px; font-weight: 600; color: #100c08; }
.req { color: #dc2626; }
.optional { font-weight: 400; color: #999; font-size: 12px; }
.form-input {
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
  font-family: inherit;
}
.form-input:focus { border-color: #FFD700; }
.form-textarea { resize: vertical; min-height: 60px; }
.field-error { font-size: 12px; color: #dc2626; font-weight: 500; }

/* ── Step Footer ──────────────────────────────────────── */
.step-footer {
  display: flex;
  justify-content: space-between;
  padding: 20px 32px;
  border-top: 1px solid #f0f0f0;
  background: #fafafa;
}

/* ── Buttons ──────────────────────────────────────────── */
.btn-primary-lg {
  padding: 12px 28px;
  background: #FFD700;
  color: #100c08;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  transition: all 0.2s;
}
.btn-primary-lg:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(230,174,13,0.4); }
.btn-primary-lg:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary-lg {
  padding: 12px 28px;
  background: #f3f4f6;
  color: #374151;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
}
.btn-secondary-lg:hover { background: #e5e7eb; }

/* ── Upload Zone ──────────────────────────────────────── */
.upload-zone {
  margin: 24px 32px;
  border: 3px dashed #d1d5db;
  border-radius: 16px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s;
  min-height: 260px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.upload-zone:hover { border-color: #FFD700; background: #fffdf5; }
.hidden-input { display: none; }

.upload-empty {
  text-align: center;
  padding: 40px 20px;
  color: #999;
}
.upload-icon-large { font-size: 56px; margin-bottom: 12px; }
.upload-empty h3 { font-size: 16px; font-weight: 700; color: #555; margin: 0 0 6px; }
.upload-empty p { font-size: 14px; margin: 2px 0; }
.upload-hint { font-size: 12px; color: #bbb; margin-top: 8px; }

.upload-preview { position: relative; width: 100%; }
.preview-image { width: 100%; max-height: 360px; object-fit: contain; display: block; padding: 16px; box-sizing: border-box; }
.preview-overlay {
  position: absolute;
  bottom: 12px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
}
.btn-change, .btn-remove {
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  border: none;
  cursor: pointer;
}
.btn-change { background: rgba(255,255,255,0.95); color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.btn-remove { background: rgba(220,38,38,0.9); color: #fff; }

/* ── Info Box ─────────────────────────────────────────── */
.info-box {
  margin: 0 32px 24px;
  padding: 16px 20px;
  background: #fef9e7;
  border-radius: 12px;
  border-left: 4px solid #FFD700;
  display: flex;
  gap: 12px;
}
.info-icon { font-size: 20px; flex-shrink: 0; }
.info-box strong { font-size: 13px; color: #100c08; }
.info-box ul { margin: 6px 0 0; padding-left: 18px; }
.info-box li { font-size: 13px; color: #555; margin: 3px 0; }

/* ── Verification Step (Step 3) ───────────────────────── */
.verify-card { padding: 40px 32px; text-align: center; }

.ai-buddy { margin-bottom: 32px; }
.buddy-avatar {
  width: 88px;
  height: 88px;
  border-radius: 50%;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 14px;
  position: relative;
  overflow: hidden;
  border: 3px solid #FFD700;
}
.buddy-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}
.buddy-pulse {
  position: absolute;
  inset: -6px;
  border-radius: 50%;
  border: 3px solid #FFD700;
  animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 0.3; transform: scale(1); }
  50% { opacity: 0.8; transform: scale(1.1); }
}
.ai-buddy h2 { font-size: 22px; font-weight: 800; color: #100c08; margin: 0 0 4px; }
.buddy-subtitle { font-size: 14px; color: #b8860b; font-weight: 600; margin: 0; }

.verify-status { margin-bottom: 32px; }
.verify-spinner {
  width: 36px;
  height: 36px;
  border: 4px solid #e5e7eb;
  border-top-color: #FFD700;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }
.verify-text { font-size: 16px; font-weight: 700; color: #100c08; margin: 0 0 6px; }
.verify-subtext { font-size: 13px; color: #999; margin: 0; }

.verify-steps-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-width: 340px;
  margin: 0 auto;
  text-align: left;
}
.verify-step {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 10px;
  background: #f9fafb;
  font-size: 14px;
  color: #aaa;
  transition: all 0.3s;
}
.verify-step.active { background: #fef9e7; color: #100c08; }
.verify-step.done { background: #f0fdf4; color: #16a34a; }
.vs-icon { font-size: 18px; flex-shrink: 0; }

/* ── Result Cards (Step 4) ────────────────────────────── */
.result-card {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 4px 24px rgba(0,0,0,0.08);
  padding: 40px 36px;
  text-align: center;
}
.result-icon { font-size: 64px; margin-bottom: 16px; }
.result-card h2 { font-size: 24px; font-weight: 800; margin: 0 0 12px; }
.result-message { font-size: 15px; color: #555; margin: 0 0 24px; max-width: 480px; margin-left: auto; margin-right: auto; }

.result-success { border-top: 5px solid #22c55e; }
.result-success h2 { color: #166534; }

.result-pending { border-top: 5px solid #f59e0b; }
.result-pending h2 { color: #92400e; }

.result-rejected { border-top: 5px solid #dc2626; }
.result-rejected h2 { color: #991b1b; }

.ai-badge {
  display: flex;
  gap: 12px;
  text-align: left;
  padding: 16px 20px;
  border-radius: 12px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  margin-bottom: 24px;
  max-width: 480px;
  margin-left: auto;
  margin-right: auto;
}
.ai-badge-warn { background: #fef9c3; border-color: #fde68a; }
.ai-badge-danger { background: #fef2f2; border-color: #fecaca; }
.ai-badge-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
  flex-shrink: 0;
  border: 2px solid #FFD700;
}
.ai-badge strong { font-size: 13px; color: #100c08; }
.ai-badge p { font-size: 13px; color: #555; margin: 4px 0 0; }

.review-info {
  max-width: 420px;
  margin: 0 auto 28px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.review-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: #f8f9fa;
  border-radius: 10px;
  font-size: 14px;
  color: #555;
  text-align: left;
}
.ri-icon { font-size: 20px; flex-shrink: 0; }

.detected-doc-type {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 10px;
  font-size: 14px;
  color: #991b1b;
  max-width: 420px;
  margin: 0 auto 20px;
}
.ddt-icon { font-size: 20px; flex-shrink: 0; }

/* Dual AI Badge */
.dual-ai-badge {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  max-width: 320px;
  margin: 0 auto 18px;
  padding: 10px 20px;
  border-radius: 30px;
  background: linear-gradient(135deg, #dcfce7, #d1fae5);
  border: 2px solid #86efac;
}
.dual-ai-single {
  background: linear-gradient(135deg, #e0f2fe, #dbeafe);
  border-color: #93c5fd;
}
.dual-ai-reject {
  background: linear-gradient(135deg, #fee2e2, #fecaca);
  border-color: #fca5a5;
}
.dual-ai-icon { font-size: 20px; }
.dual-ai-text {
  font-weight: 700;
  font-size: 14px;
  color: #100c08;
}
.dual-ai-sub {
  font-size: 11px;
  color: #6b7280;
  padding: 2px 8px;
  background: rgba(255,255,255,0.6);
  border-radius: 10px;
}

/* Authenticity Details */
.authenticity-details {
  max-width: 480px;
  margin: 0 auto 18px;
  padding: 14px 18px;
  border-radius: 10px;
  text-align: left;
}
.authenticity-success {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
}
.authenticity-danger {
  background: #fef2f2;
  border: 1px solid #fecaca;
}
.authenticity-warn {
  background: #fffbeb;
  border: 1px solid #fde68a;
}
.authenticity-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
  font-size: 14px;
  color: #100c08;
}
.auth-icon { font-size: 18px; flex-shrink: 0; }
.auth-score {
  margin-left: auto;
  font-size: 12px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 20px;
}
.auth-score-good { background: #dcfce7; color: #166534; }
.auth-score-bad { background: #fecaca; color: #991b1b; }
.auth-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.auth-tag {
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 6px;
  font-weight: 500;
}
.auth-tag-good { background: #dcfce7; color: #166534; }
.auth-tag-bad { background: #fee2e2; color: #991b1b; }
.auth-tag-warn { background: #fef3c7; color: #92400e; }

.cooldown-info {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 14px 20px;
  background: #fef9e7;
  border: 1px solid #FFD700;
  border-radius: 10px;
  font-size: 14px;
  color: #100c08;
  max-width: 420px;
  margin: 0 auto 24px;
}
.cooldown-icon { font-size: 22px; flex-shrink: 0; }

.result-actions { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }

/* ── Responsive ───────────────────────────────────────── */
@media (max-width: 640px) {
  .form-grid { grid-template-columns: 1fr; }
  .steps-bar { flex-wrap: wrap; }
  .step { padding: 6px 12px; }
  .step-label { display: none; }
  .form-grid, .step-footer, .info-box, .upload-zone { margin-left: 16px; margin-right: 16px; }
  .card-header, .form-grid { padding-left: 20px; padding-right: 20px; }
  .step-footer { padding-left: 20px; padding-right: 20px; }
}
</style>
