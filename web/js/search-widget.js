function debounce(fn, ms) {
  let t;
  return function (...args) {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), ms);
  };
}

window.SearchWidget = window.SearchWidget || (function () {
  const state = new WeakMap();

  function buildUrl(cfg, q, page) {
    const url = new URL(cfg.endpoint, window.location.origin);
    url.searchParams.set(cfg.paramName, q);
    if (cfg.type) url.searchParams.set('type', cfg.type);
    if (cfg.parentId) url.searchParams.set('parent_id', cfg.parentId);
    if (cfg.categoryId) url.searchParams.set('category_id', cfg.categoryId);
    if (cfg.perPage) url.searchParams.set('per_page', String(cfg.perPage));
    if (page && page > 1) url.searchParams.set('page', String(page));
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
    const meta = fragment.querySelector('.search-meta');
    if (!meta) return null;
    const np = meta.getAttribute('data-next-page');
    return np ? parseInt(np, 10) : null;
  }
  
  function renderSkeletons(cfg) {
    const perPage = cfg.perPage || 6;
    let html = '<div class="row">';
    for (let i = 0; i < perPage; i++) {
      html += `
        <div class="col-md-4 mb-4">
          <div class="card h-100 skeleton-card" aria-hidden="true">
            <div class="skeleton-img card-img-top placeholder" style="aspect-ratio: 16/9; background-color: #e9ecef;"></div>
            <div class="card-body">
              <h5 class="card-title placeholder-glow">
                <span class="placeholder col-6"></span>
              </h5>
              <p class="card-text placeholder-glow">
                <span class="placeholder col-7"></span>
                <span class="placeholder col-4"></span>
                <span class="placeholder col-4"></span>
                <span class="placeholder col-6"></span>
              </p>
            </div>
            <div class="card-footer p-0 border-0">
              <span class="btn btn-secondary disabled placeholder col-12 rounded-0 rounded-bottom py-2"></span>
            </div>
          </div>
        </div>`;
    }
    html += '</div>';
    cfg.resultsEl.innerHTML = html;
  }

  async function fetchAndRender(cfg, q, page, append) {
    clearError(cfg);
    const st = state.get(cfg.root) || {};
    if (st.controller) st.controller.abort();
    const controller = new AbortController();
    state.set(cfg.root, { controller, lastQ: q, page: page || 1, nextPage: st.nextPage || null });

    if (cfg.spinnerEl) {
      cfg.spinnerEl.classList.remove('d-none');
      const btnContent = cfg.spinnerEl.closest('button')?.querySelector('.search-button-content');
      if (btnContent) btnContent.classList.add('d-none');
    }

    if (!append) {
      renderSkeletons(cfg);
    }

    try {
      const url = buildUrl(cfg, q, page || 1);
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
        cfg.resultsEl.appendChild(rows);
      } else if (!append) {
        cfg.resultsEl.innerHTML = html.trim();
      }

      // Update clear button visibility based on whether results are present
      if (cfg.clearEl) {
        const hasResults = cfg.resultsEl.querySelector('.row') !== null;
        if (q.length > 0 && !hasResults) {
          cfg.clearEl.classList.remove('d-none');
        } else {
          cfg.clearEl.classList.add('d-none');
        }
      }

      const st2 = state.get(cfg.root) || {};
      st2.nextPage = nextPage || null;
      state.set(cfg.root, st2);

      if (cfg.loadMoreEl) {
        if (nextPage && Number(nextPage) > 0) {
          cfg.loadMoreEl.classList.remove('d-none');
        } else {
          cfg.loadMoreEl.classList.add('d-none');
        }
      }
    } catch (e) {
      if (e.name === 'AbortError') return;
      setError(cfg, e.message || 'Request failed');
    } finally {
      if (cfg.spinnerEl) {
        cfg.spinnerEl.classList.add('d-none');
        const btnContent = cfg.spinnerEl.closest('button')?.querySelector('.search-button-content');
        if (btnContent) btnContent.classList.remove('d-none');
      }
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
      loadMoreEl: document.getElementById(root.getAttribute('data-load-more-id')),
      categoriesEl: document.getElementById(root.getAttribute('data-categories-id')),
      clearEl: document.getElementById(root.id + '-clear'),
      categoryId: root.getAttribute('data-selected-category-id') || null,
      minLen: 2,
    };
    if (!cfg.endpoint || !cfg.formEl || !cfg.inputEl || !cfg.resultsEl) return;

    const onType = debounce(() => {
      const q = cfg.inputEl.value.trim();
      const hasResults = cfg.resultsEl.querySelector('.row') !== null;
      if (cfg.clearEl) {
        if (q.length > 0 && !hasResults) {
          cfg.clearEl.classList.remove('d-none');
        } else {
          cfg.clearEl.classList.add('d-none');
        }
      }
      if (q.length >= cfg.minLen || q.length === 0) {
        // Reset to the first page
        fetchAndRender(cfg, q, 1, false);
      } else {
        // show placeholder when too short
        const emptyMsg = cfg.resultsEl.getAttribute('data-empty') || '';
        cfg.resultsEl.innerHTML = emptyMsg ? '<div class="text-muted">' + emptyMsg + '</div>' : '';
        if (cfg.loadMoreEl) cfg.loadMoreEl.classList.add('d-none');
      }
    }, cfg.debounceMs);

    if (cfg.categoriesEl) {
      cfg.categoriesEl.addEventListener('click', function (ev) {
        const btn = ev.target.closest('[data-category-id]');
        if (!btn) return;

        const catId = btn.getAttribute('data-category-id') || null;
        if (cfg.categoryId === catId) {
          // No change
          return;
        }

        // Deselect previous
        const prev = cfg.categoriesEl.querySelector('.btn-secondary');
        if (prev) {
          prev.classList.remove('btn-secondary');
          prev.classList.add('btn-outline-secondary');
        }

        cfg.categoryId = catId;
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-secondary');

        onType();
      });
    }

    cfg.inputEl.addEventListener('input', onType);
    cfg.formEl.addEventListener('submit', function (ev) {
      ev.preventDefault();
      onType();
    });

    if (cfg.clearEl) {
      cfg.clearEl.addEventListener('click', function () {
        cfg.inputEl.value = '';
        onType();
        cfg.inputEl.focus();
      });
    }

    if (cfg.loadMoreEl) {
      cfg.loadMoreEl.addEventListener('click', function () {
        const st = state.get(cfg.root) || {};
        const next = st.nextPage || null;
        if (!next) return;
        const q = cfg.inputEl.value.trim();
        fetchAndRender(cfg, q, next, true);
      });
    }

    // Initial load
    const q0 = (cfg.inputEl.value || '').trim();
    const hasInitialResults = cfg.resultsEl.querySelector('.row') !== null;
    if (!hasInitialResults) {
      fetchAndRender(cfg, q0, 1, false);
    } else {
      // Sync state if initial results were pre-rendered
      const np = parseNextPage(cfg.resultsEl);
      state.set(cfg.root, { lastQ: q0, page: 1, nextPage: np });
      if (np) {
        cfg.loadMoreEl.classList.remove('d-none');
      }
    }
  }

  function autoInit() {
    document.querySelectorAll('[data-search]')
      .forEach((root) => initOne(root));
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', autoInit);
  } else {
    autoInit();
  }

  return { };
})();
