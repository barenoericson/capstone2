<template>
  <div class="reset-page">
    <div class="reset-card">
      <!-- Brand -->
      <div class="brand-section">
        <div class="brand-mark">
          <span class="brand-realty">RealtyLink</span><span class="brand-ph">PH</span>
        </div>
        <p class="brand-tagline">When Real Estate Meets Innovation</p>
      </div>

      <!-- ── Success view ── -->
      <template v-if="view === 'success'">
        <div class="success-view">
          <div class="success-icon">✅</div>
          <h2 class="success-title">Password Reset!</h2>
          <p class="success-subtitle">
            Your password has been updated successfully.<br />You can now log in with your new password.
          </p>
          <button @click="goToLogin" class="btn-submit btn-login">Go to Login</button>
        </div>
      </template>

      <!-- ── Invalid / expired token view ── -->
      <template v-else-if="view === 'invalid'">
        <div class="success-view">
          <div class="success-icon">⚠️</div>
          <h2 class="success-title">Link Expired</h2>
          <p class="success-subtitle">
            This password reset link is invalid or has expired.<br />
            Please request a new one.
          </p>
          <button @click="goToLogin" class="btn-submit btn-login">Request New Link</button>
        </div>
      </template>

      <!-- ── Reset form view ── -->
      <template v-else>
        <div class="form-header">
          <h1 class="reset-title">Set New Password</h1>
          <p class="reset-subtitle">Choose a strong password for your account</p>
        </div>

        <!-- Alert -->
        <transition name="slide-down">
          <div v-if="alert.show" :class="['alert', `alert-${alert.type}`]">
            <span class="alert-icon">{{ alert.type === 'error' ? '❌' : '✅' }}</span>
            <span class="alert-message">{{ alert.message }}</span>
          </div>
        </transition>

        <form @submit.prevent="handleReset" class="reset-form">
          <!-- New password -->
          <div class="form-group">
            <label>New Password</label>
            <div class="input-wrapper">
              <span class="input-icon">🔐</span>
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="At least 8 characters"
                class="form-input"
              />
              <button type="button" class="eye-btn" @click="showPassword = !showPassword">
                {{ showPassword ? '👁️‍🗨️' : '👁️' }}
              </button>
            </div>
            <transition name="slide-down">
              <span v-if="errors.password" class="form-error">{{ errors.password }}</span>
            </transition>
          </div>

          <!-- Confirm password -->
          <div class="form-group">
            <label>Confirm New Password</label>
            <div class="input-wrapper">
              <span class="input-icon">🔐</span>
              <input
                v-model="form.password_confirmation"
                :type="showConfirm ? 'text' : 'password'"
                placeholder="Repeat your password"
                class="form-input"
              />
              <button type="button" class="eye-btn" @click="showConfirm = !showConfirm">
                {{ showConfirm ? '👁️‍🗨️' : '👁️' }}
              </button>
            </div>
            <transition name="slide-down">
              <span v-if="errors.password_confirmation" class="form-error">{{ errors.password_confirmation }}</span>
            </transition>
          </div>

          <button type="submit" class="btn-submit btn-login" :disabled="loading">
            <span v-if="!loading" class="btn-text">Reset Password</span>
            <span v-else class="btn-loading"><span class="spinner"></span>Resetting...</span>
          </button>
        </form>

        <div class="back-link">
          <a href="/" class="footer-link">← Back to Home</a>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export default {
  name: 'ResetPassword',
  setup() {
    const route = useRoute()
    const router = useRouter()

    const apiUrl = localStorage.getItem('api_url') || window.__API_URL__

    const view = ref('form') // 'form' | 'success' | 'invalid'
    const loading = ref(false)
    const showPassword = ref(false)
    const showConfirm = ref(false)

    const form = reactive({
      password: '',
      password_confirmation: '',
    })
    const errors = reactive({ password: '', password_confirmation: '' })
    const alert = reactive({ show: false, type: 'error', message: '' })

    // Read token and email from URL query params
    const token = ref('')
    const email = ref('')

    onMounted(() => {
      token.value = route.query.token || ''
      email.value = route.query.email || ''
      if (!token.value || !email.value) {
        view.value = 'invalid'
      }
    })

    const validate = () => {
      errors.password = ''
      errors.password_confirmation = ''
      let valid = true
      if (!form.password || form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters'
        valid = false
      }
      if (form.password !== form.password_confirmation) {
        errors.password_confirmation = 'Passwords do not match'
        valid = false
      }
      return valid
    }

    const handleReset = async () => {
      if (!validate()) return

      loading.value = true
      try {
        const res = await fetch(`${apiUrl}/api/auth/reset-password`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({
            email: email.value,
            token: token.value,
            password: form.password,
            password_confirmation: form.password_confirmation,
          }),
        })
        const data = await res.json()

        if (res.ok) {
          view.value = 'success'
        } else if (res.status === 401) {
          view.value = 'invalid'
        } else {
          alert.show = true
          alert.type = 'error'
          alert.message = data.message || 'Something went wrong. Please try again.'
          setTimeout(() => { alert.show = false }, 4000)
        }
      } catch (e) {
        alert.show = true
        alert.type = 'error'
        alert.message = 'Connection error. Please try again.'
        setTimeout(() => { alert.show = false }, 4000)
      } finally {
        loading.value = false
      }
    }

    const goToLogin = () => {
      router.push('/')
    }

    return {
      view, loading, showPassword, showConfirm,
      form, errors, alert,
      token, email,
      handleReset, goToLogin,
    }
  },
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800&display=swap');

