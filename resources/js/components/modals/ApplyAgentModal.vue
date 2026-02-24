<template>
  <div v-if="show" class="modal-overlay" @click="$emit('close')">
    <div class="modal-container" @click.stop>
      <!-- Modal Header -->
      <div class="modal-header">
        <h2 class="modal-title">🚀 Become an Agent</h2>
        <button class="modal-close" @click="$emit('close')">✕</button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <!-- Intro Section -->
        <div class="intro-section">
          <p class="intro-text">
            Join our network of professional real estate agents and start growing your business today. 
            Fill out the form below to apply for agent verification.
          </p>
        </div>

        <!-- Form Section -->
        <form @submit.prevent="handleSubmit" class="agent-form">
          <!-- Personal Information -->
          <div class="form-section">
            <h3 class="section-heading">Personal Information</h3>
            
            <div class="form-group">
              <label class="form-label">First Name</label>
              <input 
                v-model="formData.firstName"
                type="text" 
                class="form-input" 
                placeholder="John"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Last Name</label>
              <input 
                v-model="formData.lastName"
                type="text" 
                class="form-input" 
                placeholder="Doe"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Email Address</label>
              <input 
                v-model="formData.email"
                type="email" 
                class="form-input" 
                placeholder="john@example.com"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input 
                v-model="formData.phone"
                type="tel" 
                class="form-input" 
                placeholder="+1 (555) 123-4567"
                required
              />
            </div>
          </div>

          <!-- Professional Information -->
          <div class="form-section">
            <h3 class="section-heading">Professional Information</h3>
            
            <div class="form-group">
              <label class="form-label">Years of Experience</label>
              <select v-model="formData.experience" class="form-input" required>
                <option value="">Select years of experience</option>
                <option value="0-2">0-2 years</option>
                <option value="2-5">2-5 years</option>
                <option value="5-10">5-10 years</option>
                <option value="10+">10+ years</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">License Number</label>
              <input 
                v-model="formData.license"
                type="text" 
                class="form-input" 
                placeholder="RE-123456789"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Agency Name (if applicable)</label>
              <input 
                v-model="formData.agency"
                type="text" 
                class="form-input" 
                placeholder="Your Agency"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Specialization</label>
              <select v-model="formData.specialization" class="form-input" required>
                <option value="">Select specialization</option>
                <option value="residential">Residential</option>
                <option value="commercial">Commercial</option>
                <option value="luxury">Luxury</option>
                <option value="investment">Investment</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="form-section">
            <h3 class="section-heading">Additional Information</h3>
            
            <div class="form-group">
              <label class="form-label">Service Area</label>
              <input 
                v-model="formData.serviceArea"
                type="text" 
                class="form-input" 
                placeholder="e.g., New York, California"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Bio / About You</label>
              <textarea 
                v-model="formData.bio"
                class="form-textarea" 
                rows="4"
                placeholder="Tell us about your experience and what makes you a great agent..."
                required
              ></textarea>
            </div>

            <div class="form-group checkbox-group">
              <label class="checkbox-label">
                <input 
                  v-model="formData.agreeTerms"
                  type="checkbox" 
                  required
                />
                <span>I agree to the Terms of Service and Privacy Policy</span>
              </label>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <button type="button" @click="$emit('close')" class="btn-cancel">Cancel</button>
            <button type="submit" class="btn-submit" :disabled="isSubmitting">
              {{ isSubmitting ? 'Submitting...' : 'Submit Application' }}
            </button>
          </div>
        </form>

        <!-- Success Message -->
        <div v-if="showSuccess" class="success-message">
          <div class="success-icon">✓</div>
          <h3>Application Submitted!</h3>
          <p>Thank you for applying. We'll review your application and contact you soon.</p>
          <button @click="handleSuccessClose" class="btn-success-close">Close</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ApplyAgentModal',
  props: {
    show: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      isSubmitting: false,
      showSuccess: false,
      formData: {
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        experience: '',
        license: '',
        agency: '',
        specialization: '',
        serviceArea: '',
        bio: '',
        agreeTerms: false,
      },
    };
  },
  methods: {
    async handleSubmit() {
      this.isSubmitting = true;

      // Simulate API call
      setTimeout(() => {
        console.log('Application submitted:', this.formData);
        this.isSubmitting = false;
        this.showSuccess = true;
      }, 1500);
    },
    handleSuccessClose() {
      this.showSuccess = false;
      this.resetForm();
      this.$emit('close');
    },
    resetForm() {
      this.formData = {
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        experience: '',
        license: '',
        agency: '',
        specialization: '',
        serviceArea: '',
        bio: '',
        agreeTerms: false,
      };
    },
  },
};
</script>

