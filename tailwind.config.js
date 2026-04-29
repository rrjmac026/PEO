import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'surface': {
                    'primary': 'var(--color-bg-primary)',
                    'secondary': 'var(--color-bg-secondary)',
                    'tertiary': 'var(--color-bg-tertiary)',
                    'hover': 'var(--color-bg-hover)',
                    'active': 'var(--color-bg-active)',
                },
                'text': {
                    'primary': 'var(--color-text-primary)',
                    'secondary': 'var(--color-text-secondary)',
                    'tertiary': 'var(--color-text-tertiary)',
                    'muted': 'var(--color-text-muted)',
                    'inverse': 'var(--color-text-inverse)',
                },
                'border': {
                    'primary': 'var(--color-border-primary)',
                    'secondary': 'var(--color-border-secondary)',
                    'light': 'var(--color-border-light)',
                },
                'status': {
                    'success': {
                        'bg': 'var(--color-success-bg)',
                        'text': 'var(--color-success-text)',
                        'border': 'var(--color-success-border)',
                        'dot': 'var(--color-success-dot)',
                    },
                    'error': {
                        'bg': 'var(--color-error-bg)',
                        'text': 'var(--color-error-text)',
                        'border': 'var(--color-error-border)',
                        'dot': 'var(--color-error-dot)',
                    },
                    'warning': {
                        'bg': 'var(--color-warning-bg)',
                        'text': 'var(--color-warning-text)',
                        'border': 'var(--color-warning-border)',
                        'dot': 'var(--color-warning-dot)',
                    },
                    'info': {
                        'bg': 'var(--color-info-bg)',
                        'text': 'var(--color-info-text)',
                        'border': 'var(--color-info-border)',
                        'dot': 'var(--color-info-dot)',
                    },
                },
                'action': {
                    'primary': 'var(--color-action-primary)',
                    'primary-hover': 'var(--color-action-primary-hover)',
                    'primary-active': 'var(--color-action-primary-active)',
                    'secondary': 'var(--color-action-secondary)',
                    'secondary-hover': 'var(--color-action-secondary-hover)',
                    'secondary-text': 'var(--color-action-secondary-text)',
                },
            },
            boxShadow: {
                'token-sm': 'var(--shadow-sm)',
                'token-md': 'var(--shadow-md)',
                'token-lg': 'var(--shadow-lg)',
                'token-xl': 'var(--shadow-xl)',
            },
            ringColor: {
                'token': 'var(--ring-color)',
            },
        },
    },

    plugins: [forms],
};
