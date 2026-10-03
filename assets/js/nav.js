(function () {
  var toggle = document.getElementById("mobile-toggle");
  var menu = document.getElementById("mobile-menu");
  var iconOpen = document.getElementById("menu-icon-open");
  var iconClose = document.getElementById("menu-icon-close");

  // Bewusst nur Tailwinds Basis-Klasse "hidden" (display:none) statt einer eigenen
  // max-height/opacity-Animation: die hängt von einer zusätzlichen Stylesheet-Regel ab,
  // die auf manchen Geräten nicht rechtzeitig griff — Menü blieb dauerhaft aufgeklappt
  // mit beiden Icons gleichzeitig sichtbar. "hidden" ist eine Basis-Utility und greift
  // garantiert, unabhängig von Responsive-Varianten oder Ladezeitpunkt von theme.css.
  if (toggle && menu) {
    toggle.addEventListener("click", function () {
      menu.classList.toggle("hidden");
      var isOpen = !menu.classList.contains("hidden");
      toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
      if (iconOpen) iconOpen.classList.toggle("hidden", isOpen);
      if (iconClose) iconClose.classList.toggle("hidden", !isOpen);
    });

    // Das Menü liegt als Overlay über dem Inhalt — ein Klick daneben schließt es.
    document.addEventListener("click", function (e) {
      if (menu.classList.contains("hidden") || menu.contains(e.target) || toggle.contains(e.target)) return;
      toggle.click();
    });
  }

  document.querySelectorAll(".nav-dropdown").forEach(function (dropdown) {
    var trigger = dropdown.querySelector(".nav-dropdown-trigger");
    var closeTimeout;

    function open() {
      clearTimeout(closeTimeout);
      dropdown.classList.add("is-open");
      if (trigger) trigger.setAttribute("aria-expanded", "true");
    }
    function scheduleClose() {
      closeTimeout = setTimeout(function () {
        dropdown.classList.remove("is-open");
        if (trigger) trigger.setAttribute("aria-expanded", "false");
      }, 150);
    }

    dropdown.addEventListener("mouseenter", open);
    dropdown.addEventListener("mouseleave", scheduleClose);
    if (trigger) {
      trigger.addEventListener("focus", open);
      trigger.addEventListener("click", function (e) {
        if (trigger.tagName === "BUTTON") {
          e.preventDefault();
          dropdown.classList.toggle("is-open");
        }
      });
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") dropdown.classList.remove("is-open");
    });
    document.addEventListener("click", function (e) {
      if (!dropdown.contains(e.target)) dropdown.classList.remove("is-open");
    });
  });
})();
