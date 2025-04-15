<?php 

/* =================================

  首頁主banner輪播
  "<?php require get_theme_file_path( 'inc/custom-index-banner-carousel.php' ); ?>"
  "[astra_custom_layout id=382]"

 * ================================== */

$banner_text = get_field('banner_text');
$image_carousel = get_field('image_carousel');

?>


<?php if(!empty($image_carousel)): ?>
	<div class="index-image-carousel">
		<div class="cont">
			<div class="text">
				<h1 class="text-shadow"><?php echo $banner_text['title']; ?></h1>
				<h6 class="font-en text-shadow"><?php echo $banner_text['title_sub']; ?></h6>
				<h4 class="desc text-shadow no-br"><?php echo $banner_text['desc']; ?></h4>
			</div>
			<div class="slider-nav-warp">
				<ul class="slider-nav">
					<?php 
						foreach ($image_carousel as $item):
							if (!empty($item['show'])):
					?>
						<li class="slide-item">
							<?php echo $item['title']; ?>
						</li>
					<?php 
							endif;
						endforeach;
					?>
				</ul>
			</div>
		</div>
		
		
		<ul class="slider-for">
			<?php 
				foreach ($image_carousel as $item):
					if (!empty($item['show'])):
			?>
				<li class="slide-item" style="background-image: url(<?php echo $item['img']['url']; ?>)">
					<!-- <img class="img" src="<?php //echo $item['img']['url']; ?>" alt="<?php //echo $item['title']; ?>"/> -->
				</li>
			<?php 
					endif;
				endforeach;
			?>
		</ul>

		
		

	</div>

<?php endif; ?>