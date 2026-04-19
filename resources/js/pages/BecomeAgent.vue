<template>
  <div class="become-agent-page">
    <!-- Header -->
    <header class="page-header">
      <div class="header-content">
        <router-link to="/dashboard" class="back-link">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
          Back to Dashboard
        </router-link>
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
            <svg v-if="currentStep > s.num" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            <span v-else>{{ s.num }}</span>
          </div>
          <span class="step-label">{{ s.label }}</span>
        </div>
      </div>

      <!-- Already Applied State -->
      <div v-if="existingApplication" :class="['result-card', existingApplication.status === 'approved' ? 'result-success' : existingApplication.status === 'rejected' ? 'result-rejected' : 'result-pending']">
        <div class="result-icon-wrap">
          <svg v-if="existingApplication.status === 'approved'" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <svg v-else-if="existingApplication.status === 'rejected'" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          <svg v-else width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>

        <h2 v-if="existingApplication.status === 'pending'">Application Under Review</h2>
        <h2 v-else-if="existingApplication.status === 'approved'">You're Already an Agent!</h2>
        <h2 v-else-if="existingApplication.status === 'rejected'">Application Was Rejected</h2>

        <p class="result-message" v-if="existingApplication.status === 'pending'">Your application is currently under admin review. Please allow up to 12 hours for a response.</p>
        <p class="result-message" v-else-if="existingApplication.status === 'approved'">Your application has been approved! You are now a verified agent on RealtyLinkPH.</p>
        <p class="result-message" v-else-if="existingApplication.status === 'rejected'">
          Your previous application was not approved.
          <span v-if="existingApplication.rejection_reason"><br><br>{{ existingApplication.rejection_reason }}</span>
        </p>

        <div v-if="existingApplication.status === 'rejected' && existingApplication._canReapplyAt" class="cooldown-info">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span>You can reapply at <strong>{{ formatReapplyTime(existingApplication._canReapplyAt) }}</strong></span>
        </div>

        <div class="result-actions">
          <router-link v-if="existingApplication.status === 'approved'" to="/agent/dashboard" class="btn-primary-lg">Go to Agent Dashboard</router-link>
          <router-link to="/dashboard" class="btn-secondary-lg">Back to Dashboard</router-link>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════ -->
      <!-- STEP 1: Professional Information -->
      <!-- ════════════════════════════════════════════════ -->
      <div v-else-if="currentStep === 1" class="step-content">
        <div class="step-card">
          <div class="card-header">
            <div class="card-header-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
              <h2>Professional Information</h2>
              <p>Fill in your details to start the verification process.</p>
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group full">
              <label>Full Name <span class="req">*</span></label>
              <input v-model="form.applicant_name" type="text" class="form-input" placeholder="Your full legal name" />
              <span v-if="errors.applicant_name" class="field-error">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ errors.applicant_name }}
              </span>
            </div>

            <div class="form-group">
              <label>PRC License Number <span class="req">*</span></label>
              <input v-model="form.license_number" type="text" class="form-input" placeholder="e.g. 0012345" />
              <span v-if="errors.license_number" class="field-error">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ errors.license_number }}
              </span>
            </div>

            <div class="form-group">
              <label>PRC ID Number <span class="req">*</span></label>
              <input v-model="form.prc_id" type="text" class="form-input" placeholder="e.g. REB-0012345" />
              <span v-if="errors.prc_id" class="field-error">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ errors.prc_id }}
              </span>
            </div>

            <div class="form-group">
              <label>License Expiry Date <span class="req">*</span></label>
              <input v-model="form.license_expiry_date" type="date" class="form-input" />
              <span v-if="errors.license_expiry_date" class="field-error">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ errors.license_expiry_date }}
              </span>
            </div>

            <div class="form-group">
              <label>Accreditation <span class="req">*</span></label>
              <select v-model="form.accreditation" class="form-input">
                <option value="">Select accreditation...</option>
                <option value="REIN">REIN</option>
                <option value="PAMI">PAMI</option>
                <option value="OTHER">Other</option>
              </select>
              <span v-if="errors.accreditation" class="field-error">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ errors.accreditation }}
              </span>
            </div>

            <div class="form-group">
              <label>Company Name <span class="req">*</span></label>
              <input v-model="form.company_name" type="text" class="form-input" placeholder="Your real estate company" />
              <span v-if="errors.company_name" class="field-error">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ errors.company_name }}
              </span>
            </div>

            <div class="form-group full">
              <label>Company Address <span class="req">*</span></label>
              <input v-model="form.company_address" type="text" class="form-input" placeholder="Full office address" />
              <span v-if="errors.company_address" class="field-error">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ errors.company_address }}
              </span>
            </div>

            <div class="form-group full">
              <label>Bio <span class="optional">(Optional)</span></label>
              <textarea v-model="form.bio" class="form-input form-textarea" rows="3" placeholder="Tell us about your real estate experience..."></textarea>
            </div>
          </div>

          <div class="step-footer">
            <router-link to="/dashboard" class="btn-secondary-lg">Cancel</router-link>
            <button class="btn-primary-lg" @click="goToStep2">
              Next: Upload PRC License
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════ -->
      <!-- STEP 2: Upload PRC License -->
      <!-- ════════════════════════════════════════════════ -->
      <div v-else-if="currentStep === 2" class="step-content">
        <div class="step-card">
          <div class="card-header">
            <div class="card-header-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            </div>
            <div>
              <h2>Upload PRC License</h2>
              <p>Upload a clear photo of your PRC license card. RealtyLinkPH Buddy will verify it using AI.</p>
            </div>
          </div>

          <div class="upload-zone" @click="$refs.prcInput.click()" @dragover.prevent @drop.prevent="handleDrop">
            <input ref="prcInput" type="file" accept="image/jpeg,image/png,image/jpg" class="hidden-input" @change="onFileSelected" />

            <div v-if="!prcPreviewUrl" class="upload-empty">
              <div class="upload-icon-large">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="1.2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
              </div>
              <h3>Drop your PRC license photo here</h3>
              <p>or click to browse</p>
              <p class="upload-hint">JPG or PNG, max 5MB</p>
            </div>

            <div v-else class="upload-preview" @click.stop>
              <img :src="prcPreviewUrl" alt="PRC License Preview" class="preview-image" />
              <div class="preview-overlay">
                <button class="btn-change" @click.stop="$refs.prcInput.click()">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                  Change Photo
                </button>
                <button class="btn-remove" @click.stop="removeFile">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                  Remove
                </button>
              </div>
            </div>
          </div>

          <span v-if="errors.prc_license_photo" class="field-error" style="margin: 4px 32px 0; display: flex; align-items: center; gap: 4px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ errors.prc_license_photo }}
          </span>

          <div class="info-box">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="8"/><line x1="12" y1="12" x2="12" y2="16"/></svg>
            <div>
              <strong>Tips for a clear photo:</strong>
              <ul>
                <li>Ensure the entire PRC card is visible and not cropped</li>
                <li>Take the photo in good lighting — no glare or shadows</li>
                <li>Make sure all text on the card is clearly readable</li>
                <li>Your PRC name must match your registered account name</li>
              </ul>
            </div>
          </div>

          <div class="step-footer">
            <button class="btn-secondary-lg" @click="currentStep = 1">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              Back
            </button>
            <button class="btn-primary-lg" @click="goToStep3" :disabled="!prcLicenseFile">
              Next: Face Scan
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════ -->
      <!-- STEP 3: Face Scan -->
      <!-- ════════════════════════════════════════════════ -->
      <div v-else-if="currentStep === 3" class="step-content">
        <div class="step-card">
          <div class="card-header">
            <div class="card-header-icon">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
              <h2>Face Verification</h2>
              <p>Take a selfie so we can confirm your face matches the photo on your PRC license.</p>
            </div>
          </div>

          <div class="face-scan-section">
            <div class="face-instructions">
              <div class="instr-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                Look directly at the camera
              </div>
              <div class="instr-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
                Ensure good lighting on your face
              </div>
              <div class="instr-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                Remove glasses or face coverings
              </div>
              <div class="instr-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                Match your PRC license photo appearance
              </div>
            </div>

            <div v-if="cameraError" class="camera-error">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <p>{{ cameraError }}</p>
              <button class="btn-secondary-lg" @click="startCamera">Try Again</button>
            </div>

            <div v-else class="camera-container">
              <div class="camera-frame">
                <video v-show="!selfieDataUrl" ref="videoEl" class="camera-video" autoplay muted playsinline></video>
                <img v-if="selfieDataUrl" :src="selfieDataUrl" class="selfie-preview" alt="Your selfie" />
                <canvas ref="canvasEl" style="display:none;"></canvas>
                <div v-if="!selfieDataUrl" class="camera-overlay">
                  <div class="face-guide-circle"></div>
                </div>
              </div>

              <div class="camera-actions">
                <button v-if="!selfieDataUrl" class="btn-capture" @click="capturePhoto">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                  Take Photo
                </button>
                <button v-if="selfieDataUrl" class="btn-secondary-lg" @click="retakePhoto">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.61"/></svg>
                  Retake Photo
                </button>
              </div>
            </div>

            <div v-if="selfieDataUrl" class="selfie-ready">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Selfie captured — ready to submit
            </div>
          </div>

          <div class="step-footer">
            <button class="btn-secondary-lg" @click="backToStep2">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
              Back
            </button>
            <button class="btn-primary-lg" :disabled="!selfieDataUrl" @click="submitApplication">
              Submit &amp; Verify
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════ -->
      <!-- STEP 4: AI Verification Loading -->
      <!-- ════════════════════════════════════════════════ -->
      <div v-else-if="currentStep === 4" class="step-content">
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
            <p class="verify-subtext">This usually takes 10–20 seconds. Please wait...</p>
          </div>

          <div class="verify-steps-list">
            <div class="verify-step" :class="{ active: verifyPhase >= 1, done: verifyPhase > 1 }">
              <span class="vs-icon-wrap">
                <svg v-if="verifyPhase > 1" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              </span>
              <span>Scanning document image...</span>
            </div>
            <div class="verify-step" :class="{ active: verifyPhase >= 2, done: verifyPhase > 2 }">
              <span class="vs-icon-wrap">
                <svg v-if="verifyPhase > 2" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
              </span>
              <span>Extracting license information...</span>
            </div>
            <div class="verify-step" :class="{ active: verifyPhase >= 3, done: verifyPhase > 3 }">
              <span class="vs-icon-wrap">
                <svg v-if="verifyPhase > 3" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </span>
              <span>Comparing facial features...</span>
            </div>
            <div class="verify-step" :class="{ active: verifyPhase >= 4 }">
              <span class="vs-icon-wrap">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </span>
              <span>Verifying authenticity & identity...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ════════════════════════════════════════════════ -->
      <!-- STEP 5: Result -->
      <!-- ════════════════════════════════════════════════ -->
      <div v-else-if="currentStep === 5" class="step-content">

        <!-- ✅ APPROVED -->
        <div v-if="verificationResult && verificationResult.decision === 'approved'" class="result-card result-success">
          <div class="result-header">
            <div class="result-buddy-wrap result-buddy-success">
              <img src="/images/buddy-avatar.jpg" alt="RealtyLinkPH Buddy" class="result-buddy-img" />
              <div class="result-buddy-badge result-buddy-badge-success">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
              </div>
            </div>
            <div class="result-header-text">
              <h2>Congratulations!</h2>
              <p class="result-message">{{ verificationResult.message }}</p>
            </div>
          </div>

          <div class="buddy-says buddy-says-success">
            <div class="buddy-says-label">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              RealtyLinkPH Buddy says:
            </div>
            <p>{{ verificationResult.ai_reasoning }}</p>
          </div>

          <!-- Security features side by side -->
          <div v-if="verificationResult.security_features_found && verificationResult.security_features_found.length" class="details-grid">
            <div class="detail-panel detail-panel-success">
              <div class="detail-panel-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <strong>Security Features Verified</strong>
                <span v-if="verificationResult.authenticity_score" class="score-badge score-badge-good">
                  {{ Math.round(verificationResult.authenticity_score * 100) }}% authentic
                </span>
              </div>
              <div class="tag-list">
                <span v-for="feature in verificationResult.security_features_found" :key="feature" class="tag tag-success">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                  {{ feature }}
                </span>
              </div>
            </div>
          </div>

          <div class="result-actions">
            <button class="btn-primary-lg" @click="goToAgentDashboard">
              Go to Agent Dashboard
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>
          </div>
        </div>

        <!-- ❌ REJECTED -->
        <div v-else-if="verificationResult && verificationResult.decision === 'rejected'" class="result-card result-rejected">
          <!-- Side-by-side header: sad buddy + rejection info -->
          <div class="result-header">
            <div class="result-buddy-wrap result-buddy-rejected">
              <!-- ✅ Using sad buddy avatar on rejection -->
              <img src="/images/buddy-avatar2.png" alt="RealtyLinkPH Buddy" class="result-buddy-img" />
              <div class="result-buddy-badge result-buddy-badge-rejected">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </div>
            </div>
            <div class="result-header-text">
              <h2>Application Rejected</h2>
              <p class="result-message">{{ verificationResult.message }}</p>
            </div>
          </div>

          <div class="buddy-says buddy-says-danger">
            <div class="buddy-says-label">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              RealtyLinkPH Buddy says:
            </div>
            <p>{{ verificationResult.ai_reasoning }}</p>
          </div>

          <!-- Detected doc type -->
          <div v-if="verificationResult.document_type" class="detected-doc-type">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Detected document: <strong>{{ verificationResult.document_type }}</strong>
          </div>

          <!-- ✅ Side-by-side details grid -->
          <div class="details-grid">
            <!-- Red Flags -->
            <div v-if="verificationResult.red_flags_detected && verificationResult.red_flags_detected.length" class="detail-panel detail-panel-danger">
              <div class="detail-panel-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                <strong>Issues Detected</strong>
                <span v-if="verificationResult.authenticity_score !== null && verificationResult.authenticity_score !== undefined" class="score-badge score-badge-bad">
                  {{ Math.round(verificationResult.authenticity_score * 100) }}% authentic
                </span>
              </div>
              <div class="tag-list">
                <span v-for="flag in verificationResult.red_flags_detected" :key="flag" class="tag tag-danger">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  {{ flag }}
                </span>
              </div>
            </div>

            <!-- Missing Security Features -->
            <div v-if="verificationResult.security_features_missing && verificationResult.security_features_missing.length" class="detail-panel detail-panel-warn">
              <div class="detail-panel-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <strong>Missing Security Features</strong>
              </div>
              <div class="tag-list">
                <span v-for="feature in verificationResult.security_features_missing" :key="feature" class="tag tag-warn">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                  {{ feature }}
                </span>
              </div>
            </div>
          </div>

          <!-- What to do next -->
          <div class="next-steps-section">
            <div class="next-steps-title">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B1C39" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              What you can do next
            </div>
            <div class="next-steps-grid">
              <div class="next-step-item">
                <div class="next-step-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                  <strong>Wait 12 hours</strong>
                  <p>You can reapply after the cooldown period with a valid PRC license photo</p>
                </div>
              </div>
              <div class="next-step-item">
                <div class="next-step-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div>
                  <strong>Use a valid PRC document</strong>
                  <p>Upload a clear photo of your PRC License Card or PRC Certification Letter</p>
                </div>
              </div>
              <div class="next-step-item">
                <div class="next-step-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                  <strong>Name must match</strong>
                  <p>The name on your PRC license must match your RealtyLinkPH account name</p>
                </div>
              </div>
              <div class="next-step-item">
                <div class="next-step-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </div>
                <div>
                  <strong>No fake documents</strong>
                  <p>Resumes, diplomas, edited or non-PRC documents will not be accepted</p>
                </div>
              </div>
            </div>
          </div>

          <div class="result-actions">
            <button class="btn-secondary-lg" @click="goToDashboard">Back to Dashboard</button>
          </div>
        </div>

        <!-- ⏳ PENDING / UNCLEAR — Admin Review -->
        <div v-else class="result-card result-pending">
          <div class="result-header">
            <div class="result-buddy-wrap result-buddy-pending">
              <img src="/images/buddy-avatar.jpg" alt="RealtyLinkPH Buddy" class="result-buddy-img" />
              <div class="result-buddy-badge result-buddy-badge-pending">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
            </div>
            <div class="result-header-text">
              <h2>Under Admin Review</h2>
              <p class="result-message">{{ verificationResult ? verificationResult.message : 'Your application has been submitted.' }}</p>
            </div>
          </div>

          <div class="buddy-says buddy-says-warn" v-if="verificationResult && verificationResult.ai_reasoning">
            <div class="buddy-says-label">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              RealtyLinkPH Buddy notes:
            </div>
            <p>{{ verificationResult.ai_reasoning }}</p>
          </div>

          <div class="next-steps-section">
            <div class="next-steps-title">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0B1C39" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              What happens next
            </div>
            <div class="next-steps-grid">
              <div class="next-step-item">
                <div class="next-step-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                  <strong>Admin review within 12 hours</strong>
                  <p>Our team will personally review your application and make a decision</p>
                </div>
              </div>
              <div class="next-step-item">
                <div class="next-step-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <div>
                  <strong>You'll be notified</strong>
                  <p>A notification will be sent once the admin makes a final decision</p>
                </div>
              </div>
              <div class="next-step-item">
                <div class="next-step-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D89B0F" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                </div>
                <div>
                  <strong>Check your dashboard</strong>
                  <p>You can monitor your application status anytime from your dashboard</p>
                </div>
              </div>
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
        { num: 3, label: 'Face Scan' },
        { num: 4, label: 'Verifying' },
        { num: 5, label: 'Result' },
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
      selfieDataUrl: null,
      cameraStream: null,
      cameraError: null,
      errors: {},

      isVerifying: false,
      verifyPhase: 0,
      verifyingText: 'Initializing verification...',
      verificationResult: null,

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
            if (!data.canReapply) {
              this.existingApplication = data.application;
              this.existingApplication._canReapplyAt = data.canReapplyAt;
            }
          } else {
            this.existingApplication = data.application;
          }
        }
      } catch (e) {
        // Proceed with form
      } finally {
        this.checkingStatus = false;
      }
    },

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

    onFileSelected(e) {
      const file = e.target.files[0];
      if (!file) return;
      if (file.size > 5 * 1024 * 1024) { this.errors.prc_license_photo = 'File must be under 5MB.'; return; }
      if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) { this.errors.prc_license_photo = 'Only JPG and PNG files are accepted.'; return; }
      this.errors.prc_license_photo = '';
      this.prcLicenseFile = file;
      this.prcPreviewUrl = URL.createObjectURL(file);
    },

    handleDrop(e) {
      const file = e.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) this.onFileSelected({ target: { files: [file] } });
    },

    removeFile() {
      this.prcLicenseFile = null;
      this.prcPreviewUrl = null;
      if (this.$refs.prcInput) this.$refs.prcInput.value = '';
    },

    async submitApplication() {
      if (!this.prcLicenseFile) { this.errors.prc_license_photo = 'Please upload your PRC license photo.'; return; }

      this.currentStep = 4;
      this.isVerifying = true;
      this.verifyPhase = 1;
      this.verifyingText = 'Scanning document image...';

      const phaseTimer = setInterval(() => {
        if (this.verifyPhase < 4) {
          this.verifyPhase++;
          if (this.verifyPhase === 2) this.verifyingText = 'Extracting license information...';
          if (this.verifyPhase === 3) this.verifyingText = 'Comparing facial features...';
          if (this.verifyPhase === 4) this.verifyingText = 'Verifying authenticity & identity...';
        }
      }, 3500);

      try {
        const fd = new FormData();
        Object.keys(this.form).forEach(key => { if (this.form[key]) fd.append(key, this.form[key]); });
        fd.append('prc_license_photo', this.prcLicenseFile);
        if (this.selfieDataUrl) fd.append('selfie_data', this.selfieDataUrl);

        const res = await fetch(`${this.apiUrl}/api/user/become-agent`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}` },
          body: fd,
        });

        const data = await res.json();
        clearInterval(phaseTimer);
        this.verifyPhase = 4;
        this.isVerifying = false;

        await new Promise(r => setTimeout(r, 800));
        this.currentStep = 5;

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
        this.currentStep = 5;
        this.verificationResult = {
          decision: 'unclear',
          approved: false,
          message: 'A network error occurred. Please try again.',
          ai_reasoning: '',
          document_type: null,
        };
      }
    },

    goToStep3() {
      if (!this.prcLicenseFile) { this.errors.prc_license_photo = 'Please upload your PRC license photo.'; return; }
      this.currentStep = 3;
      this.$nextTick(() => this.startCamera());
    },

    backToStep2() {
      this.stopCamera();
      this.selfieDataUrl = null;
      this.currentStep = 2;
    },

    async startCamera() {
      this.cameraError = null;
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' } });
        this.cameraStream = stream;
        this.$nextTick(() => { if (this.$refs.videoEl) this.$refs.videoEl.srcObject = stream; });
      } catch {
        this.cameraError = 'Camera access was denied. Please allow camera access in your browser settings and try again.';
      }
    },

    stopCamera() {
      if (this.cameraStream) { this.cameraStream.getTracks().forEach(t => t.stop()); this.cameraStream = null; }
    },

    capturePhoto() {
      const video = this.$refs.videoEl;
      const canvas = this.$refs.canvasEl;
      if (!video || !canvas) return;
      canvas.width = video.videoWidth || 640;
      canvas.height = video.videoHeight || 480;
      canvas.getContext('2d').drawImage(video, 0, 0);
      this.selfieDataUrl = canvas.toDataURL('image/jpeg', 0.85);
      this.stopCamera();
    },

    retakePhoto() {
      this.selfieDataUrl = null;
      this.$nextTick(() => this.startCamera());
    },

    formatReapplyTime(isoString) {
      const d = new Date(isoString);
      return d.toLocaleString('en-PH', { dateStyle: 'medium', timeStyle: 'short' });
    },

    goToAgentDashboard() { this.$router.push('/agent/dashboard'); },
    goToDashboard() { this.$router.push('/dashboard'); },
  },

  beforeUnmount() { this.stopCamera(); },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap');

.become-agent-page {
  --navy:        #0B1C39;
  --navy2:       #102445;
  --gold:        #D89B0F;
  --gold2:       #E5B332;
  --gold3:       #B07A08;
  --gold-bg:     rgba(216,155,15,0.06);
  --gold-border: rgba(216,155,15,0.25);
  --success:     #16a34a;
  --danger:      #dc2626;
  --warn:        #d97706;
  min-height: 100vh;
  background: #F0EDE8;
  font-family: 'Inter', sans-serif;
  color: #1a1a2e;
}

/* ══════════════════════════════════════
   HEADER
══════════════════════════════════════ */
.page-header {
  background: var(--navy);
  padding: 16px 32px;
  box-shadow: 0 2px 16px rgba(11,28,57,0.28);
}
.header-content { max-width: 860px; margin: 0 auto; }
.back-link {
  font-size: 13px; color: rgba(255,255,255,0.50);
  text-decoration: none; font-weight: 500;
  display: inline-flex; align-items: center; gap: 6px;
  transition: color 0.2s;
}
.back-link:hover { color: rgba(255,255,255,0.85); }
.header-title {
  font-family: 'Outfit', sans-serif; font-size: 22px;
  font-weight: 800; margin: 8px 0 0; color: #fff; letter-spacing: -0.3px;
}
.logo-realty { color: #fff; }
.logo-ph {
  background: linear-gradient(135deg, var(--gold), var(--gold2));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.header-sub { display: block; font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.40); margin-top: 2px; }

/* ══════════════════════════════════════
   PAGE BODY
══════════════════════════════════════ */
.page-body { max-width: 860px; margin: 0 auto; padding: 32px 20px 80px; }

/* ══════════════════════════════════════
   PROGRESS STEPS BAR
══════════════════════════════════════ */
.steps-bar { display: flex; justify-content: center; gap: 8px; margin-bottom: 28px; flex-wrap: wrap; }
.step {
  display: flex; align-items: center; gap: 8px;
  padding: 8px 16px; border-radius: 50px;
  background: #fff; border: 2px solid #e0ddd8;
  transition: all 0.3s; user-select: none;
}
.step.active { border-color: var(--gold); background: var(--gold-bg); }
.step.done   { border-color: #86efac; background: #f0fdf4; }
.step-circle {
  width: 26px; height: 26px; border-radius: 50%;
  background: #e5e7eb; display: flex; align-items: center;
  justify-content: center; font-size: 12px; font-weight: 700; color: #666; flex-shrink: 0;
}
.step.active .step-circle { background: var(--gold); color: #fff; }
.step.done .step-circle   { background: #22c55e; color: #fff; }
.step-label { font-size: 13px; font-weight: 600; color: #888; }
.step.active .step-label { color: var(--gold3); }
.step.done .step-label   { color: #16a34a; }

/* ══════════════════════════════════════
   STEP CARD
══════════════════════════════════════ */
.step-card {
  background: #fff; border-radius: 20px;
  box-shadow: 0 4px 28px rgba(0,0,0,0.07); overflow: hidden;
  border: 1px solid rgba(0,0,0,0.04);
}
.card-header {
  padding: 24px 32px 20px;
  border-bottom: 1px solid #f0ede8;
  display: flex; align-items: center; gap: 14px;
}
.card-header-icon {
  width: 44px; height: 44px; border-radius: 12px;
  background: var(--gold-bg); border: 1px solid var(--gold-border);
  display: flex; align-items: center; justify-content: center;
  color: var(--gold3); flex-shrink: 0;
}
.card-header h2 { font-size: 18px; font-weight: 700; color: var(--navy); font-family: Outfit, sans-serif; margin: 0 0 4px; }
.card-header p  { font-size: 13px; color: #777; margin: 0; }

/* ══════════════════════════════════════
   FORM
══════════════════════════════════════ */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 24px 32px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.full { grid-column: 1 / -1; }
.form-group label { font-size: 12px; font-weight: 700; color: var(--navy); text-transform: uppercase; letter-spacing: 0.04em; }
.req { color: #dc2626; margin-left: 2px; }
.optional { font-weight: 400; color: #aaa; font-size: 11px; text-transform: none; letter-spacing: 0; }
.form-input {
  border: 1.5px solid #e0ddd8; border-radius: 10px;
  padding: 10px 14px; font-size: 14px; outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
  width: 100%; box-sizing: border-box; font-family: inherit;
  color: var(--navy); background: #faf9f8;
}
.form-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(216,155,15,0.12); background: #fff; }
.form-textarea { resize: vertical; min-height: 72px; }
.field-error {
  display: flex; align-items: center; gap: 4px;
  font-size: 12px; color: #dc2626; font-weight: 500;
}

/* ══════════════════════════════════════
   STEP FOOTER
══════════════════════════════════════ */
.step-footer {
  display: flex; justify-content: space-between; align-items: center;
  padding: 18px 32px; border-top: 1px solid #f0ede8; background: #faf9f8;
}

/* ══════════════════════════════════════
   BUTTONS
══════════════════════════════════════ */
.btn-primary-lg {
  padding: 11px 24px;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: #fff; border: none; border-radius: 50px;
  font-size: 14px; font-weight: 700; cursor: pointer;
  text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
  transition: all 0.2s; box-shadow: 0 2px 10px rgba(216,155,15,0.30);
  font-family: inherit;
}
.btn-primary-lg:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(216,155,15,0.45); }
.btn-primary-lg:disabled { opacity: 0.45; cursor: not-allowed; }
.btn-secondary-lg {
  padding: 11px 24px; background: #f0ede8; color: #555;
  border: 1.5px solid #e0ddd8; border-radius: 50px;
  font-size: 14px; font-weight: 600; cursor: pointer;
  text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
  transition: all 0.2s; font-family: inherit;
}
.btn-secondary-lg:hover { background: #e5e2dc; border-color: #ccc; color: #333; }

/* ══════════════════════════════════════
   UPLOAD ZONE
══════════════════════════════════════ */
.upload-zone {
  margin: 24px 32px;
  border: 2px dashed #d1cdc8; border-radius: 16px;
  overflow: hidden; cursor: pointer;
  transition: all 0.2s; min-height: 260px;
  display: flex; align-items: center; justify-content: center;
}
.upload-zone:hover { border-color: var(--gold); background: var(--gold-bg); }
.hidden-input { display: none; }
.upload-empty { text-align: center; padding: 40px 20px; }
.upload-icon-large { margin-bottom: 14px; opacity: 0.85; }
.upload-empty h3 { font-size: 16px; font-weight: 700; color: #444; margin: 0 0 6px; }
.upload-empty p { font-size: 14px; color: #999; margin: 4px 0; }
.upload-hint { font-size: 12px; color: #bbb; margin-top: 8px; }
.upload-preview { position: relative; width: 100%; }
.preview-image { width: 100%; max-height: 340px; object-fit: contain; display: block; padding: 16px; box-sizing: border-box; }
.preview-overlay {
  position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%);
  display: flex; gap: 10px;
}
.btn-change, .btn-remove {
  padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600;
  border: none; cursor: pointer; display: flex; align-items: center; gap: 6px;
}
.btn-change { background: rgba(255,255,255,0.95); color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
.btn-remove { background: rgba(220,38,38,0.9); color: #fff; }

/* ══════════════════════════════════════
   INFO BOX
══════════════════════════════════════ */
.info-box {
  margin: 0 32px 24px; padding: 16px 20px;
  background: var(--gold-bg); border-radius: 12px;
  border-left: 3px solid var(--gold);
  display: flex; gap: 12px; align-items: flex-start;
}
.info-box strong { font-size: 13px; color: var(--navy); }
.info-box ul { margin: 8px 0 0; padding-left: 16px; }
.info-box li { font-size: 13px; color: #666; margin: 4px 0; }

/* ══════════════════════════════════════
   VERIFY STEP (STEP 4)
══════════════════════════════════════ */
.verify-card { padding: 44px 36px; text-align: center; }
.ai-buddy { margin-bottom: 32px; }
.buddy-avatar {
  width: 90px; height: 90px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 14px; position: relative; overflow: hidden;
  border: 3px solid var(--gold);
}
.buddy-img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
.buddy-pulse {
  position: absolute; inset: -6px; border-radius: 50%;
  border: 3px solid var(--gold);
  animation: pulse 2s ease-in-out infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 0.2; transform: scale(1); }
  50% { opacity: 0.7; transform: scale(1.08); }
}
.ai-buddy h2 { font-size: 21px; font-weight: 800; color: var(--navy); margin: 0 0 4px; font-family: Outfit, sans-serif; }
.buddy-subtitle { font-size: 13px; color: var(--gold3); font-weight: 600; margin: 0; }

.verify-status { margin-bottom: 28px; }
.verify-spinner {
  width: 36px; height: 36px; border: 3px solid #e5e7eb;
  border-top-color: var(--gold); border-radius: 50%;
  animation: spin 0.8s linear infinite; margin: 0 auto 16px;
}
@keyframes spin { to { transform: rotate(360deg); } }
.verify-text { font-size: 15px; font-weight: 700; color: var(--navy); margin: 0 0 6px; }
.verify-subtext { font-size: 13px; color: #aaa; margin: 0; }

.verify-steps-list {
  display: flex; flex-direction: column; gap: 10px;
  max-width: 360px; margin: 0 auto; text-align: left;
}
.verify-step {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 16px; border-radius: 12px;
  background: #f8f5f0; font-size: 14px; color: #bbb;
  transition: all 0.4s; border: 1px solid transparent;
}
.verify-step.active { background: var(--gold-bg); color: var(--navy); border-color: var(--gold-border); }
.verify-step.done   { background: #f0fdf4; color: #16a34a; border-color: #bbf7d0; }
.vs-icon-wrap { width: 26px; height: 26px; border-radius: 50%; background: rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.verify-step.done .vs-icon-wrap   { background: #dcfce7; }
.verify-step.active .vs-icon-wrap { background: rgba(216,155,15,0.15); }

/* ══════════════════════════════════════
   RESULT CARDS (STEP 5)
══════════════════════════════════════ */
.result-card {
  background: #fff; border-radius: 20px;
  box-shadow: 0 4px 28px rgba(0,0,0,0.07);
  padding: 36px; border: 1px solid rgba(0,0,0,0.04);
}

/* ✅ Side-by-side header layout */
.result-header {
  display: flex; align-items: flex-start; gap: 24px;
  margin-bottom: 24px; padding-bottom: 24px;
  border-bottom: 1px solid #f0ede8;
}
.result-buddy-wrap {
  position: relative; flex-shrink: 0;
  width: 88px; height: 88px;
}
.result-buddy-img {
  width: 88px; height: 88px; border-radius: 50%; object-fit: cover;
  border: 3px solid #e0ddd8;
}
.result-buddy-badge {
  position: absolute; bottom: 2px; right: 2px;
  width: 24px; height: 24px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  border: 2px solid #fff;
}
.result-buddy-badge-success  { background: #22c55e; }
.result-buddy-badge-rejected { background: #dc2626; }
.result-buddy-badge-pending  { background: #f59e0b; }

.result-buddy-success .result-buddy-img  { border-color: #86efac; }
.result-buddy-rejected .result-buddy-img { border-color: #fca5a5; }
.result-buddy-pending .result-buddy-img  { border-color: #fde68a; }

.result-header-text { flex: 1; padding-top: 4px; }
.result-header-text h2 { font-size: 22px; font-weight: 800; margin: 0 0 8px; font-family: Outfit, sans-serif; }
.result-message { font-size: 14px; color: #666; margin: 0; line-height: 1.6; }

.result-success h2 { color: #166534; }
.result-pending h2 { color: #92400e; }
.result-rejected h2 { color: #991b1b; }

/* Border top accent */
.result-success { border-top: 4px solid #22c55e; }
.result-pending { border-top: 4px solid #f59e0b; }
.result-rejected { border-top: 4px solid #dc2626; }

/* Buddy says box */
.buddy-says {
  display: flex; flex-direction: column; gap: 8px;
  padding: 16px 20px; border-radius: 14px;
  margin-bottom: 20px;
}
.buddy-says-success { background: #f0fdf4; border: 1px solid #bbf7d0; }
.buddy-says-danger  { background: #fef2f2; border: 1px solid #fecaca; }
.buddy-says-warn    { background: #fffbeb; border: 1px solid #fde68a; }
.buddy-says-label {
  display: flex; align-items: center; gap: 7px;
  font-size: 12px; font-weight: 700; color: var(--navy);
  text-transform: uppercase; letter-spacing: 0.04em;
}
.buddy-says p { font-size: 13.5px; color: #444; margin: 0; line-height: 1.65; }

/* Detected doc type */
.detected-doc-type {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 16px; background: #fef2f2;
  border: 1px solid #fecaca; border-radius: 10px;
  font-size: 13px; color: #991b1b; margin-bottom: 16px;
}

/* ✅ Side-by-side details grid */
.details-grid {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 14px; margin-bottom: 20px;
}
.detail-panel {
  border-radius: 14px; padding: 16px 18px;
}
.detail-panel-success { background: #f0fdf4; border: 1px solid #bbf7d0; }
.detail-panel-danger  { background: #fef2f2; border: 1px solid #fecaca; }
.detail-panel-warn    { background: #fffbeb; border: 1px solid #fde68a; }

.detail-panel-header {
  display: flex; align-items: center; gap: 8px;
  margin-bottom: 12px; font-size: 13px; color: var(--navy);
}
.score-badge {
  margin-left: auto; font-size: 11px; font-weight: 700;
  padding: 2px 10px; border-radius: 20px;
}
.score-badge-good { background: #dcfce7; color: #166534; }
.score-badge-bad  { background: #fee2e2; color: #991b1b; }

.tag-list { display: flex; flex-wrap: wrap; gap: 6px; }
.tag {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 12px; padding: 4px 10px; border-radius: 6px; font-weight: 500;
}
.tag-success { background: #dcfce7; color: #166534; }
.tag-danger  { background: #fee2e2; color: #991b1b; }
.tag-warn    { background: #fef3c7; color: #92400e; }

/* Next Steps section */
.next-steps-section {
  background: #faf9f7; border: 1px solid #e8e4de;
  border-radius: 16px; padding: 20px 22px; margin-bottom: 24px;
}
.next-steps-title {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; font-weight: 700; color: var(--navy);
  text-transform: uppercase; letter-spacing: 0.04em;
  margin-bottom: 16px;
}
.next-steps-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
}
.next-step-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 12px 14px; background: #fff;
  border: 1px solid #e8e4de; border-radius: 12px;
}
.next-step-icon {
  width: 36px; height: 36px; border-radius: 10px;
  background: var(--gold-bg); border: 1px solid var(--gold-border);
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.next-step-item strong { font-size: 13px; font-weight: 700; color: var(--navy); display: block; margin-bottom: 3px; }
.next-step-item p { font-size: 12px; color: #888; margin: 0; line-height: 1.5; }

/* Cooldown info */
.cooldown-info {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 18px; background: var(--gold-bg);
  border: 1px solid var(--gold-border); border-radius: 10px;
  font-size: 13px; color: var(--navy); margin-bottom: 20px;
}

.result-actions { display: flex; justify-content: flex-start; gap: 12px; flex-wrap: wrap; }

/* ══════════════════════════════════════
   FACE SCAN STEP
══════════════════════════════════════ */
.face-scan-section { display: flex; flex-direction: column; align-items: center; gap: 20px; padding: 28px 32px; }
.face-instructions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; width: 100%; max-width: 540px; }
.instr-item {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: #555; background: #faf9f7;
  border: 1px solid #e8e4de; border-radius: 50px; padding: 9px 14px; font-weight: 500;
}
.camera-container { display: flex; flex-direction: column; align-items: center; gap: 18px; }
.camera-frame {
  position: relative; width: 300px; height: 300px; border-radius: 50%;
  overflow: hidden; border: 3px solid var(--gold);
  background: #111; box-shadow: 0 0 0 6px rgba(216,155,15,0.12), 0 8px 32px rgba(0,0,0,0.18);
}
.camera-video { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
.selfie-preview { width: 100%; height: 100%; object-fit: cover; }
.camera-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; }
.face-guide-circle { width: 200px; height: 240px; border: 2.5px dashed rgba(216,155,15,0.55); border-radius: 50%; }
.camera-actions { display: flex; justify-content: center; }
.btn-capture {
  display: flex; align-items: center; gap: 10px;
  padding: 13px 32px; border-radius: 50px;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  color: #fff; font-weight: 700; font-size: 15px;
  border: none; cursor: pointer; box-shadow: 0 4px 16px rgba(216,155,15,0.35);
  transition: all 0.2s; font-family: inherit;
}
.btn-capture:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(216,155,15,0.48); }
.selfie-ready {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; font-weight: 600; color: #16a34a;
  background: #dcfce7; border: 1px solid #86efac; border-radius: 50px; padding: 8px 20px;
}
.camera-error { text-align: center; padding: 24px; color: #dc2626; display: flex; flex-direction: column; align-items: center; gap: 12px; }
.camera-error p { margin: 0; font-size: 14px; }

/* ══════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════ */
@media (max-width: 640px) {
  .form-grid { grid-template-columns: 1fr; }
  .form-grid, .step-footer, .info-box, .upload-zone { margin-left: 16px; margin-right: 16px; }
  .card-header, .form-grid { padding-left: 20px; padding-right: 20px; }
  .step-footer { padding-left: 20px; padding-right: 20px; }
  .steps-bar .step-label { display: none; }
  .result-header { flex-direction: column; align-items: center; text-align: center; }
  .result-header-text h2 { font-size: 20px; }
  .details-grid { grid-template-columns: 1fr; }
  .next-steps-grid { grid-template-columns: 1fr; }
  .face-instructions { grid-template-columns: 1fr; }
  .result-card { padding: 24px 20px; }
  .verify-card { padding: 32px 20px; }
  .face-scan-section { padding: 20px 16px; }
}
</style>