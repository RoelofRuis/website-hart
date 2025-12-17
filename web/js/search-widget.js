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
    const { resultsEl, spinnerEl, endpoint, paramName } = opts;
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
      url.searchParams.set(paramName, q);

      const headers = {'Accept': 'text/html'};
      const fetchOpts = { method: 'GET', headers, signal: st.controller.signal, credentials: 'same-origin' };

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
      minLen: 2,
    };

    const emptyMsg = resultsEl.getAttribute('data-empty');
    function showEmpty() {
      resultsEl.innerHTML = emptyMsg ? '<div class="text-muted">' + emptyMsg + '</div>' : '';
    }

    // Initial state: if no query, fetch default courses; if short query, show hint; if >= minLen, search
    if (!inputEl.value || inputEl.value.length === 0) {
      runSearch(opts, '');
    } else if (inputEl.value.length < opts.minLen) {
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
      } else if (q.length === 0) {
        // Request default results (courses) when input is empty
        runSearch(opts, '');
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
      } else if (q.length === 0) {
        // Submit with empty query to get default results
        runSearch(opts, '');
      } else {
        showEmpty();
      }
    });
  }

  return { init };
})();
