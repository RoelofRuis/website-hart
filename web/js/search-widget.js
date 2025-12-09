/* global window, document, fetch */

// Small helper: debounce a function
function debounce(fn, ms) {
  let t;
  return function debounced(...args) {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), ms);
  };
}

// Global namespace to init multiple widgets on a page
window.HartSearchWidget = window.HartSearchWidget || (function () {
  const stateByInput = new Map();

  async function runSearch(opts, q) {
    const { resultsEl, spinnerEl, endpoint, paramName, method } = opts;
    const key = opts.inputEl;
    let st = stateByInput.get(key) || { controller: null, lastQ: '' };

    // Abort previous in-flight request
    if (st.controller) {
      st.controller.abort();
    }
    st.controller = new AbortController();
    st.lastQ = q;
    stateByInput.set(key, st);

    // Loading UI
    if (spinnerEl) spinnerEl.classList.remove('d-none');

    try {
      const url = new URL(endpoint, window.location.origin);
      if (method === 'GET') {
        url.searchParams.set(paramName, q);
      }

      const headers = {};
      const fetchOpts = { method, headers, signal: st.controller.signal, credentials: 'same-origin' };
      let body = null;
      if (method !== 'GET') {
        headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
        const csrfParam = document.querySelector('meta[name="csrf-param"]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const params = new URLSearchParams();
        params.set(paramName, q);
        if (csrfParam && csrfToken) params.set(csrfParam.getAttribute('content') || '_csrf', csrfToken.getAttribute('content') || '');
        body = params.toString();
        fetchOpts.body = body;
      }

      const res = await fetch(url.toString(), fetchOpts);
      if (!res.ok) throw new Error('Search request failed: ' + res.status);
      const html = await res.text();
      resultsEl.innerHTML = html.trim();
    } catch (e) {
      if (e.name === 'AbortError') return; // benign
      // Render a minimal error state, but do not throw
      resultsEl.innerHTML = '<div class="alert alert-warning mb-0">' + (e.message || 'Search failed') + '</div>';
    } finally {
      if (spinnerEl) spinnerEl.classList.add('d-none');
    }
  }

  function init(options) {
    const inputEl = document.getElementById(options.inputId);
    const resultsEl = document.getElementById(options.resultsId);
    const spinnerEl = document.getElementById(options.spinnerId);
    const formEl = document.getElementById(options.formId);
    if (!inputEl || !resultsEl || !formEl) return;

    const opts = {
      inputEl,
      resultsEl,
      spinnerEl,
      endpoint: options.endpoint,
      paramName: options.paramName || 'q',
      method: (options.method || 'GET').toUpperCase(),
      minLen: 2,
    };

    const emptyMsg = resultsEl.getAttribute('data-empty');
    function showEmpty() {
      resultsEl.innerHTML = emptyMsg ? '<div class="text-muted">' + emptyMsg + '</div>' : '';
    }

    // Initial empty state
    if (!inputEl.value || inputEl.value.length < opts.minLen) {
      showEmpty();
    } else {
      // If page loads with query, fetch immediately to hydrate results
      runSearch(opts, inputEl.value);
    }

    // Debounced live search on input
    const onType = debounce(() => {
      const q = inputEl.value.trim();
      if (q.length >= opts.minLen) {
        runSearch(opts, q);
      } else {
        showEmpty();
      }
    }, options.debounceMs || 250);

    inputEl.addEventListener('input', onType);

    // Progressive enhancement: allow normal form submit (Enter)
    formEl.addEventListener('submit', function (ev) {
      // For full-page fallback, remove the next line.
      ev.preventDefault();
      const q = inputEl.value.trim();
      if (q.length >= opts.minLen) {
        runSearch(opts, q);
      } else {
        showEmpty();
      }
    });
  }

  return { init };
})();
