import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'primary-container': '#131b2e',
                'primary-fixed': '#dae2fd',
                'primary-fixed-dim': '#bec6e0',
                'on-primary': '#ffffff',
                'on-primary-container': '#7c839b',
                'on-primary-fixed': '#131b2e',
                'on-primary-fixed-variant': '#3f465c',
                'secondary': '#0058be',
                'secondary-container': '#2170e4',
                'secondary-fixed': '#d8e2ff',
                'secondary-fixed-dim': '#adc6ff',
                'on-secondary': '#ffffff',
                'on-secondary-container': '#fefcff',
                'on-secondary-fixed': '#001a42',
                'on-secondary-fixed-variant': '#004395',
                'background': '#f7f9fb',
                'surface': '#f7f9fb',
                'surface-bright': '#f7f9fb',
                'surface-dim': '#d8dadc',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#f2f4f6',
                'surface-container': '#eceef0',
                'surface-container-high': '#e6e8ea',
                'surface-container-highest': '#e0e3e5',
                'surface-variant': '#e0e3e5',
                'on-surface': '#191c1e',
                'on-surface-variant': '#45464d',
                'outline': '#76777d',
                'outline-variant': '#c6c6cd',
                'inverse-surface': '#2d3133',
                'inverse-on-surface': '#eff1f3',
                'inverse-primary': '#bec6e0',
                'error': '#ba1a1a',
                'error-container': '#ffdad6',
                'on-error': '#ffffff',
                'on-error-container': '#93000a',
                'tertiary-container': '#111c2d',
                'on-tertiary': '#ffffff',
                'on-tertiary-container': '#79849a',
                'tertiary-fixed': '#d8e3fb',
                'tertiary-fixed-dim': '#bcc7de',
                'on-tertiary-fixed': '#111c2d',
                'on-tertiary-fixed-variant': '#3c475a',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            fontSize: {
                'display': ['36px', { lineHeight: '44px', letterSpacing: '-0.02em', fontWeight: '700' }],
                'headline-lg': ['24px', { lineHeight: '32px', letterSpacing: '-0.01em', fontWeight: '600' }],
                'headline-md': ['20px', { lineHeight: '28px', letterSpacing: '-0.01em', fontWeight: '600' }],
                'body-lg': ['16px', { lineHeight: '24px', fontWeight: '400' }],
                'body-md': ['14px', { lineHeight: '20px', fontWeight: '400' }],
                'body-sm': ['13px', { lineHeight: '18px', fontWeight: '400' }],
                'label-md': ['12px', { lineHeight: '16px', letterSpacing: '0.02em', fontWeight: '600' }],
                'mono': ['13px', { lineHeight: '20px', fontWeight: '400' }],
            },
        },
    },

    plugins: [forms],
};
