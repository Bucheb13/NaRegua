import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            colors: {
                primary: '#CFAE70',      // Dourado principal
                secondary: '#5A3825',    // Marrom escuro
                accent: '#F8E5C0',       // Bege claro
                background: '#F9F6F1',   // Fundo leve
                dark: '#1a1410',         // Fundo escuro para dark mode
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', 'sans-serif'],
                orbitron: ['Orbitron', 'sans-serif'],
                cinzel: ['"Cinzel Decorative"', 'serif'],
            },
            borderRadius: {
                xl: '1rem',
                '2xl': '1.5rem',
            },
        },
    },

    plugins: [
        forms, // Estiliza campos de formulário com padrão Tailwind
    ],
}
