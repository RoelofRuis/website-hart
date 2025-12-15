(function(){
  function initOne(ta){
    if (!ta || ta._easymde || typeof EasyMDE === 'undefined') return;
    var placeholder = ta.dataset.placeholder ? '## ' + ta.dataset.placeholder : '';
    ta._easymde = new EasyMDE({
      element: ta,
      spellChecker: false,
      status: false,
      placeholder: placeholder,
      renderingConfig: { singleLineBreaks: false, codeSyntaxHighlighting: false },
      autosave: { enabled: false },
      toolbar: [
        'bold', 'italic', 'heading', '|', 'unordered-list', 'ordered-list', '|',
        'link', 'quote', '|', 'preview', 'guide'
      ]
    });
  }
  function init(){
    document.querySelectorAll('textarea[data-markdown-editor]')
      .forEach(initOne);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
