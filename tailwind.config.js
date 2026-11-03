import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                bjbo: {
                    primary: '#1E3A8A',
                    'primary-hover': '#172554',
                    'primary-light': '#EFF6FF',
                    accent: '#C2410C',
                    'accent-hover': '#9A3412',
                    'accent-light': '#FFF7ED',
                    bg: '#F8F9FA',
                    surface: '#FFFFFF',
                    'surface-subtle': '#F1F3F5',
                    border: '#E2E6EA',
                    'border-strong': '#CED4DA',
                    success: '#15803D',
                    'success-bg': '#F0FDF4',
                    warning: '#B45309',
                    'warning-bg': '#FFFBEB',
                    danger: '#B91C1C',
                    'danger-bg': '#FEF2F2',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            screens: {
                xs: '380px',
                ...defaultTheme.screens,
            },
        },
    },
    plugins: [],
};
