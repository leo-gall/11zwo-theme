/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./inc/**/*.{php,js,json}",
    "./assets/js/**/*.js",
  ],
  // Klassen, die nicht wörtlich im Code stehen: im Editor in Block-Attribute
  // ("Tailwind-Klassen") eingetragene und "size-full", das WordPress selbst an
  // Bilder hängt.
  safelist: [
    "lg:grid-cols-[5fr_7fr]", "pb-10", "py-0",
    "size-full",
  ],
  theme: {
    extend: {
      colors: {
        background: "#fff",
        foreground: "#2d2727",
        card: "#fff",
        border: "#e6e1e1",

        ember: "#d35055",
        signal: "#c4161c",
        "signal-foreground": "oklch(0.98 0.01 85)",
        wood: "#9d1216",
        cream: "#f3f0f0",
        ink: "#1a1a1a",
        sky: "oklch(0.82 0.06 260)",
        leaf: "#7f0e12",
        smoke: "#716868",
        ash: "#f3f0f0",
        haze: "#ebe6e6",
      },
      borderRadius: {
        sm: "0.375rem",
        DEFAULT: "0.5rem",
        md: "0.75rem",
        lg: "1rem",
        xl: "1.25rem",
        "2xl": "1.5rem",
        "3xl": "2rem",
      },
      fontFamily: {
        display: ["Titillium Web", "ui-sans-serif", "system-ui", "sans-serif"],
        sans: ["Titillium Web", "ui-sans-serif", "system-ui", "sans-serif"],
        hand: ["Titillium Web", "ui-sans-serif", "system-ui", "sans-serif"],
      },
    },
  },
};
