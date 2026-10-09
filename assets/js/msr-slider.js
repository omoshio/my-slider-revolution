jQuery(function ($) {

	const $slider = $('.msr-slider__list');

	$slider.slick({

		slidesToShow: 1,
		slidesToScroll: 1,

		fade: true,
		speed: 600,
		cssEase: 'linear',

		arrows: true,

		prevArrow: '<div class="msr-slick-arrow msr-prev-arrow"></div>',
		nextArrow: '<div class="msr-slick-arrow msr-next-arrow"></div>',

		dots: true,

		autoplay: true,
		autoplaySpeed: 5000,

		infinite: true

	});


	/**
	 * Layer animation
	 */
	function animateCurrentSlide() {

		const $currentSlide = $slider.find('.slick-current');

		$currentSlide.find('.msr-layer').each(function () {

			const $layer = $(this);

			if (
				! $layer.hasClass('msr-animation--fade') &&
				! $layer.hasClass('msr-animation--fadeUp') &&
				! $layer.hasClass('msr-animation--fadeDown') &&
				! $layer.hasClass('msr-animation--slideLeft') &&
				! $layer.hasClass('msr-animation--slideRight')
			) {
				return;
			}

			// アニメーションをリセット
			$layer.removeClass('is-animate');

			// reflow
			void $layer[0].offsetWidth;

			// アニメーション開始
			$layer.addClass('is-animate');

		});

	}


	/**
	 * 初期表示
	 */
	animateCurrentSlide();


	/**
	 * スライド切り替え後
	 */
	$slider.on('beforeChange', function () {

		const $currentSlide = $slider.find('.slick-current');

		$currentSlide.find('.msr-layer').removeClass('is-animate');

	});


	$slider.on('afterChange', function () {

		animateCurrentSlide();

	});

});