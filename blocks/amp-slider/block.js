(function (wp) {
    const {blocks, element, components, data, i18n, blockEditor, serverSideRender} = wp,
    {registerBlockType} = blocks;
    const {
            useBlockProps,
            InspectorControls
        } = blockEditor,
        {useSelect} = data,
        {
            createElement,
            Fragment
        } = element,
        {__} = i18n,
        {
            SelectControl,
            RangeControl,
            PanelBody,
            FormTokenField
        } = components;
    const blockName = 'law-lib-blocks/amp-slider';
    const blockAttributes = {
        apiVersion: 2,
        title: __('Amp Slider', 'law-lib'),
        icon: 'megaphone',
        category: 'widgets',
        attributes: {
            content: {
                type: "string",
            },
            categories: {
                label: __('Categories', 'law-lib'),
                type: "array",
                items: {
                    type: "number"
                }
            },
            tags: {
                label: __('Tags', 'law-lib'),
                type: "array",
                items: {
                    type: "number"
                }
            },
            total:  {
                type: 'number',
                label: __('Total Posts', 'law-lib'),
                defaultValue : 5,
                min: 3,
                max: 20,
                initialPosition: 5
            },
            show_thumbnail:  {
                type: 'string',
                label: __('Show Thumbnail', 'law-lib'),
                defaultValue : 'yes',
                options: [
                    {
                        label : __('Yes', 'law-lib'),
                        value: 'yes'
                    },
                    {
                        label : __('No', 'law-lib'),
                        value: 'no'
                    },
                ]
            },
            show_title:  {
                type: 'string',
                label: __('Show Title', 'law-lib'),
                defaultValue : 'yes',
                options: [
                    {
                        label : __('Yes', 'law-lib'),
                        value: 'yes'
                    },
                    {
                        label : __('No', 'law-lib'),
                        value: 'no'
                    },
                ]
            },
            show_category:  {
                type: 'string',
                label: __('Show Main Category', 'law-lib'),
                defaultValue : 'yes',
                options: [
                    {
                        label : __('Yes', 'law-lib'),
                        value: 'yes'
                    },
                    {
                        label : __('No', 'law-lib'),
                        value: 'no'
                    },
                ]
            },
            show_date:  {
                label: __('Show Date', 'law-lib'),
                type: 'string',
                defaultValue : 'yes',
                options: [
                    {
                        label : __('Yes', 'law-lib'),
                        value: 'yes'
                    },
                    {
                        label : __('No', 'law-lib'),
                        value: 'no'
                    },
                ]
            },
            order:  {
                label: __('Order By', 'law-lib'),
                type: 'string',
                defaultValue : 'date',
                options: [
                    {
                        label : __('Date', 'law-lib'),
                        value: 'date'
                    },
                    {
                        label : __('ID', 'law-lib'),
                        value: 'id'
                    },
                    {
                        label : __('Title', 'law-lib'),
                        value: 'title'
                    },
                    {
                        label : __('Author', 'law-lib'),
                        value: 'author'
                    },
                ]
            },
            sort:  {
                label: __('Sort By', 'law-lib'),
                type: 'string',
                defaultValue : 'desc',
                options: [
                    {
                        label : __('Descending', 'law-lib'),
                        value: 'desc'
                    },
                    {
                        label : __('Ascending', 'law-lib'),
                        value: 'asc'
                    },
                    {
                        label : __('Random', 'law-lib'),
                        value: 'rand'
                    },
                ]
            },
        },
        edit: function (props) {
            let categories = useSelect(select =>
                select('core').getEntityRecords('taxonomy', 'category')
            ) || [];
            let tags = useSelect(select =>
                select('core').getEntityRecords('taxonomy', 'post_tag', {per_page: -1, page: 1})
            ) || [];

            const {setAttributes, attributes} = props;
            const _attrs = blockAttributes.attributes;
            let findCategories = (arrayCategory) => {
                let category = {};
                (categories||[]).map(({id, name}) => {
                    arrayCategory.forEach((e) => {
                        if (e === name) {
                            category[id] = name;
                        }
                    });
                });
                return category;
            };
            let findTags = (arrayTags) => {
                let tag = {};
                (tags||[]).map(({id, name}) => {
                    arrayTags.forEach((e) => {
                        if (e === name) {
                            tag[id] = name;
                        }
                    });
                });
                return tag;
            };

            attributes.categories = (attributes.categories || []).map((e) => parseInt(e));
            let cats = []
            for (let ie in categories) {
                if (attributes.categories.indexOf(categories[ie].id) > -1) {
                    cats.push(categories[ie].name);
                }
            }
            attributes.tags = (attributes.tags || []).map((e) => parseInt(e));
            let tag = [];
            for (let ie in tags) {
                if (attributes.tags.indexOf(tags[ie].id) > -1) {
                    tag.push(tags[ie].name);
                }
            }

            return createElement(
                Fragment,
                {},
                createElement(
                    Fragment,
                    {},
                    createElement(
                        InspectorControls,
                        {},
                        createElement(
                            PanelBody,
                            {
                                title: __('Related Post Setting', 'law-lib'),
                                initialOpen: true
                            },
                            createElement(
                                RangeControl,
                                Object.assign({
                                    onChange: total => setAttributes({total}),
                                    value: attributes.total ||_attrs.total.defaultValue,
                                }, _attrs.total)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: show_thumbnail => setAttributes({show_thumbnail}),
                                    value: attributes.show_thumbnail ||_attrs.show_thumbnail.defaultValue,
                                }, _attrs.show_thumbnail)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: show_category => setAttributes({show_category}),
                                    value: attributes.show_category ||_attrs.show_category.defaultValue,
                                }, _attrs.show_category)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: show_date => setAttributes({show_date}),
                                    value: attributes.show_date ||_attrs.show_date.defaultValue,
                                }, _attrs.show_date)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: show_title => setAttributes({show_title}),
                                    value: attributes.show_title ||_attrs.show_title.defaultValue,
                                }, _attrs.show_title)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: order => setAttributes({order}),
                                    value: attributes.order ||_attrs.order.defaultValue,
                                }, _attrs.order)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: sort => setAttributes({sort}),
                                    value: attributes.sort ||_attrs.sort.defaultValue,
                                }, _attrs.sort)
                            ),
                            createElement(
                                FormTokenField,
                                Object.assign({
                                        //__experimentalExpandOnFocus: true,
                                        onChange: (tokens) => {
                                            this.__experimentalExpandOnFocus = true;
                                            if (!categories || categories.length === 0) {
                                                return;
                                            }
                                            cats = [];
                                            let ids = [],
                                                i,
                                                found = findCategories(tokens);
                                            for (i in found) {
                                                cats.push(found[i]);
                                                ids.push(i);
                                            }
                                            setAttributes({categories: ids});
                                        },
                                        onFocus: function () {
                                            this.__experimentalExpandOnFocus = true;
                                        },
                                        onFocusOutside: function () {
                                            this.__experimentalExpandOnFocus = false;
                                        },
                                        value: cats,
                                        multiple: true,
                                        suggestions: categories.map(({name}) => name),
                                    },
                                    _attrs.categories
                                )
                            ),
                            createElement(
                                FormTokenField,
                                Object.assign({
                                        //__experimentalExpandOnFocus: true,
                                        onChange: (tokens) => {
                                            this.__experimentalExpandOnFocus = true;
                                            if (!tags || tags.length === 0) {
                                                return;
                                            }
                                            tag = [];
                                            let ids = [],
                                                i,
                                                found = findTags(tokens);
                                            for (i in found) {
                                                tag.push(found[i]);
                                                ids.push(i);
                                            }
                                            setAttributes({tags: ids});
                                        },
                                        onFocus: function () {
                                            this.__experimentalExpandOnFocus = true;
                                        },
                                        onFocusOutside: function () {
                                            this.__experimentalExpandOnFocus = false;
                                        },
                                        value: tag,
                                        multiple: true,
                                        suggestions: tags.map(({name}) => name),
                                    },
                                    _attrs.tags
                                )
                            ),
                        ),
                    ),
                    createElement(
                        'div',
                        useBlockProps(),
                        createElement(
                            serverSideRender,
                            {
                                block: blockName,
                                attributes: attributes,
                            }
                        )
                    )
                )
            );
        },
    } ;
    registerBlockType( blockName, blockAttributes);
})(window.wp);
