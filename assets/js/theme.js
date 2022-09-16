(function (amp) {
    if (!amp || !amp.getState) {
        return;
    }
    const id = (e) => document.getElementById(e),
        event = (e, n, c) => e.addEventListener(n, c),
        setState = (...args) => amp.setState(...args),
        getState = (n, c) => amp.getState(n).then(c).catch(() => null),
        topBar = id('top-bar'),
        inputSearch = id('search-section-input-search'),
        topBarBtn = topBar ? topBar.querySelector('button') : null,
        homeUrl = window.location.origin,
        loopParent = document.querySelector('.loop-container'),
        loopContainer = document.querySelectorAll('.content-container');

    if (inputSearch) {
        event(inputSearch, 'keyup', (e) => {
            setState({searchQuerySpaceCheck: e.target.value.trim()});
        });
        event(inputSearch, 'change', (e) => {
            let value = e.target.value,
                values = value.replace(/[\s]+/g, ' ').trim();
            if (values !== value) {
                e.target.value = values;
            }
            setState({searchQuerySpaceCheck: values});
        });
    }
    /* TOP BAR */
    if (topBarBtn && topBar) {
        event(document, 'click', (e) => {
            if (!topBar.contains(e.target) && !topBar.contains(e.target)) {
                getState('searchBarShown', (e) => {
                    if (e === true || e === 'true') {
                        setState({searchBarShown: false});
                    }
                });
            }
        });
    }
    /* LOOP */
    let homeRegex = new RegExp('^' + homeUrl.replace(/(https?:\/\/[^/]+)[/].+/, '$1') + '/wp-json/law-lib/posts', 'i');
    loopContainer.forEach(function (container) {
        let default_find = container.querySelector('amp-img'),
            toGet = [
                'width',
                'height',
                'sizes',
                'data-hero-candidate',
                'class',
                'layout',
                'data-hero'
            ],
            attributes = {
                'layout': 'intrinsic',
            };
        if (default_find) {
            for (let ie in default_find.attributes) {
                let ien = default_find.attributes[ie];
                if (toGet.indexOf(ien.name) < 0) {
                    continue;
                }
                attributes[ien.name] = ien.value;
            }
        }

        let button = container.querySelector('[data-button]');
        if (!button || button.getAttribute('data-button') !== 'load-more') {
            return;
        }
        let totalResult = false,
            offset = button.getAttribute('data-offset'),
            restUrl = button.getAttribute('data-rest-url');
        if (!offset || !restUrl || typeof restUrl !== 'string' || !homeRegex.test(restUrl)) {
            return;
        }
        if (typeof offset === 'string' && !/[^0-9]/.test(offset)) {
            offset = parseInt(offset);
        } else {
            return;
        }
        let buttonLoading = button.getAttribute('data-loading'),
            buttonContent = button.innerHTML,
            buttonProcess = false,
            ei = 5,
            currentParent = button.parentNode,
            clickEventLoad = function (evt) {
                evt.preventDefault();
                if (buttonProcess) {
                    return;
                }

                let btn = evt.target,
                    Url = new URL(restUrl);

                btn.setAttribute('disabled', true);
                btn.innerHTML = buttonLoading;

                Url.searchParams.append('offset', offset);
                fetch(
                    Url.href
                ).then((response) => {
                    btn.removeAttribute('disabled');
                    btn.innerHTML = buttonContent;
                    response.json().then((json) => {
                        totalResult = json['item_total'];
                        offset += json.items.length;
                        let i;
                        for (i in json.items) {
                            let div = document.createElement('div');
                            div.innerHTML = json.items[i].content;
                            div = div.childNodes[0];
                            div.querySelectorAll('img').forEach(function (e) {
                                let new_tag = document.createElement('amp-img');
                                for (let ie in attributes) {
                                    new_tag.setAttribute(ie, attributes[ie]);
                                }
                                for (let i in e.attributes) {
                                    let at = e.attributes[i];
                                    new_tag.setAttribute(at.name, at.value);
                                }
                                if (!new_tag.hasAttribute('layout')) {
                                    new_tag.setAttribute('layout', 'intrinsic');
                                }
                                e.replaceWith(new_tag);
                            })
                            container.insertBefore(div, currentParent);
                        }
                        if (totalResult <= offset) {
                            btn.removeEventListener('click', clickEventLoad);
                            btn.setAttribute('hidden', true);
                            btn.remove();
                        }
                    });
                }).catch(() => {
                    btn.removeAttribute('disabled');
                    btn.innerHTML = buttonContent;
                });
            };
        for (; ei > 0; ei--) {
            if (currentParent.parentNode === container) {
                break;
            }
            currentParent = currentParent.parentNode;
        }
        button.addEventListener('click', clickEventLoad);
    });
})(AMP);
