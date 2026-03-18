<template>
  <div class="dashboard-wrapper">
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-header">
        <router-link to="/agent/dashboard" class="sidebar-logo">
          RealtyLink<span class="logo-ph">PH</span>
        </router-link>
      </div>

      <nav class="sidebar-nav">
        <router-link to="/agent/dashboard" exact class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          </span>
          <span>Overview</span>
        </router-link>

        <div class="nav-group-label">Properties</div>
        <router-link to="/agent/properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </span>
          <span>My Properties</span>
        </router-link>
        <router-link to="/agent/saved-properties" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </span>
          <span>My Wallet</span>
        </router-link>

        <div class="nav-group-label">Schedule</div>
        <router-link to="/agent/viewings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </span>
          <span>Viewings</span>
        </router-link>
        <router-link to="/agent/calendar" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
          </span>
          <span>Calendar</span>
        </router-link>

        <div class="nav-group-label">Communicate</div>
        <router-link to="/conversations" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </span>
          <span>Messages</span>
        </router-link>
        <router-link to="/agent/documents" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </span>
          <span>Documents</span>
        </router-link>
      </nav>

      <div class="sidebar-bottom">
        <div class="sidebar-divider"></div>
        <router-link to="/profile" class="nav-item nav-user" @click="sidebarOpen = false">
          <div class="nav-av">
            <img v-if="profilePhotoUrl" :src="profilePhotoUrl" :alt="userName" />
            <span v-else>{{ userName.charAt(0).toUpperCase() }}</span>
          </div>
          <div class="nav-user-info">
            <span class="nav-user-name">{{ userName }}</span>
            <span class="nav-user-role">Agent</span>
          </div>
        </router-link>
        <router-link to="/settings" class="nav-item" @click="sidebarOpen = false">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 21v-7M4 10V3M12 21v-9M12 8V3M20 21v-5M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
          </span>
          <span>Settings</span>
        </router-link>
        <button class="nav-item nav-logout" @click="logout">
          <span class="nav-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          </span>
          <span>Logout</span>
        </button>
      </div>
    </aside>

    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false"></div>

    <!-- Main Content -->
    <main class="main-content">
      <nav class="topbar">
        <div class="topbar-content">
          <div class="topbar-left">
            <button class="hamburger-btn" @click="sidebarOpen = !sidebarOpen"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
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

    <!-- My Uploads Section -->
    <section class="my-uploads-section">
      <div class="uploads-header">
        <h2 class="uploads-title">My Uploads</h2>
        <span class="uploads-count">{{ myUploads.length }} file{{ myUploads.length !== 1 ? 's' : '' }}</span>
      </div>
      <div
        class="personal-upload-zone"
        :class="{ 'drag-active': personalDragOver }"
        @dragover.prevent="personalDragOver = true"
        @dragleave.prevent="personalDragOver = false"
        @drop.prevent="handlePersonalDrop"
      >
        <input ref="personalUploadInput" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.webp" style="display:none" @change="handlePersonalUploadInput" />
        <div class="upload-zone-inner" @click="$refs.personalUploadInput.click()">
          <span class="upload-zone-icon">📁</span>
          <h4>{{ personalUploading ? 'Uploading...' : 'Drop files here or click to upload' }}</h4>
          <p>PDF, Word, Excel, Images — Max 20MB</p>
        </div>
      </div>
      <div v-if="myUploads.length > 0" class="my-uploads-list">
        <div v-for="doc in myUploads" :key="'upload-' + doc.id" class="upload-card">
          <span class="upload-card-icon">{{ getFileTypeIcon(doc.mime_type) }}</span>
          <div class="upload-card-info">
            <p class="upload-card-name">{{ doc.document_name }}</p>
            <p class="upload-card-date">{{ formatDate(doc.created_at) }}</p>
          </div>
          <div class="upload-card-actions">
            <a :href="doc.document_url" target="_blank" class="btn-action btn-view">👁 View</a>
            <router-link
              v-if="doc.mime_type && (doc.mime_type === 'application/pdf' || doc.mime_type.startsWith('image/'))"
              :to="`/documents/${doc.id}/edit`"
              class="btn-action btn-edit-doc"
            >✏️ Edit</router-link>
          </div>
        </div>
      </div>
    </section>

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
      <div class="modal-box sign-modal-box" :class="{ 'sign-modal-wide': signPhase === 'position' || signPhase === 'preview' }">
        <div class="modal-header">
          <div>
            <h3>Sign Document</h3>
            <p class="sign-modal-subtitle">{{ signingDoc?.document_name }}</p>
          </div>
          <button @click="closeSignModal" class="btn-close">✕</button>
        </div>

        <!-- Phase indicator -->
        <div class="sign-phases">
          <div class="phase-step" :class="{ active: signPhase === 'position', done: signPhase !== 'position' }">
            <span class="phase-num">1</span>
            <span class="phase-label">Place</span>
          </div>
          <div class="phase-connector"></div>
          <div class="phase-step" :class="{ active: signPhase === 'capture', done: signPhase === 'preview' || signPhase === 'signing' }">
            <span class="phase-num">2</span>
            <span class="phase-label">Create</span>
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
              <div class="spinner-sm"></div>
              <p>Loading document preview...</p>
            </div>

            <div v-else-if="!isSigningPdf" class="non-pdf-notice">
              <span class="notice-icon">📄</span>
              <p>This document is not a PDF. Your signature will be attached separately.</p>
              <button class="btn-primary btn-sm" @click="signPhase = 'capture'; $nextTick(() => initAgentCanvas())">Continue to Signature</button>
            </div>

            <div v-else class="pdf-preview-area">
              <div v-if="totalPages > 1" class="page-nav">
                <button class="page-nav-btn" :disabled="currentPage <= 1" @click="goToPage(currentPage - 1)">&#9664;</button>
                <span class="page-info">Page {{ currentPage }} of {{ totalPages }}</span>
                <button class="page-nav-btn" :disabled="currentPage >= totalPages" @click="goToPage(currentPage + 1)">&#9654;</button>
              </div>

              <div class="pdf-canvas-container" @click="handlePdfClick">
                <canvas ref="agentPdfCanvas" class="pdf-canvas"></canvas>
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
            <button @click="closeSignModal" class="btn-secondary">Cancel</button>
            <button class="btn-primary" :disabled="!positionSelected && isSigningPdf" @click="signPhase = 'capture'; $nextTick(() => initAgentCanvas())">
              Next: Create Signature
            </button>
          </div>
        </template>

        <!-- ── PHASE 2: DRAW / UPLOAD SIGNATURE ── -->
        <template v-else-if="signPhase === 'capture'">
          <div class="sign-tabs">
            <button :class="['sign-tab', { active: signTab === 'draw' }]" @click="switchSignTab('draw')">✏️ Draw</button>
            <button :class="['sign-tab', { active: signTab === 'upload' }]" @click="switchSignTab('upload')">📷 Upload Photo</button>
          </div>

          <div v-if="signTab === 'draw'" class="sign-tab-content">
            <p class="sign-hint">Draw your signature below:</p>
            <div class="canvas-wrapper">
              <canvas ref="agentCanvas" class="signature-canvas" width="520" height="180"
                @mousedown="agentStartDraw" @mousemove="agentDraw" @mouseup="agentStopDraw" @mouseleave="agentStopDraw"
                @touchstart.prevent="agentStartTouch" @touchmove.prevent="agentDrawTouch" @touchend.prevent="agentStopDraw">
              </canvas>
            </div>
            <div class="canvas-controls">
              <button class="btn-clear" @click="clearAgentCanvas">Clear</button>
            </div>
          </div>

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
                <p class="preview-note">Background removed</p>
              </div>
            </div>
            <div v-if="agentPhotoPreview" class="canvas-controls">
              <button class="btn-clear" @click="clearAgentPhoto">Remove Photo</button>
            </div>
          </div>

          <p v-if="signError" class="sign-error">{{ signError }}</p>

          <div class="modal-footer">
            <button @click="signPhase = 'position'" class="btn-secondary">Back</button>
            <button @click="previewSignature" class="btn-primary">Preview Signature</button>
          </div>
        </template>

        <!-- ── PHASE 3: PREVIEW WITH SIGNATURE ON PDF ── -->
        <template v-else-if="signPhase === 'preview'">
          <div class="sign-tab-content">
            <p class="sign-hint">Review your signature placement. Click "Confirm & Sign" to finalize.</p>
            <div class="preview-document-area">
              <div class="pdf-canvas-container" v-if="isSigningPdf">
                <canvas ref="previewPdfCanvas" class="pdf-canvas"></canvas>
                <img
                  v-if="agentSignaturePreview"
                  :src="agentSignaturePreview"
                  class="sig-overlay-img"
                  :style="sigOverlayStyle"
                  alt="Your signature"
                />
              </div>
              <div v-else class="sig-area-box">
                <img :src="agentSignaturePreview" class="sig-area-img" alt="Your signature preview" />
                <div class="sig-area-line">
                  <span>Agent Signature</span>
                  <span>{{ new Date().toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                </div>
              </div>
            </div>
          </div>

          <p v-if="signError" class="sign-error">{{ signError }}</p>

          <div class="modal-footer">
            <button @click="signPhase = 'capture'" class="btn-secondary" :disabled="agentSigning">Back</button>
            <button @click="submitAgentSignature" class="btn-primary" :disabled="agentSigning">
              {{ agentSigning ? 'Signing & Sending...' : 'Confirm & Sign Document' }}
            </button>
          </div>
        </template>

        <!-- ── SIGNING IN PROGRESS ── -->
        <template v-else-if="signPhase === 'signing'">
          <div class="signing-progress">
            <div class="spinner-sm spinner-lg"></div>
            <h3>Embedding signature into document...</h3>
            <p>The signed document will be sent automatically.</p>
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
import { markRaw } from 'vue';

export default {
  name: 'AgentDocumentsPage',

  data() {
    return {
      sidebarOpen: false,
      loading: true,
      documents: [],
      buyers: [],
      buyersLoading: false,
      userName: '',
      showUserMenu: false,
      profilePhotoUrl: null,
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
      signPhase: 'position',   // 'position' | 'capture' | 'preview' | 'signing'
      signTab: 'draw',
      signError: '',
      agentSigning: false,
      agentIsDrawing: false,
      agentLastX: 0,
      agentLastY: 0,
      agentHasDrawn: false,
      agentPhotoPreview: false,
      agentPhotoDataUrl: null,
      agentSignaturePreview: null,

      // PDF preview
      pdfDoc: null,
      totalPages: 1,
      currentPage: 1,
      pdfLoading: false,
      pdfPageWidth: 0,
      pdfPageHeight: 0,

      // Signature position
      signaturePosition: { x: 0, y: 0, page: 1 },
      canvasDisplayWidth: 0,
      canvasDisplayHeight: 0,
      positionSelected: false,

      toast: { show: false, type: 'success', message: '' },

      // My Uploads
      myUploads: [],
      personalDragOver: false,
      personalUploading: false,
    };
  },

  computed: {
    signedDocs() {
      return this.documents.filter(d => d.status === 'signed' && d.signature_url);
    },
    sigProofDocs() {
      return this.documents.filter(d => d.agent_signature_url || d.signature_url);
    },
    isSigningPdf() {
      if (!this.signingDoc) return false;
      const url = (this.signingDoc.document_url || '').toLowerCase();
      return url.endsWith('.pdf') || (this.signingDoc.mime_type || '').includes('pdf');
    },
    sigPlaceholderStyle() {
      if (!this.positionSelected || !this.canvasDisplayWidth) return {};
      const wPct = 25;
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

  async mounted() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    this.userName = user.name || 'Agent';
    this.token = localStorage.getItem('auth_token') || '';
    this.loadProfilePhoto();
    await this.loadDocuments();
    this.loadMyUploads();
    this.clearDocumentNotifications();
  },

  methods: {
    async clearDocumentNotifications() {
      try {
        const token = localStorage.getItem('auth_token');
        await fetch(`${this.apiUrl}/api/notifications/mark-type-read`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json', Accept: 'application/json' },
          body: JSON.stringify({ type: 'document' }),
        });
      } catch (e) { /* silent */ }
    },

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
      this.signingDoc            = doc;
      this.signPhase             = 'position';
      this.signTab               = 'draw';
      this.signError             = '';
      this.agentHasDrawn         = false;
      this.agentPhotoPreview     = false;
      this.agentPhotoDataUrl     = null;
      this.agentSignaturePreview = null;
      this.positionSelected      = false;
      this.signaturePosition     = { x: 0, y: 0, page: 1 };
      this.pdfDoc                = null;
      this.totalPages            = 1;
      this.currentPage           = 1;
      this.showSignModal         = true;

      this.$nextTick(() => {
        const url = (doc.document_url || '').toLowerCase();
        const isPdf = url.endsWith('.pdf') || (doc.mime_type || '').includes('pdf');
        if (isPdf && doc.document_url) {
          this.loadPdfPreview(doc.document_url);
        }
      });
    },

    closeSignModal() {
      if (this.agentSigning) return;
      this.showSignModal = false;
      this.signingDoc    = null;
      this.pdfDoc        = null;
    },

    switchSignTab(tab) {
      this.signTab   = tab;
      this.signError = '';
      if (tab === 'draw') this.$nextTick(() => this.initAgentCanvas());
    },

    // ─── PDF Preview Methods ──────────────────────────
    async loadPdfPreview(url) {
      this.pdfLoading = true;
      try {
        if (!window.pdfjsLib) {
          await this.loadPdfJsScript();
        }
        const pdfjsLib = window.pdfjsLib;
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

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

      const canvas = this.$refs.agentPdfCanvas;
      if (!canvas) return;
      canvas.width  = viewport.width;
      canvas.height = viewport.height;

      this.pdfPageWidth  = viewport.width;
      this.pdfPageHeight = viewport.height;

      const ctx = canvas.getContext('2d');
      await page.render({ canvasContext: ctx, viewport }).promise;

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
      const canvas = this.$refs.agentPdfCanvas;
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
      this.signPhase = 'preview';

      if (this.isSigningPdf && this.pdfDoc) {
        this.$nextTick(() => this.renderPreviewPage());
      }
    },

    async submitAgentSignature() {
      this.signError    = '';
      this.agentSigning = true;
      this.signPhase    = 'signing';
      try {
        const scaleX = this.canvasDisplayWidth ? (this.pdfPageWidth / this.canvasDisplayWidth) : 1;
        const scaleY = this.canvasDisplayHeight ? (this.pdfPageHeight / this.canvasDisplayHeight) : 1;
        const pdfX = this.signaturePosition.x * scaleX;
        const pdfY = this.signaturePosition.y * scaleY;

        const res = await fetch(`${this.apiUrl}/api/agent/documents/${this.signingDoc.id}/sign`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json' },
          body: JSON.stringify({
            signature_data: this.agentSignaturePreview,
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
          this.signError = data.message || 'Failed to sign.';
          this.signPhase = 'preview';
          return;
        }

        const idx = this.documents.findIndex(d => d.id === this.signingDoc.id);
        if (idx !== -1 && data.document) this.documents[idx] = data.document;

        this.showSignModal = false;
        this.pdfDoc = null;
        this.showToast('Document signed and sent to buyer!', 'success');
      } catch (e) {
        this.signError = 'Network error. Please try again.';
        this.signPhase = 'preview';
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

    // ─── My Uploads ─────────────────────────────────
    async loadMyUploads() {
      try {
        const res = await fetch(`${this.apiUrl}/api/documents/my`, {
          headers: { Authorization: `Bearer ${this.token}`, Accept: 'application/json' },
        });
        const data = await res.json();
        if (data.success) this.myUploads = data.documents || [];
      } catch (e) { /* silent */ }
    },
    handlePersonalUploadInput(e) {
      const file = e.target.files[0];
      if (file) this.uploadPersonalFile(file);
      e.target.value = '';
    },
    handlePersonalDrop(e) {
      this.personalDragOver = false;
      const file = e.dataTransfer.files[0];
      if (file) this.uploadPersonalFile(file);
    },
    async uploadPersonalFile(file) {
      this.personalUploading = true;
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
        this.personalUploading = false;
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

    loadProfilePhoto() {
      try {
        const user = JSON.parse(localStorage.getItem('user') || '{}');
        if (user.profile_photo_path) {
          this.profilePhotoUrl = `${window.__API_URL__ || 'http://localhost:8000'}/storage/${user.profile_photo_path}`;
        }
      } catch (e) { /* non-critical */ }
    },

    logout() { localStorage.clear(); this.$router.push('/'); },
  },
};
</script>

<style scoped>
.dashboard-wrapper {
  display: flex; min-height: 100vh; font-family: 'Inter', sans-serif; background: var(--bg);
  --navy:      #0B1C39;
  --navy2:     #102445;
  --navy3:     #1a3158;
  --gold:      #D89B0F;
  --gold2:     #E5B332;
  --gold3:     #B07A08;
  --gold-glow: rgba(216,155,15,0.12);
  --s50:  #FAFAF9; --s100: #F5F5F4; --s200: #E7E5E4;
  --s300: #D6D3D1; --s400: #A8A29E; --s500: #78716C;
  --s600: #57534E; --s700: #44403C; --s800: #292524; --s900: #1C1917;
  --white: #FFFFFF;
  --bg:   #F2F0EB;
  --sw:   242px;
  --th:   56px;
  --r:    12px;
  --fd:   'Outfit','Inter',-apple-system,sans-serif;
  --fb:   'Inter',-apple-system,sans-serif;
}

/* ── SIDEBAR ── */
.sidebar {
  position: fixed; top: 0; left: 0; bottom: 0; width: var(--sw);
  background: var(--navy); display: flex; flex-direction: column; z-index: 100;
}
.sidebar-header { padding: 22px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); flex-shrink: 0; }
.sidebar-logo { font-family: var(--fd); font-size: 18px; font-weight: 800; color: #fff; text-decoration: none; letter-spacing: -0.4px; display: block; }
.logo-ph { background: linear-gradient(135deg, var(--gold), var(--gold2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.sidebar-nav { flex: 1; overflow-y: auto; padding: 10px 10px 4px; scrollbar-width: none; }
.sidebar-nav::-webkit-scrollbar { display: none; }
.nav-item {
  display: flex; align-items: center; gap: 10px; padding: 9px 12px 9px 10px;
  border-radius: 9px; color: rgba(255,255,255,0.45); text-decoration: none;
  font-size: 13px; font-weight: 500; transition: all 0.18s ease; margin-bottom: 2px;
  cursor: pointer; border: none; background: none; width: 100%;
  text-align: left; font-family: var(--fb); position: relative;
}
.nav-item:hover { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.78); }
.nav-item.router-link-active:not(.router-link-exact-active) { color: rgba(255,255,255,0.65); }
.nav-item.router-link-exact-active {
  background: linear-gradient(90deg, rgba(216,155,15,0.14) 0%, rgba(216,155,15,0.03) 100%);
  color: var(--gold2);
}
.nav-item.router-link-exact-active::before {
  content: ''; position: absolute; left: 0; top: 7px; bottom: 7px;
  width: 3px; background: var(--gold); border-radius: 0 3px 3px 0;
}
.nav-item.router-link-exact-active .nav-icon-wrap { color: var(--gold); }
.nav-group-label {
  font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px;
  color: rgba(255,255,255,0.22); padding: 14px 12px 5px; margin: 0;
}
.nav-icon-wrap { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.nav-icon-wrap svg { width: 18px; height: 18px; }
.nav-badge {
  margin-left: auto; background: rgba(216,155,15,0.18); color: var(--gold2);
  border-radius: 99px; padding: 2px 7px; font-size: 10.5px; font-weight: 700;
}
.sidebar-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 4px 10px 8px; }
.sidebar-bottom { flex-shrink: 0; padding: 0 10px 14px; }
.nav-user { gap: 10px; padding: 8px 10px; }
.nav-user:hover, .nav-user.router-link-exact-active { background: rgba(216,155,15,0.10); color: var(--gold2); }
.nav-av {
  width: 28px; height: 28px; border-radius: 50%; overflow: hidden; flex-shrink: 0;
  background: linear-gradient(135deg, var(--gold), var(--gold3));
  display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; color: #fff;
}
.nav-av img { width: 100%; height: 100%; object-fit: cover; }
.nav-user-info { flex: 1; min-width: 0; }
.nav-user-name { display: block; font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,0.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.nav-user-role  { display: block; font-size: 10.5px; color: rgba(255,255,255,0.38); margin-top: 1px; }
.nav-logout { color: rgba(255,255,255,0.30); }
.nav-logout:hover { background: rgba(239,68,68,0.10); color: #f87171; }
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 90; backdrop-filter: blur(2px); }

.main-content { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; }
.topbar { background: #fff; border-bottom: 1px solid #f0f0f0; position: sticky; top: 0; z-index: 50; }
.topbar-content { display: flex; align-items: center; justify-content: space-between; padding: 0 24px; height: 64px; }
.page-title { font-size: 1.1rem; font-weight: 700; color: #0B1C39; margin: 0; }
.page-wrapper { padding: 24px; }

.state-box { text-align: center; padding: 60px 20px; }
.state-icon { font-size: 52px; margin-bottom: 16px; }
.state-box h3 { font-size: 1.2rem; font-weight: 700; color: #0B1C39; margin-bottom: 8px; }
.state-box p { color: #666; font-size: 0.9rem; margin-bottom: 20px; }

.doc-area { display: flex; flex-direction: column; gap: 20px; }
.doc-table-wrap { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.doc-table { width: 100%; border-collapse: collapse; }
.doc-table th { text-align: left; padding: 12px 16px; font-size: 0.75rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.06em; background: #fafafa; border-bottom: 1px solid #f0f0f0; }
.doc-table td { padding: 14px 16px; border-bottom: 1px solid #f9f9f9; font-size: 0.85rem; color: #333; vertical-align: middle; }
.doc-row:last-child td { border-bottom: none; }
.doc-row:hover { background: #fafafa; }
.doc-name-cell { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #0B1C39; }
.doc-icon { font-size: 18px; }
.buyer-cell { display: flex; align-items: center; gap: 8px; }
.buyer-avatar { width: 28px; height: 28px; border-radius: 50%; background: #D89B0F; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; color: #0B1C39; flex-shrink: 0; }
.type-badge { padding: 3px 10px; border-radius: 20px; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 600; }
.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
.badge-pending { background: #fef9c3; color: #854d0e; }
.badge-signed  { background: #dcfce7; color: #166534; }
.date-cell { color: #888; font-size: 0.8rem; }
.actions-cell { display: flex; gap: 6px; align-items: center; }
.btn-action { padding: 6px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-block; }
.btn-view { background: #f3f4f6; color: #374151; } .btn-view:hover { background: #D89B0F; color: #0B1C39; }
.btn-delete { background: #fee2e2; color: #dc2626; } .btn-delete:hover { background: #dc2626; color: #fff; }

.sig-section { background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.sig-section-title { font-size: 0.9rem; font-weight: 700; color: #0B1C39; margin-bottom: 14px; }
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
.modal-header h3 { font-size: 1rem; font-weight: 700; color: #0B1C39; margin: 0; }
.btn-close { background: none; border: none; cursor: pointer; font-size: 18px; color: #999; }
.modal-body { padding: 20px 24px; max-height: 70vh; overflow-y: auto; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #f0f0f0; }

.form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.form-group label { font-size: 0.85rem; font-weight: 600; color: #0B1C39; }
.required { color: #dc2626; }
.form-input, .form-select { border: 1.5px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; font-size: 0.88rem; outline: none; transition: border-color 0.2s; width: 100%; box-sizing: border-box; }
.form-input:focus, .form-select:focus { border-color: #D89B0F; }
.form-error { font-size: 0.78rem; color: #dc2626; }
.helper-text { padding: 10px 12px; font-size: 0.85rem; color: #888; background: #f8f9fa; border-radius: 8px; }
.helper-text.warn { background: #fef9c3; color: #854d0e; }

.file-drop { border: 2px dashed #e5e7eb; border-radius: 12px; padding: 28px; text-align: center; cursor: pointer; transition: all 0.2s; }
.file-drop:hover { border-color: #D89B0F; background: #fef9e7; }
.drop-icon { font-size: 32px; display: block; margin-bottom: 8px; }
.file-drop p { margin: 4px 0; font-size: 0.88rem; color: #555; }
.drop-hint { font-size: 0.75rem !important; color: #999 !important; }
.file-selected { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.file-name { font-weight: 600; color: #0B1C39; font-size: 0.88rem; }
.file-size { font-size: 0.75rem; color: #888; }
.hidden-input { display: none; }

.btn-primary   { padding: 10px 18px; background: #D89B0F; color: #0B1C39; border: none; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; }
.btn-primary:hover { background: #B07A08; } .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
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
.src-btn:hover { border-color: #D89B0F; }
.src-btn.active { border-color: #D89B0F; background: #fef9e7; color: #0B1C39; }

/* ── Sign modal ─────────────────────────────────────── */
.sign-modal-box { max-width: 600px; max-height: 90vh; overflow-y: auto; }
.sign-modal-wide { max-width: 800px; }
.sign-modal-subtitle { font-size: 0.8rem; color: #888; margin: 2px 0 0; font-weight: 400; }

/* Phase indicator */
.sign-phases { display: flex; align-items: center; justify-content: center; gap: 0; padding: 16px 24px 10px; }
.phase-step { display: flex; align-items: center; gap: 5px; opacity: 0.4; transition: opacity 0.2s; }
.phase-step.active { opacity: 1; }
.phase-step.done { opacity: 0.7; }
.phase-num { width: 22px; height: 22px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #666; }
.phase-step.active .phase-num { background: #D89B0F; color: #0B1C39; }
.phase-step.done .phase-num { background: #16a34a; color: #fff; }
.phase-label { font-size: 11px; font-weight: 600; color: #333; }
.phase-connector { width: 28px; height: 2px; background: #e5e7eb; margin: 0 6px; }

/* PDF Preview */
.pdf-loading { text-align: center; padding: 40px 20px; color: #666; }
.spinner-sm { width: 36px; height: 36px; border: 3px solid #e5e7eb; border-top-color: #D89B0F; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 12px; }
@keyframes spin { to { transform: rotate(360deg); } }
.pdf-preview-area { }
.page-nav { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 10px; }
.page-nav-btn { width: 30px; height: 30px; border-radius: 8px; border: 1px solid #e5e7eb; background: #fff; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; }
.page-nav-btn:hover:not(:disabled) { background: #fef9e7; border-color: #D89B0F; }
.page-nav-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-info { font-size: 12px; font-weight: 600; color: #333; }
.pdf-canvas-container { position: relative; border: 2px solid #e5e7eb; border-radius: 8px; overflow: hidden; cursor: crosshair; background: #f5f5f5; }
.pdf-canvas { display: block; width: 100%; height: auto; }
.sig-placeholder { position: absolute; border: 2px dashed #D89B0F; background: rgba(230,174,13,0.08); border-radius: 4px; display: flex; align-items: center; justify-content: center; pointer-events: none; transition: all 0.15s ease; }
.sig-placeholder-text { font-size: 10px; font-weight: 700; color: #D89B0F; text-transform: uppercase; letter-spacing: 0.5px; }

/* Non-PDF notice */
.non-pdf-notice { text-align: center; padding: 40px 20px; }
.notice-icon { font-size: 48px; display: block; margin-bottom: 12px; }
.non-pdf-notice p { color: #666; font-size: 13px; margin: 0 0 14px; }
.btn-sm { padding: 7px 16px; font-size: 12px; }

/* Preview with overlay */
.preview-document-area { position: relative; }
.sig-overlay-img { pointer-events: none; }

/* Signing progress */
.signing-progress { text-align: center; padding: 50px 24px; }
.signing-progress h3 { font-size: 16px; font-weight: 700; color: #0B1C39; margin: 14px 0 6px; }
.signing-progress p { font-size: 13px; color: #666; margin: 0; }
.spinner-lg { width: 44px; height: 44px; }

.sign-tabs { display: flex; border-bottom: 2px solid #f0f0f0; padding: 0 24px; }
.sign-tab  { padding: 12px 18px; background: none; border: none; border-bottom: 3px solid transparent; margin-bottom: -2px; font-size: 0.88rem; font-weight: 600; color: #888; cursor: pointer; transition: all 0.2s; }
.sign-tab.active { color: #0B1C39; border-bottom-color: #D89B0F; }
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
.upload-area:hover { border-color: #D89B0F; background: #fef9e7; }
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
.sig-proof-docname { font-size: 0.88rem; font-weight: 700; color: #0B1C39; margin-bottom: 12px; }
.sig-proof-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.sig-col { display: flex; flex-direction: column; gap: 8px; }
.sig-col-label { font-size: 0.75rem; font-weight: 700; color: #888; text-transform: uppercase; letter-spacing: 0.06em; margin: 0; }
.sig-box { border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; padding: 12px; display: flex; flex-direction: column; align-items: center; gap: 6px; min-height: 90px; justify-content: center; }
.sig-box-empty { background: #f9f9f9; color: #aaa; font-size: 0.82rem; flex-direction: column; gap: 8px; }
.sig-box .sig-img { height: 60px; max-width: 100%; object-fit: contain; }
.sig-badge { font-size: 0.72rem; color: #888; font-style: italic; text-align: center; }
.btn-sign-inline { padding: 6px 14px; background: #D89B0F; color: #0B1C39; border: none; border-radius: 8px; font-size: 0.78rem; font-weight: 700; cursor: pointer; }
.btn-sign-inline:hover { background: #B07A08; }

.toast { position: fixed; bottom: 24px; right: 24px; padding: 14px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; z-index: 1000; box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
.toast-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
.toast-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.slide-up-enter-active, .slide-up-leave-active { transition: all 0.3s ease; }
.slide-up-enter-from, .slide-up-leave-to { opacity: 0; transform: translateY(20px); }

/* My Uploads Section */
.my-uploads-section { margin: 0 28px 28px; }
.uploads-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.uploads-title { font-size: 18px; font-weight: 700; color: #0B1C39; margin: 0; }
.uploads-count { font-size: 13px; color: #999; background: #f5f5f5; padding: 4px 12px; border-radius: 20px; }
.personal-upload-zone { border: 2px dashed #D89B0F; border-radius: 14px; padding: 32px; text-align: center; cursor: pointer; transition: all 0.2s; background: #fffdf5; margin-bottom: 16px; }
.personal-upload-zone:hover, .personal-upload-zone.drag-active { background: #fdf5d0; border-color: #B07A08; }
.upload-zone-icon { font-size: 40px; display: block; margin-bottom: 8px; }
.upload-zone-inner h4 { font-size: 15px; font-weight: 700; color: #0B1C39; margin: 0 0 4px; }
.upload-zone-inner p { font-size: 12px; color: #888; margin: 0; }
.my-uploads-list { display: flex; flex-direction: column; gap: 10px; }
.upload-card { display: flex; align-items: center; gap: 14px; background: #fff; border: 1px solid #f0f0f0; border-radius: 12px; padding: 14px 18px; transition: box-shadow 0.2s; }
.upload-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.upload-card-icon { font-size: 28px; flex-shrink: 0; }
.upload-card-info { flex: 1; min-width: 0; }
.upload-card-name { font-size: 14px; font-weight: 600; color: #0B1C39; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.upload-card-date { font-size: 12px; color: #999; margin: 2px 0 0; }
.upload-card-actions { display: flex; gap: 8px; flex-shrink: 0; }
.btn-edit-doc { background: #fff3e0; color: #e65100; }
.btn-edit-doc:hover { background: #e65100; color: #fff; }

/* ===== Responsive ===== */
@media (max-width: 768px) {
  .sidebar { position: fixed; left: calc(-1 * var(--sw)); width: var(--sw); z-index: 1001; transition: left 0.3s ease; }
  .main-content { margin-left: 0 !important; }
  .topbar-content { flex-direction: column; align-items: flex-start; height: auto; padding: 12px 16px; gap: 10px; }
  .topbar-right { flex-wrap: wrap; width: 100%; gap: 8px; }
  .filter-tabs { flex-wrap: wrap; gap: 4px; }
  .filter-tab { padding: 5px 10px; font-size: 0.75rem; }
  .docs-grid { grid-template-columns: 1fr; gap: 14px; }
  .content-area { padding: 16px; }
  .my-uploads-section { margin: 0 16px 16px; }
  .upload-card { flex-wrap: wrap; padding: 12px; }
  .modal-box { max-width: 95vw; }
  .toast { left: 16px; right: 16px; bottom: 16px; text-align: center; }
}

@media (max-width: 480px) {
  .page-title { font-size: 18px; }
  .content-area { padding: 10px; }
  .my-uploads-section { margin: 0 10px 10px; }
  .doc-card { border-radius: 12px; }
  .doc-body { padding: 14px; }
  .doc-actions { flex-direction: column; }
  .doc-actions button,
  .doc-actions a { width: 100%; justify-content: center; }
  .personal-upload-zone { padding: 20px; }
  .empty-state { padding: 40px 16px; }
}
</style>
