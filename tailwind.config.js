/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./storage/framework/views/*.php",
  ],
  theme: {
    extend: {
      colors: {
        'saar-red': '#FF0000',
        'saar-blue': '#1E40AF',
        'saar-green': '#059669',
        'primary': {
          50: '#eff6ff',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
        }
      },
      fontFamily: {
        'sans': ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
