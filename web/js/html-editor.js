$(function() {
    function initOne(ta) {
        var $ta = $(ta);
        if ($ta.data('summernote-initialized')) return;

        var placeholder = $ta.data('placeholder') || '';
        var showFullscreen = $ta.data('show-fullscreen') !== 0;
        var showImage = $ta.data('show-image') !== 0;

        var insertItems = ['link'];
        if (showImage) {
            insertItems.push('picture');
        }
        insertItems.push('video');

        var viewItems = [];
        if (showFullscreen) {
            viewItems.push('fullscreen');
        }
        viewItems.push('codeview');
        viewItems.push('help');

        $ta.summernote({
            placeholder: placeholder,
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', insertItems],
                ['view', viewItems]
            ]
        });
        
        $ta.data('summernote-initialized', true);
    }

    function init() {
        $('textarea[data-html-editor]').each(function() {
            initOne(this);
        });
    }

    init();
    
    // Support for dynamic fields if any (e.g. in kartik-v depdrop or similar)
    $(document).on('ajaxComplete', function() {
        init();
    });
});
