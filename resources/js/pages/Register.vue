<template>
  <div class="register-container">
    <!-- BACKDROP BLUR - Blurs Landing page behind -->
    <div class="register-backdrop"></div>

    <!-- FORM MODAL -->
    <div class="register-modal">
      <!-- FORM CONTAINER - The highlighted element -->
      <div class="register-form">
        <!-- CLOSE BUTTON -->
        <button class="close-btn" @click="goBack" title="Close">
          ✕
        </button>

        <!-- BRAND LOGO -->
        <div class="brand-section">
          <div class="brand-mark">
            <span class="brand-realty">RealtyLink</span><span class="brand-ph">PH</span>
          </div>
          <p class="brand-tagline">Join our real estate community</p>
        </div>

        <!-- HEADER -->
        <div class="form-header">
          <h1 class="register-title">Create your account</h1>
          <p class="register-subtitle">
            Start your real estate journey today
          </p>
        </div>

        <!-- ALERT -->
        <transition name="slide-down">
          <div v-if="alert.show" :class="['alert', `alert-${alert.type}`]">
            <span class="alert-icon">{{ alert.type === 'error' ? '❌' : '✅' }}</span>
            <span class="alert-message">{{ alert.message }}</span>
          </div>
        </transition>

        <!-- FORM -->
        <form @submit.prevent="handleRegister" class="register-form-content">
          <!-- NAME -->
          <div class="form-group">
            <label for="name">Full Name</label>
            <div class="input-wrapper">
              <span class="input-icon">👤</span>
              <input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Juan Dela Cruz"
                @blur="validateField('name')"
                class="form-input"
              />
            </div>
            <transition name="slide-down">
              <span v-if="errors.name" class="form-error">
                {{ errors.name }}
              </span>
            </transition>
          </div>

          <!-- EMAIL -->
          <div class="form-group">
            <label for="email">Email Address</label>
            <div class="input-wrapper">
              <span class="input-icon">📧</span>
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="juan@example.com"
                @blur="validateField('email')"
                class="form-input"
              />
            </div>
            <transition name="slide-down">
              <span v-if="errors.email" class="form-error">
                {{ errors.email }}
              </span>
            </transition>
          </div>

          <!-- PASSWORD -->
          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <span class="input-icon">🔐</span>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Minimum 8 characters"
                @blur="validateField('password')"
                class="form-input"
              />
              <button
                type="button"
                class="eye-btn"
                @click="showPassword = !showPassword"
                :title="showPassword ? 'Hide password' : 'Show password'"
              >
                {{ showPassword ? '👁️‍🗨️' : '👁️' }}
              </button>
            </div>
            <transition name="slide-down">
              <span v-if="errors.password" class="form-error">
                {{ errors.password }}
              </span>
            </transition>
          </div>

          <!-- CONFIRM PASSWORD -->
          <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <div class="input-wrapper">
              <span class="input-icon">🔐</span>
              <input
                id="confirm_password"
                v-model="form.confirm_password"
                :type="showConfirmPassword ? 'text' : 'password'"
                placeholder="Re-enter password"
                @blur="validateField('confirm_password')"
                class="form-input"
              />
              <button
                type="button"
                class="eye-btn"
                @click="showConfirmPassword = !showConfirmPassword"
                :title="showConfirmPassword ? 'Hide password' : 'Show password'"
              >
                {{ showConfirmPassword ? '👁️‍🗨️' : '👁️' }}
              </button>
            </div>
            <transition name="slide-down">
              <span v-if="errors.confirm_password" class="form-error">
                {{ errors.confirm_password }}
              </span>
            </transition>
          </div>

          <!-- TERMS CHECKBOX -->
          <div class="checkbox-group">
            <input 
              id="terms"
              type="checkbox" 
              v-model="form.terms"
              @blur="validateField('terms')"
            />
            <label for="terms" class="checkbox-label">
              I agree to the
              <router-link to="/terms" class="link">Terms & Conditions</router-link>
              and
              <router-link to="/privacy" class="link">Privacy Policy</router-link>
            </label>
          </div>
          <transition name="slide-down">
            <span v-if="errors.terms" class="form-error">
              {{ errors.terms }}
            </span>
          </transition>

          <!-- REGISTER BUTTON -->
          <button 
            type="submit"
            class="btn-register" 
            :disabled="loading"
          >
            <span v-if="!loading" class="btn-text">Create Account</span>
            <span v-else class="btn-loading">
              <span class="spinner"></span>
              Creating account...
            </span>
          </button>
        </form>

        <!-- DIVIDER -->
        <div class="form-divider">
          <span>Already have an account?</span>
        </div>

        <!-- LOGIN LINK -->
        <router-link to="/login" class="btn-signin">
          Sign In Instead
        </router-link>

        <!-- FOOTER LINKS -->
        <div class="form-footer">
          <a href="#" class="footer-link">Need help?</a>
          <span class="separator">•</span>
          <a href="#" class="footer-link">Contact Support</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '../src/services/api'

