<?php
abstract class Law_Lib_Amp_Component
{
    const TAG = null;

    const
        DATA_TYPE_BOOL = 'bool',
        DATA_TYPE_STRING = 'string',
        DATA_TYPE_OBJECT = 'object',
        DATA_TYPE_FLOAT = 'float',
        DATA_TYPE_INT = 'int',
        DATA_TYPE_NUMERIC = 'numeric';

    const
        TYPE_CAROUSEL = 'carousel',
        TYPE_SLIDES = 'slides',
        LAYOUT_FILL = 'fill',
        LAYOUT_FIXED = 'fixed',
        LAYOUT_FIXED_HEIGHT = 'fixed-height',
        LAYOUT_FLEX = 'flex-item',
        LAYOUT_NO_DISPLAY = 'nodisplay',
        LAYOUT_CONTAINER = 'container',
        LAYOUT_RESPONSIVE = 'responsive';


    /**
     * @var string
     */
    protected $default_layout = self::LAYOUT_RESPONSIVE;

    protected $allowed_layouts = [
        self::LAYOUT_FILL,
        self::LAYOUT_FIXED,
        self::LAYOUT_FIXED_HEIGHT,
        self::LAYOUT_FLEX,
        self::LAYOUT_NO_DISPLAY,
        self::LAYOUT_RESPONSIVE,
        self::LAYOUT_CONTAINER,
    ];

    protected $allowed_attributes = [
        'aria-label' => self::DATA_TYPE_STRING,
        'on'    => self::DATA_TYPE_STRING,
        'height' => self::DATA_TYPE_INT,
        'width' => self::DATA_TYPE_INT,
        'role'    => self::DATA_TYPE_STRING,
        'class' => self::DATA_TYPE_STRING,
        'id' => self::DATA_TYPE_STRING,
    ];

    /**
     * @return null|string
     */
    public function getTag()
    {
        return static::TAG;
    }

    /**
     * @param $layout
     *
     * @return string
     */
    public function normalizeLayout($layout) : string
    {
        return !is_string($layout) || !in_array($layout, $this->allowed_layouts, true)
            ? $this->default_layout
            : $layout;
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function sanitizeAttributes(array $attributes = []) : array
    {
        if (isset($attributes['layout'])) {
            $attributes['layout'] = $this->normalizeLayout($attributes['layout']);
        }

        $attr = [];
        $attributes = array_intersect_key($attributes, $this->allowed_attributes);
        foreach ($attributes as $key => $item) {
            $new_item = null;
            if ($key === 'class' && is_array($item)) {
                $item = array_unique(
                    array_filter(
                        array_map(
                            'sanitize_html_class',
                            array_filter($item, 'is_string')
                        )
                    )
                );
                $item = implode(' ', $item);
            }

            switch ($this->allowed_attributes[$key]) {
                case self::DATA_TYPE_STRING:
                    $new_item = (string) $item;
                    break;
                case self::DATA_TYPE_BOOL:
                    if ($item == 'true' || $item == '1' || $item == true) {
                        $new_item = true;
                    }
                    break;
                case self::DATA_TYPE_INT:
                    $the_item = is_string($item) ? trim($item) : $item;
                    if (!is_numeric($the_item)) {
                        break;
                    }
                    $new_item = absint($the_item);
                    break;
                case self::DATA_TYPE_FLOAT:
                    $the_item = is_string($item) ? trim($item) : $item;
                    if (!is_numeric($the_item)) {
                        break;
                    }
                    $new_item = floatval($the_item);
                    break;
                case self::DATA_TYPE_NUMERIC:
                    $the_item = is_string($item) ? trim($item) : $item;
                    if (!is_numeric($the_item)) {
                        break;
                    }
                    $new_item = ($the_item);
                    break;
                case self::DATA_TYPE_OBJECT:
                    $new_item = json_encode($item, JSON_UNESCAPED_SLASHES);
                    break;
                default:
                    if (strpos($key, 'data-') === 0) {
                        if (is_object($item) || is_array($item)) {
                            $item = json_encode($item);
                        } elseif (is_bool($item)) {
                            $item = $item ? 'true' : 'false';
                        }
                        $new_item = (string) $item;
                    }
                    break;
            }
            if ($new_item !== null) {
                $attr[$key] = $new_item;
            }
        }

        return $attr;
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function buildAttributes(array $attributes) : array
    {
        $attrs = [];
        foreach ($attributes as $key => $item) {
            if (!is_string($key) || preg_match('~[^a-z0-9_\-]~i', $key)) {
                continue;
            }

            if ($key === 'class' && is_array($item)) {
                $item = array_unique(
                    array_filter(
                        array_map(
                            'sanitize_html_class',
                            array_filter($item, 'is_string')
                        )
                    )
                );
                $item = implode(' ', $item);
            }
            if (is_bool($item)) {
                if (!$item) {
                    continue;
                }
            }
            if (!is_string($item) && !is_numeric($item) && !is_bool($item)) {
                continue;
            }
            $attrs[] = $item == '' || $item === true ? $key : sprintf('%s="%s"', $key, esc_attr((string) $item));
        }

        return $attrs;
    }

    public function buildAttributesHtml(array $attributes = []) : string
    {
        return implode(' ', $this->buildAttributes($attributes));
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function sanitizeBuildAttributes(array $attributes) : array
    {
        return $this->buildAttributes($this->sanitizeAttributes($attributes));
    }

    public function sanitizeBuildAttributesHtml(array $attributes) : string
    {
        return $this->buildAttributesHtml($this->sanitizeAttributes($attributes));
    }

    /**
     * @return string
     */
    public function getDefaultLayout(): string
    {
        return $this->default_layout;
    }

    /**
     * @return string[]
     */
    public function getAllowedLayouts(): array
    {
        return $this->allowed_layouts;
    }

    /**
     * @return string[]
     */
    public function getAllowedAttributes(): array
    {
        return $this->allowed_attributes;
    }

    /**
     * @return mixed
     */
    abstract public function create();
}
