import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#D9531E',
                    50: '#FDF5F2',
                    100: '#FCEAE3',
                    200: '#F8D1C2',
                    300: '#F4B099',
                    400: '#EE8461',
                    500: '#D9531E',
                    600: '#C23D0C',
                    700: '#9C300A',
                    800: '#7F290C',
                    900: '#67250E',
                },
                secondary: {
                    DEFAULT: '#1E293B',
                    50: '#F8FAFC',
                    100: '#F1F5F9',
                    200: '#E2E8F0',
                    300: '#CBD5E1',
                    400: '#94A3B8',
                    500: '#64748B',
                    600: '#475569',
                    700: '#334155',
                    800: '#1E293B',
                    900: '#0F172A',
                },
                tertiary: {
                    DEFAULT: '#16A34A',
                    50: '#F0FDF4',
                    100: '#DCFCE7',
                    500: '#16A34A',
                    600: '#15803D',
                    700: '#166534',
                },
                neutral: {
                    DEFAULT: '#64748B',
                }
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                headline: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
