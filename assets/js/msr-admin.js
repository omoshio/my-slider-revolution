jQuery(function ($) {

    function updateSlideNumbers() {

        $('.msr-slide').each(function (index) {

            const $slide = $(this);
            const slideNumber = index + 1;

            $slide
                .find('.msr-slide__title')
                .text('Slide ' + slideNumber);

            /*
            * Slide内にあるすべてのname属性を
            * 現在のSlide番号に合わせる
            */
            $slide.find('[name]').each(function () {

                const $field = $(this);
                const name = $field.attr('name');

                if (!name) {
                    return;
                }

                const newName = name.replace(
                    /^msr_slides\[\d+\]/,
                    'msr_slides[' + index + ']'
                );

                $field.attr('name', newName);

            });

            /*
            * Slide番号
            */
            $slide
                .find('input[name$="[number]"]')
                .val(slideNumber);

        });
    }

    function updateLayerNumbers($slide) {

        const slideIndex = $slide.index();

        $slide.find('.msr-layer-editor').each(function (index) {

            const $layer = $(this);

            /*
            * Layer番号を更新
            */
            $layer
                .find('.msr-layer-editor__title')
                .first()
                .text('Layer ' + (index + 1));

            /*
            * Layer内のname属性を更新
            */
            $layer.find('[name]').each(function () {

                const $field = $(this);
                const name = $field.attr('name');

                if (!name) {
                    return;
                }

                const newName = name.replace(
                    /msr_slides\[\d+\]\[layers\]\[\d+\]/,
                    'msr_slides[' + slideIndex + '][layers][' + index + ']'
                );

                $field.attr('name', newName);

            });

        });

    }

	/* Slideを追加 */
	$('#msr-add-slide').on('click', function () {

		const slideIndex = $('.msr-slide').length;
		const slideNumber = slideIndex + 1;

        const slideHtml = `
            <div class="msr-slide">

                <strong class="msr-slide__title">
                    Slide ${slideNumber}
                </strong>

                <input
                    type="hidden"
                    name="msr_slides[${slideIndex}][number]"
                    value="${slideNumber}"
                >

                <div class="msr-slide__background">

                    <input
                        type="hidden"
                        class="msr-background-id"
                        name="msr_slides[${slideIndex}][background]"
                        value=""
                    >

                    <div class="msr-background-preview"></div>

                    <button
                        type="button"
                        class="button msr-select-background"
                    >
                        背景画像を選択
                    </button>

                    <button
                        type="button"
                        class="button msr-remove-background"
                        disabled
                    >
                        背景画像を削除
                    </button>

                </div>

                <div class="msr-slide__background msr-slide__background--sp">

                    <input
                        type="hidden"
                        class="msr-sp-background-id"
                        name="msr_slides[${slideIndex}][sp_background]"
                        value=""
                    >

                    <div class="msr-sp-background-preview"></div>

                    <button
                        type="button"
                        class="button msr-select-sp-background"
                    >
                        SP背景画像を選択
                    </button>

                    <button
                        type="button"
                        class="button msr-remove-sp-background"
                        disabled
                    >
                        背景画像を削除
                    </button>

                </div>

                <div class="msr-layers">

                    <div class="msr-layers__list"></div>

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
        `;

		$('.msr-slides__list').append(slideHtml);

		updateSlideNumbers();

	});


	/* Slideを削除 */
	$(document).on('click', '.msr-remove-slide', function () {

		$(this)
			.closest('.msr-slide')
			.remove();

		updateSlideNumbers();

	});

    /* 背景画像を選択 */
    $(document).on('click', '.msr-select-background', function () {

        const button = $(this);
        const slide = button.closest('.msr-slide');
        const input = slide.find('.msr-background-id');
        const preview = slide.find('.msr-background-preview');
        const removeButton = slide.find('.msr-remove-background');

        const frame = wp.media({
            title: '背景画像を選択',
            button: {
                text: 'この画像を使用'
            },
            multiple: false
        });

        frame.on('select', function () {

            const attachment = frame
                .state()
                .get('selection')
                .first()
                .toJSON();

            console.log('attachment:', attachment);
            console.log('sizes:', attachment.sizes);
            console.log(
                'medium:',
                attachment.sizes && attachment.sizes.medium
                    ? attachment.sizes.medium.url
                    : 'mediumなし'
            );

            const imageUrl = attachment.sizes && attachment.sizes.medium
                ? attachment.sizes.medium.url
                : attachment.url;

            console.log('使用するURL:', imageUrl);

            input.val(attachment.id);

            preview.html(
                '<img src="' + imageUrl + '" alt="">'
            );

            removeButton.prop('disabled', false);

        });

        frame.open();

    });

    /* SP背景画像を選択 */
    $(document).on('click', '.msr-select-sp-background', function () {

        const button = $(this);
        const slide = button.closest('.msr-slide');
        const input = slide.find('.msr-sp-background-id');
        const preview = slide.find('.msr-sp-background-preview');
        const removeButton = slide.find('.msr-remove-sp-background');

        const frame = wp.media({
            title: 'SP背景画像を選択',
            button: {
                text: 'この画像を使用'
            },
            multiple: false
        });

        frame.on('select', function () {

            const attachment = frame
                .state()
                .get('selection')
                .first()
                .toJSON();

            console.log('SP attachment:', attachment);
            console.log('SP sizes:', attachment.sizes);

            const imageUrl = attachment.sizes && attachment.sizes.medium
                ? attachment.sizes.medium.url
                : attachment.url;

            console.log('SP使用するURL:', imageUrl);

            input.val(attachment.id);

            preview.html(
                '<img src="' + imageUrl + '" alt="">'
            );

            removeButton.prop('disabled', false);

        });

        frame.open();

    });

    /* 背景画像を削除 */
    $(document).on('click', '.msr-remove-background', function () {

        const button = $(this);
        const slide = button.closest('.msr-slide');

        slide.find('.msr-background-id').val('');
        slide.find('.msr-background-preview').empty();

        button.prop('disabled', true);

    });

    /* SP背景画像を削除 */
    $(document).on('click', '.msr-remove-sp-background', function () {

        const button = $(this);
        const slide = button.closest('.msr-slide');

        slide.find('.msr-sp-background-id').val('');
        slide.find('.msr-sp-background-preview').empty();

        button.prop('disabled', true);

    });

    $(document).on('click', '.msr-add-layer', function () {

        const $button = $(this);
        const $slide = $button.closest('.msr-slide');
        const $layers = $slide.find('.msr-layers__list');

        const layerIndex = $layers.find('.msr-layer-editor').length;

        const layerHtml = `
            <div class="msr-layer-editor">

                <strong class="msr-layer-editor__title">
                    Layer ${layerIndex + 1}
                </strong>

                <label>
                    Layerタイプ

                    <select
                        class="msr-layer-type"
                        name="msr_slides[0][layers][${layerIndex}][type]"
                    >
                        <option value="text" selected>
                            Text
                        </option>

                        <option value="button">
                            Button
                        </option>
                    </select>
                </label>

                <label>
                    テキスト

                    <input
                        type="text"
                        name="msr_slides[0][layers][${layerIndex}][text]"
                        value=""
                        class="widefat"
                    >
                </label>

                <div class="msr-layer-editor__button">

                    <label>
                        ボタンテキスト

                        <input
                            type="text"
                            name="msr_slides[0][layers][${layerIndex}][button_text]"
                            value="詳しく見る"
                            class="widefat"
                        >
                    </label>

                    <label>
                        URL

                        <input
                            type="url"
                            name="msr_slides[0][layers][${layerIndex}][url]"
                            value=""
                            class="widefat"
                        >
                    </label>

                </div>

                <div class="msr-slide__position">

                    <label>
                        X
                        <input
                            type="number"
                            name="msr_slides[0][layers][${layerIndex}][x]"
                            value="50"
                            min="0"
                            max="100"
                        >
                        %
                    </label>

                    <label>
                        Y
                        <input
                            type="number"
                            name="msr_slides[0][layers][${layerIndex}][y]"
                            value="50"
                            min="0"
                            max="100"
                        >
                        %
                    </label>

                    <label>
                        フォントサイズ
                        <input
                            type="number"
                            name="msr_slides[0][layers][${layerIndex}][font_size]"
                            value="32"
                            min="10"
                            max="200"
                        >
                        px
                    </label>

                    <label>
                        Font Weight
                        <select
                            name="msr_slides[0][layers][${layerIndex}][font_weight]"
                        >
                            <option value="normal">normal</option>
                            <option value="bold" selected>bold</option>
                        </select>
                    </label>

                    <label>
                        Width
                        <input
                            type="number"
                            name="msr_slides[0][layers][${layerIndex}][width]"
                            value="50"
                            min="0"
                            max="100"
                        >
                        %
                    </label>

                    <label>
                        カスタムクラス
                        <input
                            type="text"
                            name="msr_slides[0][layers][${layerIndex}][custom_class]"
                            value=""
                            class="widefat"
                            placeholder="例：hero-title"
                        >
                    </label>

                    <div class="msr-slide__position msr-slide__position--sp">

                        <label>
                            SP X
                            <input
                                type="number"
                                name="msr_slides[0][layers][${layerIndex}][sp_x]"
                                value="50"
                                min="0"
                                max="100"
                            >
                            %
                        </label>

                        <label>
                            SP Y
                            <input
                                type="number"
                                name="msr_slides[0][layers][${layerIndex}][sp_y]"
                                value="50"
                                min="0"
                                max="100"
                            >
                            %
                        </label>

                        <label>
                            SPフォントサイズ
                            <input
                                type="number"
                                name="msr_slides[0][layers][${layerIndex}][sp_font_size]"
                                value="32"
                                min="10"
                                max="200"
                            >
                            px
                        </label>

                        <label>
                            SP Width
                            <input
                                type="number"
                                name="msr_slides[0][layers][${layerIndex}][sp_width]"
                                value="80"
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
                            name="msr_slides[0][layers][${layerIndex}][color]"
                            value="#333333"
                        >
                    </label>

                    <label>
                        アニメーション
                        <select
                            name="msr_slides[0][layers][${layerIndex}][animation]"
                        >
                            <option value="none">なし</option>
                            <option value="fade">fade</option>
                            <option value="fadeUp">fadeUp</option>
                            <option value="fadeDown">fadeDown</option>
                            <option value="slideLeft">slideLeft</option>
                            <option value="slideRight">slideRight</option>
                        </select>
                    </label>

                    <label>
                        Delay
                        <input
                            type="number"
                            name="msr_slides[0][layers][${layerIndex}][delay]"
                            value="0"
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
                            name="msr_slides[0][layers][${layerIndex}][duration]"
                            value="800"
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
        `;

        $layers.append(layerHtml);

        const $newLayer = $layers.find('.msr-layer-editor').last();

        $newLayer.find('.msr-layer-editor__button').hide();

        updateLayerNumbers($slide);

    });

    $(document).on('click', '.msr-remove-layer', function () {

        const $layer = $(this).closest('.msr-layer-editor');
        const $slide = $(this).closest('.msr-slide');

        $layer.remove();

        updateLayerNumbers($slide);

    });

    $(document).on('change', '.msr-layer-type', function () {

        const $select = $(this);
        const $layer = $select.closest('.msr-layer-editor');

        if ( 'button' === $select.val() ) {
            $layer.find('.msr-layer-editor__button').show();
        } else {
            $layer.find('.msr-layer-editor__button').hide();
        }

    });

    $('.msr-layer-editor').each(function () {

        const $layer = $(this);
        const type = $layer.find('.msr-layer-type').val();

        if ( 'button' === type ) {
            $layer.find('.msr-layer-editor__button').show();
        } else {
            $layer.find('.msr-layer-editor__button').hide();
        }

    });
});

