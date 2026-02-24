<template>
  <div class="dashboard-wrapper">
    <!-- Sidebar -->
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
          <router-link to="/documents" class="nav-item active">
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
          <div class="user-avatar-lg">{{ userName.charAt(0).toUpperCase() }}</div>
          <div class="user-info">
            <p class="user-name-card">{{ userName }}</p>
            <p class="user-role-card">Buyer</p>
          </div>
          <button @click="showUserMenu = !showUserMenu" class="btn-options">⋮</button>
          <div v-if="showUserMenu" class="user-dropdown">
            <router-link to="/profile" class="dropdown-item">👤 My Profile</router-link>
            <a href="#" @click.prevent="logout" class="dropdown-item logout">🚪 Logout</a>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <h1 class="page-title">My Documents</h1>
          </div>
          <div class="topbar-right">
            <div class="filter-tabs">
              <button
                v-for="tab in tabs"
                :key="tab.value"
                class="filter-tab"
                :class="{ active: activeTab === tab.value }"
                @click="activeTab = tab.value"
              >
                {{ tab.label }}
                <span v-if="tab.value === 'pending' && pendingCount > 0" class="tab-badge">{{ pendingCount }}</span>
              </button>
            </div>
          </div>
        </div>
      </nav>

      <div class="content-area">
        <!-- Loading -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Loading documents...</p>
        </div>

        <!-- Empty state -->
        <div v-else-if="filteredDocuments.length === 0" class="empty-state">
          <div class="empty-icon">📄</div>
          <h3>No documents found</h3>
          <p v-if="activeTab === 'pending'">No pending documents to sign.</p>
          <p v-else-if="activeTab === 'signed'">No signed documents yet.</p>
          <p v-else>Your agent hasn't sent you any documents yet.</p>
        </div>

        <!-- Documents list -->
        <div v-else class="documents-list">
          <div v-for="doc in filteredDocuments" :key="doc.id" class="document-card">
            <div class="doc-icon-wrap">
              <span class="doc-icon">📑</span>
            </div>

            <div class="doc-info">
              <div class="doc-header-row">
                <h3 class="doc-name">{{ doc.document_name }}</h3>
                <span class="doc-badge" :class="'badge-' + doc.status">
                  {{ doc.status === 'signed' ? '✅ Signed' : '🕐 Pending Signature' }}
                </span>
              </div>
              <div class="doc-meta">
                <span class="meta-item">
                  <span class="meta-icon">📁</span>
                  {{ formatDocType(doc.document_type) }}
                </span>
                <span class="meta-item">
                  <span class="meta-icon">👤</span>
                  From: {{ doc.agent_name }}
                </span>
                <span class="meta-item" v-if="doc.property_title">
                  <span class="meta-icon">🏠</span>
                  {{ doc.property_title }}
                </span>
                <span class="meta-item">
                  <span class="meta-icon">📅</span>
                  {{ formatDate(doc.created_at) }}
                </span>
                <span class="meta-item" v-if="doc.signed_at">
                  <span class="meta-icon">✍️</span>
                  Signed: {{ formatDate(doc.signed_at) }}
                </span>
              </div>
            </div>

            <div class="doc-actions">
              <a
                :href="doc.document_url"
                target="_blank"
                class="btn-action btn-view"
                title="View document"
              >
                👁️ View
              </a>
              <button
                v-if="doc.has_file"
                @click="downloadDoc(doc)"
                class="btn-action btn-download"
                title="Download PDF"
              >
                ⬇️ Download
              </button>
              <button
                v-if="doc.status === 'pending'"
                class="btn-action btn-sign"
                @click="openSignModal(doc)"
              >
                ✍️ Sign
              </button>
              <div v-if="doc.status === 'signed' && doc.signature_url" class="signed-proof">
                <img
                  :src="doc.signature_url"
                  alt="Signature"
                  class="signature-thumb"
                  title="Your signature"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ===================== SIGN MODAL ===================== -->
    <div v-if="showSignModal" class="modal-overlay" @click.self="closeSignModal">
      <div class="modal-box sign-modal">
        <div class="modal-header">
          <div class="modal-title-group">
            <h2 class="modal-title">Sign Document</h2>
            <p class="modal-subtitle">{{ signingDoc?.document_name }}</p>
          </div>
          <button class="modal-close" @click="closeSignModal">✕</button>
        </div>

        <!-- ── DRAW / UPLOAD STEP ── -->
        <template v-if="signMode === 'draw'">
          <div class="sign-tabs">
            <button
              class="sign-tab"
              :class="{ active: signTab === 'draw' }"
              @click="switchSignTab('draw')"
            >
              ✏️ Draw Signature
            </button>
            <button
              class="sign-tab"
              :class="{ active: signTab === 'upload' }"
              @click="switchSignTab('upload')"
            >
              📷 Upload Photo
            </button>
          </div>

          <!-- Draw Tab -->
          <div v-if="signTab === 'draw'" class="sign-tab-content">
            <p class="sign-hint">Draw your signature below using your mouse or finger:</p>
            <div class="canvas-wrapper">
              <canvas
                ref="signatureCanvas"
                class="signature-canvas"
                width="560"
                height="200"
                @mousedown="startDraw"
                @mousemove="draw"
                @mouseup="stopDraw"
                @mouseleave="stopDraw"
                @touchstart.prevent="startDrawTouch"
                @touchmove.prevent="drawTouch"
                @touchend.prevent="stopDraw"
              ></canvas>
            </div>
            <div class="canvas-controls">
              <button class="btn-clear" @click="clearCanvas">🗑️ Clear</button>
              <span class="draw-tip">Draw within the box above</span>
            </div>
          </div>

          <!-- Upload Tab -->
          <div v-if="signTab === 'upload'" class="sign-tab-content">
            <p class="sign-hint">Take a photo of your handwritten signature and upload it. The background will be removed automatically.</p>
            <div class="upload-area" @click="triggerPhotoInput" @dragover.prevent @drop.prevent="handleDrop">
              <input ref="photoInput" type="file" accept="image/*" class="hidden-input" @change="handlePhotoUpload" />
              <div v-if="!photoPreview" class="upload-placeholder">
                <span class="upload-icon">📷</span>
                <p>Click to upload or drag &amp; drop</p>
                <p class="upload-sub">JPG, PNG, WEBP — background will be removed automatically</p>
              </div>
              <div v-else class="photo-preview-wrap">
                <canvas ref="processedCanvas" class="processed-canvas" width="560" height="200"></canvas>
                <p class="preview-note">✅ Background removed</p>
              </div>
            </div>
            <div v-if="photoPreview" class="canvas-controls">
              <button class="btn-clear" @click="clearPhoto">🗑️ Remove Photo</button>
            </div>
          </div>

          <p v-if="signError" class="sign-error">{{ signError }}</p>

          <div class="modal-footer">
            <button class="btn-cancel" @click="closeSignModal" :disabled="signing">Cancel</button>
            <button class="btn-submit" @click="previewSignature">Preview Signature →</button>
          </div>
        </template>

        <!-- ── PREVIEW STEP ── -->
        <template v-else-if="signMode === 'preview'">
          <div class="preview-section">
            <p class="preview-label">Your signature will appear like this on the document:</p>
            <div class="sig-area-box">
              <img :src="signaturePreview" class="sig-area-img" alt="Your signature preview" />
              <div class="sig-area-line">
                <span>Buyer Signature</span>
                <span>{{ new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
              </div>
            </div>
          </div>

          <p v-if="signError" class="sign-error">{{ signError }}</p>

          <div class="modal-footer">
            <button class="btn-cancel" @click="signMode = 'draw'" :disabled="signing">↩ Re-draw</button>
            <button class="btn-submit" @click="submitSignature" :disabled="signing">
              {{ signing ? 'Submitting...' : '✅ Confirm & Sign Document' }}
            </button>
          </div>
        </template>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">
      {{ toast.message }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'BuyerDocuments',

  data() {
    return {
      userName: '',
      showUserMenu: false,
      apiUrl: '',
      token: '',
      loading: true,
      documents: [],
      activeTab: 'all',
      tabs: [
        { label: 'All', value: 'all' },
        { label: 'Pending', value: 'pending' },
        { label: 'Signed', value: 'signed' },
      ],

      // Sign modal
      showSignModal: false,
      signingDoc: null,
      signMode: 'draw',        // 'draw' | 'preview'
      signTab: 'draw',         // 'draw' | 'upload'
      signing: false,
      signError: '',
      signaturePreview: null,  // data URL shown in preview step

      // Draw state
      isDrawing: false,
      lastX: 0,
      lastY: 0,
      hasDrawn: false,

      // Upload state
      photoPreview: false,
      photoDataUrl: null,

      // Toast
      toast: { show: false, message: '', type: 'success' },
    };
  },

  computed: {
    filteredDocuments() {
      if (this.activeTab === 'all') return this.documents;
      return this.documents.filter(d => d.status === this.activeTab);
    },
    pendingCount() {
      return this.documents.filter(d => d.status === 'pending').length;
    },
  },

  mounted() {
    this.apiUrl = localStorage.getItem('api_url') || '/api';
    this.token = localStorage.getItem('auth_token') || '';
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Buyer';
    this.loadDocuments();
  },

  methods: {
    // ========================
    // Load documents
    // ========================
    async loadDocuments() {
      this.loading = true;
      try {
        const res = await fetch(`${this.apiUrl}/buyer/documents`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        this.documents = data.documents || [];
      } catch (e) {
        this.showToast('Failed to load documents.', 'error');
      } finally {
        this.loading = false;
      }
    },

    // ========================
    // Download
    // ========================
    async downloadDoc(doc) {
      try {
        const res = await fetch(`${this.apiUrl}/documents/${doc.id}/download`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        if (!res.ok) { this.showToast('Download failed.', 'error'); return; }
        const blob = await res.blob();
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href = url; a.download = doc.document_name + '.pdf';
        a.click();
        URL.revokeObjectURL(url);
      } catch (e) {
        this.showToast('Download error.', 'error');
      }
    },

    // ========================
    // Format helpers
    // ========================
    formatDocType(type) {
      const map = { contract: 'Contract', agreement: 'Agreement', disclosure: 'Disclosure', other: 'Other' };
      return map[type] || type;
    },

    formatDate(iso) {
      if (!iso) return '—';
      return new Date(iso).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    },

    // ========================
    // Sign modal open/close
    // ========================
    openSignModal(doc) {
      this.signingDoc        = doc;
      this.signMode          = 'draw';
      this.signTab           = 'draw';
      this.signError         = '';
      this.hasDrawn          = false;
      this.photoPreview      = false;
      this.photoDataUrl      = null;
      this.signaturePreview  = null;
      this.showSignModal     = true;
      this.$nextTick(() => this.initCanvas());
    },

    closeSignModal() {
      if (this.signing) return;
      this.showSignModal = false;
      this.signingDoc    = null;
    },

    switchSignTab(tab) {
      this.signTab   = tab;
      this.signError = '';
      if (tab === 'draw') this.$nextTick(() => this.initCanvas());
    },

    // ========================
    // Canvas drawing
    // ========================
    initCanvas() {
      const canvas = this.$refs.signatureCanvas;
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      ctx.fillStyle = '#fff';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.strokeStyle = '#1a1a2e';
      ctx.lineWidth = 2.5;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      this.hasDrawn = false;
    },

    getCanvasPos(e, canvas) {
      const rect = canvas.getBoundingClientRect();
      return {
        x: (e.clientX - rect.left) * (canvas.width / rect.width),
        y: (e.clientY - rect.top)  * (canvas.height / rect.height),
      };
    },

    startDraw(e) {
      const pos = this.getCanvasPos(e, this.$refs.signatureCanvas);
      this.isDrawing = true; this.lastX = pos.x; this.lastY = pos.y;
    },

    draw(e) {
      if (!this.isDrawing) return;
      const canvas = this.$refs.signatureCanvas;
      const ctx = canvas.getContext('2d');
      const pos = this.getCanvasPos(e, canvas);
      ctx.beginPath(); ctx.moveTo(this.lastX, this.lastY); ctx.lineTo(pos.x, pos.y); ctx.stroke();
      this.lastX = pos.x; this.lastY = pos.y; this.hasDrawn = true;
    },

    stopDraw() { this.isDrawing = false; },

    startDrawTouch(e) {
      const touch = e.touches[0];
      const canvas = this.$refs.signatureCanvas;
      const rect = canvas.getBoundingClientRect();
      this.isDrawing = true;
      this.lastX = (touch.clientX - rect.left) * (canvas.width / rect.width);
      this.lastY = (touch.clientY - rect.top)  * (canvas.height / rect.height);
    },

    drawTouch(e) {
      if (!this.isDrawing) return;
      const touch = e.touches[0];
      const canvas = this.$refs.signatureCanvas;
      const ctx = canvas.getContext('2d');
      const rect = canvas.getBoundingClientRect();
      const x = (touch.clientX - rect.left) * (canvas.width / rect.width);
      const y = (touch.clientY - rect.top)  * (canvas.height / rect.height);
      ctx.beginPath(); ctx.moveTo(this.lastX, this.lastY); ctx.lineTo(x, y); ctx.stroke();
      this.lastX = x; this.lastY = y; this.hasDrawn = true;
    },

    clearCanvas() {
      const canvas = this.$refs.signatureCanvas;
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height);
      this.hasDrawn = false;
    },

    // ========================
    // Photo upload + BG removal
    // ========================
    triggerPhotoInput() { this.$refs.photoInput?.click(); },

    handleDrop(e) {
      const file = e.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) this.processPhoto(file);
    },

    handlePhotoUpload(e) {
      const file = e.target.files[0];
      if (file) this.processPhoto(file);
    },

    processPhoto(file) {
      const reader = new FileReader();
      reader.onload = (ev) => {
        const img = new Image();
        img.onload = () => {
          this.$nextTick(() => {
            const canvas = this.$refs.processedCanvas;
            if (!canvas) return;
            const ctx  = canvas.getContext('2d');
            const cw   = canvas.width, ch = canvas.height;
            const scale = Math.min(cw / img.width, ch / img.height);
            const dw = img.width * scale, dh = img.height * scale;
            ctx.clearRect(0, 0, cw, ch);
            ctx.drawImage(img, (cw - dw) / 2, (ch - dh) / 2, dw, dh);
            // Remove near-white background
            const id = ctx.getImageData(0, 0, cw, ch);
            const d  = id.data;
            for (let i = 0; i < d.length; i += 4) {
              const brightness = (d[i] + d[i+1] + d[i+2]) / 3;
              const max = Math.max(d[i], d[i+1], d[i+2]);
              const saturation = max === 0 ? 0 : (max - Math.min(d[i], d[i+1], d[i+2])) / max;
              if (brightness > 200 && saturation < 0.25) d[i+3] = 0;
            }
            ctx.putImageData(id, 0, 0);
            this.photoPreview = true;
            this.photoDataUrl = canvas.toDataURL('image/png');
          });
        };
        img.src = ev.target.result;
      };
      reader.readAsDataURL(file);
    },

    clearPhoto() {
      this.photoPreview = false;
      this.photoDataUrl = null;
      if (this.$refs.photoInput) this.$refs.photoInput.value = '';
      if (this.$refs.processedCanvas) {
        this.$refs.processedCanvas.getContext('2d').clearRect(0, 0, 560, 200);
      }
    },

    // ========================
    // Two-step: preview then submit
    // ========================
    previewSignature() {
      this.signError = '';
      let preview = null;
      if (this.signTab === 'draw') {
        if (!this.hasDrawn) { this.signError = 'Please draw your signature first.'; return; }
        preview = this.$refs.signatureCanvas.toDataURL('image/png');
      } else {
        if (!this.photoDataUrl) { this.signError = 'Please upload a photo of your signature first.'; return; }
        preview = this.photoDataUrl;
      }
      this.signaturePreview = preview;
      this.signMode = 'preview';
    },

    async submitSignature() {
      this.signError = '';
      this.signing   = true;
      try {
        const res = await fetch(`${this.apiUrl}/buyer/documents/${this.signingDoc.id}/sign`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json' },
          body: JSON.stringify({
            signature_data: this.signaturePreview,
            signature_type: this.signTab,
          }),
        });
        const data = await res.json();
        if (!res.ok) { this.signError = data.message || 'Failed to submit signature.'; return; }

        const idx = this.documents.findIndex(d => d.id === this.signingDoc.id);
        if (idx !== -1 && data.document) this.documents[idx] = data.document;

        this.closeSignModal();
        this.showToast('Document signed successfully!', 'success');
      } catch (e) {
        this.signError = 'Network error. Please try again.';
      } finally {
        this.signing = false;
      }
    },

    // ========================
    // Toast / Auth
    // ========================
    showToast(message, type = 'success') {
      this.toast = { show: true, message, type };
      setTimeout(() => { this.toast.show = false; }, 3500);
    },

    logout() {
      fetch(`${this.apiUrl}/logout`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${this.token}` },
      }).finally(() => {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/login');
      });
    },
  },
};
</script>

<style scoped>
/* =========================================================
   Layout
   ========================================================= */
.dashboard-wrapper { display: flex; min-height: 100vh; background: #f0f2f5; font-family: 'Inter', sans-serif; }

/* =========================================================
   Sidebar
   ========================================================= */
.sidebar { width: 260px; min-height: 100vh; background: linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); display: flex; flex-direction: column; position: fixed; left: 0; top: 0; bottom: 0; z-index: 100; box-shadow: 4px 0 20px rgba(0,0,0,0.3); }
.sidebar-header { padding: 24px 20px 16px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.sidebar-logo { font-size: 22px; font-weight: 800; margin: 0; }
.logo-realty { color: #fff; } .logo-ph { color: #e94560; }
.sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
.nav-section { margin-top: 20px; }
.section-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.4); margin: 0 8px 8px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; margin-bottom: 4px; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s; }
.nav-item:hover { background: rgba(255,255,255,0.1); color: #fff; }
.nav-item.active { background: linear-gradient(135deg, #e94560, #c23152); color: #fff; box-shadow: 0 4px 15px rgba(233,69,96,0.4); }
.nav-icon { font-size: 18px; }
.sidebar-footer { padding: 16px 12px; border-top: 1px solid rgba(255,255,255,0.1); }
.user-card { position: relative; display: flex; align-items: center; gap: 10px; padding: 10px; background: rgba(255,255,255,0.08); border-radius: 12px; }
.user-avatar-lg { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #e94560, #c23152); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 16px; flex-shrink: 0; }
.user-info { flex: 1; min-width: 0; }
.user-name-card { font-size: 13px; font-weight: 600; color: #fff; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-role-card { font-size: 11px; color: rgba(255,255,255,0.5); margin: 0; }
.btn-options { background: none; border: none; color: rgba(255,255,255,0.6); font-size: 20px; cursor: pointer; padding: 2px 6px; border-radius: 4px; }
.btn-options:hover { background: rgba(255,255,255,0.1); color: #fff; }
.user-dropdown { position: absolute; bottom: 58px; left: 0; right: 0; background: #1e2a4a; border-radius: 10px; overflow: hidden; box-shadow: 0 -4px 20px rgba(0,0,0,0.3); z-index: 10; }
.dropdown-item { display: block; padding: 10px 14px; color: rgba(255,255,255,0.85); text-decoration: none; font-size: 13px; transition: background 0.2s; }
.dropdown-item:hover { background: rgba(255,255,255,0.1); }
.dropdown-item.logout { color: #e94560; }

/* =========================================================
   Main content
   ========================================================= */
.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
.topbar { background: #fff; border-bottom: 1px solid #e8ecf0; padding: 0 28px; height: 64px; position: sticky; top: 0; z-index: 50; }
.topbar-content { display: flex; align-items: center; justify-content: space-between; height: 100%; }
.page-title { font-size: 20px; font-weight: 700; color: #1a1a2e; margin: 0; }
.filter-tabs { display: flex; gap: 6px; }
.filter-tab { padding: 6px 14px; border-radius: 20px; border: 1px solid #e0e0e0; background: #fff; color: #666; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
.filter-tab:hover { border-color: #0f3460; color: #0f3460; }
.filter-tab.active { background: #0f3460; border-color: #0f3460; color: #fff; }
.tab-badge { background: #e94560; color: #fff; border-radius: 12px; padding: 1px 7px; font-size: 11px; font-weight: 700; }
.content-area { padding: 28px; flex: 1; }

/* =========================================================
   Loading / Empty
   ========================================================= */
.loading-state { text-align: center; padding: 80px 20px; color: #666; }
.spinner { width: 40px; height: 40px; border: 4px solid #e0e0e0; border-top-color: #0f3460; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 16px; }
@keyframes spin { to { transform: rotate(360deg); } }
.empty-state { text-align: center; padding: 80px 20px; }
.empty-icon { font-size: 56px; margin-bottom: 16px; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #1a1a2e; margin: 0 0 8px; }
.empty-state p { color: #666; margin: 0; }

/* =========================================================
   Document cards
   ========================================================= */
.documents-list { display: flex; flex-direction: column; gap: 16px; }
.document-card { background: #fff; border-radius: 16px; padding: 20px 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #f0f0f0; transition: box-shadow 0.2s; }
.document-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
.doc-icon-wrap { width: 52px; height: 52px; border-radius: 12px; background: #fff3f5; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.doc-icon { font-size: 26px; }
.doc-info { flex: 1; min-width: 0; }
.doc-header-row { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; flex-wrap: wrap; }
.doc-name { font-size: 16px; font-weight: 700; color: #1a1a2e; margin: 0; }
.doc-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.badge-pending { background: #fff3e0; color: #e65100; }
.badge-signed { background: #e8f5e9; color: #2e7d32; }
.doc-meta { display: flex; flex-wrap: wrap; gap: 12px; }
.meta-item { display: flex; align-items: center; gap: 4px; font-size: 13px; color: #666; }
.meta-icon { font-size: 14px; }

.doc-actions { display: flex; flex-direction: column; gap: 8px; align-items: flex-end; flex-shrink: 0; }
.btn-action { padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-view     { background: #e8eaf6; color: #3949ab; }
.btn-view:hover { background: #c5cae9; }
.btn-download { background: #eff6ff; color: #1d4ed8; }
.btn-download:hover { background: #1d4ed8; color: #fff; }
.btn-sign     { background: linear-gradient(135deg, #e94560, #c23152); color: #fff; box-shadow: 0 3px 12px rgba(233,69,96,0.3); }
.btn-sign:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(233,69,96,0.4); }

.signed-proof { display: flex; align-items: center; }
.signature-thumb { max-width: 100px; max-height: 40px; object-fit: contain; border: 1px solid #e0e0e0; border-radius: 6px; background: #fafafa; }

/* =========================================================
   Sign Modal
   ========================================================= */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
.modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 620px; box-shadow: 0 20px 60px rgba(0,0,0,0.25); overflow: hidden; }
.modal-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 24px 28px 0; }
.modal-title { font-size: 20px; font-weight: 800; color: #1a1a2e; margin: 0 0 4px; }
.modal-subtitle { font-size: 13px; color: #666; margin: 0; }
.modal-close { background: none; border: none; font-size: 20px; color: #999; cursor: pointer; padding: 4px 8px; border-radius: 6px; }
.modal-close:hover { background: #f5f5f5; color: #333; }

/* Tabs */
.sign-tabs { display: flex; padding: 20px 28px 0; border-bottom: 2px solid #f0f0f0; }
.sign-tab { flex: 1; padding: 10px; background: none; border: none; border-bottom: 3px solid transparent; font-size: 14px; font-weight: 600; color: #999; cursor: pointer; transition: all 0.2s; margin-bottom: -2px; }
.sign-tab:hover { color: #0f3460; }
.sign-tab.active { color: #0f3460; border-bottom-color: #0f3460; }

.sign-tab-content { padding: 20px 28px; }
.sign-hint { font-size: 13px; color: #666; margin: 0 0 14px; }

/* Canvas */
.canvas-wrapper { border: 2px dashed #c5cae9; border-radius: 12px; overflow: hidden; background: #fff; }
.signature-canvas { display: block; width: 100%; cursor: crosshair; touch-action: none; }
.canvas-controls { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; }
.btn-clear { background: #f5f5f5; border: 1px solid #e0e0e0; border-radius: 8px; padding: 6px 14px; font-size: 13px; color: #666; cursor: pointer; }
.btn-clear:hover { background: #ffe8e8; border-color: #e94560; color: #e94560; }
.draw-tip { font-size: 12px; color: #aaa; }

/* Upload area */
.upload-area { border: 2px dashed #c5cae9; border-radius: 12px; min-height: 200px; display: flex; align-items: center; justify-content: center; cursor: pointer; overflow: hidden; background: #fafbff; transition: border-color 0.2s; }
.upload-area:hover { border-color: #0f3460; }
.hidden-input { display: none; }
.upload-placeholder { text-align: center; color: #999; }
.upload-icon { font-size: 40px; display: block; margin-bottom: 10px; }
.upload-placeholder p { margin: 4px 0; font-size: 14px; }
.upload-sub { font-size: 12px !important; color: #bbb; }
.photo-preview-wrap { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 4px; }
.processed-canvas { display: block; width: 100%; }
.preview-note { text-align: center; font-size: 12px; color: #27ae60; margin: 0; padding: 6px 12px; }

/* Sign error */
.sign-error { color: #e94560; font-size: 13px; margin: 8px 28px 0; }

/* Preview step */
.preview-section { padding: 20px 28px; }
.preview-label { font-size: 14px; color: #555; margin: 0 0 14px; font-weight: 500; }
.sig-area-box { border: 2px dashed #c5cae9; border-radius: 12px; background: #fafbff; padding: 20px 24px 0; min-height: 130px; display: flex; flex-direction: column; justify-content: flex-end; }
.sig-area-img { max-height: 90px; max-width: 55%; object-fit: contain; display: block; margin: 0 auto 8px; }
.sig-area-line { display: flex; justify-content: space-between; border-top: 2px solid #1a1a2e; padding: 8px 4px 12px; font-size: 12px; color: #666; font-weight: 500; }

/* Footer */
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 28px 24px; border-top: 1px solid #f0f0f0; }
.btn-cancel { padding: 10px 20px; border-radius: 10px; border: 1px solid #e0e0e0; background: #fff; color: #666; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-cancel:hover:not(:disabled) { background: #f5f5f5; }
.btn-submit { padding: 10px 24px; border-radius: 10px; border: none; background: linear-gradient(135deg, #e94560, #c23152); color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(233,69,96,0.35); }
.btn-submit:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(233,69,96,0.45); }
.btn-submit:disabled, .btn-cancel:disabled { opacity: 0.6; cursor: not-allowed; }

/* =========================================================
   Toast
   ========================================================= */
.toast { position: fixed; bottom: 28px; right: 28px; padding: 14px 22px; border-radius: 12px; color: #fff; font-size: 14px; font-weight: 600; box-shadow: 0 6px 24px rgba(0,0,0,0.2); z-index: 9999; animation: slideUp 0.3s ease; }
.toast-success { background: #27ae60; }
.toast-error   { background: #e74c3c; }
.toast-info    { background: #0f3460; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
