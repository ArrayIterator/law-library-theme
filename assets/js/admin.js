(function ($) {
    if (!$) {
        return;
    }
    const $document = $(document);
    const now = window.pagenow;
    let menuDiv = '.menu_law_lib_icon_div',
        iconInputClass = '.law_lib_icon_input',
        iconSelector = '.law_lib_icon',
        navMenuInputClass = 'input.nav-menu-id',
        menuPreviewDiv = '.law_lib_icon_preview .law_lib_icon_preview_container',
        $divIcon = $('<div class="law-lib-icon-wrapper" id="law-lib-icon-wrapper"></div>'),
        $search = $('<input type="search" aria-label="search icon" value="" class="law-lib-search-icon-input">');
    let law_lib_icon_font = window['law_lib_icon_font'] || null;
    function change_color_picker(e, ui)
    {
        $(this)
            .parentsUntil(menuDiv)
            .find(iconSelector)
            .css({
                color: this.value && /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(this.value.toString()) === true
                    ? ((!ui || !ui.color) ? '' : ui.color.toString())
                    : ''
            });
    }

    function changeMenuInput() {
        if (!law_lib_icon_font || typeof law_lib_icon_font !== 'object') {
            return;
        }
        let $this = $(this),
            $root = $this.parentsUntil(menuDiv),
            $prev = $root.find(menuPreviewDiv),
            id   = $root.find(navMenuInputClass).val(),
            val = $this.val();
        if (!val || !id || law_lib_icon_font.indexOf(val) < 0) {
            $prev.html('');
            return;
        }
        let $btn = $('<div class="law_lib_close">&times;</div>'),
            $cl = $(
                '<input type="text" ' +
                'maxLength="7" class="law-lib-color-picker"'+
                ' name="law_lib_menu_item_icon['+id+'][color]"'+
                ' value="">'
            );
        $prev.html('<div class="law_lib_icon"><i class="'+val+'"></i></div>');
        $prev.append($btn);
        $prev.append($cl);
        if ($.fn.wpColorPicker) {
            $cl.wpColorPicker({change: change_color_picker, clear:change_color_picker});
        }
    }
    function buildIcon()
    {
        if (!law_lib_icon_font || typeof law_lib_icon_font !== 'object') {
            return;
        }
        let $search_wrapper = $('<div class="law-lib-search-icon"/>')
        $search.on('keyup', function (e) {
            e.preventDefault();
            let val = $(this).val().trim().toLowerCase(),
                $parent = $search_wrapper.parent(),
                icons = $parent.find('[data-icon]');
            if (!val) {
                icons.removeClass('hide');
                return;
            }
            val = val.replace(/[^a-z_\-]/, '').replace(/^ic[\-]?\s*/, '').replace(/[\s]+/g, ' ').trim();
            val = val.replace(/\s/, '-');
            let icon = $parent.find('[data-icon*="'+val+'"]');
            if (!icon.length) {
                icon = $parent.find('[data-icon*="'+val.replace(/[-]/g, '')+'"]')
            }
            icons.not(icon).addClass('hide');
            if (icon.length) {
                icon.removeClass('hide');
            }
        });

        $divIcon.append($search_wrapper);
        $search_wrapper.html($search);
        for (let key in law_lib_icon_font) {
            let icon = law_lib_icon_font[key],
                icon_text = 'Icon ' + icon
                    .replace(/^icofont-/g, '')
                    .replace(/[-\s]/g, ' '),
                $yi = $(
                    '<span title="'+
                    icon_text+
                    '" class="law-lib-icon-context" data-icon="'+
                    icon
                    +'">' +
                    '<i class="'+icon+'"></i>'+
                    '</span>'
                );

            $divIcon.append($yi);
        }

        return $divIcon;
    }

    $document.on('click', '.law-lib-icon-wrapper .law-lib-icon-context', function (e) {
        e.preventDefault();
        let icon = $(this).data('icon'),
            $pr  = $(this).closest(menuDiv).find(iconInputClass);
        $pr.val(icon);
        changeMenuInput.call($pr, e);
        $divIcon.hide();
    });

    $document.ready(function() {
        if ($.fn.select2 && now === 'widgets' && wp) {
            $document.on('widget-added', function (event, widgetContainer) {
                let widgetForm = widgetContainer.find( '> .widget-inside > .form, > .widget-inside > form' ),
                    select2 = widgetForm.find('[data-select="select2"], .select2');
                if (!select2.length) {
                    return;
                }
                select2.on('remove', function () {
                    $(this).select2('destroy');
                });
                select2.not('[data-select-id]').select2();
            });
        }
        if (now === 'nav-menus') {
            (function () {
                if (!law_lib_icon_font || typeof law_lib_icon_font !== 'object') {
                    return;
                }
                let $icon = buildIcon();
                $(window).on('keyup', iconInputClass, changeMenuInput);
                $document.on('click', menuDiv + ' button.icon-chooser', function (e) {
                    e.preventDefault();
                    let $this = $(this);
                    if (!$this.next().is($icon)) {
                        $search.val('').trigger('keyup');
                    }
                    if ($icon.is(':visible')) {
                        $icon.hide();
                        return;
                    }
                    $this.after($icon);
                    $icon.show();
                    $document.on('click', function (e) {
                        if (!$(e.target).closest($icon).length && !$(e.target).closest('.icon-chooser').length) {
                            $icon.hide();
                        }
                    });
                });
                $document.on('keyup', '.law-lib-color-picker', function () {
                    let $this = $(this),
                        val = $this.val();
                    if (!val) {
                        return;
                    }
                    if (/^#?[0-9a-f]+$/i.test(val) === false) {
                        val = val.replace(/[^0-9a-f]/g, '').substr(0, 6);
                        $this.val('#' + val);
                    } else if (/^#/.test(val) === false) {
                        $this.val('#' + val);
                    }
                });

                $document.on('click', '.law_lib_icon_preview .law_lib_close', function (e) {
                    e.preventDefault();
                    let $pr = $(this).closest(menuDiv);
                    $pr.find(iconInputClass).val('');
                    $pr.find(menuPreviewDiv).html('');
                });
                if ($.fn.wpColorPicker) {
                    $('.law-lib-color-picker').wpColorPicker({
                        change: change_color_picker,
                        clear: change_color_picker
                    });
                }
            })();
        }
    });
})(window.jQuery);
