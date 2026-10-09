<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Slider Meta Box */
class MSR_Slider_Meta_Box {

	/* Constructor */
	public function __construct() {

		add_action(
			'add_meta_boxes',
			array( $this, 'add_meta_box' )
		);

		add_action(
			'save_post_msr_slider',
			array( $this, 'save' )
		);

		add_action(
			'admin_enqueue_scripts',
			array( $this, 'enqueue_scripts' )
		);
	}

	/* Add meta box */
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

	/* Render meta box */
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

						<strong class="msr-slide__title">
							Slide <?php echo esc_html( $index + 1 ); ?>
						</strong>

						<input
							type="hidden"
							name="msr_slides[<?php echo esc_attr( $index ); ?>][number]"
							value="<?php echo esc_attr( $index + 1 ); ?>"
						>

						<?php
							$background_id = isset( $slide['background'] ) 
								? absint( $slide['background'] ) 
								: 0;

							$sp_background_id = isset( $slide['sp_background'] ) 
								? absint( $slide['sp_background'] ) 
								: 0;
						?>

						<div class="msr-slide__background">

							<input
								type="hidden"
								class="msr-background-id"
								name="msr_slides[<?php echo esc_attr( $index ); ?>][background]"
								value="<?php echo esc_attr( $background_id ); ?>"
							>

							<div class="msr-background-preview">

								<?php
								if ( $background_id ) {
									echo wp_get_attachment_image(
										$background_id,
										'medium'
									);
								}
								?>

							</div>

							<button
								type="button"
								class="button msr-select-background"
							>
								背景画像を選択
							</button>

							<button
								type="button"
								class="button msr-remove-background"
								<?php disabled( ! $background_id ); ?>
							>
								背景画像を削除
							</button>

						</div>

						<div class="msr-slide__background msr-slide__background--sp">

							<input 
								type="hidden" 
								class="msr-sp-background-id" 
								name="msr_slides[<?php echo esc_attr( $index ); ?>][sp_background]" 
								value="<?php echo esc_attr( $sp_background_id ); ?>" 
							>

							<div class="msr-sp-background-preview">

								<?php 
								if ( $sp_background_id ) {
									echo wp_get_attachment_image( 
										$sp_background_id, 
										'medium' 
									);
								}
								?>

							</div>

							<button 
								type="button" 
								class="button msr-select-sp-background"
							>
								SP背景画像を選択
							</button>

							<button 
								type="button" 
								class="button msr-remove-sp-background"
								<?php disabled( ! $sp_background_id ); ?>
							>
								背景画像を削除
							</button>

						</div>

						<div class="msr-layers">

							<div class="msr-layers__list">

								<?php
								/*
								* Layerデータ
								*
								* 新しいデータ形式:
								* $slide['layers']
								*
								* 既存データの場合は、旧テキスト設定を
								* Layer 1として扱う。
								*/
								$layers = isset( $slide['layers'] ) && is_array( $slide['layers'] )
									? $slide['layers']
									: array(
										array(
											'type'      => 'text',
											'text'      => isset( $slide['text'] ) ? $slide['text'] : '',
											'x'         => isset( $slide['text_x'] ) ? $slide['text_x'] : 50,
											'y'         => isset( $slide['text_y'] ) ? $slide['text_y'] : 50,
											'font_size' => isset( $slide['font_size'] ) ? $slide['font_size'] : 32,
											'color'     => isset( $slide['color'] ) ? $slide['color'] : '#333333',
											'animation' => isset( $slide['animation'] ) ? $slide['animation'] : 'none',
											'delay'     => isset( $slide['delay'] ) ? $slide['delay'] : 0,
											'duration'  => isset( $slide['duration'] ) ? $slide['duration'] : 800,
										),
									);
								?>

								<?php foreach ( $layers as $layer_index => $layer ) : ?>

									<div class="msr-layer-editor">

										<strong class="msr-layer-editor__title">
											Layer <?php echo esc_html( $layer_index + 1 ); ?>
										</strong>

											<label>
												Layerタイプ

