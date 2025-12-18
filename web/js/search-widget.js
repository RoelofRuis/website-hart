/* global window, document, fetch */

function debounce(fn, ms) {
  let t;
  return function (...args) {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), ms);
  };
}

window.HartSearchWidget = window.HartSearchWidget || (function () {
  const state = new WeakMap();

  function buildUrl(cfg, q, page, suppressEmpty) {
    const url = new URL(cfg.endpoint, window.location.origin);
    url.searchParams.set(cfg.paramName, q);
    if (cfg.type) url.searchParams.set('type', cfg.type);
    if (cfg.parentId) url.searchParams.set('parentId', cfg.parentId);
    if (cfg.perPage) url.searchParams.set('perPage', String(cfg.perPage));
    if (page && page > 1) url.searchParams.set('page', String(page));
    if (suppressEmpty) url.searchParams.set('suppressEmpty', '1');
    return url;
  }

  function setError(cfg, message) {
    if (!cfg.errorEl) return;
    cfg.errorEl.classList.remove('d-none');
    cfg.errorEl.innerHTML = '<div class="alert alert-danger mb-0">' + (message || 'Request failed') + '</div>';
  }

  function clearError(cfg) {
    if (cfg.errorEl) {
      cfg.errorEl.classList.add('d-none');
      cfg.errorEl.innerHTML = '';
    }
  }

  function parseNextPage(fragment) {
    const meta = fragment.querySelector('.hart-search-meta');
    if (!meta) return null;
    const np = meta.getAttribute('data-next-page');
    return np ? parseInt(np, 10) : null;
  }

  async function fetchAndRender(cfg, q, page, append) {
    clearError(cfg);
    const st = state.get(cfg.root) || {};
    if (st.controller) st.controller.abort();
    const controller = new AbortController();
    state.set(cfg.root, { controller, lastQ: q, page: page || 1 });

    if (cfg.spinnerEl) cfg.spinnerEl.classList.remove('d-none');
    try {
      const url = buildUrl(cfg, q, page || 1, append);
      const res = await fetch(url.toString(), {
        method: 'GET',
        headers: { 'Accept': 'text/html' },
        signal: controller.signal,
        credentials: 'same-origin'
      });
      if (!res.ok) throw new Error('' + res.status + ' ' + res.statusText);
      const html = await res.text();
      const temp = document.createElement('div');
      temp.innerHTML = html.trim();
      const rows = temp.querySelector('.row');
      const nextPage = parseNextPage(temp);

      if (!append) cfg.resultsEl.innerHTML = '';
      if (rows) {
        if (append) {
          cfg.resultsEl.appendChild(rows);
        } else {
          cfg.resultsEl.appendChild(rows);
        }
      } else if (!append) {
        // In case of no .row returned (e.g., messages), place whole content
        cfg.resultsEl.innerHTML = html.trim();
      }

      if (nextPage) {
        cfg.loadMoreBtn.classList.remove('d-none');
        cfg.loadMoreBtn.disabled = false;
        cfg.loadMoreBtn.dataset.nextPage = String(nextPage);
      } else {
        cfg.loadMoreBtn.classList.add('d-none');
        delete cfg.loadMoreBtn.dataset.nextPage;
      }
    } catch (e) {
      if (e.name === 'AbortError') return;
      setError(cfg, e.message || 'Request failed');
    } finally {
      if (cfg.spinnerEl) cfg.spinnerEl.classList.add('d-none');
    }
  }

  function initOne(root) {
    const cfg = {
      root,
      endpoint: root.getAttribute('data-endpoint'),
      paramName: root.getAttribute('data-param') || 'q',
      type: root.getAttribute('data-type') || 'all',
      parentId: root.getAttribute('data-parent-id') || '',
      perPage: parseInt(root.getAttribute('data-per-page') || '12', 10),
      debounceMs: parseInt(root.getAttribute('data-debounce') || '250', 10),
      formEl: document.getElementById(root.getAttribute('data-form-id')),
      inputEl: document.getElementById(root.getAttribute('data-input-id')),
      resultsEl: document.getElementById(root.getAttribute('data-results-id')),
      spinnerEl: document.getElementById(root.getAttribute('data-spinner-id')),
      errorEl: document.getElementById(root.getAttribute('data-error-id')),
      loadMoreBtn: document.getElementById(root.getAttribute('data-load-more-id')),
      minLen: 2,
    };
    if (!cfg.endpoint || !cfg.formEl || !cfg.inputEl || !cfg.resultsEl) return;

    const onType = debounce(() => {
      const q = cfg.inputEl.value.trim();
      if (q.length >= cfg.minLen || q.length === 0) {
        // Reset to first page
        fetchAndRender(cfg, q, 1, false);
      } else {
        // show placeholder when too short
        const emptyMsg = cfg.resultsEl.getAttribute('data-empty') || '';
        cfg.resultsEl.innerHTML = emptyMsg ? '<div class="text-muted">' + emptyMsg + '</div>' : '';
        cfg.loadMoreBtn.classList.add('d-none');
      }
    }, cfg.debounceMs);

    cfg.inputEl.addEventListener('input', onType);
    cfg.formEl.addEventListener('submit', function (ev) {
      ev.preventDefault();
      onType();
    });

    if (cfg.loadMoreBtn) {
      cfg.loadMoreBtn.addEventListener('click', () => {
        const q = cfg.inputEl.value.trim();
        const next = parseInt(cfg.loadMoreBtn.dataset.nextPage || '0', 10);
        if (!next) return;
        cfg.loadMoreBtn.disabled = true;
        fetchAndRender(cfg, q, next, true);
      });
    }

    // Initial load
    const q0 = (cfg.inputEl.value || '').trim();
    if (q0.length >= cfg.minLen || q0.length === 0) {
      fetchAndRender(cfg, q0, 1, false);
    }
  }

  function init(options) {
    // Backward compatibility: allow manual init with options (not used when data-init present)
    const root = document.getElementById(options.rootId) || document;
    const el = root.querySelector('[data-hart-search]');
    if (el) initOne(el);
  }

  function autoInit() {
    document.querySelectorAll('[data-hart-search]')
      .forEach((root) => initOne(root));
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', autoInit);
  } else {
    autoInit();
  }

  return { init };
})();
