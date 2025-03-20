module.exports = {
    content: [
        "./*.html",          // Includes HTML files
        "./php/**/*.php",    // Includes PHP files inside the /php/ folder
        "./**/*.php",        // Ensures Tailwind works with all PHP files
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