												<select
													class="msr-layer-type"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][type]"
												>
													<option
														value="text"
														<?php selected(
															isset( $layer['type'] ) ? $layer['type'] : 'text',
															'text'
														); ?>
													>
														Text
													</option>

													<option
														value="button"
														<?php selected(
															isset( $layer['type'] ) ? $layer['type'] : 'text',
															'button'
														); ?>
													>
														Button
													</option>
												</select>
											</label>

											<label>
												テキスト

												<input
													type="text"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][text]"
													value="<?php echo isset( $layer['text'] ) ? esc_attr( $layer['text'] ) : ''; ?>"
													class="widefat"
												>
											</label>

											<div class="msr-layer-editor__button">

												<label>
													ボタンテキスト

													<input
														type="text"
														name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][button_text]"
														value="<?php echo isset( $layer['button_text'] ) ? esc_attr( $layer['button_text'] ) : '詳しく見る'; ?>"
														class="widefat"
													>
												</label>

												<label>
													URL

													<input
														type="url"
														name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][url]"
														value="<?php echo isset( $layer['url'] ) ? esc_url( $layer['url'] ) : ''; ?>"
														class="widefat"
													>
												</label>

											</div>

										<div class="msr-slide__position">

											<label>
												X
												<input
													type="number"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][x]"
													value="<?php echo isset( $layer['x'] ) ? esc_attr( $layer['x'] ) : 50; ?>"
													min="0"
													max="100"
												>
												%
											</label>

											<label>
												Y
												<input
													type="number"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][y]"
													value="<?php echo isset( $layer['y'] ) ? esc_attr( $layer['y'] ) : 50; ?>"
													min="0"
													max="100"
												>
												%
											</label>

											<label>
												フォントサイズ
												<input
													type="number"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][font_size]"
													value="<?php echo isset( $layer['font_size'] ) ? esc_attr( $layer['font_size'] ) : 32; ?>"
													min="10"
													max="200"
												>
												px
											</label>

											<label>
												Width
												<input
													type="number"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][width]"
													value="<?php echo isset( $layer['width'] ) ? esc_attr( $layer['width'] ) : 50; ?>"
													min="0"
													max="100"
												>
												%
											</label>

											<label>
												カスタムクラス

												<input
													type="text"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][custom_class]"
													value="<?php echo isset( $layer['custom_class'] ) ? esc_attr( $layer['custom_class'] ) : ''; ?>"
													class="widefat"
													placeholder="例：hero-title"
												>
											</label>

											<div class="msr-slide__position msr-slide__position--sp">

												<label>
													SP X
													<input
														type="number"
														name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][sp_x]"
														value="<?php echo isset( $layer['sp_x'] ) ? esc_attr( $layer['sp_x'] ) : 50; ?>"
														min="0"
														max="100"
													>
													%
												</label>

												<label>
													SP Y
													<input
														type="number"
														name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][sp_y]"
														value="<?php echo isset( $layer['sp_y'] ) ? esc_attr( $layer['sp_y'] ) : 50; ?>"
														min="0"
														max="100"
													>
													%
												</label>

												<label>
													SP フォントサイズ
													<input
														type="number"
														name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][sp_font_size]"
														value="<?php echo isset( $layer['sp_font_size'] ) ? esc_attr( $layer['sp_font_size'] ) : 32; ?>"
														min="10"
														max="200"
													>
													px
												</label>

												<label>
													SP Width
													<input
														type="number"
														name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][sp_width]"
														value="<?php echo isset( $layer['sp_width'] ) ? esc_attr( $layer['sp_width'] ) : 80; ?>"
														min="0"
														max="100"
													>
													%
												</label>

											</div>

											<label>
												文字色
												<input
													type="color"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][color]"
													value="<?php echo isset( $layer['color'] ) ? esc_attr( $layer['color'] ) : '#333333'; ?>"
												>
											</label>

											<label>
												アニメーション
												<select
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][animation]"
												>
													<option
														value="none"
														<?php selected(
															isset( $layer['animation'] ) ? $layer['animation'] : 'none',
															'none'
														); ?>
													>
														なし
													</option>

													<option
														value="fade"
														<?php selected(
															isset( $layer['animation'] ) ? $layer['animation'] : 'none',
															'fade'
														); ?>
													>
														fade
													</option>

													<option
														value="fadeUp"
														<?php selected(
															isset( $layer['animation'] ) ? $layer['animation'] : 'none',
															'fadeUp'
														); ?>
													>
														fadeUp
													</option>

													<option
														value="fadeDown"
														<?php selected(
															isset( $layer['animation'] ) ? $layer['animation'] : 'none',
															'fadeDown'
														); ?>
													>
														fadeDown
													</option>

													<option
														value="slideLeft"
														<?php selected(
															isset( $layer['animation'] ) ? $layer['animation'] : 'none',
															'slideLeft'
														); ?>
													>
														slideLeft
													</option>

													<option
														value="slideRight"
														<?php selected(
															isset( $layer['animation'] ) ? $layer['animation'] : 'none',
															'slideRight'
														); ?>
													>
														slideRight
													</option>
												</select>
											</label>

											<label>
												Delay
												<input
													type="number"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][delay]"
													value="<?php echo isset( $layer['delay'] ) ? esc_attr( $layer['delay'] ) : 0; ?>"
													min="0"
													max="10000"
													step="100"
												>
												ms
											</label>

											<label>
												Duration
												<input
													type="number"
													name="msr_slides[<?php echo esc_attr( $index ); ?>][layers][<?php echo esc_attr( $layer_index ); ?>][duration]"
													value="<?php echo isset( $layer['duration'] ) ? esc_attr( $layer['duration'] ) : 800; ?>"
													min="100"
													max="5000"
													step="100"
												>
												ms
											</label>

										</div>

										<button
											type="button"
											class="button msr-remove-layer"
										>
											Layerを削除
										</button>

									</div>

								<?php endforeach; ?>

							</div>

							<button
								type="button"
								class="button msr-add-layer"
							>
								Layerを追加
							</button>

						</div>

						<button
							type="button"
							class="button msr-remove-slide"
						>
							Slideを削除
						</button>

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

	/* Save slides */
	public function save( $post_id ) {

		/* Nonce check */
		if (
			! isset( $_POST['msr_slides_nonce'] )
			|| ! wp_verify_nonce(
				$_POST['msr_slides_nonce'],
				'msr_save_slides'
			)
		) {
			return;
		}

		/* Autosave check */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		/* Permission check */
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/* Slides */
		$slides = array();

		if (
			isset( $_POST['msr_slides'] )
			&& is_array( $_POST['msr_slides'] )
		) {
			$slides = $_POST['msr_slides'];
		}

		$slides = array_map(
			function ( $slide ) {

				$layers = array();

				if ( isset( $slide['layers'] ) && is_array( $slide['layers'] ) ) {

					foreach ( $slide['layers'] as $layer ) {

						$animation = isset( $layer['animation'] )
							? $layer['animation']
							: 'none';

						$allowed_animations = array(
							'none',
							'fade',
							'fadeUp',
							'fadeDown',
							'slideLeft',
							'slideRight',
						);

						if ( ! in_array( $animation, $allowed_animations, true ) ) {
							$animation = 'none';
						}

						$layers[] = array(
							'type' => isset( $layer['type'] )
								&& in_array(
									$layer['type'],
									array( 'text', 'button' ),
									true
								)
								? $layer['type']
								: 'text',

							'text' => isset( $layer['text'] )
								? sanitize_text_field( $layer['text'] )
								: '',

							'x' => isset( $layer['x'] )
								? min( 100, max( 0, absint( $layer['x'] ) ) )
								: 50,

							'y' => isset( $layer['y'] )
								? min( 100, max( 0, absint( $layer['y'] ) ) )
								: 50,

							'font_size' => isset( $layer['font_size'] )
								? min( 200, max( 10, absint( $layer['font_size'] ) ) )
								: 32,

							'width' => isset( $layer['width'] )
								? min( 100, max( 0, absint( $layer['width'] ) ) )
								: 50,

							'custom_class' => isset( $layer['custom_class'] )
								? sanitize_text_field( $layer['custom_class'] )
								: '',

							'sp_x' => isset( $layer['sp_x'] )
								? min( 100, max( 0, absint( $layer['sp_x'] ) ) )
								: 50,

							'sp_y' => isset( $layer['sp_y'] )
								? min( 100, max( 0, absint( $layer['sp_y'] ) ) )
								: 50,

							'sp_font_size' => isset( $layer['sp_font_size'] )
								? min( 200, max( 10, absint( $layer['sp_font_size'] ) ) )
								: 32,

							'sp_width' => isset( $layer['sp_width'] )
								? min( 100, max( 0, absint( $layer['sp_width'] ) ) )
								: 80,

							'color' => isset( $layer['color'] )
								? sanitize_hex_color( $layer['color'] ) ?: '#333333'
								: '#333333',

							'animation' => $animation,

							'delay' => isset( $layer['delay'] )
								? min( 10000, max( 0, absint( $layer['delay'] ) ) )
								: 0,

							'duration' => isset( $layer['duration'] )
								? min( 5000, max( 100, absint( $layer['duration'] ) ) )
								: 800,

							'button_text' => isset( $layer['button_text'] )
								? sanitize_text_field( $layer['button_text'] )
								: '',

							'url' => isset( $layer['url'] )
								? esc_url_raw( $layer['url'] )
								: '',
						);
					}
				}

				return array(
					'number' => isset( $slide['number'] )
						? absint( $slide['number'] )
						: 0,

					'background' => isset( $slide['background'] )
						? absint( $slide['background'] )
						: 0,

					'sp_background' => isset( $slide['sp_background'] ) 
						? absint( $slide['sp_background'] ) 
						: 0, 

					'layers' => $layers,
				);

			},
			$slides
		);

		update_post_meta(
			$post_id,
			'msr_slides',
			$slides
		);
	}

	/* Enqueue admin scripts */
	public function enqueue_scripts( $hook ) {

		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || 'msr_slider' !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style(
			'msr-admin',
			MSR_URL . 'assets/css/msr-admin.css',
			array(),
			MSR_VERSION
		);

		wp_enqueue_script(
			'msr-admin',
			MSR_URL . 'assets/js/msr-admin.js',
			array( 'jquery' ),
			MSR_VERSION,
			true
		);
	}
}

new MSR_Slider_Meta_Box();