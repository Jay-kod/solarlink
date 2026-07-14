import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                solar: {
                    primary: {
                        DEFAULT: '#6C3BFF',
                        dark: '#2B145A',
                        light: '#F3EEFF',
                        accent: '#8A5BFF',
                        active: '#582BE6',
                    },
                    success: '#22C55E',
                    warning: '#F59E0B',
                    danger: '#EF4444',
                    muted: '#8B8BA7',
                    bg: {
                        light: '#F9F7FF',
                        dark: '#0B061A',
                    },
                    card: {
                        light: 'rgba(255, 255, 255, 0.7)',
                        dark: 'rgba(21, 11, 46, 0.65)',
                    }
                }
            },
            fontFamily: {
                sans: ['Outfit', 'Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                'solar': '0 8px 32px 0 rgba(108, 59, 255, 0.08)',
                'solar-lg': '0 12px 40px 0 rgba(108, 59, 255, 0.15)',
                'solar-glow': '0 0 20px 0 rgba(108, 59, 255, 0.25)',
                'solar-glow-lg': '0 0 35px 0 rgba(108, 59, 255, 0.4)',
                'success-glow': '0 0 20px 0 rgba(34, 197, 94, 0.2)',
            },
            backdropBlur: {
                'solar': '16px',
            },
            animation: {
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'float': 'float 6s ease-in-out infinite',
                'fade-in-up': 'fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                }
            }
        },
    },

    plugins: [forms],
};
