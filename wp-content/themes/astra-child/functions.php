<?php

/**
 * astra-child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package astra-child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define('CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0');

/**
 * Enqueue styles
 */
function child_enqueue_styles()
{

	wp_enqueue_script('slick-js', get_stylesheet_directory_uri() . '/assets/js/slick.min.js', array('jquery'), '', true);
	wp_enqueue_script('main', get_stylesheet_directory_uri() . '/assets/js/main.js', array('jquery'), '', true);


	//css
	wp_enqueue_style('slick-css', get_stylesheet_directory_uri() . '/assets/css/slick.css', array(), CHILD_THEME_ASTRA_CHILD_VERSION);
	wp_enqueue_style('astra-child-theme-css', get_stylesheet_directory_uri() . '/assets/css/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all');
}

add_action('wp_enqueue_scripts', 'child_enqueue_styles', 15);



// footer copyright year
function current_year_init()
{
	function current_year_fn()
	{
		$year = date_i18n('Y');
		return $year;
	}
	add_shortcode('current_year', 'current_year_fn');
}
add_action('init', 'current_year_init');

// 客製化 post type
function create_post_type()
{

	register_post_type(
		'case',
		array(
			'labels' 				=> array(
				'name' 				=> __('案例見證'),
				'singular_name' 	=> __('案例見證')
			),
			'public' 				=> true,
			'has_archive' 			=> true,
			'menu_icon' 			=> 'dashicons-welcome-write-blog',
			'supports' 				=> array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
			'taxonomies' 			=> array('case-type', 'case-tag'),
			'capability_type' 		=> 'page',
			'map_meta_cap'			=> true,
			// 'show_in_rest'      	=> true, // To use Gutenberg editor.
			// 'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'taxonomies'  ),
		)
	);

	//flush_rewrite_rules();
}
add_action('init', 'create_post_type');

function astra_child_rewrite_flush()
{
	create_post_type();            // 先確保 CPT 已註冊
	flush_rewrite_rules(false);  // 第二個參數 false 避免過度重建
}
add_action('after_switch_theme', 'astra_child_rewrite_flush');

// 新增 Taxonomy 給客製化的 post type
function create_custom_taxonomy()
{

	// 分類
	register_taxonomy(
		'case-type',
		array('case'),
		array(
			'labels' 				=> array(
				'name' 				=> __('案例見證分類'),
				'singular_name' 	=> __('案例見證分類')
			),
			'show_ui' 				=> true,
			'show_admin_column' 	=> true,
			'query_var' 			=> true,
			'hierarchical' 			=> true,
			'rewrite' 				=> array('slug' => 'case-type'),
		)
	);
}
add_action('init', 'create_custom_taxonomy', 0);

// 個人預約諮詢 - 想了解哪個方案 欄位
add_shortcode('checkup_plan_list', function () {
	$args = array(
		'post_type' => 'checkup-plan',
		'posts_per_page' => -1,
		'post_status' => 'publish'
	);
	$posts = get_posts($args);
	$html = '<div class="checkup-plan-list">';
	foreach ($posts as $post) {
		$id = $post->ID;
		$title = esc_html(get_the_title($id));
		$male = get_field('plan_price_male', $id) ? get_field('plan_price_male', $id) : '未定';
		$female = get_field('plan_price_female', $id) ? get_field('plan_price_female', $id) : '未定';
		$url = esc_url(get_permalink($id));
		$checkup_price_gender_and_items = get_field("checkup_price_gender_and_items", $id);

		$html .= '<label class="plan-item">';
		$html .= '<input class="plan-checkbox" type="checkbox" name="checkup_plans[]" value="' . esc_attr($title) . '">';
		$html .= '<span class="plan-name">' . $title . '</span>';
		foreach ($checkup_price_gender_and_items as $checkup_price_gender_and_item) {
			if ($checkup_price_gender_and_item['gender'] == 'male') {
				$html .= '<span class="plan-price male">男性';
			} else if ($checkup_price_gender_and_item['gender'] == 'female') {
				$html .= '<span class="plan-price female">女性';
			}
			$html .= 'NT$' . $checkup_price_gender_and_item['price'] . '</span>';
		}
		$html .= '<span class="line">｜</span>';
		$html .= '<a href="' . $url . '" target="_blank" class="plan-link">了解方案</a>';
		$html .= '</label>';
	}
	$html .= '</div>';
	return $html;
});
// 想了解哪個方案，用、分隔
add_filter('wpcf7_mail_tag_replaced', function ($replaced, $submitted, $html) {
	// 確認 submitted 是 array
	if (is_array($submitted)) {
		// 這邊可以根據 _wpcf7_container_post 確認是哪一個表單
		$form_id = isset($submitted['_wpcf7']) ? intval($submitted['_wpcf7']) : 0;

		// 只有特定 ID 的表單才進行處理
		if ($form_id === '436b503' && isset($submitted['name']) && $submitted['name'] === 'checkup_plans') {
			if (is_array($replaced)) {
				$replaced = implode('、', array_map('sanitize_text_field', $replaced));
			} elseif (is_string($replaced)) {
				$replaced = sanitize_text_field($replaced);
			}
		}
	}
	return $replaced;
}, 10, 3);

//上方選單判斷 非管理員權限隱藏的項目
add_filter('wp_get_nav_menu_items', 'hide_menu_item_by_class_for_non_admins', 10, 3);
function hide_menu_item_by_class_for_non_admins($items, $menu, $args)
{
	if (!is_admin() && !current_user_can('administrator')) {
		foreach ($items as $key => $item) {
			if (in_array('admin-only', $item->classes)) {
				unset($items[$key]);
			}
		}
		$items = array_values($items); // 重排索引，避免主題崩
	}
	return $items;
}

//全站停用 contact form 7 自動加上的<p>、<br>...
add_filter('wpcf7_autop_or_not', '__return_false');

// use function WPvividGuzzleHttp\json_decode;

require_once "health_checkup.php";



?>