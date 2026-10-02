<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slider Post Type
 */
class MSR_Slider_Post_Type {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action(
			'init',
			array( $this, 'register_post_type' )
		);
	}

	/**
	 * Register Slider post type
	 */
	public function register_post_type() {

		$labels = array(
			'name'               => 'スライダー',
			'singular_name'      => 'スライダー',
			'add_new'            => '新規追加',
			'add_new_item'       => 'スライダーを追加',
			'edit_item'          => 'スライダーを編集',
			'new_item'           => '新しいスライダー',
			'view_item'          => 'スライダーを表示',
			'search_items'       => 'スライダーを検索',
			'not_found'          => 'スライダーが見つかりません。',
			'menu_name'          => 'スライダー',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-images-alt2',
			'supports'           => array( 'title' ),
			'has_archive'        => false,
			'rewrite'            => false,
			'exclude_from_search' => true,
			'show_in_nav_menus'  => false,
		);

		register_post_type(
			'msr_slider',
			$args
		);
	}
}

/**
 * Initialize
 */
new MSR_Slider_Post_Type();