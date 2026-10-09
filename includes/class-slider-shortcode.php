<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slider Shortcode
 */
class MSR_Slider_Shortcode {

	public function __construct() {

		add_shortcode(
			'my_slider',
			array( $this, 'render_slider' )
		);

		add_action(
			'wp_enqueue_scripts',
			array( $this, 'enqueue_scripts' )
		);
	}

	/**
	 * Enqueue frontend styles
	 */
    public function enqueue_scripts() {

        wp_enqueue_style(
            'msr-slick',
            MSR_URL . 'assets/css/slick.css',
            array(),
            '1.8.1'
        );

        wp_enqueue_style(
            'msr-slick-theme',
            MSR_URL . 'assets/css/slick-theme.css',
            array( 'msr-slick' ),
            '1.8.1'
        );

        wp_enqueue_style(
            'msr-slider',
            MSR_URL . 'assets/css/msr-slider.css',
            array( 'msr-slick', 'msr-slick-theme' ),
            MSR_VERSION
        );

        wp_enqueue_script(
            'msr-slick',
            MSR_URL . 'assets/js/slick.min.js',
            array( 'jquery' ),
            '1.8.1',
            true
        );

        wp_enqueue_script(
            'msr-slider',
            MSR_URL . 'assets/js/msr-slider.js',
            array( 'jquery', 'msr-slick' ),
            MSR_VERSION,
            true
        );
    }

	/**
	 * Render slider
	 */
	public function render_slider( $atts ) {

		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'my_slider'
		);

		$slider_id = absint( $atts['id'] );

		if ( ! $slider_id ) {
			return '';
		}

		$slides = get_post_meta(
			$slider_id,
			'msr_slides',
			true
		);

		if ( ! is_array( $slides ) || empty( $slides ) ) {
			return '';
		}

		ob_start();
		?>

		<div class="msr-slider">

			<div class="msr-slider__list">

				<?php foreach ( $slides as $slide ) : ?>

                <?php
                $background_id = isset( $slide['background'] )
                    ? absint( $slide['background'] )
                    : 0;

                $sp_background_id = isset( $slide['sp_background'] )
                    ? absint( $slide['sp_background'] )
                    : 0;

                if ( ! $background_id ) {
                    continue;
                }

                $background_url = wp_get_attachment_image_url(
                    $background_id,
                    'full'
                );

                $sp_background_url = $sp_background_id
                    ? wp_get_attachment_image_url(
                        $sp_background_id,
                        'full'
                    )
                    : $background_url;
                ?>

                <div
                    class="msr-slider__slide"
                    style="
                        --msr-bg-pc: url('<?php echo esc_url( $background_url ); ?>');
                        --msr-bg-sp: url('<?php echo esc_url( $sp_background_url ); ?>');
                    "
                >

                        <?php if ( ! empty( $slide['layers'] ) && is_array( $slide['layers'] ) ) : ?>

                        <?php foreach ( $slide['layers'] as $layer ) : ?>

                            <?php

                            if ( ! isset( $layer['type'] ) ) {
                                continue;
                            }

                            $text_x = isset( $layer['x'] )
                                ? min( 100, max( 0, absint( $layer['x'] ) ) )
                                : 50;

                            $text_y = isset( $layer['y'] )
                                ? min( 100, max( 0, absint( $layer['y'] ) ) )
                                : 50;

                            $font_size = isset( $layer['font_size'] )
                                ? min( 200, max( 10, absint( $layer['font_size'] ) ) )
                                : 32;

                            $custom_class = isset( $layer['custom_class'] )
                                ? sanitize_text_field( $layer['custom_class'] )
                                : '';

                            $sp_x = isset( $layer['sp_x'] )
                                ? min( 100, max( 0, absint( $layer['sp_x'] ) ) )
                                : $text_x;

                            $sp_y = isset( $layer['sp_y'] )
                                ? min( 100, max( 0, absint( $layer['sp_y'] ) ) )
                                : $text_y;

