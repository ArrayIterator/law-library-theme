(function (wp) {
    const {blocks, element, components, i18n, blockEditor, serverSideRender} = wp,
    {registerBlockType} = blocks;
    const {
            useBlockProps,
            InspectorControls
        } = blockEditor,
        {
            createElement,
            Fragment
        } = element,
        {__} = i18n,
        {
            SelectControl,
            RangeControl,
            PanelBody,
        } = components;
    const blockName = 'law-lib-blocks/popular-posts';
    const blockAttributes = {
        apiVersion: 2,
        title: __('Popular Post', 'law-lib'),
        icon: 'megaphone',
        category: 'widgets',
        attributes: {
            days: {
                type: 'number',
                label: __('Day Range', 'law-lib'),
                min: 3,
                max: 60,
                defaultValue : 30,
                initialPosition: 30
            },
            total:  {
                type: 'number',
                label: __('Total Posts', 'law-lib'),
                defaultValue : 5,
                min: 3,
                max: 30,
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
            show_views:  {
                type: 'string',
                label: __('Show Total Views', 'law-lib'),
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
        },
        edit: function (props) {
            const {setAttributes, attributes} = props;
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
                                title: __('Popular Post Setting', 'law-lib'),
                                initialOpen: true
                            },
                            createElement(
                                RangeControl,
                                Object.assign({
                                    onChange: (days) => setAttributes({days}),
                                    value: attributes.days || blockAttributes.attributes.days.defaultValue,
                                }, blockAttributes.attributes.days)
                            ),
                            createElement(
                                RangeControl,
                                Object.assign({
                                    onChange: (total) => setAttributes({total}),
                                    value: attributes.total || blockAttributes.attributes.total.defaultValue,
                                }, blockAttributes.attributes.total)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: (show_thumbnail) => setAttributes({show_thumbnail}),
                                    value: attributes.show_thumbnail || blockAttributes.attributes.show_thumbnail.defaultValue
                                }, blockAttributes.attributes.show_thumbnail)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: (show_category) => setAttributes({show_category}),
                                    value: attributes.show_category || blockAttributes.attributes.show_category.defaultValue
                                }, blockAttributes.attributes.show_category)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: (show_views) => setAttributes({show_views}),
                                    value: attributes.show_views || blockAttributes.attributes.show_views.defaultValue
                                }, blockAttributes.attributes.show_views)
                            ),
                            createElement(
                                SelectControl,
                                Object.assign({
                                    onChange: (show_date) => setAttributes({show_date}),
                                    value: attributes.show_date || blockAttributes.attributes.show_date.defaultValue
                                }, blockAttributes.attributes.show_date)
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
