<?php
if (!defined('ABSPATH')) :
    return;
endif;

if (!function_exists('law_lib_customizer_register')) {
    function law_lib_customizer_register(WP_Customize_Manager $wp_customizer)
    {
        $panel = 'law_lib';
        $wp_customizer->add_panel(
            new WP_Customize_Panel(
                $wp_customizer,
                $panel,
                [
                    'title'       => __( 'Law Library', 'law-lib' ),
                    'description' => __( 'Law Library Theme Settings', 'law-lib' ),
                    'capability'  => 'edit_theme_options',
                ]
            )
        );
        $custom_logo_args = get_theme_support( 'custom-mobile-logo' );
        if ( $custom_logo_args ) {
            $wp_customizer->add_setting(
                'custom-mobile-logo',
                [
                    'theme_supports' => [ 'custom-mobile-logo' ],
                    'transport'      => 'postMessage',
                ]
            );
            $wp_customizer->add_control(
                new WP_Customize_Cropped_Image_Control(
                    $wp_customizer,
                    'custom-mobile-logo',
                    [
                        'label'         => __( 'Mobile Logo', 'law-lib' ),
                        'section'       => 'title_tagline',
                        'priority'      => 100,
                        'height'        => $custom_logo_args[0]['height'] ?? null,
                        'width'         => $custom_logo_args[0]['width'] ?? null,
                        'flex_height'   => $custom_logo_args[0]['flex-height'] ?? null,
                        'flex_width'    => $custom_logo_args[0]['flex-width'] ?? null,
                        'button_labels' => [
                            'select'       => __( 'Select Logo', 'law-lib' ),
                            'change'       => __( 'Change Logo', 'law-lib' ),
                            'remove'       => __( 'Remove', 'law-lib' ),
                            'default'      => __( 'Default', 'law-lib' ),
                            'placeholder'  => __( 'No Logo Selected', 'law-lib' ),
                            'frame_title'  => __( 'Select Logo', 'law-lib' ),
                            'frame_button' => __( 'Choose Logo', 'law-lib' ),
                        ],
                    ]
                )
            );
        }

        $sections = law_lib_component_options_default();
        foreach ($sections as $section => $arr) {
            if (!is_array($arr)) {
                continue;
            }
            $args     = ($arr['args']??[]);
            $controls = $arr['settings']??[];
            $controls = (array) $controls;
            if (!isset($args['panel']) || !is_string($args['panel'])) {
                $args['panel'] = $panel;
            }
            if (!isset($args['title']) && isset($arr['title'])) {
                $args['title'] = $arr['title'];
            }
            $section = new WP_Customize_Section(
                $wp_customizer,
                $section,
                $args
            );
            $wp_customizer->add_section($section);
            $section_id = $section->id;
            foreach ($controls as $id => $control) {
                if ($control instanceof WP_Customize_Control) {
                    $wp_customizer->add_control($control);
                    $setting_id = "{$section_id}[$control->id]";
                    $control->id = $setting_id;
                    $wp_customizer->add_setting($setting_id);
                    continue;
                }
                if (!is_string($id) || !is_array($control)) {
                    continue;
                }
                if (isset($control['render']) && $control['render'] === false) {
                    continue;
                }

                if (isset($controls['display_callback']) && is_callable($control['display_callback'])) {
                    if (!$controls['display_callback']($wp_customizer, $section_id, $control, $controls)) {
                        continue;
                    }
                }

                $settings = [
                    'transport' => 'postMessage'
                ];
                $setting_id = "{$section_id}[$id]";
                $type = $control['type']??null;
                $control['section'] = $section->id;
                $new_settings = $control['settings']??[];
                if (is_array($new_settings)) {
                    if (isset($control['default'])) {
                        $new_settings['default'] = $control['default'];
                    }
                    $settings = array_merge($settings, $new_settings);
                }
                $settings['transport'] = $control['transport']??$settings['transport'];
                $wp_customizer->add_setting($setting_id, $settings);
                unset($control['settings']);
                switch ($type) {
                    case 'multiselect':
                    case 'multi':
                    case 'multi-select':
                    case 'multiple':
                            $options = $control['choices']??null;
                            $options = !is_array($options) ? $control['options']??[] : $options;
                            $control['choices'] = $options;
                            $wp_customizer->add_control(
                                new Law_Lib_Customizer_Multi_Select_Control(
                                    $wp_customizer,
                                    $setting_id,
                                    $control
                                )
                            );
                        break;
                    default:
                        if (is_string($type) && is_subclass_of($type, WP_Customize_Control::class, true)) {
                            unset($control['type']);
                            $control = new $type(
                                $wp_customizer,
                                $setting_id,
                                $control
                            );
                        } else {
                            $control = new WP_Customize_Control(
                                $wp_customizer,
                                $setting_id,
                                $control
                            );
                        }
                        $wp_customizer->add_control($control);
                }
            }
        }
    }
}

add_action( 'customize_register', 'law_lib_customizer_register', 100 );
