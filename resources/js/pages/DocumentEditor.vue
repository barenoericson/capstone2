<template>
  <div class="editor-wrapper">
    <!-- Toolbar -->
    <div class="editor-toolbar">
      <button class="btn-back-editor" @click="$router.back()">← Back</button>
      <h3 class="editor-doc-name">{{ document?.document_name || 'Document Editor' }}</h3>
      <div class="toolbar-tools">
        <button :class="['tool-btn', { active: activeTool === 'select' }]" @click="activeTool = 'select'" title="Select">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 2l7.5 18L13 13l7-2.5z"/></svg>
        </button>
        <button :class="['tool-btn', { active: activeTool === 'text' }]" @click="activeTool = 'text'" title="Add Text">T</button>
        <button :class="['tool-btn', { active: activeTool === 'draw' }]" @click="activeTool = 'draw'" title="Draw">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/></svg>
        </button>
        <button :class="['tool-btn', { active: activeTool === 'highlight' }]" @click="activeTool = 'highlight'" title="Highlight">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" fill="rgba(230,174,13,0.3)"/></svg>
        </button>
        <button :class="['tool-btn', { active: activeTool === 'signature' }]" @click="openSignatureForPlacement" title="Place Signature">
          ✍️ Signature
        </button>
        <span class="toolbar-divider"></span>
        <button class="tool-btn btn-undo" @click="undoAnnotation" title="Undo last" :disabled="annotations.length === 0">↩</button>
        <button class="tool-btn btn-save" @click="saveAnnotations" :disabled="saving">
          {{ saving ? 'Saving...' : '💾 Save' }}
        </button>
        <button class="tool-btn btn-export" @click="exportDocument" :disabled="exporting">
          {{ exporting ? 'Exporting...' : '📤 Export' }}
        </button>
      </div>
    </div>

    <!-- Page navigation -->
    <div class="page-nav" v-if="totalPages > 1">
      <button @click="goToPage(currentPage - 1)" :disabled="currentPage <= 1">← Prev</button>
      <span>Page {{ currentPage }} / {{ totalPages }}</span>
      <button @click="goToPage(currentPage + 1)" :disabled="currentPage >= totalPages">Next →</button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="editor-loading">
      <div class="editor-spinner"></div>
      <p>Loading document...</p>
    </div>

    <!-- Unsupported format -->
    <div v-else-if="unsupported" class="editor-unsupported">
      <div class="unsupported-icon">📋</div>
      <h3>Preview not available</h3>
      <p>This file type ({{ document?.mime_type }}) cannot be edited in the browser.</p>
      <p>You can download it and edit with a desktop application.</p>
      <a v-if="document?.document_url" :href="document.document_url" target="_blank" class="btn-download-fallback">⬇️ Download File</a>
    </div>

    <!-- Canvas area -->
    <div v-else class="editor-canvas-area" ref="canvasArea">
      <div class="canvas-container" ref="canvasContainer">
        <canvas ref="pageCanvas" class="page-canvas"></canvas>
        <canvas
          ref="annotationCanvas"
          class="annotation-canvas"
          :class="{ 'cursor-crosshair': activeTool === 'draw' || activeTool === 'highlight', 'cursor-text': activeTool === 'text', 'cursor-signature': activeTool === 'signature' }"
          @mousedown="onCanvasMouseDown"
          @mousemove="onCanvasMouseMove"
          @mouseup="onCanvasMouseUp"
          @mouseleave="onCanvasMouseUp"
        ></canvas>
        <textarea
          v-if="textInput.visible"
          v-model="textInput.value"
          class="floating-text-input"
          :style="{ left: textInput.screenX + 'px', top: textInput.screenY + 'px' }"
          @blur="commitTextAnnotation"
          @keydown.enter.ctrl="commitTextAnnotation"
          ref="floatingInput"
          placeholder="Type here..."
        ></textarea>
      </div>
    </div>

    <!-- Signature Modal -->
    <div v-if="showSignatureModal" class="modal-overlay" @click.self="showSignatureModal = false">
      <div class="modal-box">
        <div class="modal-header">
          <h3>Draw Your Signature</h3>
          <button class="modal-close" @click="showSignatureModal = false">✕</button>
        </div>
        <div class="modal-body">
          <div class="sign-tabs">
            <button :class="['sign-tab', { active: sigTab === 'draw' }]" @click="switchSigTab('draw')">✏️ Draw</button>
            <button :class="['sign-tab', { active: sigTab === 'upload' }]" @click="switchSigTab('upload')">📷 Upload</button>
          </div>
          <div v-if="sigTab === 'draw'" class="sig-content">
            <div class="sig-canvas-wrap">
              <canvas
                ref="sigCanvas"
                class="sig-canvas"
                width="480"
                height="160"
                @mousedown="sigStartDraw"
                @mousemove="sigDraw"
                @mouseup="sigStopDraw"
                @mouseleave="sigStopDraw"
                @touchstart.prevent="sigStartTouch"
                @touchmove.prevent="sigDrawTouch"
                @touchend.prevent="sigStopDraw"
              ></canvas>
            </div>
            <button class="btn-clear-sig" @click="clearSigCanvas">🗑️ Clear</button>
          </div>
          <div v-if="sigTab === 'upload'" class="sig-content">
            <div class="sig-upload-area" @click="$refs.sigPhotoInput.click()">
              <input ref="sigPhotoInput" type="file" accept="image/*" style="display:none" @change="handleSigPhotoUpload" />
              <div v-if="!sigPhotoDataUrl" class="sig-upload-placeholder">
                <span>📷</span>
                <p>Click to upload signature photo</p>
              </div>
              <div v-else class="sig-photo-preview">
                <canvas ref="sigProcessedCanvas" width="480" height="160" class="sig-processed"></canvas>
                <p class="sig-preview-note">✅ Background removed</p>
              </div>
            </div>
            <button v-if="sigPhotoDataUrl" class="btn-clear-sig" @click="clearSigPhoto">🗑️ Remove</button>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn-cancel-modal" @click="showSignatureModal = false">Cancel</button>
          <button class="btn-confirm-sig" @click="confirmSignature">Use This Signature</button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <transition name="toast-slide">
      <div v-if="toast.show" class="toast" :class="'toast-' + toast.type">{{ toast.message }}</div>
    </transition>
  </div>