<style scoped>
/* =====================================================================
   MODAL OVERLAY
   ===================================================================== */

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  backdrop-filter: blur(4px);
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal-container {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    transform: translateY(30px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

/* =====================================================================
   MODAL HEADER
   ===================================================================== */

.modal-header {
  padding: 24px;
  border-bottom: 2px solid #f5f5f5;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(135deg, #f5f5f5 0%, white 100%);
  border-radius: 12px 12px 0 0;
}

.modal-title {
  font-size: 24px;
  font-weight: 800;
  margin: 0;
  color: #100c08;
  font-family: 'Poppins', sans-serif;
  letter-spacing: -0.3px;
}

.modal-close {
  background: none;
  border: none;
  font-size: 28px;
  cursor: pointer;
  color: #999;
  transition: all 0.3s;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.modal-close:hover {
  background: rgba(230, 174, 13, 0.1);
  color: #e6ae0d;
}

/* =====================================================================
   MODAL BODY
   ===================================================================== */

.modal-body {
  padding: 32px;
}

.intro-section {
  margin-bottom: 32px;
}

.intro-text {
  font-size: 14px;
  color: #666;
  margin: 0;
  line-height: 1.6;
  font-family: 'Inter', sans-serif;
}

/* =====================================================================
   FORM STYLES
   ===================================================================== */

.agent-form {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.form-section {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.section-heading {
  font-size: 14px;
  font-weight: 700;
  text-transform: uppercase;
  color: #100c08;
  margin: 0;
  letter-spacing: 1px;
  font-family: 'Inter', sans-serif;
  padding-bottom: 12px;
  border-bottom: 2px solid #e6ae0d;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  font-size: 13px;
  font-weight: 600;
  color: #100c08;
  font-family: 'Inter', sans-serif;
}

.form-input,
.form-textarea {
  padding: 12px 16px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  transition: all 0.3s;
  background: white;
  color: #100c08;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #e6ae0d;
  background: #fffbf0;
  box-shadow: 0 0 0 3px rgba(230, 174, 13, 0.1);
}

.form-input::placeholder,
.form-textarea::placeholder {
  color: #999;
}

.form-textarea {
  resize: vertical;
  font-family: 'Inter', sans-serif;
}

.checkbox-group {
  gap: 12px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-size: 13px;
  color: #666;
  font-family: 'Inter', sans-serif;
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: #e6ae0d;
}

/* =====================================================================
   FORM ACTIONS
   ===================================================================== */

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 24px;
  border-top: 1px solid #f5f5f5;
}

.btn-cancel {
  padding: 12px 28px;
  background: #f5f5f5;
  color: #100c08;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  transition: all 0.3s;
}

.btn-cancel:hover {
  background: #e0e0e0;
}

.btn-submit {
  padding: 12px 28px;
  background: linear-gradient(135deg, #e6ae0d, #d4a000);
  color: #100c08;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  transition: all 0.3s;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(230, 174, 13, 0.3);
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* =====================================================================
   SUCCESS MESSAGE
   ===================================================================== */

.success-message {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: white;
  padding: 48px 32px;
  border-radius: 12px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  text-align: center;
  max-width: 400px;
  animation: slideUp 0.3s ease;
  z-index: 2001;
}

.success-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #e6ae0d, #d4a000);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  color: #100c08;
  margin: 0 auto 20px;
  font-weight: 800;
}

.success-message h3 {
  font-size: 20px;
  font-weight: 700;
  color: #100c08;
  margin: 0 0 12px 0;
  font-family: 'Poppins', sans-serif;
}

.success-message p {
  font-size: 14px;
  color: #666;
  margin: 0 0 24px 0;
  font-family: 'Inter', sans-serif;
  line-height: 1.6;
}

.btn-success-close {
  padding: 12px 32px;
  background: linear-gradient(135deg, #e6ae0d, #d4a000);
  color: #100c08;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  transition: all 0.3s;
}

.btn-success-close:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(230, 174, 13, 0.3);
}

/* =====================================================================
   RESPONSIVE
   ===================================================================== */

@media (max-width: 768px) {
  .modal-container {
    width: 95%;
    max-height: 95vh;
  }

  .modal-body {
    padding: 24px;
  }

  .modal-title {
    font-size: 20px;
  }

  .form-actions {
    flex-direction: column-reverse;
  }

  .btn-cancel,
  .btn-submit {
    width: 100%;
  }

  .success-message {
    width: 90%;
    padding: 32px 24px;
  }
}

@media (max-width: 480px) {
  .modal-header {
    padding: 16px;
  }

  .modal-body {
    padding: 16px;
  }

  .modal-title {
    font-size: 18px;
  }

  .section-heading {
    font-size: 12px;
  }

  .form-input,
  .form-textarea {
    font-size: 16px; /* Prevents zoom on iOS */
  }
}

/* Scrollbar Styling */
.modal-container::-webkit-scrollbar {
  width: 8px;
}

.modal-container::-webkit-scrollbar-track {
  background: #f5f5f5;
  border-radius: 10px;
}

.modal-container::-webkit-scrollbar-thumb {
  background: #e6ae0d;
  border-radius: 10px;
}

.modal-container::-webkit-scrollbar-thumb:hover {
  background: #d4a000;
}
</style>