* { margin: 0; padding: 0; box-sizing: border-box; }

:root {
  --color-mustard: #e6ae0d;
  --color-black: #100c08;
  --color-smoke: #f5f5f5;
}

.reset-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #fef9e7 0%, #fff8e1 50%, #fdf3d0 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  font-family: 'Inter', sans-serif;
}

.reset-card {
  background: white;
  border-radius: 20px;
  padding: 40px 36px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
}

/* Brand */
.brand-section {
  text-align: center;
  margin-bottom: 28px;
}
.brand-mark {
  font-size: 1.6rem;
  font-weight: 800;
  font-family: 'Poppins', sans-serif;
  display: inline-flex;
}
.brand-realty { color: var(--color-black); }
.brand-ph { color: var(--color-mustard); margin-left: 2px; }
.brand-tagline { font-size: 0.78rem; color: #999; margin-top: 4px; }

/* Header */
.form-header { text-align: center; margin-bottom: 20px; }
.reset-title { font-size: 1.4rem; font-weight: 700; color: var(--color-black); margin-bottom: 6px; }
.reset-subtitle { font-size: 0.85rem; color: #777; }

/* Alert */
.alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  border-radius: 10px;
  font-size: 0.85rem;
  margin-bottom: 16px;
}
.alert-error { background: #fff5f5; border: 1px solid #fecaca; color: #dc2626; }
.alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; }

/* Form */
.reset-form { display: flex; flex-direction: column; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-size: 0.85rem; font-weight: 600; color: var(--color-black); }
.input-wrapper {
  display: flex;
  align-items: center;
  border: 2px solid #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
  transition: border-color 0.2s;
}
.input-wrapper:focus-within { border-color: var(--color-mustard); }
.input-icon { padding: 0 12px; font-size: 16px; }
.form-input {
  flex: 1;
  border: none;
  outline: none;
  padding: 12px 8px;
  font-size: 0.9rem;
  background: transparent;
}
.eye-btn {
  background: none;
  border: none;
  padding: 0 12px;
  cursor: pointer;
  font-size: 16px;
}
.form-error { font-size: 0.78rem; color: #dc2626; }

/* Submit button */
.btn-submit {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  margin-top: 4px;
}
.btn-login {
  background: var(--color-mustard);
  color: var(--color-black);
}
.btn-login:hover:not(:disabled) { background: #d4a000; transform: translateY(-1px); }
.btn-login:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-text { display: block; }
.btn-loading { display: flex; align-items: center; justify-content: center; gap: 8px; }
.spinner {
  width: 16px; height: 16px;
  border: 2px solid rgba(0,0,0,0.3);
  border-top-color: var(--color-black);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Success / Invalid view */
.success-view {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 8px 0;
}
.success-icon { font-size: 52px; margin-bottom: 16px; }
.success-title { font-size: 1.4rem; font-weight: 700; color: var(--color-black); margin-bottom: 10px; }
.success-subtitle { font-size: 0.88rem; color: #555; line-height: 1.6; margin-bottom: 24px; }

/* Back link */
.back-link { text-align: center; margin-top: 20px; }
.footer-link { font-size: 0.82rem; color: #999; text-decoration: none; }
.footer-link:hover { color: var(--color-mustard); }

/* Slide-down transition */
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.3s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
