<template>
    <div class="app">
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
import ChatBubble from './components/ChatBubble.vue';

export default {
    name: 'App',

    components: { ChatBubble },

    computed: {
        showChatBubble() {
            const hasToken = !!localStorage.getItem('auth_token');
            const requiresAuth = this.$route && this.$route.meta && this.$route.meta.requiresAuth;
            return hasToken && requiresAuth;
        },
    },

    mounted() {
        this.applyStoredTheme();

        // Re-apply when OS dark/light preference changes (for system mode)
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', () => this.applyStoredTheme());
        }

        // Re-apply when another tab changes the preference
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