                            $sp_font_size = isset( $layer['sp_font_size'] )
                                ? min( 200, max( 10, absint( $layer['sp_font_size'] ) ) )
                                : $font_size;

                            $width = isset( $layer['width'] )
                                ? min( 100, max( 0, absint( $layer['width'] ) ) )
                                : 50;

                            $sp_width = isset( $layer['sp_width'] )
                                ? min( 100, max( 0, absint( $layer['sp_width'] ) ) )
                                : 80;

                            $color = isset( $layer['color'] )
                                ? sanitize_hex_color( $layer['color'] ) ?: '#333333'
                                : '#333333';

                            $animation = isset( $layer['animation'] )
                                ? $layer['animation']
                                : 'none';

                            $delay = isset( $layer['delay'] )
                                ? min( 10000, max( 0, absint( $layer['delay'] ) ) )
                                : 0;

                            $duration = isset( $layer['duration'] )
                                ? min( 5000, max( 100, absint( $layer['duration'] ) ) )
                                : 800;

                            ?>

                            <?php if ( 'text' === $layer['type'] && ! empty( $layer['text'] ) ) : ?>

                                <div
                                    class="msr-layer msr-layer--text msr-animation--<?php echo esc_attr( $animation ); ?> <?php echo esc_attr( $custom_class ); ?>"
                                    style="
                                        left: <?php echo esc_attr( $text_x ); ?>%;
                                        top: <?php echo esc_attr( $text_y ); ?>%;
                                        font-size: <?php echo esc_attr( $font_size ); ?>px;
                                        color: <?php echo esc_attr( $color ); ?>;
                                        --msr-sp-x: <?php echo esc_attr( $sp_x ); ?>%;
                                        --msr-sp-y: <?php echo esc_attr( $sp_y ); ?>%;
                                        --msr-sp-font-size: <?php echo esc_attr( $sp_font_size ); ?>px;
                                        --msr-width: <?php echo esc_attr( $width ); ?>%;
                                        --msr-sp-width: <?php echo esc_attr( $sp_width ); ?>%;
                                        animation-delay: <?php echo esc_attr( $delay ); ?>ms;
                                        animation-duration: <?php echo esc_attr( $duration ); ?>ms;
                                    "
                                >
                                    <?php echo esc_html( $layer['text'] ); ?>
                                </div>

                            <?php elseif ( 'button' === $layer['type'] && ! empty( $layer['button_text'] ) ) : ?>

                                <a
                                    href="<?php echo esc_url( $layer['url'] ?? '#' ); ?>"
                                    class="msr-layer msr-layer--button msr-animation--<?php echo esc_attr( $animation ); ?>"
                                    style="
                                        left: <?php echo esc_attr( $text_x ); ?>%;
                                        top: <?php echo esc_attr( $text_y ); ?>%;
                                        font-size: <?php echo esc_attr( $font_size ); ?>px;
                                        color: <?php echo esc_attr( $color ); ?>;
                                        --msr-sp-x: <?php echo esc_attr( $sp_x ); ?>%;
                                        --msr-sp-y: <?php echo esc_attr( $sp_y ); ?>%;
                                        --msr-sp-font-size: <?php echo esc_attr( $sp_font_size ); ?>px;
                                        --msr-width: <?php echo esc_attr( $width ); ?>%;
                                        --msr-sp-width: <?php echo esc_attr( $sp_width ); ?>%;
                                        animation-delay: <?php echo esc_attr( $delay ); ?>ms;
                                        animation-duration: <?php echo esc_attr( $duration ); ?>ms;
                                    "
                                >
                                    <?php echo esc_html( $layer['button_text'] ); ?>
                                </a>

                            <?php endif; ?>

                        <?php endforeach; ?>

                        <?php endif; ?>

                    </div>

				<?php endforeach; ?>

			</div>

		</div>

		<?php

		return ob_get_clean();
	}
}

new MSR_Slider_Shortcode();