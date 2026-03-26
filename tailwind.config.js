import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                // Core Brand Colors
                primary: {
                    50: '#eff6ff',  // Lightest blue - good for tinted backgrounds
                    100: '#dbeafe',
                    400: '#60a5fa',
                    500: '#3b82f6', // Base LabSync Blue
                    600: '#2563eb', // Hover states for primary buttons
                    900: '#1e3a8a', // Deep blue for high-contrast headers
                },
                accent: {
                    DEFAULT: '#0d9488', // Teal - great for "Sync" buttons or active states
                    hover: '#0f766e',
                    light: '#ccfbf1',
                },
                // Neutrals for layout
                surface: {
                    50: '#f8fafc',  // Main app background (slate-50)
                    100: '#ffffff', // Card/Panel background
                    200: '#e2e8f0', // Borders and dividers
                    800: '#1e293b', // Dark mode card background
                    900: '#0f172a', // Dark mode app background
                },
                // Semantic colors for lab statuses
                status: {
                    success: '#10b981', // Emerald - passed tests, successful sync
                    warning: '#f59e0b', // Amber - pending processes, attention needed
                    error: '#ef4444',   // Red - failed states, critical alerts
                    info: '#0ea5e9',    // Sky - general information
                }
            },
            fontFamily: {
                // Inter is the gold standard for clean, legible dashboard UIs
                sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                // A mono font is incredibly useful in a lab app for Sample IDs, data arrays, or timestamps
                mono: ['JetBrains Mono', 'Fira Code', 'ui-monospace', 'monospace'],
            },
            boxShadow: {
                'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                'glow': '0 0 15px rgba(13, 148, 136, 0.4)', // A teal glow for active "syncing" states
            },
            animation: {
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            }
        },
    },

    plugins: [forms],
};
