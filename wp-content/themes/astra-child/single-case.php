<?php


get_header();

global $post;
$page_id = $post->ID;
$page_title = $post->post_title;
$parent_title = get_post(2441)->post_title;
$category = get_the_terms($page_id, 'case-type'); // get case-type
// $tags = get_the_terms($page_id, 'post_tag'); // get post_tag
$date = strtotime($post->post_date);


?>

<!-- 內容開始 -->
<main class="article-single animated-slow animated fadeInUp">
    <div class="article-single-cont">
      <div class="article-warp">
        <div class="article-header">
          <div class="date text-crimson-text">
            <?php echo date("Y.m.d", $date); ?>
          </div>
          <h1 class="title">
            <?php echo $page_title; ?>
          </h1>
          <div class="meta">
            <ul class="categories">
              <?php foreach ($category as $key => $cat):?>
                <li class="item">
                  <a href="<?php echo esc_url( home_url( 'cases?case-type=' ) ) . $cat->slug; ?>"><?php echo $cat->name; ?></a>
                </li>
              <?php endforeach; ?>
            </ul>
            <?php /* if(!empty($tags)): ?>
              <ul class="tags">
                <?php foreach ($tags as $key => $tag):?>
                  <li class="item">
                    <a href="<?php echo esc_url( home_url( 'cases?post_tag=' ) ) . $tag->slug; ?>"><?php echo $tag->name; ?></a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; */?>
          </div>
        </div>
        <div class="article-cont">
          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <div class="btn-group">
      <?php                
            // 該分類的列表
            $args_column_article = array(
                'post_type' => 'case',
                'post_status'		=> 'publish',
                'posts_per_page' => -1,
                "order"     => "desc"
            );
            $column_article_list = get_posts($args_column_article);
            $current_key = ''; //當前專欄文章
            $len = 0;

            foreach ($column_article_list as $key => $column_article) {
                if ($column_article->ID == $page_id) {
                    $current_key = $key; 
                }
            }
            
    ?>
        <!-- 上一篇 -->
        <span class="btn-warp prev <?php echo $current_key > 0 ? 'show' : 'no-show'?>">
            <?php 
              if ($current_key > 0): 
                $prev_key = $column_article_list[$current_key - 1]; //上一篇
            ?>
                <a class="previous-btn btn" href="<?php echo esc_url( home_url( 'cases/' ) ) .  $prev_key->post_name; ?>">
                  <svg width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.8099 6.00244L5.7809 10.5394C5.92307 10.7092 6.00098 10.9235 6.00098 11.1449C6.00098 11.3664 5.92307 11.5807 5.7809 11.7504C5.71605 11.8283 5.63487 11.891 5.5431 11.934C5.45134 11.977 5.35124 11.9993 5.2499 11.9993C5.14856 11.9993 5.04846 11.977 4.9567 11.934C4.86493 11.891 4.78375 11.8283 4.7189 11.7504L0.218901 6.60944C0.0814613 6.44405 0.0044047 6.23681 0.000406265 6.0218C-0.00359217 5.8068 0.0657044 5.59683 0.196899 5.42644L4.7159 0.250444C4.78055 0.172256 4.86166 0.10931 4.95346 0.0661011C5.04525 0.0228925 5.14545 0.000488281 5.2469 0.000488281C5.34835 0.000488281 5.44855 0.0228925 5.54034 0.0661011C5.63214 0.10931 5.71325 0.172256 5.7779 0.250444C5.92007 0.420182 5.99798 0.634533 5.99798 0.855944C5.99798 1.07735 5.92007 1.29171 5.7779 1.46144L1.8099 6.00244Z" fill="#4B4B4B"/></svg>上一篇文章
                </a>
            <?php endif; ?>
        </span>
        <!-- 回專欄文章列表 -->
        <span class="btn-warp goback">
            <a class="btn" href="<?php echo esc_url( home_url( 'cases' ) ); ?>">返回文章目錄</a>
        </span>
        <!-- 下一篇 -->
        <span class="btn-warp next <?php echo $current_key < count($column_article_list) - 1 ? 'show' : 'no-show'?>">
            <?php 
              if ($current_key < count($column_article_list) - 1): 
                $next_key = $column_article_list[$current_key + 1]; //下一篇 
            ?>
                <a class="previous-btn btn" href="<?php echo esc_url( home_url( 'cases/' ) ) .  $next_key->post_name; ?>">下一篇文章<svg width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.8099 6.00244L5.7809 10.5394C5.92307 10.7092 6.00098 10.9235 6.00098 11.1449C6.00098 11.3664 5.92307 11.5807 5.7809 11.7504C5.71605 11.8283 5.63487 11.891 5.5431 11.934C5.45134 11.977 5.35124 11.9993 5.2499 11.9993C5.14856 11.9993 5.04846 11.977 4.9567 11.934C4.86493 11.891 4.78375 11.8283 4.7189 11.7504L0.218901 6.60944C0.0814613 6.44405 0.0044047 6.23681 0.000406265 6.0218C-0.00359217 5.8068 0.0657044 5.59683 0.196899 5.42644L4.7159 0.250444C4.78055 0.172256 4.86166 0.10931 4.95346 0.0661011C5.04525 0.0228925 5.14545 0.000488281 5.2469 0.000488281C5.34835 0.000488281 5.44855 0.0228925 5.54034 0.0661011C5.63214 0.10931 5.71325 0.172256 5.7779 0.250444C5.92007 0.420182 5.99798 0.634533 5.99798 0.855944C5.99798 1.07735 5.92007 1.29171 5.7779 1.46144L1.8099 6.00244Z" fill="#4B4B4B"/></svg></a>
            <?php endif; ?>
        </span>
    </div>
  

</main>

<?php
get_footer();
