<?php
if ( ! defined( 'ABSPATH' ) ) :
    return;
endif;

if (!function_exists('law_lib_notice_create')) {
    function law_lib_notice_create(string $message, string $type = '', bool $dismissible = false)
    {
        $array_type = [
            'warning' => 'notice-warning',
            'success' => 'notice-success',
            'error' => 'notice-error',
            'info' => 'notice-info'
        ];
        $class = 'notice';
        if (isset($array_type[$type])) {
            $class .= " {$array_type[$type]}";
        }
        $before = '';
        if ($dismissible) {
            $class = " is-dismissible";
            $before = "<div class='close'>&times;</div>";
        }
        if (!preg_match('~<((?:div|p)+)(?:\s+[^>]*)?>.*</\1>~i', $message)) {
            $message = sprintf('<p>%s</p>', $message);
        }
    ?>
        <div class="<?= $class;?>">
        <?= $before;?>
            <?= force_balance_tags($message);?>
        </div>
    <?php
    }
}

if (!function_exists('law_lib_notice_error')) {
    function law_lib_notice_error(string $message, bool $dismissible = false)
    {
        law_lib_notice_create($message, 'error', $dismissible);
    }
}
if (!function_exists('law_lib_notice_success')) {
    function law_lib_notice_success(string $message, bool $dismissible = false)
    {
        law_lib_notice_create($message, 'success', $dismissible);
    }
}

if (!function_exists('law_lib_notice_warning')) {
    function law_lib_notice_warning(string $message, bool $dismissible = false)
    {
        law_lib_notice_create($message, 'warning', $dismissible);
    }
}

if (!function_exists('law_lib_notice_info')) {
    function law_lib_notice_info(string $message, bool $dismissible = false)
    {
        law_lib_notice_create($message, 'info', $dismissible);
    }
}
