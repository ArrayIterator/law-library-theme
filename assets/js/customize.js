(function ($, wp) {
    if (!$ || !wp) {
        return;
    }
    if (!wp.customize || !wp.customize.bind) {
        return;
    }
    wp.customize.bind('ready', () => {
        if (!$.fn.select2) {
            return;
        }
        wp.customize.section.each(function (sections) {
            if (sections.params.panel !== 'law_lib') {
                return;
            }
            let containerParent = $(sections.contentContainer);
            containerParent.find('select, select.select2').select2({
                dropdownParent: containerParent
            })
        });
    });
})(window.jQuery, wp);
