<template>
  <div class="dashboard-wrapper">
    <!-- Sidebar -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>
    <aside class="sidebar" :class="{ open: sidebarOpen }">
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
          <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen">☰</button>
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
        <!-- Upload Zone (shown on My Uploads tab) -->
        <div
          v-if="activeTab === 'uploads'"
          class="upload-zone"
          :class="{ 'drag-active': dragOver }"
          @dragover.prevent="dragOver = true"
          @dragleave.prevent="dragOver = false"
          @drop.prevent="handleUploadDrop"
        >
          <input ref="docUploadInput" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.webp" style="display:none" @change="handleDocUploadInput" />
          <div class="upload-zone-content" @click="$refs.docUploadInput.click()">
            <span class="upload-zone-icon">📁</span>
            <h4>{{ uploading ? 'Uploading...' : 'Drop files here or click to upload' }}</h4>
            <p>PDF, Word, Excel, Images — Max 20MB</p>
          </div>
        </div>

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
          <p v-else-if="activeTab === 'uploads'">Upload your first document above.</p>
          <p v-else>Your agent hasn't sent you any documents yet.</p>
        </div>

        <!-- Documents list -->
        <div v-else class="documents-list">
          <div v-for="doc in filteredDocuments" :key="doc.id" class="document-card">
            <div class="doc-icon-wrap">
              <span class="doc-icon">{{ activeTab === 'uploads' ? getFileTypeIcon(doc.mime_type) : '📑' }}</span>
            </div>

            <div class="doc-info">
              <div class="doc-header-row">
                <h3 class="doc-name">{{ doc.document_name }}</h3>
                <span v-if="activeTab !== 'uploads'" class="doc-badge" :class="'badge-' + doc.status">
                  {{ doc.status === 'signed' ? '✅ Signed' : '🕐 Pending Signature' }}
                </span>
              </div>
              <div class="doc-meta">
                <span class="meta-item">
                  <span class="meta-icon">📁</span>
                  {{ formatDocType(doc.document_type) }}
                </span>
                <span v-if="activeTab !== 'uploads' && doc.agent_name" class="meta-item">
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
                title="Download"
              >
                ⬇️ Download
              </button>
              <router-link
                v-if="activeTab === 'uploads' && doc.mime_type && (doc.mime_type === 'application/pdf' || doc.mime_type.startsWith('image/'))"
                :to="`/documents/${doc.id}/edit`"
                class="btn-action btn-edit"
              >
                ✏️ Edit
              </router-link>
              <button
                v-if="activeTab !== 'uploads' && doc.status === 'pending'"
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
      <div class="modal-box sign-modal" :class="{ 'sign-modal-wide': signPhase === 'position' || signPhase === 'preview' }">
        <div class="modal-header">
          <div class="modal-title-group">
            <h2 class="modal-title">Sign Document</h2>
            <p class="modal-subtitle">{{ signingDoc?.document_name }}</p>
          </div>
          <button class="modal-close" @click="closeSignModal">✕</button>
        </div>

        <!-- Phase indicator -->
        <div class="sign-phases">
          <div class="phase-step" :class="{ active: signPhase === 'position', done: signPhase !== 'position' }">
            <span class="phase-num">1</span>
            <span class="phase-label">Place Signature</span>
          </div>
          <div class="phase-connector"></div>
          <div class="phase-step" :class="{ active: signPhase === 'capture', done: signPhase === 'preview' || signPhase === 'signing' }">
            <span class="phase-num">2</span>
            <span class="phase-label">Create Signature</span>
          </div>
          <div class="phase-connector"></div>
          <div class="phase-step" :class="{ active: signPhase === 'preview', done: signPhase === 'signing' }">
            <span class="phase-num">3</span>
            <span class="phase-label">Confirm</span>
          </div>
        </div>

        <!-- ── PHASE 1: PDF PREVIEW + CLICK TO PLACE ── -->
        <template v-if="signPhase === 'position'">
          <div class="sign-tab-content">
            <p class="sign-hint">Click on the document where you want to place your signature:</p>

            <div v-if="pdfLoading" class="pdf-loading">
              <div class="spinner"></div>
              <p>Loading document preview...</p>
            </div>

            <div v-else-if="!isPdfDoc" class="non-pdf-notice">
              <span class="notice-icon">📄</span>
              <p>This document is not a PDF. Your signature will be attached separately.</p>
              <button class="btn-submit btn-sm" @click="signPhase = 'capture'; $nextTick(() => initCanvas())">Continue to Signature</button>
            </div>

            <div v-else class="pdf-preview-area">
              <!-- Page navigation -->
              <div v-if="totalPages > 1" class="page-nav">
                <button class="page-nav-btn" :disabled="currentPage <= 1" @click="goToPage(currentPage - 1)">◀</button>
                <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
                <button class="page-nav-btn" :disabled="currentPage >= totalPages" @click="goToPage(currentPage + 1)">▶</button>
              </div>

              <div class="pdf-canvas-container" @click="handlePdfClick">
                <canvas ref="pdfCanvas" class="pdf-canvas"></canvas>
                <!-- Signature placement indicator -->
                <div
                  v-if="positionSelected"
                  class="sig-placeholder"
                  :style="sigPlaceholderStyle"
                >
                  <span class="sig-placeholder-text">Signature Here</span>
                </div>
              </div>
            </div>
          </div>

          <p v-if="signError" class="sign-error">{{ signError }}</p>

          <div class="modal-footer">
            <button class="btn-cancel" @click="closeSignModal">Cancel</button>
            <button
              class="btn-submit"
              :disabled="!positionSelected && isPdfDoc"
              @click="signPhase = 'capture'; $nextTick(() => initCanvas())"
            >
              Next: Create Signature
            </button>
          </div>
        </template>

        <!-- ── PHASE 2: DRAW / UPLOAD SIGNATURE ── -->
        <template v-else-if="signPhase === 'capture'">
          <div class="sign-tabs">
            <button class="sign-tab" :class="{ active: signTab === 'draw' }" @click="switchSignTab('draw')">
              ✏️ Draw Signature
            </button>
            <button class="sign-tab" :class="{ active: signTab === 'upload' }" @click="switchSignTab('upload')">
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
                <p class="preview-note">Background removed</p>
              </div>
            </div>
            <div v-if="photoPreview" class="canvas-controls">
              <button class="btn-clear" @click="clearPhoto">🗑️ Remove Photo</button>
            </div>
          </div>

          <p v-if="signError" class="sign-error">{{ signError }}</p>

          <div class="modal-footer">
            <button class="btn-cancel" @click="signPhase = 'position'" :disabled="signing">Back</button>
            <button class="btn-submit" @click="previewSignature">Preview Signature</button>
          </div>
        </template>

        <!-- ── PHASE 3: PREVIEW WITH SIGNATURE ON PDF ── -->
        <template v-else-if="signPhase === 'preview'">
          <div class="sign-tab-content">
            <p class="sign-hint">Review your signature placement. Click "Confirm & Sign" to finalize.</p>
            <div class="preview-document-area">
              <div class="pdf-canvas-container" v-if="isPdfDoc">
                <canvas ref="previewPdfCanvas" class="pdf-canvas"></canvas>
                <img
                  v-if="signaturePreview"
                  :src="signaturePreview"
                  class="sig-overlay-img"
                  :style="sigOverlayStyle"
                  alt="Your signature"
                />
              </div>
              <div v-else class="sig-area-box">
                <img :src="signaturePreview" class="sig-area-img" alt="Your signature preview" />
                <div class="sig-area-line">
                  <span>Buyer Signature</span>
                  <span>{{ new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                </div>
              </div>
            </div>
          </div>

          <p v-if="signError" class="sign-error">{{ signError }}</p>

          <div class="modal-footer">
            <button class="btn-cancel" @click="signPhase = 'capture'" :disabled="signing">Back</button>
            <button class="btn-submit" @click="submitSignature" :disabled="signing">
              {{ signing ? 'Signing & Sending...' : 'Confirm & Sign Document' }}
            </button>
          </div>
        </template>

        <!-- ── SIGNING IN PROGRESS ── -->
        <template v-else-if="signPhase === 'signing'">
          <div class="signing-progress">
            <div class="spinner spinner-lg"></div>
            <h3>Embedding signature into document...</h3>
            <p>The signed document will be sent automatically.</p>
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
import { markRaw } from 'vue';

export default {
  name: 'BuyerDocuments',

  data() {
    return {
      sidebarOpen: false,
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
        { label: 'My Uploads', value: 'uploads' },
      ],
      myUploads: [],
      dragOver: false,
      uploading: false,

      // Sign modal
      showSignModal: false,
      signingDoc: null,
      signPhase: 'position',   // 'position' | 'capture' | 'preview' | 'signing'
      signTab: 'draw',         // 'draw' | 'upload'
      signing: false,
      signError: '',
      signaturePreview: null,

      // PDF preview
      pdfDoc: null,
      totalPages: 1,
      currentPage: 1,
      pdfLoading: false,
      pdfPageWidth: 0,
      pdfPageHeight: 0,

      // Signature position (relative to canvas)
      signaturePosition: { x: 0, y: 0, page: 1 },
      canvasDisplayWidth: 0,
      canvasDisplayHeight: 0,
      positionSelected: false,

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
      if (this.activeTab === 'uploads') return this.myUploads;
      if (this.activeTab === 'all') return this.documents;
      return this.documents.filter(d => d.status === this.activeTab);
    },
    pendingCount() {
      return this.documents.filter(d => d.status === 'pending').length;
    },
    isPdfDoc() {
      if (!this.signingDoc) return false;
      const url = (this.signingDoc.document_url || '').toLowerCase();
      return url.endsWith('.pdf') || (this.signingDoc.mime_type || '').includes('pdf');
    },
    sigPlaceholderStyle() {
      if (!this.positionSelected || !this.canvasDisplayWidth) return {};
      const wPct = 25; // 25% of document width
      const hPx  = 40;
      const leftPct = (this.signaturePosition.x / this.canvasDisplayWidth) * 100;
      const topPx   = this.signaturePosition.y;
      return {
        left: Math.min(leftPct, 100 - wPct) + '%',
        top: topPx + 'px',
        width: wPct + '%',
        height: hPx + 'px',
      };
    },
    sigOverlayStyle() {
      if (!this.positionSelected || !this.canvasDisplayWidth) return {};
      const wPct = 25;
      const hPx  = 40;
      const leftPct = (this.signaturePosition.x / this.canvasDisplayWidth) * 100;
      const topPx   = this.signaturePosition.y;
      return {
        position: 'absolute',
        left: Math.min(leftPct, 100 - wPct) + '%',
        top: topPx + 'px',
        width: wPct + '%',
        height: hPx + 'px',
        objectFit: 'contain',
        pointerEvents: 'none',
      };
    },
  },

  mounted() {
    this.apiUrl = localStorage.getItem('api_url') || '/api';
    this.token = localStorage.getItem('auth_token') || '';
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Buyer';
    this.loadDocuments();
    this.loadMyUploads();
    this.clearDocumentNotifications();
  },

  methods: {
    async clearDocumentNotifications() {
      try {
        await fetch(`${this.apiUrl}/api/notifications/mark-type-read`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ type: 'document' }),
        });
      } catch (e) { /* silent */ }
    },

    // ========================
    // Load documents
    // ========================
    async loadDocuments() {
      this.loading = true;
      try {
        const res = await fetch(`${this.apiUrl}/api/buyer/documents`, {
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
    // Load my uploads
    // ========================
    async loadMyUploads() {
      try {
        const res = await fetch(`${this.apiUrl}/api/documents/my`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        this.myUploads = data.documents || [];
      } catch (e) { /* silent */ }
    },

    // ========================
    // Upload document
    // ========================
    handleDocUploadInput(e) {
      const file = e.target.files[0];
      if (file) this.uploadFile(file);
      e.target.value = '';
    },
    handleUploadDrop(e) {
      this.dragOver = false;
      const file = e.dataTransfer.files[0];
      if (file) this.uploadFile(file);
    },
    async uploadFile(file) {
      this.uploading = true;
      const fd = new FormData();
      fd.append('file', file);
      fd.append('document_name', file.name.replace(/\.[^.]+$/, ''));
      try {
        const res = await fetch(`${this.apiUrl}/api/documents/upload`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}` },
          body: fd,
        });
        const data = await res.json();
        if (data.success) {
          this.myUploads.unshift(data.document);
          this.showToast('Document uploaded!', 'success');
        } else {
          this.showToast(data.message || 'Upload failed.', 'error');
        }
      } catch (e) {
        this.showToast('Upload failed.', 'error');
      } finally {
        this.uploading = false;
      }
    },

    getFileTypeIcon(mimeType) {
      if (!mimeType) return '📄';
      if (mimeType === 'application/pdf') return '📕';
      if (mimeType.includes('word') || mimeType.includes('document')) return '📘';
      if (mimeType.includes('sheet') || mimeType.includes('excel')) return '📗';
      if (mimeType.startsWith('image/')) return '🖼️';
      return '📄';
    },

    // ========================
    // Download
    // ========================
    async downloadDoc(doc) {
      try {
        const res = await fetch(`${this.apiUrl}/api/documents/${doc.id}/download`, {
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
      this.signPhase         = 'position';
      this.signTab           = 'draw';
      this.signError         = '';
      this.hasDrawn          = false;
      this.photoPreview      = false;
      this.photoDataUrl      = null;
      this.signaturePreview  = null;
      this.positionSelected  = false;
      this.signaturePosition = { x: 0, y: 0, page: 1 };
      this.pdfDoc            = null;
      this.totalPages        = 1;
      this.currentPage       = 1;
      this.showSignModal     = true;

      // Load PDF preview if it's a PDF
      this.$nextTick(() => {
        const url = (doc.document_url || '').toLowerCase();
        const isPdf = url.endsWith('.pdf') || (doc.mime_type || '').includes('pdf');
        if (isPdf && doc.document_url) {
          this.loadPdfPreview(doc.document_url);
        }
      });
    },

    closeSignModal() {
      if (this.signing) return;
      this.showSignModal = false;
      this.signingDoc    = null;
      this.pdfDoc        = null;
    },

    switchSignTab(tab) {
      this.signTab   = tab;
      this.signError = '';
      if (tab === 'draw') this.$nextTick(() => this.initCanvas());
    },

    // ========================
    // PDF Preview loading
    // ========================
    async loadPdfPreview(url) {
      this.pdfLoading = true;
      try {
        // Load PDF.js from CDN if not already loaded
        if (!window.pdfjsLib) {
          await this.loadPdfJsScript();
        }
        const pdfjsLib = window.pdfjsLib;
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // Fetch the PDF
        const loadingTask = pdfjsLib.getDocument(url);
        this.pdfDoc = markRaw(await loadingTask.promise);
        this.totalPages = this.pdfDoc.numPages;
        this.currentPage = 1;
        await this.renderPdfPage(1);
      } catch (e) {
        console.error('PDF preview failed:', e);
        this.signError = 'Could not load PDF preview. You can still sign the document.';
      } finally {
        this.pdfLoading = false;
      }
    },

    loadPdfJsScript() {
      return new Promise((resolve, reject) => {
        if (window.pdfjsLib) { resolve(); return; }
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Failed to load PDF.js'));
        document.head.appendChild(script);
      });
    },

    async renderPdfPage(pageNum) {
      if (!this.pdfDoc) return;
      const page = await this.pdfDoc.getPage(pageNum);
      const scale = 1.5;
      const viewport = page.getViewport({ scale });

      const canvas = this.$refs.pdfCanvas;
      if (!canvas) return;
      canvas.width  = viewport.width;
      canvas.height = viewport.height;

      this.pdfPageWidth  = viewport.width;
      this.pdfPageHeight = viewport.height;

      const ctx = canvas.getContext('2d');
      await page.render({ canvasContext: ctx, viewport }).promise;

      // Store displayed dimensions for coordinate mapping
      this.$nextTick(() => {
        this.canvasDisplayWidth  = canvas.offsetWidth;
        this.canvasDisplayHeight = canvas.offsetHeight;
      });
    },

    async goToPage(num) {
      if (num < 1 || num > this.totalPages) return;
      this.currentPage = num;
      this.positionSelected = false;
      await this.renderPdfPage(num);
    },

    handlePdfClick(e) {
      const canvas = this.$refs.pdfCanvas;
      if (!canvas) return;
      const rect = canvas.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      this.canvasDisplayWidth  = canvas.offsetWidth;
      this.canvasDisplayHeight = canvas.offsetHeight;

      this.signaturePosition = { x, y, page: this.currentPage };
      this.positionSelected = true;
      this.signError = '';
    },

    async renderPreviewPage() {
      if (!this.pdfDoc) return;
      const page = await this.pdfDoc.getPage(this.signaturePosition.page);
      const scale = 1.5;
      const viewport = page.getViewport({ scale });

      const canvas = this.$refs.previewPdfCanvas;
      if (!canvas) return;
      canvas.width  = viewport.width;
      canvas.height = viewport.height;

      const ctx = canvas.getContext('2d');
      await page.render({ canvasContext: ctx, viewport }).promise;
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
      ctx.strokeStyle = '#0B1C39';
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
    // Preview then submit
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
      this.signPhase = 'preview';

      // Render the PDF page in preview if it's a PDF
      if (this.isPdfDoc && this.pdfDoc) {
        this.$nextTick(() => this.renderPreviewPage());
      }
    },

    async submitSignature() {
      this.signError = '';
      this.signing   = true;
      this.signPhase = 'signing';
      try {
        // Calculate position in PDF coordinate space
        const scaleX = this.canvasDisplayWidth ? (this.pdfPageWidth / this.canvasDisplayWidth) : 1;
        const scaleY = this.canvasDisplayHeight ? (this.pdfPageHeight / this.canvasDisplayHeight) : 1;
        const pdfX = this.signaturePosition.x * scaleX;
        const pdfY = this.signaturePosition.y * scaleY;

        const res = await fetch(`${this.apiUrl}/api/buyer/documents/${this.signingDoc.id}/sign`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json' },
          body: JSON.stringify({
            signature_data: this.signaturePreview,
            signature_type: this.signTab === 'draw' ? 'drawn' : 'photo',
            position_x: pdfX,
            position_y: pdfY,
            position_page: this.signaturePosition.page || 1,
            page_width: this.pdfPageWidth || 595,
            page_height: this.pdfPageHeight || 842,
          }),
        });
        const data = await res.json();
        if (!res.ok) {
          this.signError = data.message || 'Failed to submit signature.';
          this.signPhase = 'preview';
          return;
        }

        const idx = this.documents.findIndex(d => d.id === this.signingDoc.id);
        if (idx !== -1 && data.document) this.documents[idx] = data.document;

        this.showSignModal = false;
        this.signingDoc = null;
        this.pdfDoc = null;
        this.showToast('Document signed and sent!', 'success');
      } catch (e) {
        this.signError = 'Network error. Please try again.';
        this.signPhase = 'preview';
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
      fetch(`${this.apiUrl}/api/auth/logout`, {
        method: 'POST',
        headers: { Authorization: `Bearer ${this.token}` },
      }).finally(() => {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.$router.push('/');
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
.sidebar { width: 260px; min-height: 100vh; background: #0B1C39; display: flex; flex-direction: column; position: fixed; left: 0; top: 0; bottom: 0; z-index: 100; box-shadow: 4px 0 20px rgba(0,0,0,0.3); }
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
.page-title { font-size: 20px; font-weight: 700; color: #0B1C39; margin: 0; }
.filter-tabs { display: flex; gap: 6px; }
.filter-tab { padding: 6px 14px; border-radius: 20px; border: 1px solid #e0e0e0; background: #fff; color: #666; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 6px; }
.filter-tab:hover { border-color: #D89B0F; color: #D89B0F; }
.filter-tab.active { background: #D89B0F; border-color: #D89B0F; color: #fff; }
.tab-badge { background: #e94560; color: #fff; border-radius: 12px; padding: 1px 7px; font-size: 11px; font-weight: 700; }
.content-area { padding: 28px; flex: 1; }

/* =========================================================
   Loading / Empty
   ========================================================= */
.loading-state { text-align: center; padding: 80px 20px; color: #666; }
.spinner { width: 40px; height: 40px; border: 4px solid #e0e0e0; border-top-color: #D89B0F; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 16px; }
@keyframes spin { to { transform: rotate(360deg); } }
.empty-state { text-align: center; padding: 80px 20px; }
.empty-icon { font-size: 56px; margin-bottom: 16px; }
.empty-state h3 { font-size: 20px; font-weight: 700; color: #0B1C39; margin: 0 0 8px; }
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
.doc-name { font-size: 16px; font-weight: 700; color: #0B1C39; margin: 0; }
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

/* Upload zone */
.upload-zone { border: 2px dashed #D89B0F; border-radius: 16px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.2s; background: rgba(216,155,15,0.04); margin-bottom: 24px; }
.upload-zone:hover, .upload-zone.drag-active { background: #e0e8ff; border-color: #e94560; }
.upload-zone-icon { font-size: 48px; display: block; margin-bottom: 12px; }
.upload-zone h4 { font-size: 16px; font-weight: 700; color: #0B1C39; margin: 0 0 6px; }
.upload-zone p { font-size: 13px; color: #888; margin: 0; }
.btn-edit { background: #fff3e0; color: #e65100; }
.btn-edit:hover { background: #e65100; color: #fff; }

/* =========================================================
   Sign Modal
   ========================================================= */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000; padding: 20px; }
.modal-box { background: #fff; border-radius: 20px; width: 100%; max-width: 620px; box-shadow: 0 20px 60px rgba(0,0,0,0.25); overflow: hidden; max-height: 90vh; overflow-y: auto; }
.sign-modal-wide { max-width: 800px; }
.modal-header { display: flex; align-items: flex-start; justify-content: space-between; padding: 24px 28px 0; }
.modal-title { font-size: 20px; font-weight: 800; color: #0B1C39; margin: 0 0 4px; }
.modal-subtitle { font-size: 13px; color: #666; margin: 0; }
.modal-close { background: none; border: none; font-size: 20px; color: #999; cursor: pointer; padding: 4px 8px; border-radius: 6px; }
.modal-close:hover { background: #f5f5f5; color: #333; }

/* Tabs */
.sign-tabs { display: flex; padding: 20px 28px 0; border-bottom: 2px solid #f0f0f0; }
.sign-tab { flex: 1; padding: 10px; background: none; border: none; border-bottom: 3px solid transparent; font-size: 14px; font-weight: 600; color: #999; cursor: pointer; transition: all 0.2s; margin-bottom: -2px; }
.sign-tab:hover { color: #D89B0F; }
.sign-tab.active { color: #D89B0F; border-bottom-color: #D89B0F; }

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
.upload-area:hover { border-color: #D89B0F; }
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
.sig-area-line { display: flex; justify-content: space-between; border-top: 2px solid #0B1C39; padding: 8px 4px 12px; font-size: 12px; color: #666; font-weight: 500; }

/* Footer */
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 28px 24px; border-top: 1px solid #f0f0f0; }
.btn-cancel { padding: 10px 20px; border-radius: 10px; border: 1px solid #e0e0e0; background: #fff; color: #666; font-size: 14px; font-weight: 600; cursor: pointer; }
.btn-cancel:hover:not(:disabled) { background: #f5f5f5; }
.btn-submit { padding: 10px 24px; border-radius: 10px; border: none; background: linear-gradient(135deg, #e94560, #c23152); color: #fff; font-size: 14px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(233,69,96,0.35); }
.btn-submit:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(233,69,96,0.45); }
.btn-submit:disabled, .btn-cancel:disabled { opacity: 0.6; cursor: not-allowed; }

/* =========================================================
   Phase Indicator
   ========================================================= */
.sign-phases { display: flex; align-items: center; justify-content: center; gap: 0; padding: 18px 28px 12px; }
.phase-step { display: flex; align-items: center; gap: 6px; opacity: 0.4; transition: opacity 0.2s; }
.phase-step.active { opacity: 1; }
.phase-step.done { opacity: 0.7; }
.phase-num { width: 24px; height: 24px; border-radius: 50%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #666; }
.phase-step.active .phase-num { background: #D89B0F; color: #fff; }
.phase-step.done .phase-num { background: #27ae60; color: #fff; }
.phase-label { font-size: 12px; font-weight: 600; color: #333; }
.phase-connector { width: 32px; height: 2px; background: #e0e0e0; margin: 0 8px; }

/* =========================================================
   PDF Preview
   ========================================================= */
.pdf-loading { text-align: center; padding: 40px 20px; color: #666; }
.pdf-preview-area { }
.page-nav { display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 12px; }
.page-nav-btn { width: 32px; height: 32px; border-radius: 8px; border: 1px solid #e0e0e0; background: #fff; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; }
.page-nav-btn:hover:not(:disabled) { background: rgba(216,155,15,0.04); border-color: #D89B0F; }
.page-nav-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-info { font-size: 13px; font-weight: 600; color: #333; }
.pdf-canvas-container { position: relative; border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; cursor: crosshair; background: #f5f5f5; }
.pdf-canvas { display: block; width: 100%; height: auto; }
.sig-placeholder { position: absolute; border: 2px dashed #e94560; background: rgba(233,69,96,0.08); border-radius: 4px; display: flex; align-items: center; justify-content: center; pointer-events: none; transition: all 0.15s ease; }
.sig-placeholder-text { font-size: 11px; font-weight: 700; color: #e94560; text-transform: uppercase; letter-spacing: 0.5px; }

/* Non-PDF notice */
.non-pdf-notice { text-align: center; padding: 40px 20px; }
.notice-icon { font-size: 48px; display: block; margin-bottom: 12px; }
.non-pdf-notice p { color: #666; font-size: 14px; margin: 0 0 16px; }
.btn-sm { padding: 8px 18px; font-size: 13px; }

/* Preview with overlay */
.preview-document-area { position: relative; }
.sig-overlay-img { pointer-events: none; }

/* Signing progress */
.signing-progress { text-align: center; padding: 60px 28px; }
.signing-progress h3 { font-size: 18px; font-weight: 700; color: #0B1C39; margin: 16px 0 8px; }
.signing-progress p { font-size: 14px; color: #666; margin: 0; }
.spinner-lg { width: 48px; height: 48px; border: 4px solid #e0e0e0; border-top-color: #D89B0F; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto; }

/* =========================================================
   Toast
   ========================================================= */
.toast { position: fixed; bottom: 28px; right: 28px; padding: 14px 22px; border-radius: 12px; color: #fff; font-size: 14px; font-weight: 600; box-shadow: 0 6px 24px rgba(0,0,0,0.2); z-index: 9999; animation: slideUp 0.3s ease; }
.toast-success { background: #27ae60; }
.toast-error   { background: #e74c3c; }
.toast-info    { background: #D89B0F; }
@keyframes slideUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .sidebar { position: fixed; left: -280px; width: 280px; z-index: 1001; transition: left 0.3s ease; }
  .main-content { margin-left: 0; }
  .topbar { padding: 0 16px; height: auto; }
  .topbar-content { flex-direction: column; align-items: flex-start; padding: 12px 0; gap: 10px; height: auto; }
  .topbar-right { flex-wrap: wrap; gap: 8px; }
  .filter-tabs { flex-wrap: wrap; gap: 4px; }
  .filter-tab { padding: 5px 10px; font-size: 0.75rem; }
  .content-area { padding: 16px; }
  .docs-grid { grid-template-columns: 1fr; gap: 14px; }
  .doc-card-header { flex-direction: column; align-items: flex-start; gap: 8px; }
  .modal-box { max-width: 95vw; }
  .sign-modal-wide { max-width: 95vw; }
  .sign-tabs { padding: 14px 16px 0; }
  .sign-tab-content { padding: 14px 16px; }
  .modal-header { padding: 16px 16px 0; }
  .modal-footer { padding: 14px 16px 18px; }
  .sign-phases { padding: 14px 16px 8px; }
  .toast { left: 16px; right: 16px; bottom: 16px; text-align: center; }
}

@media (max-width: 480px) {
  .page-title { font-size: 18px; }
  .content-area { padding: 10px; }
  .doc-card { border-radius: 12px; }
  .doc-body { padding: 14px; }
  .doc-actions { flex-direction: column; }
  .doc-actions button,
  .doc-actions a { width: 100%; justify-content: center; }
  .empty-state { padding: 40px 16px; }
  .sign-phases { flex-wrap: wrap; gap: 4px; }
  .phase-connector { width: 16px; }
}
</style>
