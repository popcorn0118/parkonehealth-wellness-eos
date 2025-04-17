<?php 

/* =================================

  首頁健康專欄
  "<?php require get_theme_file_path( 'inc/custom-index-column.php' ); ?>"
  "[astra_custom_layout id=1095]"

 * ================================== */


// 文章列表
$args_column = array(
    'post_type' => 'post',
    'post_status'		=> 'publish',
    'posts_per_page' => 4,
    'paged'         => 1,
    "order"     => "desc",
    // 's' => !empty($search_query) ? $search_query : "",
	// 'tax_query'       => array(
	// 	'relation' => 'AND',
	// 	  array(
	// 		'taxonomy' => 'post_tag',
	// 		'field' => 'slug',
	// 		'terms' => array( '首頁精選' ),
	// 	  ),
	// ),
);

$column = get_posts($args_column);

?>

<?php if (!empty($column)): ?>
<ul class="index-column">
	<?php 	
	foreach ($column as $item): 
		$title = $item->post_title;
		$name = $item->post_name;
		$date = strtotime($item->post_date);
		$excerpt = $item->post_excerpt;
		$desc = $item->post_content;
		$category = get_the_terms($item->ID, 'category'); // get category
		$tags = get_the_terms($item->ID, 'post_tag'); // get tag
		$img = wp_get_attachment_image_src( get_post_thumbnail_id( $item ), 'full' );
		$img_default = '';
		// $img_default = get_stylesheet_directory_uri() . '/assets/img/img-default.jpg';

	?>
	<li class="item">
		<a href="<?php echo esc_url( home_url( '/'.$name ) ); ?>" class="info">
			
			<div class="cont">
				<div class="img" style="background-image: url(<?php echo !empty($img[0]) ? $img[0] : $img_default; ?>);"></div>
				<div class="meta">
					<span class="date"><?php echo date("Y.m.d", $date); ?></span>
					<span class="cat"><?php echo $category[0]->name; ?></span>
					<!-- <?php foreach ($category as $key => $cat):?>
						<span class="cat"><?php //echo $cat->name; ?></span>
					<?php endforeach; ?>
					<?php foreach ($tags as $key => $tag):?>
						<span class="tag"><?php //echo $tag->name; ?></span>
					<?php endforeach; ?> -->
				</div>
				<h4 class="title"><?php echo $title; ?></h4>
				
				<div class="desc">
					<?php echo $excerpt; ?>
				</div>
				<!-- <div class="read-more">
					<a href="<?php //echo esc_url( home_url( 'news/'.$name ) ); ?>">閱讀全文</a>
				</div> -->

			</div>
			
			
		</a>
	</li>		

	<?php endforeach; ?>
</ul>
<?php endif; ?>
	