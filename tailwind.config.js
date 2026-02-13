import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: [
                    'Inter',
                    'Instrument Sans',
                    ...defaultTheme.fontFamily.sans,
                ],
            },
            colors: {
                brand: {
                    yellow: '#FCEE23',
                    dark: '#0f172a',
                    light: '#f8fafc',
                },
            },
        },
    },
    plugins: [],
};
