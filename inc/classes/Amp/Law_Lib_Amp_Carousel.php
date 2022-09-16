<?php
if (!class_exists(Law_Lib_Amp_Component::class)) {
    return;
}

class Law_Lib_Amp_Carousel extends Law_Lib_Amp_Component
{
    const TAG = 'amp-carousel';
    private static $count = 0;

    /**
     * @var string[]
     */
    protected $allowed_types = [
        self::TYPE_SLIDES,
        self::TYPE_CAROUSEL
    ];

    protected $allowed_attributes = [
        'on'    => self::DATA_TYPE_STRING,
        'height' => self::DATA_TYPE_INT,
        'width' => self::DATA_TYPE_INT,
        'type'  => self::DATA_TYPE_STRING,
        'layout'  => self::DATA_TYPE_STRING,
        'id' => self::DATA_TYPE_STRING,
        'class' => self::DATA_TYPE_STRING,
        'data-next-button-aria-label' => self::DATA_TYPE_STRING,
        'data-prev-button-aria-label' => self::DATA_TYPE_STRING,
        'data-button-count-format' => self::DATA_TYPE_STRING,
        'delay' => self::DATA_TYPE_INT,
        'controls'  => self::DATA_TYPE_BOOL,
        'autoplay' => self::DATA_TYPE_BOOL,
        'loop' => self::DATA_TYPE_BOOL,
        'aria-label' => self::DATA_TYPE_STRING,
        'role' => self::DATA_TYPE_STRING,
    ];
    protected $allowed_layouts = [
        self::LAYOUT_FILL,
        self::LAYOUT_FIXED,
        self::LAYOUT_FIXED_HEIGHT,
        self::LAYOUT_FLEX,
        self::LAYOUT_RESPONSIVE,
    //        self::LAYOUT_NO_DISPLAY,
//        self::LAYOUT_CONTAINER,
    ];
    /**
     * @var string
     */
    protected $default_type = self::TYPE_CAROUSEL;

    public static function getCount() : int
    {
        return self::$count;
    }

    /**
     * @param string|null $id
     * @param string|null $layout
     * @param string|null $type
     * @param array<string, mixed> $attributes
     *
     * @return Law_Lib_Amp_Carousel_List
     */
    public function create(string $id = null, string $layout = null, string $type = null, array $attributes = []) : Law_Lib_Amp_Carousel_List
    {
        self::$count++;
        $count = self::$count;
        if (empty($attributes['id']) || !is_string($attributes['id']) || trim($attributes['id']) === '') {
            $id = $id ? sanitize_html_class($id) : '';
            if ( ! $id) {
                $attributes['id'] = 'carousel-id-' . $count;
            }
        }

        if ($layout === null || empty($attributes['layout'])) {
            $attributes['layout'] = is_string($layout) && in_array($layout, $this->allowed_layouts) ? $layout : $this->default_layout;
        }

        $attributes['type'] = $type;
        if ($type === null || empty($attributes['type'])) {
            $attributes['type'] = $this->default_type;
        }

        if (!in_array($attributes['layout'], $this->allowed_layouts)) {
            $attributes['layout'] = $this->default_layout;
        }
        if (!in_array($attributes['type'], $this->allowed_types)) {
            $attributes['type'] = $this->default_type;
        }

        return new Law_Lib_Amp_Carousel_List($this, $this->sanitizeAttributes($attributes));
    }
}
