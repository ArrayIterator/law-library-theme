<?php
if (!defined('ABSPATH')) {
    return;
}

if (!class_exists('Law_Lib_Image')) {
    class Law_Lib_Image
    {
        public static $thumbnails_path = '/assets/images/thumbnails/';

        /**
         * Thumbnail List
         * @uses add_image_size();
         */
        const POST_THUMBNAIL = 'post-thumbnail';
        const POST_THUMBNAIL_WIDEST = 'thumbnail-widest';
        const POST_THUMBNAIL_BIG = 'thumbnail-big';
        const POST_THUMBNAIL_WIDE = 'thumbnail-wide';
        const POST_THUMBNAIL_MINI = 'thumbnail-mini';
        const POST_SQUARE = 'square';
        const POST_SQUARE_MINI = 'square-mini';
        const POST_THUMBNAIL_SIZE = [
            'width' => 240,
            'height' => 180,
        ];
        const POST_THUMBNAIL_WIDE_SIZE = [
            'width' => 640,
            'height' => 360,
        ];
        const POST_THUMBNAIL_WIDEST_SIZE = [
            'width' => 1200,
            'height' => 675,
        ];
        const POST_THUMBNAIL_BIG_SIZE = [
            'width' => 1200,
            'height' => 900,
        ];
        const POST_THUMBNAIL_MINI_SIZE = [
            'width' => 120,
            'height' => 90,
        ];
        const POST_SQUARE_SIZE = [
            'width' => 200,
            'height' => 200,
        ];
        const POST_SQUARE_MINI_SIZE = [
            'width' => 100,
            'height' => 100,
        ];

        /**
         * @var array<int, false|int>
         */
        protected static $posts_cached = [];

        /**
         * @param int $post_id
         *
         * @return string
         */
        public static function thumbnailAltText(int $post_id): string
        {
            $text = get_post_meta($post_id, '_wp_attachment_image_alt', true);
            if ( ! $text) {
                $text = wp_get_attachment_caption($post_id);
            }
            if ( ! $text) {
                $base = wp_get_attachment_image_src($post_id, 'full');
                if ( ! empty($base) && isset($base[0])) {
                    $text = str_replace('-', ' ', strchr(basename($base[0]), '.', true));
                }
            }
            $text = (string)$text;

            return strip_tags(trim($text));
        }

        /**
         * @param $post_id
         *
         * @return false|int
         */
        private static function alternativeImagePost($post_id)
        {
            $post = get_post($post_id);
            if ( ! $post || strpos($post->post_content, '<img') === false) {
                return false;
            }
            $post_id = $post->ID;
            if (isset(self::$posts_cached[$post_id])) {
                return self::$posts_cached[$post_id];
            }

            $post = Law_Lib_Resolver::contentParsed($post_id);
            if ( ! $post) {
                return false;
            }
            preg_match_all(
                '~<img.+class=[\'\"][^\"\']*wp-image-([0-9]+)(\s*|[\'\"])~',
                $post,
                $match
            );
            if ( ! empty($match[1])) {
                foreach ($match[1] as $item) {
                    $meta = wp_get_attachment_metadata($item);
                    if (is_array($meta) && isset($meta['image_meta'])) {
                        self::$posts_cached[$post_id] = absint($item);

                        return self::$posts_cached[$post_id];
                    }
                }
            }

            return false;
        }
        public static function defaultImage($thumbnails = false)
        {
            static $image_sizes = null;
            if ( ! is_array($image_sizes)) {
                $image_sizes = [];
                $thumbs = '/'.trim(self::$thumbnails_path, '/');
                foreach (wp_get_additional_image_sizes() as $key => $item) {
                    /* @todo change pointed to*/
                    $path = "{$thumbs}/{$item['width']}x{$item['height']}.png";
                    $file = get_theme_file_path($path);
                    if ( ! $file || ! file_exists($file)) {
                        continue;
                    }
                    $uri = get_theme_file_uri($path);
                    if (!$uri) {
                        continue;
                    }
                    $image_sizes[$key] = [
                        'src' => $uri,
                        'width' => $item['width'],
                        'height' => $item['height'],
                    ];
                }
            }
            if (is_string($thumbnails)) {
                return $image_sizes[$thumbnails] ?? false;
            }
            return $image_sizes;
        }
        /**
         * @param null $post_id
         *
         * @return false|int
         */
        public static function postThumbnailID($post_id = null)
        {
            if ($post_id instanceof WP_Post) {
                $post_id = $post_id->ID;
            } else {
                $post_id = Law_Lib_Meta_Data::determinePostId($post_id);
            }
            if (!$post_id || ! is_numeric($post_id)) {
                return false;
            }
            if (isset(self::$posts_cached[$post_id])) {
                return self::$posts_cached[$post_id];
            }

            $thumbnail_id                 = get_post_thumbnail_id($post_id);
            $thumbnail_id                 = $thumbnail_id ? absint($thumbnail_id) : false;
            self::$posts_cached[$post_id] = $thumbnail_id;
            if ($thumbnail_id) {
                return $thumbnail_id;
            }
            $attached = get_attached_media('image', $post_id);
            if (empty($attached)) {
                $thumbnail_id = self::alternativeImagePost($post_id);
                if ($thumbnail_id) {
                    self::$posts_cached[$post_id] = $thumbnail_id;
                }

                return false;
            }
            foreach ($attached as $post) {
                if (strpos(($post->post_mime_type ?: ''), 'image') !== false) {
                    self::$posts_cached[$post_id] = absint($post->ID);

                    return self::$posts_cached[$post_id];
                }
            }

            return false;
        }

        /**
         * @param null|false|numeric|int|WP_Post $post_id
         * @param string $size
         * @param array $attributes
         * @param bool|null $optimize
         *
         * @return array|false
         */
        public static function thumbnailImageAttachment(
            $post_id = null,
            string $size = self::POST_THUMBNAIL,
            array $attributes = [],
            bool $optimize = null
        ) {
            $post_id = Law_Lib_Meta_Data::determinePostId($post_id);
            if ( !$post_id || !($_post = get_post($post_id))) {
                return false;
            }
            $_thumbnail_id = self::postThumbnailID( $post_id );
            $alt           = '';
            if ( $_thumbnail_id ) {
                $is_default = false;
                $_size      = wp_get_attachment_image_src( $_thumbnail_id, $size );
                $alt        = self::thumbnailAltText($_thumbnail_id);
            } else {
                $_size = self::defaultImage( $size );
                if ( ! $_size ) {
                    $_size = self::defaultImage();
                    $_size = reset( $_size )?:[];
                }
                $is_default = true;
            }

            $alt  = $alt ?: trim( strip_tags( $_post->post_title ) );
            $attr = [
                'alt'    => $alt,
                'src'    => array_shift( $_size ),
                'width'  => array_shift( $_size ),
                'height' => array_shift( $_size ),
            ];
            static $is_lazy = null;
            if ( $is_lazy === null ) {
                $is_lazy = (bool) wp_lazy_loading_enabled( 'img', 'wp_get_attachment_image' );
            }
            if ( $is_lazy ) {
                $attr['loading'] = 'lazy';
            }
            if ( $_thumbnail_id ) {
                $attr['sizes']  = wp_get_attachment_image_sizes( $_thumbnail_id, $size );
                $attr['srcset'] = wp_get_attachment_image_srcset( $_thumbnail_id, $size );
                $_currents_attr = [
                    self::POST_THUMBNAIL => self::POST_THUMBNAIL_MINI,
                    self::POST_SQUARE => self::POST_SQUARE_MINI,
                ];

                $current_size = $_currents_attr[$size]??null;
                $optimize = $optimize === null ? true : $optimize;
                if ( $current_size && apply_filters( 'law_lib_optimize_image_responsive', $optimize , $_size, $current_size)) {
                    $_sizes = wp_get_additional_image_sizes();
                    $_current_sizes = $_sizes[$current_size]??null;
                    $mini_size = apply_filters( 'law_lib_optimize_image_responsive_small_window_size', 480, $_size, $current_size);
                    if ($_current_sizes && is_int($mini_size)) {
                        $__url = wp_get_attachment_image_src($_thumbnail_id, $current_size);
                        if ( ! empty($__url) && isset($__url[1]) && $__url[1] == $_current_sizes['width']) {
                            $attr['sizes']  = "(max-width: {$mini_size}px) {$_current_sizes['width']}w, {$attr['width']}w";
                            $src_set        = [
                                sprintf('%s %dw', $__url[0], $_current_sizes['width']),
                                sprintf('%s %dw', $attr['src'], $attr['width']),
                            ];
                            $attr['srcset'] = implode(', ', $src_set);
                        }
                    }
                }
            } elseif ( ! empty( $_size['srcset'] ) ) {
                $attr['srcset'] = $_size['srcset'];
                $attr['sizes']  = sprintf( '(max-width: %1$dpx) %1$dw', $attr['width'] );
            }
            $attr       = array_map( function ( $e ) {
                return (string) $e;
            }, array_merge( $attr, $attributes ) );
            $attachment = $_thumbnail_id ? get_post( $_thumbnail_id ) : false;
            if ( $attachment ) {
                $attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment, $size );
            }
            $html = '';
            foreach ( array_map( 'esc_attr', $attr ) as $key => $v ) {
                $html .= " {$key}='{$v}'";
            }

            return [
                'attributes'       => $attr,
                'html'             => "<img$html>",
                'is_default_image' => $is_default,
                'thumbnail_id'     => $_thumbnail_id,
                'attachment'       => $attachment,
                'size'             => $size,
            ];
        }

        /**
         * @param $width
         * @param $height
         *
         * @return mixed
         */
        public static function getDivisor($width, $height)
        {
            return ($width % $height) ? self::getDivisor($height, $width % $height) : $height;
        }

        /**
         * @param int $width
         * @param int $height
         *
         * @return float[]|int[]
         */
        public static function getRatio(int $width, int $height): array
        {
            $divisor = self::getDivisor($width, $height);

            return [
                $width / $divisor,
                $height / $divisor
            ];
        }
    }
}
