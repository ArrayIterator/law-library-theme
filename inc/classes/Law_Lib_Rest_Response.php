<?php
if (!class_exists('WP_REST_Response')) {
    return;
}

if (!class_exists('Law_Lib_Rest_Response')) {
    class Law_Lib_Rest_Response extends WP_REST_Response
    {
        protected $meta_data = [];

        public $single_response = false;

        public function setMetaData($key, $value)
        {
            $this->meta_data[$key] = $value;
        }

        /**
         * @param false|int|float|string $key
         *
         * @return array|mixed
         */
        public function getMetaData($key = false, $default = null)
        {
            if ($key === false) {
                return $this->meta_data;
            }

            return array_key_exists($key, $this->meta_data)
                ? $this->meta_data[$key]
                : $default;
        }

        /**
         * @param WP_REST_Response $response
         *
         * @return static
         */
        public static function fromResponse(WP_REST_Response $response)
        {
            return new static(
                $response->get_data(),
                $response->get_status(),
                $response->get_headers()
            );
        }
    }
}
