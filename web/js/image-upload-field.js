(function(){
  function initOne(input){
    if (!input || input._inited) return;
    input._inited = true;
    input.addEventListener('change', async function(){
      if (!input.files || !input.files[0]) return;
      const file = input.files[0];
      const fd = new FormData();
      fd.append('file', file);
      if (input.dataset.csrfParam && input.dataset.csrfToken) {
        fd.append(input.dataset.csrfParam, input.dataset.csrfToken);
      }
      const hidden = document.getElementById(input.dataset.targetHidden || '');
      const preview = document.getElementById(input.dataset.targetPreview || '');
      const help = document.getElementById(input.dataset.helpId || '');
      function setHelp(text, isError){ if(help){ help.textContent = text || ''; help.classList.toggle('text-danger', !!isError); } }
      setHelp('Uploading...', false);
      try {
        const res = await fetch(input.dataset.uploadUrl, { method: 'POST', body: fd, credentials: 'same-origin' });
        if (!res.ok) throw new Error('Upload failed ('+res.status+')');
        const data = await res.json();
        if (!data.success) throw new Error(data.message || 'Upload failed');
        if (hidden) hidden.value = data.url;
        if (preview) {
          if (preview.tagName === 'IMG') {
            preview.src = data.url;
          } else {
            const img = document.createElement('img');
            img.className = preview.className;
            img.style = preview.getAttribute('style');
            img.id = preview.id;
            img.src = data.url;
            preview.replaceWith(img);
          }
        }
        setHelp('Image uploaded.', false);
      } catch (e) {
        console.error(e);
        setHelp(e.message, true);
      } finally {
        input.value = '';
      }
    });
  }
  function init(){
    document.querySelectorAll('input[type="file"][data-image-upload]')
      .forEach(initOne);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
