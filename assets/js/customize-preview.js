(function ($, customize) {
    if (!$ || !customize) {
        return;
    }
    $(document).ready(function () {
        let $body = $('body'),
            wpCSS = $('#wp-custom-css'),
            filter_hex = function (data) {
            return typeof data === 'string' && data && /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(data)
                ? data
                : false
        };
        if (!wpCSS.length) {
            wpCSS = $('<style id="wp-custom-css"></style>');
            $('head').append(wpCSS)
        }
        let html = wpCSS.html();
        let _css = {};
        function change_color_with(key, value)
        {
            if (key) {
                value = filter_hex(value);
                if (!value) {
                    delete _css[key];
                } else {
                    _css[key] = value;
                }
            }
            let data = ':root {', i;
            for (i in _css) {
                data += '--' + i + ':'+ _css[i] +';';
            }
            data += '}';
            wpCSS.html(html + data);
        }
        const change_layout = function (key, matched, value) {
            value.bind(function (to) {
                change_color_with(key, to);
            })
        };
        let val = customize.settings.values || {}, i;
        for (i in val) {
            let matched = i.match(/layout\[([a-zA-Z0-9_\-]+)\]/);
            if (!matched || !matched[1]) {
                continue;
            }
            let _color = filter_hex(val[i]);
            if (_color) {
                _css[matched[1]] = val[i];
            }
            customize(i, (function (i, matched, key) {
                return (value) => {
                    change_layout(key, matched, value);
                }
            })(i, matched, matched[1]));
        }
        html = html.replace(/\s*([:]root\s*[{])([^}]*)([}])\s*/g, function (a, b, c, d) {
            c = c.replace(/[;]+/, ';').replace(/[;]*\s*$/, '').trim().split(';');
            let css = [];
            for (let i=0;c < i; i++) {
                let key = c.replace(/^[-]{2}/, '');
                if (/^[-]/.test(key)) {
                    css.push(c[i]);
                    continue;
                }
                key  = key.split(':').trim();
                if (_css[key] === undefined) {
                    css.push(c[i]);
                }
            }
            return b + css.join(';') + d;
        });
        html = html.replace(/\s*:root\s*[{]\s*[}]\s*/, '');
        wpCSS.html(html);
        change_color_with();
    });
})(window.jQuery, wp && wp.customize ? wp.customize : null);