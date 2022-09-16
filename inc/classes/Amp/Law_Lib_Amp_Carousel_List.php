<?php

class Law_Lib_Amp_Carousel_List
{
    protected $attributes = [];

    /**
     * @var array<string, array<string, array>>
     */
    protected $images = [];
    protected $preview = [];
    protected $carousel;
    protected $post_thumbnail_size = Law_Lib_Image::POST_THUMBNAIL_WIDE;
    protected $post_preview_thumbnail_size = Law_Lib_Image::POST_THUMBNAIL_MINI;
    protected $image_tag = 'amp-img';
    protected $latest_succeed = false;
    protected $default_height = 450;
    protected $enable_preview = true;

    /**
     * @param Law_Lib_Amp_Carousel $carousel
     * @param array $attribute
     */
    public function __construct(Law_Lib_Amp_Carousel $carousel, array $attribute)
    {
        $this->carousel = $carousel;
        $this->attributes = $attribute;
    }

    /**
     * @return bool
     */
    public function isEnablePreview(): bool
    {
        return $this->enable_preview;
    }

    /**
     * @param bool $enable_preview
     *
     * @noinspection PhpUnused*/
    public function setEnablePreview(bool $enable_preview)
    {
        $this->enable_preview = $enable_preview;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<int, array>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return Law_Lib_Amp_Carousel
     */
    public function getCarousel(): Law_Lib_Amp_Carousel
    {
        return $this->carousel;
    }

    /**
     * @return bool
     * @noinspection PhpUnused
     */
    public function isLatestSucceed(): bool
    {
        return $this->latest_succeed;
    }

    /**
     * @return string
     */
    public function getPostThumbnailSize(): string
    {
        return $this->post_thumbnail_size;
    }

    /**
     * @return array
     */
    public function getPreview(): array
    {
        return $this->preview;
    }

    /**
     * @param string $post_preview_thumbnail_size
     *
     * @noinspection PhpUnused*/
    public function setPostPreviewThumbnailSize(string $post_preview_thumbnail_size)
    {
        $this->post_preview_thumbnail_size = $post_preview_thumbnail_size;
    }

    /**
     * @return string
     */
    public function getPostPreviewThumbnailSize(): string
    {
        return $this->post_preview_thumbnail_size;
    }

    /**
     * @return int
     */
    public function getDefaultHeight(): int
    {
        return $this->default_height;
    }

    /**
     * @param string $post_thumbnail_size
     *
     * @noinspection PhpUnused*/
    public function setPostThumbnailSize(string $post_thumbnail_size)
    {
        $this->post_thumbnail_size = $post_thumbnail_size;
        $sizes = wp_get_additional_image_sizes();
        if (isset($sizes[$post_thumbnail_size])) {
            $this->default_height = absint($sizes[$post_thumbnail_size]['height']);
        }
    }

    /**
     * @param string $src
     * @param string|null $srcset
     * @param array $attributes
     *
     * @return $this
     */
    private function addImage(
        string $src,
        string $srcset = null,
        array $attributes = []
    ): Law_Lib_Amp_Carousel_List {
        $this->latest_succeed = true;
        $attributes['src'] = $src;
        if ($srcset) {
            $attributes['srcset'] = $srcset;
        }
        if (isset($attributes['layout'])) {
            $attributes['layout'] = $this->getCarousel()->normalizeLayout($attributes['layout']);
        }
        $this->images[] = $attributes;
        return $this;
    }

    /**
     * @param int|numeric $id
     * @param string $size
     * @param string $html_caption
     * @param string $before_image
     * @param string $after_image
     *
     * @return Law_Lib_Amp_Carousel_List|false
     */
    public function addFromAttachment(
        $id,
        string $size = null,
        string $html_caption = null,
        string $before_image = null,
        string $after_image = null
    ) {
        $size = !$size ? $this->getPostThumbnailSize() : $size;

        if (!is_numeric($id)) {
            $this->latest_succeed = false;
            return false;
        }
        $image = wp_get_attachment_image_src($id, $size);
        if (empty($image)) {
            $this->latest_succeed = false;
            return false;
        }
        $attributes = [
            'src' => $image[0],
            'width' => $image[1],
            'height' => $image[2],
            'data-attachment-id' => $id,
        ];
        $srcset = wp_get_attachment_image_srcset($id, $size);
        if ($srcset) {
            $attributes['srcset'] = $srcset;
        }

        $caption = Law_Lib_Image::thumbnailAltText($id);
        if ($caption) {
            $attributes['alt'] = $caption;
        } else {
            $base = str_replace('-', ' ', strchr(basename($image[0]), '.', true));
            $attributes['alt'] = $base;
        }
        $sizes = wp_get_attachment_image_sizes($id, $size);
        if (!empty($sizes)) {
            $attributes['sizes'] = $sizes;
        }

        $image = wp_get_attachment_image_src($id, $this->post_preview_thumbnail_size);
        $previews = [
            'src' => $image[0],
            'width' => $image[1],
            'height' => $image[2],
            'data-attachment-id' => $id
        ];
        $srcset = wp_get_attachment_image_srcset($id, $size);
        if ($srcset) {
            $previews['srcset'] = $srcset;
        }
        $sizes = wp_get_attachment_image_sizes($id, $size);
        if (!empty($sizes)) {
            $previews['sizes'] = $sizes;
            $previews['alt'] = $attributes['alt'];
        }
        $this->preview[] = $previews;
        $attributes['caption'] = $html_caption;
        $attributes['before_image'] = $before_image;
        $attributes['after_image'] = $after_image;
        return $this->addImage($attributes['src'], $attributes['srcset'] ?? '', $attributes);
    }

    /**
     * @param $post_id
     * @param null $size
     * @param string|null $html_caption
     * @param string|null $before_image
     * @param string|null $after_image
     *
     * @return $this|Law_Lib_Amp_Carousel_List|false
     * @noinspection PhpUnused
     */
    public function addFromPost(
        $post_id,
        $size = null,
        string $html_caption = null,
        string $before_image = null,
        string $after_image = null
    ) {
        $post = get_post($post_id);
        if (!$post || !($thumbnail_id = Law_Lib_Image::postThumbnailID($post))) {
            $this->latest_succeed = false;
            return false;
        }

        return $this->addFromAttachment($thumbnail_id, $size, $html_caption, $before_image, $after_image);
    }

    /** @noinspection PhpUnused */
    public function toHtml(int $height = null) : string
    {
        if (empty($this->images)) {
            return '';
        }

        $image_sizes = wp_get_additional_image_sizes();
        $id = $this->attributes['id'] ??'';
        $id = !$id ? sprintf('carousel-id-%d', $this->carousel->getCount()) : $id;
        if (empty($this->attributes['height']) || !is_numeric($this->attributes['height'])) {
            $height = $height ?? $this->getDefaultHeight();
            $this->attributes['height'] = $height;
        }

        if (empty($this->attributes['width']) || !is_numeric($this->attributes['width'])) {
            if (isset($image_sizes[$this->post_thumbnail_size])) {
                $h = $image_sizes[$this->post_thumbnail_size]['height'];
                $w = $image_sizes[$this->post_thumbnail_size]['width'];
                $ratio = Law_Lib_Image::getRatio($w, $h);
                $width = $this->attributes['height'] / $ratio[1] * $ratio[0];
            } else {
                $width                     = $this->attributes['height'] / 16 * 9;
            }
            $this->attributes['width'] = $width;
        }
        if (!isset($this->attributes['id']) || !is_string($this->attributes['id']) || trim($this->attributes['id']) === '') {
            $this->attributes['id'] = $id;
        }
        $attributes = $this->attributes;
        $attributes['on'] = sprintf(
            'slideChange:%1$s-selector.toggle(index=event.index, value=true)',
            $id
        );
        if ($this->isEnablePreview()) {
            $attributes['on'] .= sprintf(
                ', %1$s-preview.goToSlide(index=event.index)',
                $id
            );
        }

        if ($attributes['layout'] === Law_Lib_Amp_Component::LAYOUT_FIXED_HEIGHT) {
            unset($attributes['width']);
        }
        $result = [
            sprintf(
                '<%1$s %2$s>',
                Law_Lib_Amp_Carousel::TAG,
                $this->carousel->sanitizeBuildAttributesHtml($attributes)
            )
        ];

        $c = 0;
        foreach ($this->images as $img) {
            $caption = $img['caption']??null;
            $before_img = $img['before_image']??null;
            $before_img = is_string($before_img) ? $before_img : null;
            $after_img  = $img['after_image']??null;
            $after_img = is_string($after_img) ? $after_img : null;
            unset($img['before_image'], $img['after_image'], $img['caption']);

            $result[] = "\t<div class='slide'>";
            if ($before_img && $after_img) {
                $result[] = $before_img;
            }
            $result[] = sprintf(
                "\t\t<amp-img %s></amp-img>\n",
                $this->carousel->buildAttributesHtml($img)
            );
            if ($before_img && $after_img) {
                $result[] = $after_img;
            }
            if ($caption) {
                $result[] = $caption;
            }
            $result[] = "\t</div>";
        }
        $result[] = sprintf('</%s>', Law_Lib_Amp_Carousel::TAG);
        if ($this->isEnablePreview()) {
            $preview_size = $this->getPostPreviewThumbnailSize();
            if (!isset($image_sizes[$preview_size])) {
                $preview_size = 'small-post-thumbnail';
            }
            $preview_sizes = $image_sizes[$preview_size];
            $att_selector = [
                'id' => "$id-selector",
                'class' => 'carousel-preview',
                'on' => "select:$id.goToSlide(index=event.targetOption)",
                'layout' => 'container',
                'height' => $preview_sizes['width'],
                'width' => $preview_sizes['height'],
                'aria-label' => __('Selector', 'law-lib'),
            ];
            $attr_carousel = [
                'id' => "$id-preview",
                'role' => 'option',
                'height' => $preview_sizes['height'],
                'layout' => Law_Lib_Amp_Component::LAYOUT_FIXED_HEIGHT,
                'type' => Law_Lib_Amp_Component::TYPE_CAROUSEL,
                'hide-scrollbar' => true,
            ];
            $result[] = sprintf(
                '<amp-selector %s>',
                $this->carousel->buildAttributesHtml($att_selector)
            );
            $result[] = sprintf(
                "<amp-carousel %s>",
                $this->carousel->sanitizeBuildAttributesHtml($attr_carousel)
            );
            $c = 0;
            foreach ($this->getPreview() as $preview) {
                $preview['option'] = $c++;
                if ($preview['option'] === 0) {
                    $preview['selected'] = true;
                }
                unset($preview['srcset'], $preview['sizes']);
                $result[] = sprintf(
                    '<div class="selector-preview-wrapper"><div role="listbox" aria-label="%1$s"><%2$s %3$s></%2$s></div></div>',
                    __('Navigation', 'law-lib'),
                    $this->image_tag,
                    $this->carousel->buildAttributesHtml($preview)
                );
            }
            $result []= "</amp-carousel>\n";
            $result []= "</amp-selector>\n";
        } else {
            $att_selector = [
                'id' => "$id-selector",
                'class' => 'carousel-preview carousel-button-navigation',
                'on' => "select:$id.goToSlide(index=event.targetOption)",
                'layout' => 'container',
                'height' => 60,
            ];
            $result[] = sprintf(
                '<amp-selector %s>',
                $this->carousel->buildAttributesHtml($att_selector)
            );
            $btn_label = __('Navigation', 'law-lib');
            foreach ($this->preview as $ignored) {
                $opt = [
                    'class' => 'preview-selector'
                ];
                $opt['option'] = $c++;
                if ($opt['option'] === 0) {
                    $opt['selected'] = true;
                }
                $opt['aria-label'] = $btn_label;
                $result[] = sprintf(
                    '<%1$s %2$s><span></span></%1$s>',
                    'button',
                    $this->carousel->buildAttributesHtml($opt)
                );
            }
        }
        return implode("\n", $result);
    }
}
