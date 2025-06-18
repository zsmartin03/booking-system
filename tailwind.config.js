const defaultTheme = require("tailwindcss/defaultTheme");
const forms = require("@tailwindcss/forms");

const colors = require("tailwindcss/colors");

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                'frappe-red': '#e78284',
                // Catppuccin Frappé palette
                frappe: {
                    base: "#303446",
                    mantle: "#292c3c",
                    crust: "#232634",
                    text: "#c6d0f5",
                    subtext1: "#b5bfe2",
                    subtext0: "#a5adce",
                    overlay2: "#949cbb",
                    overlay1: "#838ba7",
                    overlay0: "#737994",
                    surface2: "#626880",
                    surface1: "#51576d",
                    surface0: "#414559",
                    blue: "#8caaee",
                    lavender: "#babbf1",
                    sapphire: "#85c1dc",
                    sky: "#99d1db",
                    teal: "#81c8be",
                    green: "#a6d189",
                    yellow: "#e5c890",
                    peach: "#ef9f76",
                    maroon: "#ea999c",
                    red: "#e78284",
                    mauve: "#ca9ee6",
                    pink: "#f4b8e4",
                    flamingo: "#eebebe",
                    rosewater: "#f2d5cf",
                },
            },
        },
    },
    plugins: [forms],
};
