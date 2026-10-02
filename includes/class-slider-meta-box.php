<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slider Meta Box
 */
class MSR_Slider_Meta_Box {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action(
			'add_meta_boxes',
			array( $this, 'add_meta_box' )
		);

		add_action(
			'save_post_msr_slider',
			array( $this, 'save' )
		);
	}

	/**
	 * Add meta box
	 */
	public function add_meta_box() {

		add_meta_box(
			'msr_slides',
			'Slides',
			array( $this, 'render' ),
			'msr_slider',
			'normal',
			'high'
		);
	}

	/**
	 * Render meta box
	 */
	public function render( $post ) {

		$slides = get_post_meta(
			$post->ID,
			'msr_slides',
			true
		);

		if ( ! is_array( $slides ) ) {
			$slides = array();
		}

		wp_nonce_field(
			'msr_save_slides',
			'msr_slides_nonce'
		);
		?>

		<div class="msr-slides">

			<div class="msr-slides__list">

				<?php foreach ( $slides as $index => $slide ) : ?>

					<div class="msr-slide">
						<strong>
							Slide <?php echo esc_html( $index + 1 ); ?>
						</strong>
					</div>

				<?php endforeach; ?>

			</div>

			<p>
				<button
					type="button"
					class="button"
					id="msr-add-slide"
				>
					＋ Slideを追加
				</button>
			</p>

		</div>

		<?php
	}

	/**
	 * Save slides
	 */
	public function save( $post_id ) {

		/*
		 * Nonce check
		 */
		if (
			! isset( $_POST['msr_slides_nonce'] )
			|| ! wp_verify_nonce(
				$_POST['msr_slides_nonce'],
				'msr_save_slides'
			)
		) {
			return;
		}

		/*
		 * Autosave check
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		/*
		 * Permission check
		 */
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/*
		 * Slides
		 */
		$slides = isset( $_POST['msr_slides'] )
			? $_POST['msr_slides']
			: array();

		update_post_meta(
			$post_id,
			'msr_slides',
			$slides
		);
	}
}

new MSR_Slider_Meta_Box();