<template>
  <div class="panorama-viewer-wrapper">
    <!-- Loading State -->
    <div v-if="loading" class="panorama-loading">
      <div class="spinner"></div>
      <p>Loading 360° Tour...</p>
    </div>

    <!-- Error State -->
    <div v-if="error" class="panorama-error">
      <p>{{ error }}</p>
    </div>

    <!-- Pannellum Container -->
    <div ref="panoramaContainer" class="panorama-container"></div>

    <!-- Room Navigation Bar -->
    <div v-if="scenes.length > 1" class="room-nav">
      <button
        v-for="scene in scenes"
        :key="scene.id"
        :class="['room-btn', { active: currentSceneId === scene.id }]"
        @click="goToScene(scene.id)"
      >
        {{ scene.title }}
      </button>
    </div>

    <!-- Current Room Label -->
    <div v-if="currentRoomName" class="current-room-label">
      {{ currentRoomName }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'PanoramaViewer',

  props: {
    panoramas: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      viewer: null,
      loading: true,
      error: '',
      currentSceneId: '',
      scenes: [],
    };
  },

  computed: {
    currentRoomName() {
      const scene = this.scenes.find(s => s.id === this.currentSceneId);
      return scene ? scene.title : '';
    },
  },

  methods: {
    async loadPannellum() {
      if (window.pannellum) return;

      // Load CSS
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css';
      document.head.appendChild(link);

      // Load JS
      await new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js';
        script.onload = resolve;
        script.onerror = () => reject(new Error('Failed to load Pannellum'));
        document.head.appendChild(script);
      });
    },

    buildTourConfig() {
      const scenes = {};
      const scenesList = [];

      this.panoramas.forEach((p) => {
        const sceneId = 'scene-' + p.id;
        scenesList.push({ id: sceneId, title: p.room_name });

        const hotSpots = (p.hotspots || []).map((hs) => ({
          id: hs.id,
          pitch: hs.pitch,
          yaw: hs.yaw,
          type: hs.type,
          text: hs.text,
          sceneId: hs.sceneId,
        }));

        scenes[sceneId] = {
          title: p.room_name,
          type: 'equirectangular',
          panorama: p.image_url,
          autoLoad: true,
          autoRotate: -2,
          compass: true,
          showZoomCtrl: true,
          showFullscreenCtrl: false,
          hfov: parseFloat(p.hfov) || 100,
          pitch: parseFloat(p.default_pitch) || 0,
          yaw: parseFloat(p.default_yaw) || 0,
          hotSpots: hotSpots,
        };
      });

      this.scenes = scenesList;
      const firstSceneId = scenesList.length > 0 ? scenesList[0].id : '';
      this.currentSceneId = firstSceneId;

      return {
        default: {
          firstScene: firstSceneId,
          sceneFadeDuration: 1000,
          autoLoad: true,
          autoRotate: -2,
          compass: true,
        },
        scenes: scenes,
      };
    },

    async initViewer() {
      try {
        this.loading = true;
        this.error = '';

        await this.loadPannellum();
        await this.$nextTick();

        const config = this.buildTourConfig();

        if (this.viewer) {
          this.viewer.destroy();
          this.viewer = null;
        }

        this.viewer = window.pannellum.viewer(this.$refs.panoramaContainer, config);

        this.viewer.on('scenechange', (sceneId) => {
          this.currentSceneId = sceneId;
        });

        this.viewer.on('load', () => {
          this.loading = false;
        });

        // Fallback timeout for loading state
        setTimeout(() => {
          this.loading = false;
        }, 5000);
      } catch (e) {
        console.error('Panorama viewer error:', e);
        this.error = 'Failed to load 360° tour viewer.';
        this.loading = false;
      }
    },

    goToScene(sceneId) {
      if (this.viewer && sceneId !== this.currentSceneId) {
        this.viewer.loadScene(sceneId);
      }
    },

    destroy() {
      if (this.viewer) {
        try {
          this.viewer.destroy();
        } catch (e) {
          // Ignore destroy errors
        }
        this.viewer = null;
      }
    },
  },

  mounted() {
    if (this.panoramas && this.panoramas.length > 0) {
      this.initViewer();
    }
  },

  beforeUnmount() {
    this.destroy();
  },

  watch: {
    panoramas: {
      handler() {
        this.destroy();
        if (this.panoramas && this.panoramas.length > 0) {
          this.$nextTick(() => {
            this.initViewer();
          });
        }
      },
      deep: true,
    },
  },
};
</script>

<style scoped>
.panorama-viewer-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  background: #100c08;
  border-radius: 8px;
  overflow: hidden;
}

.panorama-container {
  width: 100%;
  height: 100%;
}

.panorama-loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
  color: white;
  z-index: 10;
  background: rgba(16, 12, 8, 0.8);
  font-family: 'Inter', sans-serif;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid rgba(255, 255, 255, 0.2);
  border-top-color: #FFD700;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.panorama-error {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #ff6b6b;
  font-size: 14px;
  text-align: center;
  z-index: 10;
  font-family: 'Inter', sans-serif;
}

.room-nav {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
  background: rgba(0, 0, 0, 0.75);
  padding: 8px 12px;
  border-radius: 24px;
  z-index: 10;
  backdrop-filter: blur(10px);
  max-width: 90%;
  overflow-x: auto;
}

.room-btn {
  padding: 6px 14px;
  background: transparent;
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  white-space: nowrap;
  font-family: 'Inter', sans-serif;
}

.room-btn:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: #FFD700;
}

.room-btn.active {
  background: #FFD700;
  color: #100c08;
  border-color: #FFD700;
  font-weight: 700;
}

.current-room-label {
  position: absolute;
  top: 16px;
  left: 16px;
  background: rgba(0, 0, 0, 0.7);
  color: #FFD700;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 700;
  z-index: 10;
  font-family: 'Poppins', sans-serif;
  backdrop-filter: blur(10px);
}
</style>
