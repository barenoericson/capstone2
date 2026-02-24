<template>
  <div class="dashboard-wrapper">
    <!-- Sidebar (buyer sidebar) -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-logo">
          <span class="logo-realty">RealtyLink</span><span class="logo-ph">PH</span>
        </h2>
      </div>
      <nav class="sidebar-nav">
        <router-link to="/dashboard" class="nav-item">
          <span class="nav-icon">📊</span>
          <span class="nav-label">Dashboard</span>
        </router-link>
        <div class="nav-section">
          <h3 class="section-title">Browse</h3>
          <router-link to="/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Discover</span>
          </router-link>
          <router-link to="/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">Saved</span>
          </router-link>
        </div>
        <div class="nav-section">
          <h3 class="section-title">Activity</h3>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">Inbox</span>
          </router-link>
          <router-link to="/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
          <router-link to="/documents" class="nav-item">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
        </div>
        <div class="nav-section">
          <h3 class="section-title">Settings</h3>
          <router-link to="/profile" class="nav-item">
            <span class="nav-icon">👤</span>
            <span class="nav-label">Profile</span>
          </router-link>
        </div>
      </nav>
      <div class="sidebar-footer">
        <div class="user-card">
          <div class="user-avatar-lg">{{ myName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name-card">{{ myName }}</p>
            <p class="user-role-card">Buyer</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Back button topbar -->
      <nav class="topbar">
        <div class="topbar-content">
          <button @click="$router.back()" class="btn-back">← Back</button>
          <div class="topbar-right">
            <router-link
              v-if="agent"
              :to="`/conversations/${agent.user_id}`"
              class="btn-message-agent"
            >
              💬 Message Agent
            </router-link>
          </div>
        </div>
      </nav>

      <div class="content-area">
        <!-- Loading -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading profile...</p>
        </div>

        <template v-else-if="agent">
          <!-- Agent Profile Card -->
          <div class="profile-card">
            <!-- Cover / Avatar -->
            <div class="profile-cover">
              <div class="agent-avatar-wrap">
                <div
                  class="agent-avatar"
                  :style="agent.profile_photo_url
                    ? `background-image:url(${agent.profile_photo_url});background-size:cover;background-position:center;`
                    : ''"
                >
                  <span v-if="!agent.profile_photo_url">{{ agent.name.charAt(0).toUpperCase() }}</span>
                </div>
                <span :class="['verification-badge', agent.verification_status]">
                  {{ agent.verification_status === 'verified' ? '✅ Verified' : agent.verification_status }}
                </span>
              </div>
            </div>

            <div class="profile-body">
              <div class="profile-head">
                <div>
                  <h1 class="agent-name">{{ agent.name }}</h1>
                  <p class="agent-company" v-if="agent.company_name">{{ agent.company_name }}</p>
                </div>

                <!-- Star Rating Display -->
                <div class="rating-display">
                  <div class="stars-row">
                    <span
                      v-for="star in 5"
                      :key="star"
                      class="star"
                      :class="star <= Math.round(agent.average_rating) ? 'filled' : ''"
                    >★</span>
                  </div>
                  <span class="rating-value">{{ agent.average_rating > 0 ? agent.average_rating.toFixed(1) : 'No rating' }}</span>
                  <span class="rating-count">({{ agent.total_reviews }} review{{ agent.total_reviews !== 1 ? 's' : '' }})</span>
                </div>
              </div>

              <!-- Info grid -->
              <div class="info-grid">
                <div class="info-item" v-if="agent.bio">
                  <span class="info-label">About</span>
                  <p class="info-value bio">{{ agent.bio }}</p>
                </div>
                <div class="info-item" v-if="agent.company_address">
                  <span class="info-label">📍 Office</span>
                  <p class="info-value">{{ agent.company_address }}</p>
                </div>
                <div class="info-item" v-if="agent.license_number">
                  <span class="info-label">🪪 License</span>
                  <p class="info-value">{{ agent.license_number }}</p>
                </div>
                <div class="info-item" v-if="agent.accreditation">
                  <span class="info-label">📜 Accreditation</span>
                  <p class="info-value">{{ agent.accreditation }}</p>
                </div>
                <div class="info-item">
                  <span class="info-label">📅 Member Since</span>
                  <p class="info-value">{{ formatDate(agent.created_at) }}</p>
                </div>
              </div>

              <!-- Rate this agent button -->
              <button class="btn-rate" @click="openRateModal">
                ⭐ Rate this Agent
              </button>
            </div>
          </div>

          <!-- Reviews Section -->
          <div class="reviews-section">
            <h2 class="section-heading">Reviews ({{ reviews.length }})</h2>

            <div v-if="reviews.length === 0" class="no-reviews">
              <p>No reviews yet. Be the first to rate this agent!</p>
            </div>

            <div v-else class="reviews-list">
              <div v-for="review in reviews" :key="review.id" class="review-card">
                <div class="review-header">
                  <div class="reviewer-avatar">
                    <img
                      v-if="review.buyer && review.buyer.profile_photo_url"
                      :src="review.buyer.profile_photo_url"
                      :alt="review.buyer.name"
                      class="reviewer-photo"
                    />
                    <span v-else>{{ review.buyer ? review.buyer.name.charAt(0).toUpperCase() : '?' }}</span>
                  </div>
                  <div class="reviewer-info">
                    <p class="reviewer-name">{{ review.buyer ? review.buyer.name : 'Anonymous' }}</p>
                    <div class="review-stars">
                      <span v-for="s in 5" :key="s" class="star-sm" :class="s <= review.rating ? 'filled' : ''">★</span>
                    </div>
                  </div>
                  <span class="review-date">{{ formatDate(review.created_at) }}</span>
                </div>
                <p v-if="review.review_title" class="review-title">{{ review.review_title }}</p>
                <p v-if="review.review_content" class="review-content">{{ review.review_content }}</p>
              </div>
            </div>
          </div>
        </template>
      </div>
    </main>

    <!-- Rate Modal -->
    <div v-if="showRateModal" class="modal-overlay" @click.self="showRateModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h2 class="modal-title">Rate {{ agent?.name }}</h2>
          <button class="modal-close" @click="showRateModal = false">✕</button>
        </div>

        <div class="modal-body">
          <!-- Star picker -->
          <div class="star-picker">
            <span
              v-for="star in 5"
              :key="star"
              class="star-pick"
              :class="{ filled: star <= hoverRating || (!hoverRating && star <= ratingForm.rating) }"
              @mouseenter="hoverRating = star"
              @mouseleave="hoverRating = 0"
              @click="ratingForm.rating = star"
            >★</span>
          </div>
          <p class="rating-label">{{ ratingLabels[ratingForm.rating] || 'Click to rate' }}</p>

          <div class="form-group">
            <label class="form-label">Review Title (optional)</label>
            <input
              v-model="ratingForm.review_title"
              type="text"
              class="form-input"
              placeholder="e.g. Great agent, very professional"
              maxlength="100"
            />
          </div>

          <div class="form-group">
            <label class="form-label">Your Review (optional)</label>
            <textarea
              v-model="ratingForm.review_content"
              class="form-textarea"
              placeholder="Share your experience..."
              rows="4"
              maxlength="1000"
            ></textarea>
          </div>

          <p v-if="rateError" class="form-error">{{ rateError }}</p>
        </div>

        <div class="modal-footer">
          <button class="btn-cancel" @click="showRateModal = false" :disabled="submittingRate">Cancel</button>
          <button class="btn-submit" @click="submitRating" :disabled="submittingRate || !ratingForm.rating">
            {{ submittingRate ? 'Submitting...' : '⭐ Submit Rating' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">{{ toast.message }}</div>
  </div>
</template>

<script>
export default {
  name: 'AgentPublicProfile',

  data() {
    return {
      apiUrl: localStorage.getItem('api_url') || '',
      token: '',
      myName: '',
      myId: null,
      loading: true,
      agent: null,
      reviews: [],

      // Rate modal
      showRateModal: false,
      hoverRating: 0,
      ratingForm: { rating: 0, review_title: '', review_content: '' },
      rateError: '',
      submittingRate: false,

      ratingLabels: {
        1: 'Poor',
        2: 'Fair',
        3: 'Good',
        4: 'Very Good',
        5: 'Excellent',
      },

      toast: { show: false, message: '', type: 'success' },
    };
  },

  mounted() {
    this.token = localStorage.getItem('auth_token') || '';
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.myName = user.name || '';
    this.myId = user.id;
    this.apiUrl = localStorage.getItem('api_url') || '';
    this.loadProfile();
  },

  methods: {
    async loadProfile() {
      this.loading = true;
      try {
        // userId from the route param (the agent's user_id)
        const userId = this.$route.params.userId;
        const res = await fetch(`${this.apiUrl}/api/users/${userId}/agent-profile`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        if (data.success) {
          this.agent = data.agent;
          this.reviews = data.reviews || [];
        }
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },

    openRateModal() {
      this.ratingForm = { rating: 0, review_title: '', review_content: '' };
      this.rateError = '';
      this.hoverRating = 0;
      this.showRateModal = true;
    },

    async submitRating() {
      if (!this.ratingForm.rating) {
        this.rateError = 'Please select a star rating.';
        return;
      }
      this.rateError = '';
      this.submittingRate = true;
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/agents/${this.agent.id}/reviews`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${this.token}`,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(this.ratingForm),
        });
        const data = await res.json();
        if (!res.ok) {
          this.rateError = data.message || 'Failed to submit rating.';
          return;
        }
        // Update local agent rating
        if (data.average_rating !== undefined) {
          this.agent.average_rating = data.average_rating;
          this.agent.total_reviews = data.total_reviews;
        }
        // Add or update review in list
        if (data.review) {
          const idx = this.reviews.findIndex(r => r.id === data.review.id);
          if (idx !== -1) {
            this.reviews.splice(idx, 1, data.review);
          } else {
            this.reviews.unshift(data.review);
          }
        }
        this.showRateModal = false;
        this.showToast('Rating submitted! Thank you.', 'success');
      } catch (e) {
        this.rateError = 'Network error. Please try again.';
      } finally {
        this.submittingRate = false;
      }
    },

    formatDate(iso) {
      if (!iso) return '—';
      return new Date(iso).toLocaleDateString('en-PH', {
        year: 'numeric', month: 'long', day: 'numeric',
      });
    },

    showToast(message, type = 'success') {
      this.toast = { show: true, message, type };
      setTimeout(() => { this.toast.show = false; }, 3500);
    },
  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@600;700;800&display=swap');

:root {
  --gold: #e6ae0d;
  --gold-dark: #d4a000;
  --black: #100c08;
  --smoke: #f5f5f5;
  --gray: #e0e0e0;
  --white: #ffffff;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

/* =========================================================
   Layout
   ========================================================= */
.dashboard-wrapper {
  display: flex; min-height: 100vh;
  background: var(--smoke);
  font-family: 'Inter', sans-serif;
}

/* =========================================================
   Sidebar  (white + gold — same as MessageConversation)
   ========================================================= */
.sidebar {
  width: 260px; min-height: 100vh;
  background: var(--white);
  border-right: 1px solid var(--gray);
  display: flex; flex-direction: column;
  position: fixed; left: 0; top: 0; height: 100vh;
  overflow-y: auto;
  box-shadow: 2px 0 8px rgba(0,0,0,.07);
  z-index: 100;
}
.sidebar-header { padding: 24px 16px; border-bottom: 1px solid var(--gray); }
.sidebar-logo { font-size: 22px; font-weight: 800; font-family: 'Poppins', sans-serif; display: flex; margin: 0; }
.logo-realty { color: var(--black); }
.logo-ph { color: var(--gold); margin-left: 2px; }

.sidebar-nav { flex: 1; padding: 16px 0; }
.nav-section { margin-top: 16px; }
.section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #aaa; margin: 0 16px 6px; display: block; }

.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 11px 16px; margin: 0 8px; border-radius: 8px;
  color: var(--black); text-decoration: none; font-size: 14px; font-weight: 500;
  transition: all .2s;
}
.nav-item:hover { background: var(--smoke); color: var(--gold); }
.nav-item.active {
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-weight: 700;
}
.nav-icon { font-size: 17px; min-width: 22px; text-align: center; }

.sidebar-footer { padding: 14px; border-top: 1px solid var(--gray); }
.user-card { background: var(--smoke); border-radius: 8px; padding: 10px; display: flex; align-items: center; gap: 10px; }
.user-avatar-lg {
  width: 38px; height: 38px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 15px; flex-shrink: 0;
}
.user-name-card { font-size: 13px; font-weight: 700; color: var(--black); margin: 0; }
.user-role-card { font-size: 11px; color: #999; margin: 2px 0 0; }

/* =========================================================
   Main Content
   ========================================================= */
.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

.topbar {
  background: var(--white);
  border-bottom: 1px solid var(--gray);
  padding: 0 28px; height: 64px;
  position: sticky; top: 0; z-index: 50;
  box-shadow: 0 2px 6px rgba(0,0,0,.06);
}
.topbar-content { display: flex; align-items: center; justify-content: space-between; height: 100%; }

.btn-back {
  background: none; border: none;
  color: var(--black); font-size: 15px; font-weight: 600;
  cursor: pointer; padding: 8px 12px; border-radius: 8px;
}
.btn-back:hover { background: var(--smoke); color: var(--gold); }

.btn-message-agent {
  padding: 9px 20px; border-radius: 8px;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-size: 14px; font-weight: 700;
  text-decoration: none; transition: transform .2s, box-shadow .2s;
}
.btn-message-agent:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(230,174,13,.4); }

.content-area { padding: 28px; max-width: 900px; }

/* =========================================================
   Loading
   ========================================================= */
.loading-state { text-align: center; padding: 80px; color: #999; }
.spinner { width: 40px; height: 40px; border: 4px solid var(--gray); border-top-color: var(--gold); border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* =========================================================
   Profile Card
   ========================================================= */
.profile-card {
  background: var(--white); border-radius: 16px; overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,.08); margin-bottom: 24px;
}
.profile-cover {
  height: 140px;
  background: linear-gradient(135deg, var(--black) 0%, #2c2520 100%);
  display: flex; align-items: flex-end; padding: 0 32px;
}
.agent-avatar-wrap { position: relative; margin-bottom: -40px; }
.agent-avatar {
  width: 100px; height: 100px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  border: 4px solid var(--white);
  display: flex; align-items: center; justify-content: center;
  font-size: 40px; font-weight: 700; color: var(--black); overflow: hidden;
}

.verification-badge {
  position: absolute; bottom: 4px; right: -4px;
  background: var(--black); color: #22c55e;
  border: 2px solid var(--white);
  border-radius: 20px; padding: 2px 8px; font-size: 10px; font-weight: 700;
}
.verification-badge.verified { color: #22c55e; }
.verification-badge.pending { color: var(--gold); }

.profile-body { padding: 52px 32px 32px; }
.profile-head { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }

.agent-name { font-size: 26px; font-weight: 800; color: var(--black); margin: 0; font-family: 'Poppins', sans-serif; }
.agent-company { color: #777; font-size: 15px; margin: 4px 0 0; }

/* Stars — gold is universally expected for ratings */
.rating-display { display: flex; align-items: center; gap: 8px; }
.stars-row { display: flex; gap: 2px; }
.star { font-size: 22px; color: var(--gray); }
.star.filled { color: var(--gold); }
.rating-value { font-size: 18px; font-weight: 700; color: var(--black); }
.rating-count { color: #999; font-size: 13px; }

.info-grid { display: grid; gap: 16px; margin-bottom: 28px; }
.info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #aaa; display: block; margin-bottom: 4px; }
.info-value { font-size: 14px; color: #444; margin: 0; }
.info-value.bio { line-height: 1.6; color: #555; }

.btn-rate {
  padding: 12px 28px; border-radius: 10px;
  border: 2px solid var(--gold);
  background: var(--white); color: var(--black);
  font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s;
}
.btn-rate:hover { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); box-shadow: 0 4px 14px rgba(230,174,13,.3); }

/* =========================================================
   Reviews Section
   ========================================================= */
.reviews-section {
  background: var(--white); border-radius: 16px; padding: 28px;
  box-shadow: 0 2px 12px rgba(0,0,0,.08);
}
.section-heading { font-size: 20px; font-weight: 700; color: var(--black); margin: 0 0 20px; font-family: 'Poppins', sans-serif; }
.no-reviews { text-align: center; padding: 40px; color: #999; }
.reviews-list { display: flex; flex-direction: column; gap: 16px; }

.review-card {
  padding: 16px; border: 1px solid #f0f0f0; border-radius: 10px;
  transition: box-shadow 0.2s;
}
.review-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,.06); }
.review-header { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }

.reviewer-avatar {
  width: 40px; height: 40px; border-radius: 50%;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  display: flex; align-items: center; justify-content: center;
  color: var(--black); font-weight: 700; font-size: 16px;
  overflow: hidden; flex-shrink: 0;
}
.reviewer-photo { width: 100%; height: 100%; object-fit: cover; }
.reviewer-info { flex: 1; }
.reviewer-name { font-size: 14px; font-weight: 700; color: var(--black); margin: 0 0 2px; }
.review-stars { display: flex; gap: 2px; }
.star-sm { font-size: 14px; color: var(--gray); }
.star-sm.filled { color: var(--gold); }
.review-date { font-size: 12px; color: #aaa; flex-shrink: 0; }
.review-title { font-size: 14px; font-weight: 700; color: var(--black); margin: 0 0 6px; }
.review-content { font-size: 14px; color: #666; line-height: 1.6; margin: 0; }

/* =========================================================
   Rating Modal
   ========================================================= */
.modal-overlay { position: fixed; inset: 0; background: rgba(16,12,8,0.6); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
.modal-box { background: var(--white); border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,.25); overflow: hidden; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 24px 28px 0; }
.modal-title { font-size: 20px; font-weight: 800; color: var(--black); margin: 0; font-family: 'Poppins', sans-serif; }
.modal-close { background: none; border: none; font-size: 20px; color: #aaa; cursor: pointer; }
.modal-close:hover { color: var(--black); }
.modal-body { padding: 20px 28px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 28px 24px; border-top: 1px solid var(--gray); }

.star-picker { display: flex; gap: 8px; margin-bottom: 4px; }
.star-pick { font-size: 36px; color: var(--gray); cursor: pointer; transition: transform 0.1s, color 0.1s; }
.star-pick:hover { transform: scale(1.15); }
.star-pick.filled { color: var(--gold); }
.rating-label { font-size: 13px; color: #999; margin: 0 0 20px; height: 18px; font-weight: 600; }

.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: var(--black); margin-bottom: 6px; }
.form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--gray); border-radius: 8px; font-size: 14px; outline: none; font-family: 'Inter', sans-serif; }
.form-input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(230,174,13,.15); }
.form-textarea { width: 100%; padding: 10px 14px; border: 1px solid var(--gray); border-radius: 8px; font-size: 14px; resize: vertical; font-family: 'Inter', sans-serif; outline: none; }
.form-textarea:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(230,174,13,.15); }
.form-error { color: #e74c3c; font-size: 13px; margin-top: 8px; }

.btn-cancel {
  padding: 10px 20px; border-radius: 8px;
  border: 1px solid var(--gray); background: var(--white);
  color: #666; font-size: 14px; font-weight: 600; cursor: pointer;
}
.btn-cancel:hover { background: var(--smoke); }

.btn-submit {
  padding: 10px 24px; border-radius: 8px; border: none;
  background: linear-gradient(135deg, var(--gold), var(--gold-dark));
  color: var(--black); font-size: 14px; font-weight: 700;
  cursor: pointer; box-shadow: 0 4px 14px rgba(230,174,13,.35);
  transition: transform .2s, box-shadow .2s;
}
.btn-submit:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(230,174,13,.45); }
.btn-submit:disabled { opacity: 0.55; cursor: not-allowed; }

/* =========================================================
   Toast
   ========================================================= */
.toast {
  position: fixed; bottom: 28px; right: 28px;
  padding: 14px 22px; border-radius: 10px; color: var(--white);
  font-size: 14px; font-weight: 600;
  box-shadow: 0 6px 24px rgba(0,0,0,.2);
  z-index: 9999; animation: slideUp 0.3s ease;
}
.toast-success { background: #22c55e; }
.toast-error { background: #ef4444; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
