import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';

const app = createApp(App);

app.directive('click-outside', {
    beforeMount(el, binding) {
        el._clickOutsideHandler = (event) => {
            if (!el.contains(event.target)) {
                binding.value(event);
            }
        };
        document.addEventListener('click', el._clickOutsideHandler);
    },
    unmounted(el) {
        document.removeEventListener('click', el._clickOutsideHandler);
    },
});

app.use(router);
app.mount('#app');