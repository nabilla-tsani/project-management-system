import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
        daisyui, // tambahkan daisyUI sebagai plugin
    ],

    daisyui: {
        themes: ['light'], // bisa ganti 'light' dengan tema lain, misal 'dark' atau 'cupcake'
        styled: true,       // aktifkan styling bawaan daisyUI
        base: true,         // aktifkan base styles
        utils: true,        // aktifkan utility classes (bg-primary, text-base-100, dsb)
    },
};
