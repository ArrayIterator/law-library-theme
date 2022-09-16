<?php
if (!class_exists('Law_Lib_Resolver')) {
    class Law_Lib_Resolver
    {
        const RESOLVE_REFERRER = 'ref';
        const RESOLVE_NONE = 'none';
        private static $host = null;

        /**
         * @param string|mixed $content
         * @param null $mode
         * @param bool $resolve_image
         * @param bool $resolve_link
         * @param mixed $resolve_link_mode
         *
         * @return array|mixed|string|string[]|null
         */
        public static function autoFixAttachmentImageAndLink(
            $content,
            $mode = null,
            bool $resolve_image = null,
            bool $resolve_link = null,
            $resolve_link_mode = self::RESOLVE_NONE
        ) {
            if ( ! is_string($content) || $mode === 'content_parsing') {
                return $content;
            }

            if (self::$host === null) {
                $host       = wp_parse_url(site_url())['host'] ?? false;
                self::$host = $host ? preg_quote($host, '~') : false;
            }
            $resolve_link      = $resolve_link === null
                ? apply_filters('law_lib_util_resolver_resolve_link', false)
                : $resolve_link;
            $resolve_image     = $resolve_image === null
                ? apply_filters('law_lib_util_resolver_resolve_image', false)
                : $resolve_image;
            $resolve_link_mode = $resolve_link_mode === null
                ? apply_filters('law_lib_util_resolver_resolve_image', self::RESOLVE_REFERRER)
                : $resolve_link_mode;
            if ( ! $resolve_image && ! $resolve_link) {
                return $content;
            }

            if ($resolve_link && self::$host) {
                // rel="noopener noreferrer"
                $content = preg_replace_callback(
                    '~(?P<TAG><a)(?P<P1>.+)(href=")(?P<LINK>[^"]+)(?P<P2>"[^>]*>)~',
                    function ($m) use ($resolve_link, $resolve_link_mode) {
                        $host = self::$host;
                        if (preg_match("~^https?://{$host}(?:/.*|)$~i", $m['LINK'])) {
                            return $m[0];
                        }
                        $addReferer = $resolve_link_mode === self::RESOLVE_REFERRER;
                        $match      = null;
                        if (stripos($m[2], 'rel="') !== false) {
                            preg_match('~rel="([^"])+"~', $m[2], $match);
                        } elseif (stripos($m[5], 'rel="') !== false) {
                            preg_match('~rel="([^"])+"~', $m[5], $match);
                        }
                        $link = $m[0];
                        if ($match === null) {
                            $ref  = $addReferer ? ' noreferrer' : '';
                            $link = "{$m[1]} rel=\"external noopener{$ref}\"{$m[2]}{$m[3]}{$m[4]}{$m[5]}";
                        } elseif ( ! empty($match)) {
                            $match = $match[1];
                            if (stripos($match, 'external') === false) {
                                $match .= ' external';
                            }
                            if (stripos($match, 'noopener') === false) {
                                $match .= ' noopener';
                            }
                            if ($addReferer && stripos($match, 'noreferrer') === false) {
                                $match .= ' noreferrer';
                            }
                            $match = trim($match);
                        }

                        return $link;
                    },
                    $content
                );
            }

            if ( ! $resolve_image) {
                return $content;
            }

            $replacer = apply_filters(
                'law_lib_util_resolver_image_alt_default',
                __('Image Attachment', 'law-lib')
            );
            $replacer = esc_attr($replacer);
            $content  = preg_replace_callback(
                '~(<img)((?:[^>](?!alt=))*+>)~i',
                function ($e) use ($replacer) {
                    return "{$e[1]} alt=\"$replacer\"{$e[2]}";
                },
                $content
            );

            return preg_replace_callback(
                '~(<img [^>]*alt=")([^"]*)("[^>]*>)~i', function ($e) use ($replacer) {
                if (trim($e[2]) !== '') {
                    return $e[0];
                }

                return "{$e[1]}{$replacer}{$e[3]}";
            },
                $content
            );
        }

        /**
         * @param null $postId
         *
         * @return false|string
         */
        public static function contentParsed($postId = null)
        {
            $postId = Law_Lib_Meta_Data::determinePostId($postId);
            if ( ! $postId) {
                return false;
            }
            $post = get_post($postId);
            if ($post instanceof WP_Post && $post->ID > 0) {
                // replace next page
                // $post = str_replace('<!--nextpage-->', '', $post->post_content);
                return apply_filters('the_content', $post->post_content, 'content_parsing');
            }

            return false;
        }
    }
}