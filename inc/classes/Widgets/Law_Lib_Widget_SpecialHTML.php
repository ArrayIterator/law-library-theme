<?php
if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WP_Widget' ) ) {
	return;
}

class Law_Lib_Widget_SpecialHTML extends \WP_Widget {
	public $posts = [];

	// The construct part
	public function __construct() {
		parent::__construct(
			'law-lib-html-code',
			__( 'Law Library HTML Code', 'law-lib' ),
			[
				'description' => __( 'Special HTML Code', 'law-lib' ),
			]
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$instance = $this->filter_data( $instance );
		echo force_balance_tags( $instance['content'] );
	}

	// Creating widget Backend
	public function form( $instance ) {
		$instance = $this->filter_data( $instance );
		$name     = $this->get_field_name( 'content' );
		?>
        <label>
            <textarea class="widefat" rows="10"
                      name="<?= esc_attr( $name ); ?>"><?= esc_attr( $instance['content'] ); ?></textarea>
        </label>
		<?php
	}

	protected function filter_data( $new_instance ) {
		$instance            = array_merge( [ 'content' => '' ], $new_instance );
		$instance['content'] = ! is_string( $instance['content'] ) ? '' : trim( $instance['content'] );

		return $instance;
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		return $this->filter_data( $new_instance );
	}
}
