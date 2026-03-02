<template>
  <div class="verify-wrapper">
    <div class="verify-card">
      <div class="logo">
        <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="state-box">
        <div class="spinner"></div>
        <p>Verifying your email...</p>
      </div>

      <!-- Success -->
      <div v-else-if="verified" class="state-box">
        <div class="state-icon success-icon">✓</div>
        <h2>Email Verified!</h2>
        <p>Your email has been successfully verified. You can now enjoy all features of RealtyLinkPH.</p>
        <router-link to="/login" class="btn-primary">Go to Login</router-link>
      </div>

      <!-- Already Verified -->
      <div v-else-if="alreadyVerified" class="state-box">
        <div class="state-icon info-icon">ℹ</div>
        <h2>Already Verified</h2>
        <p>Your email has already been verified. You can log in to your account.</p>
        <router-link to="/login" class="btn-primary">Go to Login</router-link>
      </div>

      <!-- Error -->
      <div v-else class="state-box">
        <div class="state-icon error-icon">✕</div>
        <h2>Verification Failed</h2>
        <p>{{ errorMessage }}</p>
        <router-link to="/login" class="btn-secondary">Go to Login</router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'VerifyEmail',

  data() {
    return {
      loading: true,
      verified: false,
      alreadyVerified: false,
      errorMessage: 'The verification link is invalid or has expired.',
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
    };
  },

  async mounted() {
    const token = this.$route.query.token;
    if (!token) {
      this.loading = false;
      this.errorMessage = 'No verification token provided.';
      return;
    }

    try {
      const res = await fetch(`${this.apiUrl}/api/auth/verify-email`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: JSON.stringify({ token }),
      });
      const data = await res.json();

      if (res.ok && data.message?.includes('successfully')) {
        this.verified = true;
      } else if (data.message?.includes('already')) {
        this.alreadyVerified = true;
      } else {
        this.errorMessage = data.message || 'Verification failed. The link may have expired.';
      }
    } catch (e) {
      this.errorMessage = 'An error occurred. Please try again later.';
    } finally {
      this.loading = false;
    }
  },
};
</script>

<style scoped>
.verify-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f4f4f5;
  font-family: 'Inter', sans-serif;
  padding: 20px;
}

.verify-card {
  background: #fff;
  border-radius: 16px;
  padding: 40px;
  max-width: 440px;
  width: 100%;
  text-align: center;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
}

.logo {
  font-size: 24px;
  font-weight: 800;
  margin-bottom: 32px;
}
.logo-realty { color: #100c08; }
.logo-ph { color: #FFD700; }

.state-box { display: flex; flex-direction: column; align-items: center; gap: 12px; }
.state-box h2 { font-size: 1.3rem; font-weight: 700; color: #100c08; margin: 0; }
.state-box p { font-size: 0.9rem; color: #666; margin: 0 0 8px; line-height: 1.6; }

.state-icon {
  width: 64px; height: 64px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 28px; font-weight: 700; margin-bottom: 8px;
}
.success-icon { background: #dcfce7; color: #16a34a; }
.info-icon { background: #dbeafe; color: #2563eb; }
.error-icon { background: #fee2e2; color: #dc2626; }

.spinner {
  width: 40px; height: 40px; border: 4px solid #e5e7eb;
  border-top-color: #FFD700; border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.btn-primary {
  display: inline-block; padding: 12px 28px; background: #FFD700; color: #100c08;
  text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 0.9rem;
  margin-top: 8px; transition: background 0.2s;
}
.btn-primary:hover { background: #DAB600; }

.btn-secondary {
  display: inline-block; padding: 12px 28px; background: #f3f4f6; color: #374151;
  text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 0.9rem;
  margin-top: 8px; transition: background 0.2s;
}
.btn-secondary:hover { background: #e5e7eb; }
</style>
