import './bootstrap';
import '../css/app.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue', { eager: false })),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        // Global reveal-on-scroll directive
        app.directive('reveal', {
            mounted(el: HTMLElement, binding: any) {
                const animType = binding.arg || 'up';
                const animClass = animType === 'zoom' ? 'reveal-zoom-in' : `reveal-fade-${animType}`;
                
                el.classList.add('reveal-hidden');
                el.classList.add(animClass);

                if (binding.value) {
                    if (typeof binding.value === 'number') {
                        el.style.transitionDelay = `${binding.value}ms`;
                    } else {
                        if (binding.value.delay) {
                            el.style.transitionDelay = `${binding.value.delay}ms`;
                        }
                        if (binding.value.duration) {
                            el.style.transitionDuration = `${binding.value.duration}ms`;
                        }
                    }
                }

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            el.classList.add('reveal-visible');
                            observer.unobserve(el);
                        }
                    });
                }, {
                    threshold: 0.05,
                    rootMargin: '0px 0px -40px 0px'
                });

                observer.observe(el);
                (el as any)._revealObserver = observer;
            },
            unmounted(el: HTMLElement) {
                if ((el as any)._revealObserver) {
                    (el as any)._revealObserver.disconnect();
                }
            }
        });

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
