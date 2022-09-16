<?php
if (! class_exists( 'WP_Customize_Control' ) ) {
	return;
}
if (!class_exists('Law_Lib_Customizer_Multi_Select_Control')) {
    class Law_Lib_Customizer_Multi_Select_Control extends WP_Customize_Control {

        /**
         * The type of customize control being rendered.
         */
        public $type = 'multiselect';

        /**
         * Displays the multiple select on the customize screen.
         */
        public function render_content() {
            if ( empty( $this->choices ) ) {
                return;
            }
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <select <?php $this->link(); ?> multiple="multiple" style="height: 100%;" class="select2">
                    <?php
                    $values = $this->value();
                    $values = ! is_array( $values ) ? [] : $values;
                    foreach ( $this->choices as $value => $label ) {
                        $selected = ( in_array( $value, $values ) ) ? selected( 1, 1, false ) : '';
                        echo '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . $label . '</option>';
                    }
                    ?>
                </select>
            </label>
        <?php }
    }
}
