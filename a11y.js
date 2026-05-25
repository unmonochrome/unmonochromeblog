/* Accessibility widget: toggles large text, high contrast, reduce motion, spacing */
(function () {
  const storageKey = 'site_a11y_prefs_v1';

  function createToolbar() {
    const toolbar = document.createElement('div');
    toolbar.className = 'a11y-toolbar';

    const panel = document.createElement('div');
    panel.className = 'a11y-panel';
    panel.setAttribute('role', 'region');
    panel.setAttribute('aria-label', 'Opções de acessibilidade');

    const title = document.createElement('h4');
    title.textContent = 'Acessibilidade';
    panel.appendChild(title);

    const opts = [
      { id: 'large', label: 'Aumentar fonte' },
      { id: 'contrast', label: 'Alto contraste' },
      { id: 'motion', label: 'Reduzir animações' },
      { id: 'spacing', label: 'Maior espaçamento' }
    ];

    opts.forEach(o => {
      const row = document.createElement('div');
      row.className = 'a11y-option';

      const label = document.createElement('label');
      label.htmlFor = 'a11y-' + o.id;
      label.textContent = o.label;

      const btn = document.createElement('button');
      btn.id = 'a11y-' + o.id;
      btn.setAttribute('aria-pressed', 'false');
      btn.textContent = 'Off';
      btn.addEventListener('click', () => toggleOption(o.id, btn));

      row.appendChild(label);
      row.appendChild(btn);
      panel.appendChild(row);
    });

    const resetRow = document.createElement('div');
    resetRow.style.marginTop = '8px';
    const resetBtn = document.createElement('button');
    resetBtn.textContent = 'Redefinir';
    resetBtn.addEventListener('click', resetAll);
    resetRow.appendChild(resetBtn);
    panel.appendChild(resetRow);

    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'a11y-toggle-btn';
    toggleBtn.setAttribute('aria-label', 'Abrir opções de acessibilidade');
    toggleBtn.innerHTML = 'A';
    toggleBtn.addEventListener('click', () => panel.classList.toggle('open'));

    toolbar.appendChild(panel);
    toolbar.appendChild(toggleBtn);
    document.body.appendChild(toolbar);
  }

  function applyPrefs(prefs) {
    const html = document.documentElement;
    html.classList.toggle('a11y-large', !!prefs.large);
    html.classList.toggle('a11y-high-contrast', !!prefs.contrast);
    html.classList.toggle('a11y-reduce-motion', !!prefs.motion);
    html.classList.toggle('a11y-spacing', !!prefs.spacing);

    // update buttons state
    Object.keys(prefs).forEach(k => {
      const btn = document.getElementById('a11y-' + k);
      if (btn) {
        btn.textContent = prefs[k] ? 'On' : 'Off';
        btn.setAttribute('aria-pressed', prefs[k] ? 'true' : 'false');
      }
    });
  }

  function loadPrefs() {
    try {
      const raw = localStorage.getItem(storageKey);
      return raw ? JSON.parse(raw) : { large: false, contrast: false, motion: false, spacing: false };
    } catch (e) {
      return { large: false, contrast: false, motion: false, spacing: false };
    }
  }

  function savePrefs(prefs) {
    try { localStorage.setItem(storageKey, JSON.stringify(prefs)); } catch (e) {}
    // try to post to server endpoint if available
    try {
      const script = document.querySelector('script[src$="a11y.js"]');
      if (script && script.src) {
        const base = script.src.replace(/a11y\.js(\?.*)?$/, '');
        const url = base + 'backend/save_a11y.php';
        fetch(url, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(prefs),
          credentials: 'same-origin'
        }).catch(() => {});
      }
    } catch (e) {}
  }

  function toggleOption(id, btn) {
    const prefs = loadPrefs();
    prefs[id] = !prefs[id];
    savePrefs(prefs);
    applyPrefs(prefs);
  }

  function resetAll() {
    const prefs = { large: false, contrast: false, motion: false, spacing: false };
    savePrefs(prefs);
    applyPrefs(prefs);
  }

  // init
  document.addEventListener('DOMContentLoaded', () => {
    createToolbar();
    const prefs = loadPrefs();
    applyPrefs(prefs);
    // keyboard shortcut: Alt+Shift+A to toggle panel
    document.addEventListener('keydown', (e) => {
      if (e.altKey && e.shiftKey && e.key.toLowerCase() === 'a') {
        const panel = document.querySelector('.a11y-panel');
        if (panel) panel.classList.toggle('open');
      }
    });
    // REMOVIDO: botão extra do canto superior esquerdo
  });
})();