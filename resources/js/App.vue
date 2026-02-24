<template>
    <div class="app">
        <router-view v-slot="{ Component, route }">
            <transition :name="route.meta.transition || 'fade'" mode="out-in">
                <component :is="Component" />
            </transition>
        </router-view>
    </div>
</template>

<script>
export default {
    name: 'App',
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
