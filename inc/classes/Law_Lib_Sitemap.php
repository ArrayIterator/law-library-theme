<?php
if (!class_exists(WP_Sitemaps::class)) {
    return;
}

if (!class_exists('Law_Lib_Sitemap')) {
    class Law_Lib_Sitemap extends WP_Sitemaps
    {
        public function __construct()
        {
            parent::__construct();
            $this->renderer = new Law_Lib_Sitemap_Renderer();
        }
    }
}
