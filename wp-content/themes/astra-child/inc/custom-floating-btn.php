<?php 

/* =================================

  全站浮動按鈕
  "<?php require get_theme_file_path( 'inc/custom-floating-btn.php' ); ?>"
  "[astra_custom_layout id=963]"

 * ================================== */

$floating_btn = get_field('floating_btn', 'option');

if ($floating_btn):
?>
	<div class="floating-btn">
	
	  <?php if (!empty($floating_btn['btn_top'])): ?>
		<div class="floating-btn-top">
		  <?php foreach ($floating_btn['btn_top'] as $btn): ?>
			<?php if (!empty($btn['show'])): ?>
			  <?php $has_link = !empty($btn['link']['url']); ?>
			  <<?php echo $has_link ? 'a' : 'div'; ?>
				<?php if ($has_link): ?>
				  href="<?php echo esc_url($btn['link']['url']); ?>"
				  <?php if (!empty($btn['link']['target'])): ?> target="_blank"<?php endif; ?>
				<?php endif; ?>
				class="floating-btn-item">
				
				<?php if (!empty($btn['icon'])): ?>
				  <div class="floating-btn-icon">
					<img src="<?php echo esc_url($btn['icon']['url']); ?>" alt="">
				  </div>
				<?php endif; ?>
	
				<?php if (!empty($btn['title'])): ?>
				  <div class="floating-btn-title">
					<?php echo esc_html($btn['title']); ?>
				  </div>
				<?php endif; ?>
	
			  </<?php echo $has_link ? 'a' : 'div'; ?>>
			<?php endif; ?>
		  <?php endforeach; ?>
		</div>
	  <?php endif; ?>
	
	  <?php if (!empty($floating_btn['btn_botton'])): ?>
		<div class="floating-btn-bottom">
		  <?php foreach ($floating_btn['btn_botton'] as $btn): ?>
			<?php if (!empty($btn['show'])): ?>
			  <?php $has_link = !empty($btn['link']['url']); ?>
			  <<?php echo $has_link ? 'a' : 'div'; ?>
				<?php if ($has_link): ?>
				  href="<?php echo esc_url($btn['link']['url']); ?>"
				  <?php if (!empty($btn['link']['target'])): ?> target="_blank"<?php endif; ?>
				<?php endif; ?>
				class="floating-btn-item">
	
				<?php if (!empty($btn['icon'])): ?>
				  <div class="floating-btn-icon">
					<img src="<?php echo esc_url($btn['icon']['url']); ?>" alt="">
				  </div>
				<?php endif; ?>
	
			  </<?php echo $has_link ? 'a' : 'div'; ?>>
			<?php endif; ?>
		  <?php endforeach; ?>
		</div>
	  <?php endif; ?>
	
	</div>
<?php endif; ?>