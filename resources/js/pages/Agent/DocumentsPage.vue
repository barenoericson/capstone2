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
          <h3 class="section-title">Management</h3>
          <router-link to="/agent/dashboard" class="nav-item">
            <span class="nav-icon">📊</span>
            <span class="nav-label">Overview</span>
          </router-link>
          <router-link to="/agent/properties" class="nav-item">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">My Properties</span>
          </router-link>
          <router-link to="/agent/saved-properties" class="nav-item">
            <span class="nav-icon">❤️</span>
            <span class="nav-label">My Wallet</span>
          </router-link>
          <router-link to="/agent/viewings" class="nav-item">
            <span class="nav-icon">📅</span>
            <span class="nav-label">Viewings</span>
          </router-link>
          <router-link to="/agent/documents" class="nav-item active">
            <span class="nav-icon">📄</span>
            <span class="nav-label">Documents</span>
          </router-link>
          <router-link to="/conversations" class="nav-item">
            <span class="nav-icon">💬</span>
            <span class="nav-label">Messages</span>
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
            <p class="user-role-card">Agent</p>
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
            <h1 class="page-title">Documents</h1>
          </div>
          <div class="topbar-right">
            <button class="btn-primary" @click="openUploadModal">
              ➕ Send Document
            </button>
          </div>
        </div>
      </nav>

      <div class="page-wrapper">
        <div v-if="loading" class="state-box">
          <div class="state-icon">⏳</div>
          <p>Loading documents...</p>
        </div>

        <div v-else-if="documents.length === 0" class="state-box">
          <div class="state-icon">📋</div>
          <h3>No documents sent yet</h3>
          <p>Send contracts or agreements to buyers who have messaged you.</p>
          <button class="btn-primary" @click="openUploadModal">Send First Document</button>
        </div>

        <div v-else class="doc-area">
          <!-- Documents Table -->
          <div class="doc-table-wrap">
            <table class="doc-table">
              <thead>
                <tr>
                  <th>Document</th>
                  <th>Type</th>
                  <th>Sent To</th>
                  <th>Status</th>
                  <th>Date Sent</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="doc in documents" :key="doc.id" class="doc-row">
                  <td class="doc-name-cell">
                    <span class="doc-icon">📄</span>
                    <span>{{ doc.document_name }}</span>
                  </td>
                  <td>
                    <span class="type-badge">{{ capitalize(doc.document_type) }}</span>
                  </td>
                  <td>
                    <div class="buyer-cell">
                      <span class="buyer-avatar">{{ (doc.buyer?.name || 'B').charAt(0).toUpperCase() }}</span>
                      <span>{{ doc.buyer?.name || 'Unknown' }}</span>
                    </div>
                  </td>
                  <td>
                    <span class="status-badge" :class="'badge-' + doc.status">
                      {{ doc.status === 'signed' ? '✅ Signed' : '🕐 Pending' }}
                    </span>
                  </td>
                  <td class="date-cell">{{ formatDate(doc.created_at) }}</td>
                  <td>
                    <div class="actions-cell">
                      <a v-if="doc.document_url" :href="doc.document_url" target="_blank" class="btn-action btn-view">👁 View</a>
                      <a v-if="doc.document_path" :href="`${apiUrl}/api/documents/${doc.id}/download`" class="btn-action btn-download" :headers="{ Authorization: `Bearer ${token}` }" @click.prevent="downloadDoc(doc)">⬇ Download</a>
                      <button v-if="!doc.agent_signature_url" @click="openAgentSignModal(doc)" class="btn-action btn-sign">✍️ Sign</button>
                      <button @click="confirmDelete(doc)" class="btn-action btn-delete">🗑</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Signature proofs -->
          <div v-if="sigProofDocs.length > 0" class="sig-section">
            <h3 class="sig-section-title">Signature Proofs</h3>
            <div v-for="doc in sigProofDocs" :key="'sig-' + doc.id" class="sig-proof-card">
              <div class="sig-proof-docname">📄 {{ doc.document_name }}</div>
              <div class="sig-proof-cols">
                <div class="sig-col">
                  <p class="sig-col-label">Agent Signature (You)</p>
                  <div v-if="doc.agent_signature_url" class="sig-box">
                    <img :src="doc.agent_signature_url" class="sig-img" alt="Agent signature" />
                    <span class="sig-badge">{{ doc.agent_signature_type === 'drawn' ? 'Hand drawn' : 'Photo upload' }}</span>
                  </div>
                  <div v-else class="sig-box sig-box-empty">
                    <span>Not yet signed</span>
                    <button @click="openAgentSignModal(doc)" class="btn-sign-inline">✍️ Sign Now</button>
                  </div>
                </div>
                <div class="sig-col">
                  <p class="sig-col-label">Buyer Signature</p>
                  <div v-if="doc.signature_url" class="sig-box">
                    <img :src="doc.signature_url" class="sig-img" alt="Buyer signature" />
                    <span class="sig-badge">{{ doc.buyer?.name }} — {{ formatDate(doc.signed_at) }}</span>
                  </div>
                  <div v-else class="sig-box sig-box-empty">
                    <span>Awaiting buyer signature</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- ============================================================ -->
    <!-- SEND DOCUMENT MODAL -->
    <!-- ============================================================ -->
    <div v-if="showUploadModal" class="modal-overlay" @click.self="closeUploadModal">
      <div class="modal-box">
        <div class="modal-header">
          <h3>📤 Send Document to Buyer</h3>
          <button @click="closeUploadModal" class="btn-close">✕</button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Select Buyer <span class="required">*</span></label>
            <div v-if="buyersLoading" class="helper-text">Loading buyers...</div>
            <div v-else-if="buyers.length === 0" class="helper-text warn">
              No buyers found. You need to have a conversation with a buyer first before sending a document.
            </div>
            <select v-else v-model="uploadForm.buyer_id" class="form-select">
              <option value="">Choose a buyer...</option>
              <option v-for="b in buyers" :key="b.id" :value="b.id">
                {{ b.name }} — {{ b.email }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label>Document Name <span class="required">*</span></label>
            <input v-model="uploadForm.document_name" type="text" class="form-input" placeholder="e.g. Sale Agreement for Unit 4B" />
          </div>

          <div class="form-group">
            <label>Document Type <span class="required">*</span></label>
            <select v-model="uploadForm.document_type" class="form-select">
              <option value="contract">Contract</option>
              <option value="agreement">Agreement</option>
              <option value="disclosure">Disclosure</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label>Document Source <span class="required">*</span></label>
            <div class="source-toggle">
              <button type="button" :class="['src-btn', { active: uploadForm.sourceType === 'file' }]" @click="uploadForm.sourceType = 'file'">📁 Upload PDF</button>
              <button type="button" :class="['src-btn', { active: uploadForm.sourceType === 'link' }]" @click="uploadForm.sourceType = 'link'">🔗 External Link</button>
            </div>
          </div>

          <!-- PDF File Upload -->
          <div v-if="uploadForm.sourceType === 'file'" class="form-group">
            <div class="file-drop" @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleFileDrop">
              <template v-if="!uploadForm.file">
                <span class="drop-icon">📁</span>
                <p>Click to select or drag &amp; drop a PDF</p>
                <p class="drop-hint">PDF only · Max 10MB</p>
              </template>
              <template v-else>
                <div class="file-selected">
                  <span class="file-name">📄 {{ uploadForm.file.name }}</span>
                  <span class="file-size">{{ formatFileSize(uploadForm.file.size) }}</span>
                </div>
              </template>
            </div>
            <input ref="fileInput" type="file" accept=".pdf" class="hidden-input" @change="handleFileChange" />
          </div>

          <!-- External Link -->
          <div v-if="uploadForm.sourceType === 'link'" class="form-group">
            <input v-model="uploadForm.document_link" type="url" class="form-input" placeholder="https://docs.google.com/... or any PDF URL" />
            <p class="drop-hint" style="margin-top:6px">Paste a Google Docs link, Dropbox link, or any direct PDF URL.</p>
          </div>

          <p v-if="uploadError" class="form-error">{{ uploadError }}</p>
        </div>

        <div class="modal-footer">
          <button @click="closeUploadModal" class="btn-secondary">Cancel</button>
          <button @click="submitUpload" class="btn-primary" :disabled="uploading">
            {{ uploading ? 'Sending...' : '📤 Send Document' }}
          </button>
        </div>
      </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
    <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
      <div class="modal-box modal-sm">
        <div class="modal-header">
          <h3>Delete Document?</h3>
          <button @click="showDeleteModal = false" class="btn-close">✕</button>
        </div>
        <div class="modal-body">
          <p>Delete <strong>"{{ deleteTarget?.document_name }}"</strong>? This cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button @click="showDeleteModal = false" class="btn-secondary">Cancel</button>
          <button @click="deleteDocument" class="btn-danger" :disabled="deleteLoading">
            {{ deleteLoading ? 'Deleting...' : 'Yes, Delete' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- AGENT SIGN MODAL -->
    <!-- ============================================================ -->
    <div v-if="showSignModal" class="modal-overlay" @click.self="closeSignModal">
      <div class="modal-box sign-modal-box">
        <div class="modal-header">
          <div>
            <h3>✍️ Sign Document</h3>
            <p class="sign-modal-subtitle">{{ signingDoc?.document_name }}</p>
          </div>
          <button @click="closeSignModal" class="btn-close">✕</button>
        </div>

        <template v-if="signMode === 'draw'">
          <!-- Tabs -->
          <div class="sign-tabs">
            <button :class="['sign-tab', { active: signTab === 'draw' }]" @click="switchSignTab('draw')">✏️ Draw</button>
            <button :class="['sign-tab', { active: signTab === 'upload' }]" @click="switchSignTab('upload')">📷 Upload Photo</button>
          </div>

          <!-- Draw tab -->
          <div v-if="signTab === 'draw'" class="sign-tab-content">
            <p class="sign-hint">Draw your signature below:</p>
            <div class="canvas-wrapper">
              <canvas ref="agentCanvas" class="signature-canvas" width="520" height="180"
                @mousedown="agentStartDraw" @mousemove="agentDraw" @mouseup="agentStopDraw" @mouseleave="agentStopDraw"
                @touchstart.prevent="agentStartTouch" @touchmove.prevent="agentDrawTouch" @touchend.prevent="agentStopDraw">
              </canvas>
            </div>
            <div class="canvas-controls">
              <button class="btn-clear" @click="clearAgentCanvas">🗑️ Clear</button>
            </div>
          </div>

          <!-- Upload tab -->
          <div v-if="signTab === 'upload'" class="sign-tab-content">
            <p class="sign-hint">Upload a photo of your handwritten signature. Background will be removed automatically.</p>
            <div class="upload-area" @click="$refs.agentPhotoInput.click()" @dragover.prevent @drop.prevent="handleAgentDrop">
              <input ref="agentPhotoInput" type="file" accept="image/*" class="hidden-input" @change="handleAgentPhotoUpload" />
              <div v-if="!agentPhotoPreview" class="upload-placeholder">
                <span class="upload-icon">📷</span>
                <p>Click to upload or drag &amp; drop</p>
                <p class="upload-sub">Background will be automatically removed</p>
              </div>
              <div v-else class="photo-preview-wrap">
                <canvas ref="agentProcessedCanvas" class="processed-canvas" width="520" height="180"></canvas>
                <p class="preview-note">✅ Background removed</p>
              </div>
            </div>
            <div v-if="agentPhotoPreview" class="canvas-controls">
              <button class="btn-clear" @click="clearAgentPhoto">🗑️ Remove Photo</button>
            </div>
          </div>

          <p v-if="signError" class="sign-error">{{ signError }}</p>

          <div class="modal-footer">
            <button @click="closeSignModal" class="btn-secondary">Cancel</button>
            <button @click="previewSignature" class="btn-primary">Preview Signature →</button>
          </div>
        </template>

        <!-- Preview step: signature in the designated area -->
        <template v-else-if="signMode === 'preview'">
          <div class="preview-section">
            <p class="preview-label">Your signature will appear like this on the document:</p>
            <div class="sig-area-box">
              <img :src="agentSignaturePreview" class="sig-area-img" alt="Your signature preview" />
              <div class="sig-area-line">
                <span>Signature</span>
                <span>{{ new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
              </div>
            </div>
          </div>

          <p v-if="signError" class="sign-error">{{ signError }}</p>

          <div class="modal-footer">
            <button @click="signMode = 'draw'" class="btn-secondary" :disabled="agentSigning">↩ Re-draw</button>
            <button @click="submitAgentSignature" class="btn-primary" :disabled="agentSigning">
              {{ agentSigning ? 'Signing...' : '✅ Confirm & Sign Document' }}
            </button>
          </div>
        </template>
      </div>
    </div>

    <!-- Toast -->
    <transition name="slide-up">
      <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">{{ toast.message }}</div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'AgentDocumentsPage',

  data() {
    return {
      loading: true,
      documents: [],
      buyers: [],
      buyersLoading: false,
      userName: '',
      showUserMenu: false,
      apiUrl: localStorage.getItem('api_url') || window.__API_URL__,
      token: localStorage.getItem('auth_token') || '',

      showUploadModal: false,
      uploading: false,
      uploadError: '',
      uploadForm: { buyer_id: '', document_name: '', document_type: 'contract', file: null, sourceType: 'file', document_link: '' },

      showDeleteModal: false,
      deleteTarget: null,
      deleteLoading: false,

      // Agent signing
      showSignModal: false,
      signingDoc: null,
      signMode: 'draw',   // 'draw' | 'preview'
      signTab: 'draw',    // 'draw' | 'upload'
      signError: '',
      agentSigning: false,
      agentIsDrawing: false,
      agentLastX: 0,
      agentLastY: 0,
      agentHasDrawn: false,
      agentPhotoPreview: false,
      agentPhotoDataUrl: null,
      agentSignaturePreview: null,

      toast: { show: false, type: 'success', message: '' },
    };
  },

  computed: {
    signedDocs() {
      return this.documents.filter(d => d.status === 'signed' && d.signature_url);
    },
    sigProofDocs() {
      return this.documents.filter(d => d.agent_signature_url || d.signature_url);
    },
  },

  async mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Agent';
    this.token = localStorage.getItem('auth_token') || '';
    await this.loadDocuments();
  },

  methods: {
    async loadDocuments() {
      try {
        this.loading = true;
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/documents`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) this.documents = data.documents;
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },

    async loadBuyers() {
      try {
        this.buyersLoading = true;
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/conversation-buyers`, {
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) this.buyers = data.buyers;
      } catch (e) {
        console.error(e);
      } finally {
        this.buyersLoading = false;
      }
    },

    openUploadModal() {
      this.uploadForm  = { buyer_id: '', document_name: '', document_type: 'contract', file: null, sourceType: 'file', document_link: '' };
      this.uploadError = '';
      this.showUploadModal = true;
      this.loadBuyers();
    },

    closeUploadModal() {
      this.showUploadModal = false;
    },

    handleFileChange(e) {
      this.uploadForm.file = e.target.files[0] || null;
    },

    handleFileDrop(e) {
      const file = e.dataTransfer.files[0];
      if (file && file.type === 'application/pdf') {
        this.uploadForm.file = file;
      }
    },

    async submitUpload() {
      this.uploadError = '';
      if (!this.uploadForm.buyer_id)      { this.uploadError = 'Please select a buyer.'; return; }
      if (!this.uploadForm.document_name) { this.uploadError = 'Please enter a document name.'; return; }
      if (this.uploadForm.sourceType === 'file' && !this.uploadForm.file) {
        this.uploadError = 'Please select a PDF file.'; return;
      }
      if (this.uploadForm.sourceType === 'link' && !this.uploadForm.document_link) {
        this.uploadError = 'Please enter a document link.'; return;
      }

      try {
        this.uploading = true;
        const fd = new FormData();
        fd.append('buyer_id',      this.uploadForm.buyer_id);
        fd.append('document_name', this.uploadForm.document_name);
        fd.append('document_type', this.uploadForm.document_type);
        if (this.uploadForm.sourceType === 'file') {
          fd.append('file', this.uploadForm.file);
        } else {
          fd.append('document_link', this.uploadForm.document_link);
        }

        const res  = await fetch(`${this.apiUrl}/api/agent/documents`, {
          method: 'POST', headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' }, body: fd,
        });
        const data = await res.json();

        if (data.success) {
          this.documents.unshift(data.document);
          this.showUploadModal = false;
          this.showToast('Document sent to buyer successfully!', 'success');
        } else {
          this.uploadError = data.message || 'Upload failed.';
        }
      } catch (e) {
        this.uploadError = 'Error sending document.';
      } finally {
        this.uploading = false;
      }
    },

    // ─── Download ─────────────────────────────────────
    async downloadDoc(doc) {
      const res = await fetch(`${this.apiUrl}/api/documents/${doc.id}/download`, {
        headers: { Authorization: `Bearer ${this.token}` },
      });
      if (!res.ok) { this.showToast('Download failed.', 'error'); return; }
      const blob = await res.blob();
      const url  = URL.createObjectURL(blob);
      const a    = document.createElement('a');
      a.href = url; a.download = doc.document_name + '.pdf';
      a.click(); URL.revokeObjectURL(url);
    },

    // ─── Agent Signing ────────────────────────────────
    openAgentSignModal(doc) {
      this.signingDoc           = doc;
      this.signMode             = 'draw';
      this.signTab              = 'draw';
      this.signError            = '';
      this.agentHasDrawn        = false;
      this.agentPhotoPreview    = false;
      this.agentPhotoDataUrl    = null;
      this.agentSignaturePreview = null;
      this.showSignModal        = true;
      this.$nextTick(() => this.initAgentCanvas());
    },

    closeSignModal() {
      if (this.agentSigning) return;
      this.showSignModal = false;
      this.signingDoc    = null;
    },

    switchSignTab(tab) {
      this.signTab   = tab;
      this.signError = '';
      if (tab === 'draw') this.$nextTick(() => this.initAgentCanvas());
    },

    initAgentCanvas() {
      const c = this.$refs.agentCanvas;
      if (!c) return;
      const ctx = c.getContext('2d');
      ctx.fillStyle = '#fff';
      ctx.fillRect(0, 0, c.width, c.height);
      ctx.strokeStyle = '#1a1a2e';
      ctx.lineWidth   = 2.5;
      ctx.lineCap     = 'round';
      ctx.lineJoin    = 'round';
      this.agentHasDrawn = false;
    },

    _agentPos(e, c) {
      const r = c.getBoundingClientRect();
      return { x: (e.clientX - r.left) * (c.width / r.width), y: (e.clientY - r.top) * (c.height / r.height) };
    },

    agentStartDraw(e) {
      const c = this.$refs.agentCanvas, p = this._agentPos(e, c);
      this.agentIsDrawing = true; this.agentLastX = p.x; this.agentLastY = p.y;
    },
    agentDraw(e) {
      if (!this.agentIsDrawing) return;
      const c = this.$refs.agentCanvas, ctx = c.getContext('2d'), p = this._agentPos(e, c);
      ctx.beginPath(); ctx.moveTo(this.agentLastX, this.agentLastY); ctx.lineTo(p.x, p.y); ctx.stroke();
      this.agentLastX = p.x; this.agentLastY = p.y; this.agentHasDrawn = true;
    },
    agentStopDraw() { this.agentIsDrawing = false; },

    agentStartTouch(e) {
      const t = e.touches[0], c = this.$refs.agentCanvas, r = c.getBoundingClientRect();
      this.agentIsDrawing = true;
      this.agentLastX = (t.clientX - r.left) * (c.width / r.width);
      this.agentLastY = (t.clientY - r.top)  * (c.height / r.height);
    },
    agentDrawTouch(e) {
      if (!this.agentIsDrawing) return;
      const t = e.touches[0], c = this.$refs.agentCanvas, ctx = c.getContext('2d'), r = c.getBoundingClientRect();
      const x = (t.clientX - r.left) * (c.width / r.width);
      const y = (t.clientY - r.top)  * (c.height / r.height);
      ctx.beginPath(); ctx.moveTo(this.agentLastX, this.agentLastY); ctx.lineTo(x, y); ctx.stroke();
      this.agentLastX = x; this.agentLastY = y; this.agentHasDrawn = true;
    },

    clearAgentCanvas() {
      const c = this.$refs.agentCanvas;
      if (!c) return;
      const ctx = c.getContext('2d');
      ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, c.width, c.height);
      this.agentHasDrawn = false;
    },

    handleAgentDrop(e) {
      const file = e.dataTransfer.files[0];
      if (file && file.type.startsWith('image/')) this.processAgentPhoto(file);
    },
    handleAgentPhotoUpload(e) {
      const file = e.target.files[0];
      if (file) this.processAgentPhoto(file);
    },

    processAgentPhoto(file) {
      const reader = new FileReader();
      reader.onload = (ev) => {
        const img = new Image();
        img.onload = () => {
          this.$nextTick(() => {
            const c = this.$refs.agentProcessedCanvas;
            if (!c) return;
            const ctx = c.getContext('2d');
            const scale = Math.min(c.width / img.width, c.height / img.height);
            const dw = img.width * scale, dh = img.height * scale;
            ctx.clearRect(0, 0, c.width, c.height);
            ctx.drawImage(img, (c.width - dw) / 2, (c.height - dh) / 2, dw, dh);
            // Remove white/near-white background
            const id = ctx.getImageData(0, 0, c.width, c.height);
            const d  = id.data;
            for (let i = 0; i < d.length; i += 4) {
              const brightness  = (d[i] + d[i+1] + d[i+2]) / 3;
              const max = Math.max(d[i], d[i+1], d[i+2]);
              const saturation  = max === 0 ? 0 : (max - Math.min(d[i], d[i+1], d[i+2])) / max;
              if (brightness > 200 && saturation < 0.25) d[i+3] = 0;
            }
            ctx.putImageData(id, 0, 0);
            this.agentPhotoPreview = true;
            this.agentPhotoDataUrl = c.toDataURL('image/png');
          });
        };
        img.src = ev.target.result;
      };
      reader.readAsDataURL(file);
    },

    clearAgentPhoto() {
      this.agentPhotoPreview = false;
      this.agentPhotoDataUrl = null;
      if (this.$refs.agentPhotoInput) this.$refs.agentPhotoInput.value = '';
    },

    previewSignature() {
      this.signError = '';
      let preview = null;
      if (this.signTab === 'draw') {
        if (!this.agentHasDrawn) { this.signError = 'Please draw your signature first.'; return; }
        preview = this.$refs.agentCanvas.toDataURL('image/png');
      } else {
        if (!this.agentPhotoDataUrl) { this.signError = 'Please upload a photo first.'; return; }
        preview = this.agentPhotoDataUrl;
      }
      this.agentSignaturePreview = preview;
      this.signMode = 'preview';
    },

    async submitAgentSignature() {
      this.signError   = '';
      this.agentSigning = true;
      try {
        const res  = await fetch(`${this.apiUrl}/api/agent/documents/${this.signingDoc.id}/sign`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json' },
          body: JSON.stringify({
            signature_data: this.agentSignaturePreview,
            signature_type: this.signTab,
          }),
        });
        const data = await res.json();
        if (!res.ok) { this.signError = data.message || 'Failed to sign.'; return; }

        const idx = this.documents.findIndex(d => d.id === this.signingDoc.id);
        if (idx !== -1 && data.document) this.documents[idx] = data.document;

        this.showSignModal = false;
        this.showToast('Document signed successfully!', 'success');
      } catch (e) {
        this.signError = 'Network error. Please try again.';
      } finally {
        this.agentSigning = false;
      }
    },

    confirmDelete(doc) {
      this.deleteTarget   = doc;
      this.showDeleteModal = true;
    },

    async deleteDocument() {
      try {
        this.deleteLoading = true;
        const token = localStorage.getItem('auth_token');
        const res   = await fetch(`${this.apiUrl}/api/agent/documents/${this.deleteTarget.id}`, {
          method:  'DELETE',
          headers: { Authorization: `Bearer ${token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
          this.documents   = this.documents.filter(d => d.id !== this.deleteTarget.id);
          this.showDeleteModal = false;
          this.showToast('Document deleted.', 'success');
        }
      } catch (e) {
        this.showToast('Error deleting document.', 'error');
      } finally {
        this.deleteLoading = false;
      }
    },

    capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; },

    formatDate(d) {
      if (!d) return 'N/A';
      return new Date(d).toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    },

    formatFileSize(bytes) {
      if (!bytes) return '';
      if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
      return (bytes / 1048576).toFixed(1) + ' MB';
    },

    showToast(message, type = 'success') {
      this.toast = { show: true, type, message };
      setTimeout(() => { this.toast.show = false; }, 3500);
    },

    logout() { localStorage.clear(); this.$router.push('/login'); },
  },
};
</script>

<style scoped>
.dashboard-wrapper { display: flex; min-height: 100vh; font-family: 'Inter', sans-serif; background: #f8f9fa; }
.sidebar { width: 240px; min-height: 100vh; background: #fff; border-right: 1px solid #f0f0f0; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 100; }
.sidebar-header { padding: 20px; border-bottom: 1px solid #f5f5f5; }
.sidebar-logo { font-size: 1.2rem; font-weight: 800; }
.logo-realty { color: #100c08; } .logo-ph { color: #e6ae0d; }
.sidebar-nav { flex: 1; padding: 12px 10px; }
.nav-section { margin-top: 16px; }
.section-title { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.08em; color: #bbb; font-weight: 700; padding: 0 10px; margin-bottom: 6px; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 10px; color: #555; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: all 0.15s; }
.nav-item:hover, .nav-item.active { background: #fef9e7; color: #100c08; } .nav-item.active { font-weight: 700; }
.nav-icon { font-size: 16px; width: 20px; text-align: center; }
.sidebar-footer { padding: 12px; border-top: 1px solid #f5f5f5; }
.user-card { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 12px; background: #fafafa; position: relative; }
.user-avatar-lg { width: 36px; height: 36px; border-radius: 50%; background: #e6ae0d; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: #100c08; flex-shrink: 0; }
.user-info { flex: 1; min-width: 0; }
.user-name-card { font-size: 0.82rem; font-weight: 700; color: #100c08; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.user-role-card { font-size: 0.72rem; color: #999; margin: 0; }
.btn-options { background: none; border: none; cursor: pointer; font-size: 18px; color: #999; padding: 0 4px; }
.user-dropdown { position: absolute; bottom: 56px; left: 0; right: 0; background: #fff; border: 1px solid #f0f0f0; border-radius: 10px; padding: 6px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); z-index: 10; }
.dropdown-item { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; color: #333; text-decoration: none; font-size: 0.82rem; cursor: pointer; }
.dropdown-item:hover { background: #fef9e7; } .dropdown-item.logout { color: #dc2626; }

.main-content { margin-left: 240px; flex: 1; display: flex; flex-direction: column; }
.topbar { background: #fff; border-bottom: 1px solid #f0f0f0; position: sticky; top: 0; z-index: 50; }
.topbar-content { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 64px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: #100c08; margin: 0; }
.page-wrapper { padding: 24px; }

.state-box { text-align: center; padding: 60px 20px; }
.state-icon { font-size: 52px; margin-bottom: 16px; }
.state-box h3 { font-size: 1.2rem; font-weight: 700; color: #100c08; margin-bottom: 8px; }
.state-box p { color: #666; font-size: 0.9rem; margin-bottom: 20px; }

.doc-area { display: flex; flex-direction: column; gap: 20px; }
.doc-table-wrap { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.doc-table { width: 100%; border-collapse: collapse; }
.doc-table th { text-align: left; padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.06em; background: #fafafa; border-bottom: 1px solid #f0f0f0; }
.doc-table td { padding: 14px 16px; border-bottom: 1px solid #f9f9f9; font-size: 0.85rem; color: #333; vertical-align: middle; }
.doc-row:last-child td { border-bottom: none; }
.doc-row:hover { background: #fafafa; }
.doc-name-cell { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #100c08; }
.doc-icon { font-size: 18px; }
.buyer-cell { display: flex; align-items: center; gap: 8px; }
.buyer-avatar { width: 28px; height: 28px; border-radius: 50%; background: #e6ae0d; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #100c08; flex-shrink: 0; }
.type-badge { padding: 3px 10px; border-radius: 20px; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 600; }
.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
.badge-pending { background: #fef9c3; color: #854d0e; }
.badge-signed  { background: #dcfce7; color: #166534; }
.date-cell { color: #888; font-size: 0.8rem; }
.actions-cell { display: flex; gap: 6px; align-items: center; }
.btn-action { padding: 6px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-block; }
.btn-view { background: #f3f4f6; color: #374151; } .btn-view:hover { background: #e6ae0d; color: #100c08; }
.btn-delete { background: #fee2e2; color: #dc2626; } .btn-delete:hover { background: #dc2626; color: #fff; }

.sig-section { background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.sig-section-title { font-size: 0.9rem; font-weight: 700; color: #100c08; margin-bottom: 14px; }
.sig-proof-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-top: 1px solid #f0f0f0; flex-wrap: wrap; gap: 10px; }
.sig-proof-row:first-of-type { border-top: none; padding-top: 0; }
.sig-proof-info { font-size: 0.85rem; color: #555; }
.sig-method { font-size: 0.78rem; color: #999; }
.sig-preview { display: flex; align-items: center; gap: 10px; }
.sig-label { font-size: 0.78rem; font-weight: 600; color: #888; }
.sig-img { height: 52px; max-width: 180px; border: 1px solid #e5e7eb; border-radius: 6px; background: #fff; object-fit: contain; padding: 4px; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 20px; }
.modal-box { background: #fff; border-radius: 16px; width: 100%; max-width: 520px; overflow: hidden; }
.modal-sm { max-width: 400px; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #f0f0f0; }
.modal-header h3 { font-size: 1rem; font-weight: 700; color: #100c08; margin: 0; }
.btn-close { background: none; border: none; cursor: pointer; font-size: 18px; color: #999; }
.modal-body { padding: 20px 24px; max-height: 70vh; overflow-y: auto; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f0f0f0; }

.form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.form-group label { font-size: 0.85rem; font-weight: 600; color: #100c08; }
.required { color: #dc2626; }
.form-input, .form-select { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 0.88rem; outline: none; transition: border-color 0.2s; width: 100%; box-sizing: border-box; }
.form-input:focus, .form-select:focus { border-color: #e6ae0d; }
.form-error { font-size: 0.78rem; color: #dc2626; }
.helper-text { padding: 10px 12px; font-size: 0.85rem; color: #888; background: #f8f9fa; border-radius: 8px; }
.helper-text.warn { background: #fef9c3; color: #854d0e; }

.file-drop { border: 2px dashed #e5e7eb; border-radius: 12px; padding: 28px; text-align: center; cursor: pointer; transition: all 0.2s; }
.file-drop:hover { border-color: #e6ae0d; background: #fef9e7; }
.drop-icon { font-size: 32px; display: block; margin-bottom: 8px; }
.file-drop p { margin: 4px 0; font-size: 0.88rem; color: #555; }
.drop-hint { font-size: 0.75rem !important; color: #999 !important; }
.file-selected { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.file-name { font-weight: 600; color: #100c08; font-size: 0.88rem; }
.file-size { font-size: 0.75rem; color: #888; }
.hidden-input { display: none; }

.btn-primary   { padding: 10px 18px; background: #e6ae0d; color: #100c08; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-primary:hover { background: #d4a000; } .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-secondary { padding: 10px 18px; background: #f3f4f6; color: #374151; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-danger    { padding: 10px 18px; background: #dc2626; color: #fff; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── Action button variants ─────────────────────────── */
.btn-download { background: #eff6ff; color: #1d4ed8; }
.btn-download:hover { background: #1d4ed8; color: #fff; }
.btn-sign     { background: #f0fdf4; color: #16a34a; }
.btn-sign:hover { background: #16a34a; color: #fff; }

/* ── Source toggle (Upload PDF / External Link) ─────── */
.source-toggle { display: flex; gap: 8px; }
.src-btn { flex: 1; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 10px; background: #fff; color: #555; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.src-btn:hover { border-color: #e6ae0d; }
.src-btn.active { border-color: #e6ae0d; background: #fef9e7; color: #100c08; }

/* ── Sign modal ─────────────────────────────────────── */
.sign-modal-box { max-width: 600px; }
.sign-modal-subtitle { font-size: 0.8rem; color: #888; margin: 2px 0 0; font-weight: 400; }

.sign-tabs { display: flex; border-bottom: 2px solid #f0f0f0; padding: 0 24px; }
.sign-tab  { padding: 12px 18px; background: none; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; font-size: 0.88rem; font-weight: 600; color: #888; cursor: pointer; transition: all 0.2s; }
.sign-tab.active { color: #100c08; border-bottom-color: #e6ae0d; }
.sign-tab:hover:not(.active) { color: #555; }

.sign-tab-content { padding: 20px 24px; }
.sign-hint { font-size: 0.82rem; color: #666; margin: 0 0 12px; }

/* Canvas drawing area */
.canvas-wrapper { border: 2px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #fff; cursor: crosshair; }
.signature-canvas { display: block; width: 100%; height: auto; touch-action: none; }
.canvas-controls { display: flex; justify-content: flex-end; margin-top: 10px; }
.btn-clear { padding: 7px 14px; background: #f3f4f6; color: #555; border: none; border-radius: 8px; font-size: 0.82rem; font-weight: 600; cursor: pointer; }
.btn-clear:hover { background: #e5e7eb; }

/* Upload photo area */
.upload-area { border: 2px dashed #e5e7eb; border-radius: 12px; padding: 28px; text-align: center; cursor: pointer; transition: all 0.2s; min-height: 150px; display: flex; align-items: center; justify-content: center; }
.upload-area:hover { border-color: #e6ae0d; background: #fef9e7; }
.upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 6px; }
.upload-icon { font-size: 36px; }
.upload-area p { margin: 2px 0; font-size: 0.88rem; color: #555; }
.upload-sub { font-size: 0.75rem !important; color: #999 !important; }
.photo-preview-wrap { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 8px; }
.processed-canvas { display: block; width: 100%; height: auto; border-radius: 8px; }
.preview-note { font-size: 0.78rem; color: #16a34a; font-weight: 600; margin: 0; }

/* Sign error */
.sign-error { font-size: 0.82rem; color: #dc2626; padding: 0 24px 4px; margin: 0; }

/* Signature placement preview */
.preview-section { padding: 20px 24px; }
.preview-label { font-size: 0.85rem; color: #555; margin: 0 0 14px; }
.sig-area-box { border: 2px dashed #d1d5db; border-radius: 12px; background: #fafafa; padding: 20px 24px 0; min-height: 120px; display: flex; flex-direction: column; justify-content: flex-end; }
.sig-area-img { max-height: 90px; max-width: 60%; object-fit: contain; display: block; margin: 0 auto 8px; }
.sig-area-line { display: flex; justify-content: space-between; border-top: 2px solid #374151; padding: 8px 4px 10px; font-size: 0.75rem; color: #666; font-weight: 500; }

/* ── Signature proof section ─────────────────────────── */
.sig-proof-card { background: #fafafa; border: 1px solid #f0f0f0; border-radius: 12px; padding: 16px 18px; margin-bottom: 12px; }
.sig-proof-card:last-child { margin-bottom: 0; }
.sig-proof-docname { font-size: 0.88rem; font-weight: 700; color: #100c08; margin-bottom: 12px; }
.sig-proof-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.sig-col { display: flex; flex-direction: column; gap: 8px; }
.sig-col-label { font-size: 0.75rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.06em; margin: 0; }
.sig-box { border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; padding: 12px; display: flex; flex-direction: column; align-items: center; gap: 6px; min-height: 90px; justify-content: center; }
.sig-box-empty { background: #f9f9f9; color: #aaa; font-size: 0.82rem; flex-direction: column; gap: 8px; }
.sig-box .sig-img { height: 60px; max-width: 100%; object-fit: contain; }
.sig-badge { font-size: 0.72rem; color: #888; font-style: italic; text-align: center; }
.btn-sign-inline { padding: 6px 14px; background: #e6ae0d; color: #100c08; border: none; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer; }
.btn-sign-inline:hover { background: #d4a000; }

.toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 1000; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.toast-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.toast-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px); }
</style>
