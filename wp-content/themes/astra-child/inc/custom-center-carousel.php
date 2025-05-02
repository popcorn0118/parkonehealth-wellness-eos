<?php 

/* =================================

  中心介紹 > 中心環境 輪播
  "<?php require get_theme_file_path( 'inc/custom-center-carousel.php' ); ?>"
  "[astra_custom_layout id=2273]"

* ================================== */

$carousel = get_field('carousel');

?>
<div class="center-carousel">

	<ul class="slider-for">
		<?php 
			foreach ($carousel as $item):
				if (!empty($item['show'])):
		?>
			<li class="slide-item" style="background-image: url(<?php echo $item['img']['url']; ?>)">
				<div class="aspect-ratio-box"></div>
				<!-- <img class="img" src="<?php //echo $item['img']['url']; ?>" alt="<?php //echo $item['title']; ?>"/> -->
			</li>
		<?php 
				endif;
			endforeach;
		?>
	</ul>

	<div class="slider-nav-warp">
		<ul class="slider-nav">
			<?php 
				foreach ($carousel as $item):
					if (!empty($item['show'])):
			?>
				<li class="slide-item">
					<h3 class="title"><?php echo $item['title']; ?></h3>
					<h5 class="title-sub"><?php echo $item['title_sub']; ?></h5>
				</li>
			<?php 
					endif;
				endforeach;
			?>
		</ul>
	</div>

</div>
