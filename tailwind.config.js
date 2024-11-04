/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{html,js,php}", // Inclure les fichiers PHP dans le dossier src
    "./**/*.php",               // Inclure tous les fichiers PHP dans le projet
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
