<?php 

/* =================================

  首頁主banner輪播
  "<?php require get_theme_file_path( 'inc/custom-index-banner-carousel.php' ); ?>"
  "[astra_custom_layout id=382]"

 * ================================== */

$image_carousel = get_field('image_carousel');

?>


<?php if(!empty($image_carousel)): ?>
	<div class="index-image-carousel">
		<div class="cont">
			<div class="text">
				<h1 class="text-shadow">博田國際健康管理中心</h1>
				<h6 class="font-en text-shadow">International Health Management Center</h6>
				<h4 class="desc text-shadow no-br">以醫學中心規格打造五星級受檢感受，<br/>各類高端新穎的醫學檢查儀器、舒適隱密的診療空間及全方位的貼心服務。</h4>
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