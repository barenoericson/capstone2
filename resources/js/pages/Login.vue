<template>
  <div class="login-container">
    <!-- BACKDROP BLUR - Uses backdrop-filter to blur Landing page behind -->
    <div class="login-backdrop"></div>

    <!-- FORM MODAL -->
    <div class="login-modal">
      <!-- FORM CONTAINER - The highlighted element -->
      <div class="login-form">
        <!-- CLOSE BUTTON -->
        <button class="close-btn" @click="goBack" title="Close">
          ✕
        </button>

        <!-- BRAND LOGO -->
        <div class="brand-section">
          <div class="brand-mark">
            <span class="brand-realty">RealtyLink</span><span class="brand-ph">PH</span>
          </div>
          <p class="brand-tagline">When Real Estate Meets Innovation</p>
        </div>

        <!-- HEADER -->
        <div class="form-header">
          <h1 class="login-title">Login your account</h1>
          <p class="login-subtitle">
            Sign in to continue to RealtyLinkPH
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
        <form @submit.prevent="handleLogin" class="login-form-content">
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
                placeholder="Enter your password"
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

          <!-- REMEMBER / FORGOT -->
          <div class="form-row">
            <label class="remember">
              <input type="checkbox" v-model="form.remember" />
              <span>Remember me</span>
            </label>
            <router-link to="/forgot-password" class="forgot">
              Forgot password?
            </router-link>
          </div>

          <!-- LOGIN BUTTON -->
          <button 
            type="submit"
            class="btn-login" 
            :disabled="loading"
          >
            <span v-if="!loading" class="btn-text">Sign In</span>
            <span v-else class="btn-loading">
              <span class="spinner"></span>
              Signing in...
            </span>
          </button>
        </form>

        <!-- DIVIDER -->
        <div class="form-divider">
          <span>Don't have an account?</span>
        </div>

        <!-- SIGNUP -->
        <router-link to="/register" class="btn-signup">
          Create New Account
        </router-link>

        <!-- FOOTER LINKS -->
        <div class="form-footer">
          <a href="#" class="footer-link">Terms & Conditions</a>
          <span class="separator">•</span>
          <a href="#" class="footer-link">Privacy Policy</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '../src/services/api'

export default {
  name: 'Login',
  setup() {
    const router = useRouter()
    const loading = ref(false)
    const showPassword = ref(false)

    const form = reactive({
      email: '',
      password: '',
      remember: false
    })

    const errors = reactive({
      email: '',
      password: ''
    })

    const alert = reactive({
      show: false,
      type: 'success',
      message: ''
    })

    const validateField = (field) => {
      errors[field] = ''

      if (field === 'email' && !form.email) errors.email = 'Email is required'
      if (field === 'password' && !form.password) errors.password = 'Password is required'
    }

    const handleLogin = async () => {
      validateField('email')
      validateField('password')

      if (errors.email || errors.password) {
        return
      }

      loading.value = true

      try {
        const res = await authAPI.login(form)

        // Store token
        localStorage.setItem('auth_token', res.data.token)

        // Store user
        localStorage.setItem('user', JSON.stringify(res.data.user))

        // Show success alert
        alert.show = true
        alert.type = 'success'
        alert.message = 'Login successful! Redirecting...'

        // Redirect after 1 second based on role
        setTimeout(() => {
          const userData = res.data.user
          if (userData.role === 'admin') {
            router.push('/admin/dashboard')
          } else if (userData.role === 'agent') {
            router.push('/agent/dashboard')
          } else {
            router.push('/dashboard')
          }
        }, 1000)
      } catch (e) {
        alert.show = true
        alert.type = 'error'
        alert.message = 'Invalid credentials. Please try again.'

        // Hide alert after 3 seconds
        setTimeout(() => {
          alert.show = false
        }, 3000)
      } finally {
        loading.value = false
      }
    }

    const goBack = () => {
      router.back()
    }

    return {
      form,
      errors,
      alert,
      loading,
      showPassword,
      validateField,
      handleLogin,
      goBack
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* {
  box-sizing: border-box;
}

.login-container {
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
.login-backdrop {
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
.login-modal {
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
.login-form {
  position: relative;
  width: 100%;
  max-width: 480px;
  background: white;
  border-radius: 20px;
  padding: 48px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
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

.login-title {
  font-size: 2rem;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0 0 8px 0;
  letter-spacing: -0.5px;
}

.login-subtitle {
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
.login-form-content {
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

/* REMEMBER / FORGOT */
.form-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
  margin-bottom: 16px;
}

.remember {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  color: #666;
}

.remember input {
  width: 18px;
  height: 18px;
  accent-color: #FFD700;
  cursor: pointer;
  border-radius: 4px;
}

.forgot {
  color: #FFD700;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.9rem;
  transition: opacity 0.3s ease;
}

.forgot:hover {
  opacity: 0.8;
}

/* BUTTONS */
.btn-login {
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
}

.btn-login:hover:not(:disabled) {
  background: linear-gradient(135deg, #FFD700 0%, #FFE44D 100%);
  color: #1a1a1a;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(255, 215, 0, 0.3);
}

.btn-login:disabled {
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

/* SIGNUP BUTTON */
.btn-signup {
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

.btn-signup:hover {
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
@media (max-width: 600px) {
  .login-form {
    padding: 32px 24px;
    max-width: 100%;
    margin: 0 20px;
  }

  .login-title {
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

  .login-form-content {
    gap: 16px;
  }
}

@media (max-width: 400px) {
  .login-container {
    padding: 10px;
  }

  .login-form {
    padding: 24px 16px;
  }

  .login-title {
    font-size: 1.3rem;
  }

  .brand-mark {
    font-size: 1.5rem;
  }

  .btn-login,
  .btn-signup {
    padding: 12px;
    font-size: 0.9rem;
  }
}
</style>