</template>

<script>
import { markRaw } from 'vue';

export default {
  name: 'DocumentEditor',

  data() {
    return {
      apiUrl: localStorage.getItem('api_url') || '',
      token: localStorage.getItem('auth_token') || '',
      document: null,
      loading: true,
      saving: false,
      exporting: false,
      unsupported: false,

      // PDF rendering
      pdfDoc: null,
      totalPages: 0,
      currentPage: 1,
      pageScale: 1.5,

      // Annotations
      annotations: [],
      activeTool: 'select',
      isDrawing: false,
      currentPath: [],
      highlightStart: null,
      drawColor: '#100c08',
      highlightColor: 'rgba(230,174,13,0.35)',

      // Text input
      textInput: { visible: false, x: 0, y: 0, screenX: 0, screenY: 0, value: '' },

      // Signature
      showSignatureModal: false,
      savedSignatureDataUrl: null,
      sigTab: 'draw',
      sigIsDrawing: false,
      sigLastX: 0,
      sigLastY: 0,
      sigHasDrawn: false,
      sigPhotoDataUrl: null,

      // Preloaded signature images for redraw
      sigImages: {},

      toast: { show: false, message: '', type: 'success' },
    };
  },

  mounted() {
    this.loadDocument();
  },

  methods: {
    // === Load document ===
    async loadDocument() {
      this.loading = true;
      try {
        const res = await fetch(`${this.apiUrl}/api/documents/my`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        const data = await res.json();
        const docId = parseInt(this.$route.params.id);
        this.document = (data.documents || []).find(d => d.id === docId);

        if (!this.document) {
          this.unsupported = true;
          return;
        }

        if (this.document.annotations) {
          this.annotations = this.document.annotations;
        }

        await this.renderDocument();
      } catch (e) {
        console.error(e);
        this.unsupported = true;
      } finally {
        this.loading = false;
      }
    },

    // === Render document ===
    async renderDocument() {
      const url = this.document.document_url;
      const mime = this.document.mime_type || '';

      if (mime === 'application/pdf') {
        await this.loadPdfJs();
        try {
          this.pdfDoc = markRaw(await window.pdfjsLib.getDocument(url).promise);
          this.totalPages = this.pdfDoc.numPages;
          await this.renderPage(1);
        } catch (e) {
          console.error('PDF load error', e);
          this.unsupported = true;
        }
      } else if (mime.startsWith('image/')) {
        this.totalPages = 1;
        this.currentPage = 1;
        await this.renderImage(url);
      } else {
        this.unsupported = true;
      }
    },

    async loadPdfJs() {
      if (window.pdfjsLib) return;
      await this.loadScript('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
      window.pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    },

    loadScript(src) {
      return new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = src;
        s.onload = resolve;
        s.onerror = reject;
        document.head.appendChild(s);
      });
    },

    async renderPage(pageNum) {
      this.currentPage = pageNum;
      const page = await this.pdfDoc.getPage(pageNum);
      const viewport = page.getViewport({ scale: this.pageScale });
      const pc = this.$refs.pageCanvas;
      const ac = this.$refs.annotationCanvas;

      pc.width = viewport.width;
      pc.height = viewport.height;
      ac.width = viewport.width;
      ac.height = viewport.height;

      await page.render({ canvasContext: pc.getContext('2d'), viewport }).promise;
      this.redrawAnnotations();
    },

    async renderImage(url) {
      return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => {
          const maxW = 900;
          const scale = img.width > maxW ? maxW / img.width : 1;
          const w = img.width * scale;
          const h = img.height * scale;

          const pc = this.$refs.pageCanvas;
          const ac = this.$refs.annotationCanvas;
          pc.width = w; pc.height = h;
          ac.width = w; ac.height = h;
          pc.getContext('2d').drawImage(img, 0, 0, w, h);
          this.redrawAnnotations();
          resolve();
        };
        img.onerror = () => { this.unsupported = true; resolve(); };
        img.src = url;
      });
    },

    goToPage(n) {
      if (n < 1 || n > this.totalPages) return;
      this.renderPage(n);
    },

    // === Canvas events ===
    getCanvasPos(e) {
      const c = this.$refs.annotationCanvas;
      const r = c.getBoundingClientRect();
      return {
        x: (e.clientX - r.left) * (c.width / r.width),
        y: (e.clientY - r.top) * (c.height / r.height),
        screenX: e.clientX - r.left,
        screenY: e.clientY - r.top,
      };
    },

    onCanvasMouseDown(e) {
      const pos = this.getCanvasPos(e);

      if (this.activeTool === 'draw') {
        this.isDrawing = true;
        this.currentPath = [[pos.x, pos.y]];
      } else if (this.activeTool === 'text') {
        this.textInput = { visible: true, x: pos.x, y: pos.y, screenX: pos.screenX, screenY: pos.screenY, value: '' };
        this.$nextTick(() => this.$refs.floatingInput?.focus());
      } else if (this.activeTool === 'highlight') {
        this.isDrawing = true;
        this.highlightStart = { x: pos.x, y: pos.y };
      } else if (this.activeTool === 'signature' && this.savedSignatureDataUrl) {
        this.placeSignature(pos.x, pos.y);
      }
    },

    onCanvasMouseMove(e) {
      if (!this.isDrawing) return;
      const pos = this.getCanvasPos(e);
      const ctx = this.$refs.annotationCanvas.getContext('2d');

      if (this.activeTool === 'draw') {
        this.currentPath.push([pos.x, pos.y]);
        this.redrawAnnotations();
        ctx.strokeStyle = this.drawColor;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.beginPath();
        this.currentPath.forEach(([x, y], i) => {
          if (i === 0) ctx.moveTo(x, y);
          else ctx.lineTo(x, y);
        });
        ctx.stroke();
      } else if (this.activeTool === 'highlight') {
        this.redrawAnnotations();
        ctx.fillStyle = this.highlightColor;
        ctx.fillRect(this.highlightStart.x, this.highlightStart.y,
          pos.x - this.highlightStart.x, pos.y - this.highlightStart.y);
      }
    },

    onCanvasMouseUp(e) {
      if (!this.isDrawing) return;
      this.isDrawing = false;

      if (this.activeTool === 'draw' && this.currentPath.length > 1) {
        this.annotations.push({
          id: Date.now().toString(),
          type: 'drawing',
          page: this.currentPage,
          data: { paths: [this.currentPath], color: this.drawColor },
        });
        this.currentPath = [];
      } else if (this.activeTool === 'highlight' && this.highlightStart) {
        const pos = this.getCanvasPos(e);
        const w = pos.x - this.highlightStart.x;
        const h = pos.y - this.highlightStart.y;
        if (Math.abs(w) > 5 && Math.abs(h) > 5) {
          this.annotations.push({
            id: Date.now().toString(),
            type: 'highlight',
            page: this.currentPage,
            x: this.highlightStart.x,
            y: this.highlightStart.y,
            width: w,
            height: h,
            data: { color: this.highlightColor },
          });
        }
        this.highlightStart = null;
      }
      this.redrawAnnotations();
    },

    commitTextAnnotation() {
      if (this.textInput.value.trim()) {
        this.annotations.push({
          id: Date.now().toString(),
          type: 'text',
          page: this.currentPage,
          x: this.textInput.x,
          y: this.textInput.y,
          data: { text: this.textInput.value.trim(), fontSize: 16, color: '#100c08' },
        });
      }
      this.textInput = { visible: false, x: 0, y: 0, screenX: 0, screenY: 0, value: '' };
      this.redrawAnnotations();
    },

    placeSignature(x, y) {
      this.annotations.push({
        id: Date.now().toString(),
        type: 'signature',
        page: this.currentPage,
        x: x - 75,
        y: y - 25,
        width: 150,
        height: 50,
        data: { signatureDataUrl: this.savedSignatureDataUrl },
      });
      this.activeTool = 'select';
      this.redrawAnnotations();
    },

    undoAnnotation() {
      const pageAnns = this.annotations.filter(a => a.page === this.currentPage);
      if (pageAnns.length > 0) {
        const last = pageAnns[pageAnns.length - 1];
        this.annotations = this.annotations.filter(a => a.id !== last.id);
        this.redrawAnnotations();
      }
    },

    // === Redraw annotations ===
    redrawAnnotations() {
      const canvas = this.$refs.annotationCanvas;
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      const pageAnns = this.annotations.filter(a => a.page === this.currentPage);
      for (const ann of pageAnns) {
        if (ann.type === 'drawing') {
          ctx.strokeStyle = ann.data.color || '#100c08';
          ctx.lineWidth = 2;
          ctx.lineCap = 'round';
          for (const path of ann.data.paths) {
            ctx.beginPath();
            path.forEach(([x, y], i) => {
              if (i === 0) ctx.moveTo(x, y);
              else ctx.lineTo(x, y);
            });
            ctx.stroke();
          }
        } else if (ann.type === 'highlight') {
          ctx.fillStyle = ann.data.color || 'rgba(230,174,13,0.35)';
          ctx.fillRect(ann.x, ann.y, ann.width, ann.height);
        } else if (ann.type === 'text') {
          ctx.font = `${ann.data.fontSize || 16}px Inter, sans-serif`;
          ctx.fillStyle = ann.data.color || '#100c08';
          const lines = ann.data.text.split('\n');
          lines.forEach((line, i) => {
            ctx.fillText(line, ann.x, ann.y + (i + 1) * (ann.data.fontSize || 16));
          });
        } else if (ann.type === 'signature') {
          const key = ann.id;
          if (this.sigImages[key]) {
            ctx.drawImage(this.sigImages[key], ann.x, ann.y, ann.width, ann.height);
          } else {
            const img = new Image();
            img.onload = () => {
              this.sigImages[key] = img;
              this.redrawAnnotations();
            };
            img.src = ann.data.signatureDataUrl;
          }
        }
      }
    },

    // === Save & Export ===
    async saveAnnotations() {
      this.saving = true;
      try {
        const res = await fetch(`${this.apiUrl}/api/documents/${this.document.id}/annotations`, {
          method: 'PUT',
          headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json' },
          body: JSON.stringify({ annotations: this.annotations }),
        });
        if (res.ok) {
          this.showToast('Annotations saved!', 'success');
        } else {
          this.showToast('Save failed.', 'error');
        }
      } catch (e) {
        this.showToast('Save failed.', 'error');
      } finally {
        this.saving = false;
      }
    },

    async exportDocument() {
      this.exporting = true;
      try {
        const pc = this.$refs.pageCanvas;
        const ac = this.$refs.annotationCanvas;
        const merged = document.createElement('canvas');
        merged.width = pc.width;
        merged.height = pc.height;
        const ctx = merged.getContext('2d');
        ctx.drawImage(pc, 0, 0);
        ctx.drawImage(ac, 0, 0);
        const dataUrl = merged.toDataURL('image/png');

        const res = await fetch(`${this.apiUrl}/api/documents/${this.document.id}/save-edited`, {
          method: 'POST',
          headers: { Authorization: `Bearer ${this.token}`, 'Content-Type': 'application/json' },
          body: JSON.stringify({ edited_image: dataUrl, page_number: this.currentPage }),
        });
        if (res.ok) {
          this.showToast('Document exported successfully!', 'success');
        } else {
          this.showToast('Export failed.', 'error');
        }
      } catch (e) {
        this.showToast('Export failed.', 'error');
      } finally {
        this.exporting = false;
      }
    },

    // === Signature Modal ===
    openSignatureForPlacement() {
      if (this.savedSignatureDataUrl) {
        this.activeTool = 'signature';
      } else {
        this.sigTab = 'draw';
        this.sigHasDrawn = false;
        this.sigPhotoDataUrl = null;
        this.showSignatureModal = true;
        this.$nextTick(() => this.initSigCanvas());
      }
    },

    switchSigTab(tab) {
      this.sigTab = tab;
      if (tab === 'draw') this.$nextTick(() => this.initSigCanvas());
    },

    initSigCanvas() {
      const c = this.$refs.sigCanvas;
      if (!c) return;
      const ctx = c.getContext('2d');
      ctx.fillStyle = '#fff';
      ctx.fillRect(0, 0, c.width, c.height);
      ctx.strokeStyle = '#1a1a2e';
      ctx.lineWidth = 2.5;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      this.sigHasDrawn = false;
    },

    _sigPos(e, c) {
      const r = c.getBoundingClientRect();
      return { x: (e.clientX - r.left) * (c.width / r.width), y: (e.clientY - r.top) * (c.height / r.height) };
    },

    sigStartDraw(e) {
      const p = this._sigPos(e, this.$refs.sigCanvas);
      this.sigIsDrawing = true; this.sigLastX = p.x; this.sigLastY = p.y;
    },
    sigDraw(e) {
      if (!this.sigIsDrawing) return;
      const c = this.$refs.sigCanvas, ctx = c.getContext('2d'), p = this._sigPos(e, c);
      ctx.beginPath(); ctx.moveTo(this.sigLastX, this.sigLastY); ctx.lineTo(p.x, p.y); ctx.stroke();
      this.sigLastX = p.x; this.sigLastY = p.y; this.sigHasDrawn = true;
    },
    sigStopDraw() { this.sigIsDrawing = false; },
    sigStartTouch(e) {
      const t = e.touches[0], c = this.$refs.sigCanvas, r = c.getBoundingClientRect();
      this.sigIsDrawing = true;
      this.sigLastX = (t.clientX - r.left) * (c.width / r.width);
      this.sigLastY = (t.clientY - r.top) * (c.height / r.height);
    },
    sigDrawTouch(e) {
      if (!this.sigIsDrawing) return;
      const t = e.touches[0], c = this.$refs.sigCanvas, ctx = c.getContext('2d'), r = c.getBoundingClientRect();
      const x = (t.clientX - r.left) * (c.width / r.width);
      const y = (t.clientY - r.top) * (c.height / r.height);
      ctx.beginPath(); ctx.moveTo(this.sigLastX, this.sigLastY); ctx.lineTo(x, y); ctx.stroke();
      this.sigLastX = x; this.sigLastY = y; this.sigHasDrawn = true;
    },

    clearSigCanvas() {
      const c = this.$refs.sigCanvas;
      if (!c) return;
      const ctx = c.getContext('2d');
      ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, c.width, c.height);
      this.sigHasDrawn = false;
    },

    handleSigPhotoUpload(e) {
      const file = e.target.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => {
        const img = new Image();
        img.onload = () => {
          this.$nextTick(() => {
            const c = this.$refs.sigProcessedCanvas;
            if (!c) return;
            const ctx = c.getContext('2d');
            const scale = Math.min(c.width / img.width, c.height / img.height);
            const dw = img.width * scale, dh = img.height * scale;
            ctx.clearRect(0, 0, c.width, c.height);
            ctx.drawImage(img, (c.width - dw) / 2, (c.height - dh) / 2, dw, dh);
            const id = ctx.getImageData(0, 0, c.width, c.height);
            const d = id.data;
            for (let i = 0; i < d.length; i += 4) {
              const brightness = (d[i] + d[i+1] + d[i+2]) / 3;
              const max = Math.max(d[i], d[i+1], d[i+2]);
              const sat = max === 0 ? 0 : (max - Math.min(d[i], d[i+1], d[i+2])) / max;
              if (brightness > 200 && sat < 0.25) d[i+3] = 0;
            }
            ctx.putImageData(id, 0, 0);
            this.sigPhotoDataUrl = c.toDataURL('image/png');
          });
        };
        img.src = ev.target.result;
      };
      reader.readAsDataURL(file);
    },

    clearSigPhoto() {
      this.sigPhotoDataUrl = null;
      if (this.$refs.sigPhotoInput) this.$refs.sigPhotoInput.value = '';
    },

    confirmSignature() {
      if (this.sigTab === 'draw') {
        if (!this.sigHasDrawn) return;
        this.savedSignatureDataUrl = this.$refs.sigCanvas.toDataURL('image/png');
      } else {
        if (!this.sigPhotoDataUrl) return;
        this.savedSignatureDataUrl = this.sigPhotoDataUrl;
      }
      this.showSignatureModal = false;
      this.activeTool = 'signature';
      this.showToast('Click on the document to place your signature.', 'info');
    },

    showToast(message, type = 'success') {
      this.toast = { show: true, message, type };
      setTimeout(() => { this.toast.show = false; }, 3500);
    },
  },
};
</script>

