/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./inc/**/*.{php,js,json}",
    "./assets/js/**/*.js",
  ],
  // Klassen, die nicht wörtlich im Code stehen: die Akzentfarben-Palette aus
  // blocks-common.js (setzt "bg-<farbe>/<deckkraft>" zur Laufzeit zusammen),
  // Klassen, die im Editor in Block-Attribute ("Tailwind-Klassen") eingetragen
  // wurden, und "size-full", das WordPress selbst an Bilder hängt.
  safelist: [
    { pattern: /^text-(signal|ember|wood|ink|cream|sky|leaf)$/ },
    "bg-signal/30", "bg-ember/40", "bg-wood/35", "bg-ink/20", "bg-cream", "bg-sky/40", "bg-leaf/40",
    "lg:grid-cols-[5fr_7fr]", "pb-10", "py-0",
    "size-full",
  ],
  theme: {
    extend: {
      colors: {
        background: "oklch(98.531% 0.005 260)",
        foreground: "oklch(0.22 0.035 265)",
        card: "oklch(0.99 0.006 260)",
        border: "oklch(0.88 0.015 260)",

        ember: "#db6a66",
        signal: "#d44c47",
        "signal-foreground": "oklch(0.98 0.01 85)",
        wood: "#a92c28",
        cream: "oklch(0.96 0.01 260)",
        ink: "#1a1a1a",
        sky: "oklch(0.82 0.06 260)",
        leaf: "#8c2521",
        smoke: "oklch(0.48 0.03 265)",
        ash: "oklch(0.94 0.012 260)",
        haze: "oklch(0.93 0.015 260)",
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
