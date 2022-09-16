<?php
if (!class_exists(WP_Sitemaps_Renderer::class)) {
    return;
}

if (!class_exists('Law_Lib_Sitemap_Renderer')) {
    class Law_Lib_Sitemap_Renderer extends WP_Sitemaps_Renderer
    {
        /**
         * @var string
         */
        protected $xmlns = '"http://www.sitemaps.org/schemas/sitemap/0.9';

        /**
         * @var array<string, string>
         */
        protected $xmlns_list = [
            'xsi'   => "https://www.w3.org/2001/XMLSchema-instance",
            'image' => "https://www.google.com/schemas/sitemap-image/1.1",
        ];

        /**
         * @var array<string, array<int, string>>
         */
        protected $xsi = [
            'schemaLocation' => [
                "https://www.sitemaps.org/schemas/sitemap/0.9",
                "https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd",
                "https://www.google.com/schemas/sitemap-image/1.1",
                "https://www.google.com/schemas/sitemap-image/1.1/sitemap-image.xsd"
            ]
        ];

        /**
         * @param array $url_list
         *
         * @return false|string
         */
        public function get_sitemap_xml($url_list)
        {
            $attrs = '';
            foreach ($this->xmlns_list as $key => $item) {
                $item  = esc_url($item);
                $attrs .= " xmlns:{$key}='$item'";
            }
            foreach ($this->xmlns_list as $key => $item) {
                $item  = (array)$item;
                $item  = implode(' ', array_map('esc_url', $item));
                $attrs .= " xsi:{$key}='$item'";
            }
            $xmlns     = esc_url($this->xmlns);
            $attrs     .= " xmlns='{$xmlns}'";
            $xml_array = [
                '<?xml version="1.0" encoding="UTF-8"?>',
                $this->stylesheet,
                sprintf("<urlset %s>", trim($attrs))
            ];
            foreach ($url_list as $url_item) {
                $xml_array[] = "<url>";
                foreach ($url_item as $name => $value) {
                    if ('loc' === $name) {
                        $xml_array[] = sprintf('<loc>%s</loc>', esc_url($value));
                        continue;
                    }
                    if (in_array($name, ['lastmod', 'changefreq', 'priority'], true)) {
                        $xml_array[] = sprintf('<%1$s>%2$s</%1$s>', esc_attr($name), esc_xml($value));
                        continue;
                    }
                    if ($name === 'image') {
                        if ( ! is_array($value)) {
                            continue;
                        }
                        foreach ($value as $item) {
                            if ( ! is_array($item) || ! isset($item['loc'])) {
                                continue;
                            }
                            $xml_array[] = "<image:image>";
                            $xml_array[] = sprintf('<image:loc>%s</image:loc>', esc_url($item['loc']));
                            if ( ! empty($item['title']) && is_string($item['title'])) {
                                $xml_array[] = sprintf('<image:title><![CDATA[%s]]></image:title>',
                                    esc_xml($item['title']));
                            }
                            if ( ! empty($item['caption']) && is_string($item['caption'])) {
                                $xml_array[] = sprintf('<image:caption><![CDATA[%s]]></image:caption>',
                                    esc_xml($item['caption']));
                            }
                            $xml_array[] = "</image:image>";
                        }
                        continue;
                    }
                    _doing_it_wrong(
                        __METHOD__,
                        sprintf(
                        /* translators: %s: List of element names. */
                            __('Fields other than %s are not currently supported for sitemaps.', 'law-lib'),
                            implode(',', ['loc', 'lastmod', 'changefreq', 'priority', 'image'])
                        ),
                        '5.5.0'
                    );
                }
                $xml_array[] = "</url>";
            }

            $xml_array[] = "</urlset>";
            $xml_array   = implode("", $xml_array);
            if ( ! apply_filters('format_sitemap_output', true) && class_exists(DOMDocument::class)) {
                $dom                     = new DOMDocument();
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput       = true;
                $dom->loadXML($xml_array);
                $xml_array = $dom->saveXML();
            }

            return $xml_array;
        }
    }
}
