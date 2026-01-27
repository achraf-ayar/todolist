(function () {
  const body = document.body;
  const toggleBtn = document.getElementById("darkModeToggle");
  const palette = document.getElementById("commandPalette");
  const paletteInput = document.getElementById("commandPaletteInput");
  const paletteList = document.getElementById("commandPaletteList");
  const paletteClose = document.getElementById("commandPaletteClose");
  const paletteBtn = document.getElementById("commandPaletteBtn");
  const quickSearchBtn = document.getElementById("quickSearchBtn");
  const toastStack = document.querySelector(".toast-stack");

  const THEME_KEY = "jikko-theme";

  function setTheme(mode) {
    body.classList.toggle("theme-dark", mode === "dark");
    localStorage.setItem(THEME_KEY, mode);
    if (toggleBtn) {
      toggleBtn.innerHTML =
        mode === "dark"
          ? '<i class="fas fa-sun"></i>'
          : '<i class="fas fa-moon"></i>';
    }
  }

  function initTheme() {
    const stored = localStorage.getItem(THEME_KEY);
    const prefersDark = window.matchMedia(
      "(prefers-color-scheme: dark)",
    ).matches;
    const mode = stored || (prefersDark ? "dark" : "light");
    setTheme(mode);
  }

  function toggleTheme() {
    const next = body.classList.contains("theme-dark") ? "light" : "dark";
    setTheme(next);
  }

  function formatRelative(dateStr) {
    const date = new Date(dateStr);
    if (Number.isNaN(date.getTime())) return dateStr;
    const now = new Date();
    const diffMs = date - now;
    const diffDays = Math.round(diffMs / (1000 * 60 * 60 * 24));

    if (Math.abs(diffDays) >= 7) {
      return date.toLocaleDateString("fr-FR", {
        day: "numeric",
        month: "short",
      });
    }
    if (diffDays === 0) return "Aujourd'hui";
    if (diffDays === 1) return "Demain";
    if (diffDays === -1) return "Hier";
    if (diffDays > 1) return `Dans ${diffDays} j`;
    return `Il y a ${Math.abs(diffDays)} j`;
  }

  function enhanceRelativeDates() {
    document.querySelectorAll(".relative-date[data-date]").forEach((el) => {
      const dateStr = el.getAttribute("data-date");
      el.textContent = formatRelative(dateStr);
      el.title = new Date(dateStr).toLocaleString("fr-FR");
    });
  }

  function showToast(title, desc) {
    if (!toastStack) return;
    const toast = document.createElement("div");
    toast.className = "toast";
    toast.innerHTML = `<div class="toast-body"><div class="toast-title">${title}</div><div class="toast-desc">${desc || ""}</div></div>`;
    toastStack.appendChild(toast);
    setTimeout(() => {
      toast.classList.add("fade-out");
      setTimeout(() => toast.remove(), 250);
    }, 2600);
  }

  function buildPalette(commands) {
    if (!palette || !paletteList) return;

    function render(items) {
      paletteList.innerHTML = "";
      if (!items.length) {
        const empty = document.createElement("div");
        empty.className = "command-item text-subtle";
        empty.textContent = "Aucun résultat";
        paletteList.appendChild(empty);
        return;
      }
      items.forEach((cmd, idx) => {
        const item = document.createElement("div");
        item.className = "command-item";
        item.setAttribute("role", "option");
        item.dataset.index = idx;
        item.innerHTML = `<i class="${cmd.icon}"></i><span>${cmd.label}</span>${cmd.shortcut ? `<span class="shortcut">${cmd.shortcut}</span>` : ""}`;
        item.addEventListener("click", () => {
          cmd.action();
          closePalette();
        });
        paletteList.appendChild(item);
      });
    }

    function openPalette() {
      palette.hidden = false;
      render(commands);
      paletteInput.value = "";
      paletteInput.focus();
    }

    function closePalette() {
      palette.hidden = true;
    }

    function filterCommands(query) {
      const q = query.toLowerCase();
      const filtered = commands.filter((c) =>
        c.label.toLowerCase().includes(q),
      );
      render(filtered);
    }

    paletteInput?.addEventListener("input", (e) =>
      filterCommands(e.target.value),
    );
    paletteClose?.addEventListener("click", closePalette);
    palette?.addEventListener("click", (e) => {
      if (e.target === palette) closePalette();
    });

    document.addEventListener("keydown", (e) => {
      const metaK = (e.ctrlKey || e.metaKey) && e.key.toLowerCase() === "k";
      if (metaK) {
        e.preventDefault();
        palette.hidden ? openPalette() : closePalette();
      }
      if (!palette.hidden && e.key === "Escape") {
        closePalette();
      }
      if (!palette.hidden && e.key === "Enter") {
        const first = paletteList.querySelector(".command-item");
        first?.click();
      }
    });

    paletteBtn?.addEventListener("click", openPalette);

    return { openPalette, closePalette, filterCommands };
  }

  function initShortcuts() {
    document.addEventListener("keydown", (e) => {
      if (e.target && ["INPUT", "TEXTAREA"].includes(e.target.tagName)) return;
      if (e.key === "n") {
        const btn = document.querySelector('[data-bs-target="#addTaskModal"]');
        if (btn) {
          e.preventDefault();
          btn.click();
        }
      }
    });
  }

  function init() {
    initTheme();
    toggleBtn?.addEventListener("click", toggleTheme);
    enhanceRelativeDates();
    initShortcuts();

    const commands = [
      {
        label: "Nouvelle tâche",
        icon: "fas fa-plus",
        shortcut: "n",
        action: () =>
          document.querySelector('[data-bs-target="#addTaskModal"]')?.click(),
      },
      {
        label: "Mes tâches",
        icon: "fas fa-list",
        action: () => (window.location.href = "index.php"),
      },
      {
        label: "Projets",
        icon: "fas fa-folder",
        action: () => (window.location.href = "projects.php"),
      },
      {
        label: "Kanban",
        icon: "fas fa-columns",
        action: () => (window.location.href = "kanban.php"),
      },
      {
        label: "Statistiques",
        icon: "fas fa-chart-line",
        action: () => (window.location.href = "stats.php"),
      },
      {
        label: "Tags",
        icon: "fas fa-hashtag",
        action: () => (window.location.href = "tags.php"),
      },
      { label: "Basculer le thème", icon: "fas fa-moon", action: toggleTheme },
    ];

    const paletteApi = buildPalette(commands);

    quickSearchBtn?.addEventListener("click", (e) => {
      e.preventDefault();
      paletteApi?.openPalette();
    });

    window.Jikko = {
      showToast,
      formatRelative,
      toggleTheme,
      openPalette: paletteApi?.openPalette,
    };
  }

  document.addEventListener("DOMContentLoaded", init);
})();