<style scoped>
:root {
  --gold: #FFD700;
  --gold-dark: #DAB600;
  --black: #100c08;
  --white: #ffffff;
}

/* Layout */
.editor-wrapper { display: flex; flex-direction: column; height: 100vh; background: #2a2a2a; font-family: 'Inter', -apple-system, sans-serif; }

/* Toolbar */
.editor-toolbar {
  display: flex; align-items: center; gap: 16px;
  padding: 10px 20px; background: #100c08; color: #fff;
  border-bottom: 2px solid #FFD700;
  flex-shrink: 0;
}
.btn-back-editor {
  color: #FFD700; text-decoration: none; font-weight: 600; font-size: 13px;
  padding: 6px 14px; border-radius: 6px; border: 1px solid #FFD700;
  background: transparent; cursor: pointer; transition: all 0.2s;
}
.btn-back-editor:hover { background: #FFD700; color: #100c08; }
.editor-doc-name { flex: 1; font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0; }
.toolbar-tools { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
.tool-btn {
  padding: 8px 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15);
  background: transparent; color: #fff; font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 4px;
}
.tool-btn:hover { background: rgba(255,255,255,0.1); }
.tool-btn.active { background: #FFD700; color: #100c08; border-color: #FFD700; }
.tool-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.btn-save { background: #FFD700; color: #100c08; border-color: #FFD700; }
.btn-save:hover { background: #DAB600; }
.btn-export { background: transparent; border-color: #FFD700; color: #FFD700; }
.btn-export:hover { background: #FFD700; color: #100c08; }
.toolbar-divider { width: 1px; height: 24px; background: rgba(255,255,255,0.2); }

/* Page nav */
.page-nav {
  display: flex; align-items: center; justify-content: center; gap: 14px;
  padding: 10px; background: #333; color: #ccc; font-size: 13px; font-weight: 500;
  flex-shrink: 0;
}
.page-nav button {
  padding: 5px 14px; border-radius: 6px; border: 1px solid #555;
  background: transparent; color: #ccc; cursor: pointer; font-size: 13px; transition: all 0.2s;
}
.page-nav button:hover:not(:disabled) { background: #FFD700; color: #100c08; border-color: #FFD700; }
.page-nav button:disabled { opacity: 0.3; cursor: not-allowed; }

/* Loading */
.editor-loading { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #999; }
.editor-spinner { width: 40px; height: 40px; border: 4px solid #555; border-top-color: #FFD700; border-radius: 50%; animation: spin 0.8s linear infinite; margin-bottom: 16px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Unsupported */
.editor-unsupported { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #999; text-align: center; padding: 40px; }
.unsupported-icon { font-size: 64px; margin-bottom: 16px; opacity: 0.5; }
.editor-unsupported h3 { color: #ddd; margin: 0 0 8px; }
.editor-unsupported p { margin: 0 0 8px; font-size: 14px; }
.btn-download-fallback {
  margin-top: 16px; padding: 10px 24px; border-radius: 8px;
  background: #FFD700; color: #100c08; text-decoration: none;
  font-weight: 700; font-size: 14px; transition: all 0.2s;
}
.btn-download-fallback:hover { background: #DAB600; }

/* Canvas area */
.editor-canvas-area { flex: 1; overflow: auto; display: flex; justify-content: center; padding: 24px; }
.canvas-container { position: relative; box-shadow: 0 4px 24px rgba(0,0,0,0.5); background: #fff; }
.page-canvas { display: block; }
.annotation-canvas { position: absolute; top: 0; left: 0; }
.cursor-crosshair { cursor: crosshair; }
.cursor-text { cursor: text; }
.cursor-signature { cursor: copy; }

.floating-text-input {
  position: absolute; min-width: 120px; min-height: 28px;
  border: 2px solid #FFD700; background: rgba(255,255,255,0.95);
  font-size: 16px; font-family: 'Inter', sans-serif;
  padding: 4px 8px; outline: none; resize: both; z-index: 10;
  border-radius: 4px; color: #100c08;
}

/* Modal */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(16,12,8,0.6);
  backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  z-index: 1000; padding: 20px;
}
.modal-box {
  background: #fff; border-radius: 16px; width: 100%; max-width: 540px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;
}
.modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px; border-bottom: 1px solid #f0f0f0;
}
.modal-header h3 { font-size: 18px; font-weight: 700; color: #100c08; margin: 0; }
.modal-close {
  background: #fff; border: 2px solid #FFD700; width: 32px; height: 32px;
  border-radius: 50%; font-size: 14px; color: #FFD700; cursor: pointer;
  display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.modal-close:hover { background: #FFD700; color: #100c08; }
.modal-body { padding: 20px 24px; }
.modal-footer {
  display: flex; justify-content: flex-end; gap: 10px;
  padding: 16px 24px; border-top: 1px solid #f0f0f0;
}

/* Signature tabs */
.sign-tabs { display: flex; gap: 8px; margin-bottom: 16px; }
.sign-tab {
  flex: 1; padding: 10px; border: 2px solid #f0f0f0; border-radius: 8px;
  background: #fff; color: #666; font-size: 13px; font-weight: 600;
  cursor: pointer; text-align: center; transition: all 0.2s;
}
.sign-tab.active { border-color: #FFD700; color: #100c08; background: #fffdf5; }
.sig-content { margin-top: 12px; }
.sig-canvas-wrap { border: 2px dashed #FFD700; border-radius: 10px; overflow: hidden; background: #fff; }
.sig-canvas { display: block; width: 100%; cursor: crosshair; touch-action: none; }
.btn-clear-sig {
  margin-top: 8px; padding: 6px 14px; border-radius: 6px;
  border: 1px solid #e0e0e0; background: #fff; color: #666;
  font-size: 13px; cursor: pointer;
}
.btn-clear-sig:hover { background: #fee; border-color: #e74c3c; color: #e74c3c; }
.sig-upload-area {
  border: 2px dashed #FFD700; border-radius: 10px; min-height: 160px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; background: #fffdf5; transition: border-color 0.2s;
}
.sig-upload-area:hover { border-color: #DAB600; }
.sig-upload-placeholder { text-align: center; color: #999; }
.sig-upload-placeholder span { font-size: 36px; display: block; margin-bottom: 8px; }
.sig-upload-placeholder p { margin: 0; font-size: 13px; }
.sig-photo-preview { width: 100%; display: flex; flex-direction: column; align-items: center; }
.sig-processed { display: block; width: 100%; }
.sig-preview-note { text-align: center; font-size: 12px; color: #27ae60; margin: 4px 0; }
.btn-cancel-modal {
  padding: 10px 20px; border-radius: 8px; border: 2px solid #100c08;
  background: #fff; color: #100c08; font-size: 14px; font-weight: 600;
  cursor: pointer; transition: all 0.2s;
}
.btn-cancel-modal:hover { background: #100c08; color: #FFD700; }
.btn-confirm-sig {
  padding: 10px 24px; border-radius: 8px; border: 2px solid #FFD700;
  background: #FFD700; color: #100c08; font-size: 14px; font-weight: 700;
  cursor: pointer; transition: all 0.2s;
}
.btn-confirm-sig:hover { background: #DAB600; }

/* Toast */
.toast {
  position: fixed; bottom: 28px; right: 28px;
  padding: 14px 22px; border-radius: 12px;
  font-size: 14px; font-weight: 600; z-index: 9999;
  box-shadow: 0 6px 24px rgba(0,0,0,0.2);
}
.toast-success { background: #100c08; color: #FFD700; }
.toast-error { background: #dc2626; color: #fff; }
.toast-info { background: #100c08; color: #FFD700; }
.toast-slide-enter-active { transition: all 0.3s ease; }
.toast-slide-leave-active { transition: all 0.25s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateY(12px); }
</style>
