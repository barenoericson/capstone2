<template>
    <div class="app">
        <!-- Full-screen premium loading overlay -->
        <transition name="loader-fade">
            <div v-if="routeLoading" class="route-loader">
                <!-- Floating shapes -->
                <div class="rl-shape" v-for="s in shapes" :key="s.id"
                     :style="{ left: s.left, width: s.size, height: s.size, animationDuration: s.dur, animationDelay: s.delay }">
                </div>
                <div class="rl-gradient-overlay"></div>

                <!-- Logo + shuttle bar -->
                <div class="rl-content">
                    <h1 class="rl-wordmark">
                        Realty<span class="rl-white">Link</span><span class="rl-gold">PH</span>
                    </h1>
                    <div class="rl-bar-track">
                        <div class="rl-bar-shuttle"></div>
                    </div>
                    <p class="rl-text">Loading</p>
                </div>
            </div>
        </transition>

        <router-view v-slot="{ Component, route }">
            <transition :name="route.meta.transition || 'fade'" mode="out-in">
                <component :is="Component" />
            </transition>
        </router-view>

        <!-- RealtyLinkPH Buddy Chat (global, authenticated pages only) -->
        <ChatBubble v-if="showChatBubble" />
    </div>
</template>

<script>
import { defineAsyncComponent } from 'vue';

const ChatBubble = defineAsyncComponent(() =>
    import('./components/ChatBubble.vue')
);

export default {
    name: 'App',

    components: { ChatBubble },

    data() {
        return {
            routeLoading: false,
            shapes: [
                { id: 0, left: '8%', size: '120px', dur: '22s', delay: '0s' },
                { id: 1, left: '25%', size: '80px', dur: '18s', delay: '1s' },
                { id: 2, left: '42%', size: '140px', dur: '25s', delay: '2.5s' },
                { id: 3, left: '60%', size: '60px', dur: '16s', delay: '0.5s' },
                { id: 4, left: '75%', size: '100px', dur: '20s', delay: '3s' },
                { id: 5, left: '90%', size: '70px', dur: '17s', delay: '1.5s' },
                { id: 6, left: '15%', size: '90px', dur: '23s', delay: '4s' },
                { id: 7, left: '50%', size: '110px', dur: '19s', delay: '2s' },
                { id: 8, left: '35%', size: '75px', dur: '21s', delay: '3.5s' },
                { id: 9, left: '68%', size: '130px', dur: '24s', delay: '1.2s' },
            ],
        };
    },

    computed: {
        showChatBubble() {
            const hasToken = !!localStorage.getItem('auth_token');
            const requiresAuth = this.$route && this.$route.meta && this.$route.meta.requiresAuth;
            return hasToken && requiresAuth;
        },
    },

    created() {
        // Show loading screen during lazy route chunk loading
        this.$router.beforeEach(() => {
            this.routeLoading = true;
        });
        this.$router.afterEach(() => {
            this.routeLoading = false;
        });
        this.$router.onError(() => {
            this.routeLoading = false;
        });

        // Expose global show/hide so pages (like Login) can trigger it
        window.__showLoading = () => { this.routeLoading = true; };
        window.__hideLoading = () => { this.routeLoading = false; };
    },

    mounted() {
        this.applyStoredTheme();

        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', () => this.applyStoredTheme());
        }

        window.addEventListener('storage', (e) => {
            if (e.key === 'theme_preference') this.applyStoredTheme();
        });
    },

    methods: {
        applyStoredTheme() {
            const theme = localStorage.getItem('theme_preference') || 'light';
            const html = document.documentElement;
            html.classList.remove('theme-light', 'theme-dark');

            if (theme === 'dark') {
                html.classList.add('theme-dark');
            } else if (theme === 'system') {
                const prefersDark = window.matchMedia &&
                    window.matchMedia('(prefers-color-scheme: dark)').matches;
                html.classList.add(prefersDark ? 'theme-dark' : 'theme-light');
            } else {
                html.classList.add('theme-light');
            }
        },
    },
};
</script>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

#app {
    width: 100%;
}

/* ═══════════════════════════════════════════
   PREMIUM ROUTE LOADING SCREEN
   Matches the splash screen in app.blade.php
   ═══════════════════════════════════════════ */
.route-loader {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at center, #0F172A 0%, #0B1120 100%);
    overflow: hidden;
}

/* Floating shapes */
.rl-shape {
    position: absolute;
    bottom: -150px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.02);
    animation: rl-float-up 18s linear infinite;
    pointer-events: none;
}
@keyframes rl-float-up {
    0% { transform: translateY(0) scale(1); opacity: 0; }
    20% { opacity: 0.4; }
    100% { transform: translateY(-110vh) scale(0.8); opacity: 0; }
}

/* Gradient overlay */
.rl-gradient-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, #0B1120, transparent, transparent);
    opacity: 0.8;
    pointer-events: none;
}

/* Content */
.rl-content {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 360px;
    width: 100%;
    opacity: 0;
    transform: translateY(20px);
    animation: rl-fade-in-up 0.8s ease-out 0.1s forwards;
}
@keyframes rl-fade-in-up {
    to { opacity: 1; transform: translateY(0); }
}

/* Wordmark */
.rl-wordmark {
    font-family: 'Playfair Display', 'Georgia', serif;
    font-size: 3.5rem;
    font-weight: 400;
    letter-spacing: -0.02em;
    color: #FAFAF9;
    text-align: center;
    margin: 0 0 2.5rem 0;
    text-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
}
.rl-white { color: #fff; }
.rl-gold { color: #D4A853; }

/* Shuttle loading bar */
.rl-bar-track {
    width: 16rem;
    height: 3px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 9999px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}
.rl-bar-shuttle {
    position: absolute;
    inset: 0;
    width: 50%;
    background: linear-gradient(90deg, transparent, #D4A853, transparent);
    opacity: 0.8;
    animation: rl-shuttle 2.5s ease-in-out infinite;
}
@keyframes rl-shuttle {
    0% { transform: translateX(-100%); }
    50% { transform: translateX(200%); }
    100% { transform: translateX(-100%); }
}

/* Loading text */
.rl-text {
    font-family: 'Inter', -apple-system, sans-serif;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.3em;
    color: #78716C;
    margin-top: 1.5rem;
    font-weight: 500;
    animation: rl-pulse 2s ease-in-out infinite;
}
@keyframes rl-pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

/* Mobile responsive */
@media (max-width: 480px) {
    .rl-wordmark { font-size: 2.5rem; }
    .rl-bar-track { width: 12rem; }
}

/* Fade transitions */
.loader-fade-enter-active {
    transition: opacity 0.3s ease;
}
.loader-fade-leave-active {
    transition: opacity 0.5s ease;
}
.loader-fade-enter-from,
.loader-fade-leave-to {
    opacity: 0;
}

/* =========================
   DEFAULT FADE (fallback)
   ========================= */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