const router = useRouter()
const loading = ref(false)
const showPassword = ref(false)
const showConfirmPassword = ref(false)

const form = reactive({
  name: '',
  email: '',
  password: '',
  confirm_password: '',
  terms: false
})

const errors = reactive({})
const alert = reactive({ show: false, message: '', type: '' })

const validateField = (field) => {
  errors[field] = ''

  if (field === 'name' && !form.name) {
    errors.name = 'Full name is required'
  }
  
  if (field === 'email' && !form.email) {
    errors.email = 'Email address is required'
  }
  
  if (field === 'password' && form.password.length < 8) {
    errors.password = 'Password must be at least 8 characters'
  }
  
  if (field === 'confirm_password' && form.password !== form.confirm_password) {
    errors.confirm_password = 'Passwords do not match'
  }
  
  if (field === 'terms' && !form.terms) {
    errors.terms = 'You must accept the terms and privacy policy'
  }
}

const handleRegister = async () => {
  // Validate all fields
  Object.keys(form).forEach(validateField)
  
  if (Object.values(errors).some(Boolean)) {
    alert.show = true
    alert.type = 'error'
    alert.message = 'Please fix the errors in the form'
    setTimeout(() => { alert.show = false }, 3000)
    return
  }

  loading.value = true
  try {
    await authAPI.register({
      name: form.name,
      email: form.email,
      password: form.password,
      password_confirmation: form.confirm_password
    })

    alert.message = 'Account created successfully! Redirecting to login...'
    alert.type = 'success'
    alert.show = true

    setTimeout(() => {
      router.push('/login')
    }, 1500)
  } catch (e) {
    alert.message = e.response?.data?.message || 'Registration failed. Please try again.'
    alert.type = 'error'
    alert.show = true

    setTimeout(() => { alert.show = false }, 3000)
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.back()
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* {
  box-sizing: border-box;
}

.register-container {
  position: fixed;
  inset: 0;
  width: 100%;
  height: 100vh;
  font-family: 'Plus Jakarta Sans', sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

/* BACKDROP BLUR - Blurs Landing page behind */
.register-backdrop {
  position: fixed;
  inset: 0;
  backdrop-filter: blur(12px);
  background: rgba(0, 0, 0, 0.35);
  z-index: 1000;
  animation: blurIn 0.6s ease-out;
}

@keyframes blurIn {
  from {
    backdrop-filter: blur(0px);
    background: rgba(0, 0, 0, 0);
  }
  to {
    backdrop-filter: blur(12px);
    background: rgba(0, 0, 0, 0.35);
  }
}

/* FORM MODAL - Container for form */
.register-modal {
  position: relative;
  z-index: 1001;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* FORM CONTAINER - The highlighted white card */
.register-form {
  position: relative;
  width: 100%;
  max-width: 520px;
  background: white;
  border-radius: 20px;
  padding: 48px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
  max-height: 95vh;
  overflow-y: auto;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* SCROLLBAR STYLING */
.register-form::-webkit-scrollbar {
  width: 6px;
}

.register-form::-webkit-scrollbar-track {
  background: transparent;
}

.register-form::-webkit-scrollbar-thumb {
  background: #FFD700;
  border-radius: 3px;
}

/* CLOSE BUTTON */
.close-btn {
  position: absolute;
  top: 20px;
  right: 20px;
  width: 40px;
  height: 40px;
  border: none;
  background: #f5f5f5;
  border-radius: 50%;
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #999;
  z-index: 10;
}

.close-btn:hover {
  background: #FFD700;
  color: white;
  transform: rotate(90deg);
}

/* BRAND SECTION */
.brand-section {
  text-align: center;
  margin-bottom: 32px;
}

.brand-mark {
  font-size: 2.2rem;
  font-weight: 800;
  letter-spacing: -0.5px;
  margin-bottom: 8px;
}

.brand-realty {
  color: #1a1a1a;
}

.brand-ph {
  color: #FFD700;
  margin-left: 4px;
}

.brand-tagline {
  font-size: 0.9rem;
  color: #999;
  font-weight: 500;
  margin: 0;
}

/* FORM HEADER */
.form-header {
  margin-bottom: 32px;
  text-align: center;
}

.register-title {
  font-size: 2rem;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0 0 8px 0;
  letter-spacing: -0.5px;
}

.register-subtitle {
  color: #666;
  font-size: 0.95rem;
  font-weight: 500;
  margin: 0;
}

/* ALERT */
.alert {
  margin-bottom: 24px;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  gap: 12px;
  animation: slideDown 0.3s ease-out;
}

.alert-icon {
  font-size: 1.1rem;
  flex-shrink: 0;
}

.alert-message {
  flex: 1;
}

.alert-error {
  background: #fee;
  color: #c33;
  border-left: 4px solid #c33;
}

.alert-success {
  background: #efe;
  color: #3c3;
  border-left: 4px solid #3c3;
}

/* FORM */
.register-form-content {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

label {
  font-size: 0.85rem;
  font-weight: 600;
  color: #1a1a1a;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 14px;
  font-size: 1.1rem;
  pointer-events: none;
}

.form-input {
  width: 100%;
  padding: 12px 14px 12px 44px;
  border-radius: 12px;
  border: 2px solid #eee;
  font-size: 0.95rem;
  font-family: 'Plus Jakarta Sans', sans-serif;
  transition: all 0.3s ease;
  background: #f9f9f9;
}

.form-input:focus {
  outline: none;
  border-color: #FFD700;
  background: white;
  box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.1);
}

.form-input::placeholder {
  color: #999;
}

.eye-btn {
  position: absolute;
  right: 12px;
  border: none;
  background: none;
  cursor: pointer;
  font-size: 1.1rem;
  padding: 4px;
  transition: transform 0.2s ease;
}

.eye-btn:hover {
  transform: scale(1.15);
}

.form-error {
  font-size: 0.8rem;
  color: #d9534f;
  font-weight: 500;
  animation: slideDown 0.2s ease-out;
}

/* CHECKBOX */
.checkbox-group {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-top: 8px;
  margin-bottom: 8px;
}

.checkbox-group input[type="checkbox"] {
  width: 20px;
  height: 20px;
  margin-top: 2px;
  cursor: pointer;
  accent-color: #FFD700;
  flex-shrink: 0;
  border-radius: 4px;
  border: 2px solid #eee;
  transition: all 0.3s ease;
}

.checkbox-group input[type="checkbox"]:focus {
  border-color: #FFD700;
  box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
}

.checkbox-label {
  font-size: 0.9rem;
  color: #666;
  line-height: 1.4;
  cursor: pointer;
  flex: 1;
}

.link {
  color: #FFD700;
  font-weight: 600;
  text-decoration: none;
  transition: opacity 0.3s ease;
}

.link:hover {
  opacity: 0.8;
  text-decoration: underline;
}

/* BUTTONS */
.btn-register {
  width: 100%;
  padding: 14px;
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
  color: white;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: 'Plus Jakarta Sans', sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  position: relative;
  overflow: hidden;
  margin-top: 8px;
}

.btn-register:hover:not(:disabled) {
  background: linear-gradient(135deg, #FFD700 0%, #FFE44D 100%);
  color: #1a1a1a;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(255, 215, 0, 0.3);
}

.btn-register:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-text {
  display: flex;
  align-items: center;
}

.btn-loading {
  display: flex;
  align-items: center;
  gap: 8px;
}

.spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* DIVIDER */
.form-divider {
  text-align: center;
  margin: 28px 0;
  font-size: 0.85rem;
  color: #999;
  position: relative;
}

.form-divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #eee;
}

.form-divider span {
  background: white;
  padding: 0 12px;
  position: relative;
}

/* SIGNIN BUTTON */
.btn-signin {
  display: block;
  width: 100%;
  padding: 14px;
  border-radius: 12px;
  border: 2px solid #FFD700;
  background: transparent;
  color: #FFD700;
  font-weight: 700;
  font-size: 0.95rem;
  text-decoration: none;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: 'Plus Jakarta Sans', sans-serif;
}

.btn-signin:hover {
  background: #FFD700;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(255, 215, 0, 0.2);
}

/* FOOTER */
.form-footer {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 24px;
  font-size: 0.8rem;
}

.footer-link {
  color: #999;
  text-decoration: none;
  transition: color 0.3s ease;
}

.footer-link:hover {
  color: #FFD700;
}

.separator {
  color: #ddd;
}

/* ANIMATIONS */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* RESPONSIVE */
@media (max-width: 700px) {
  .register-form {
    padding: 32px 24px;
    max-width: 100%;
    margin: 0 20px;
  }

  .register-title {
    font-size: 1.5rem;
  }

  .brand-mark {
    font-size: 1.8rem;
  }

  .close-btn {
    width: 36px;
    height: 36px;
    font-size: 1.2rem;
  }

  .form-header {
    margin-bottom: 24px;
  }

  .register-form-content {
    gap: 16px;
  }
}

@media (max-width: 450px) {
  .register-container {
    padding: 10px;
  }

  .register-form {
    padding: 24px 16px;
  }

  .register-title {
    font-size: 1.3rem;
  }

  .brand-mark {
    font-size: 1.5rem;
  }

  .btn-register,
  .btn-signin {
    padding: 12px;
    font-size: 0.9rem;
  }

  .close-btn {
    width: 32px;
    height: 32px;
    font-size: 1rem;
  }
}
</style>