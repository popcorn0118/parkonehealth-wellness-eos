<?php 

/* =================================

  中心介紹 > 儀器介紹
  "<?php require get_theme_file_path( 'inc/custom-center-instrument.php' ); ?>"
  "[astra_custom_layout id=2309]"

* ================================== */

$instr_intro = get_field('instr_intro');

?>

<ul class="center-instrument">
<?php 	

foreach ($instr_intro as $item): 
	$title = $item->post_title;
	$desc = $item->post_content;
	$img = wp_get_attachment_image_src( get_post_thumbnail_id( $item ), 'full' );
	$img_default = '';

?>
	<li class="item">			
		<div class="cont">
			<div class="img">
			<img src="<?php echo !empty($img[0]) ? $img[0] : $img_default; ?>" alt="<?php echo $title; ?>">
			</div>
			<h3 class="title"><?php echo $title; ?></h3>
			<div class="desc">
				<?php echo $desc; ?>
			</div>
		</div>
			
	</li>	


<?php endforeach; ?>
</ul>